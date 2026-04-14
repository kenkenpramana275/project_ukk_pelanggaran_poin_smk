<?php
session_start();
include '../config/database.php';

$popup = false;
$popup_type = '';
$popup_message = '';
$redirect_url = '';

if (!isset($_SESSION['login'])) {
    header("Location: ../auth/login.php");
    exit;
}

// Cek apakah ada ID
if (!isset($_GET['id']) || $_GET['id'] == '') {
    header("Location: index.php");
    exit;
}

$id = $_GET['id'];

// Ambil data berdasarkan ID
$stmt = $pdo->prepare("SELECT * FROM jenis_pelanggaran WHERE id_jenis = ?");
$stmt->execute([$id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

// Jika data tidak ditemukan
if (!$data) {
    header("Location: index.php");
    exit;
}

// PROSES UPDATE
if (isset($_POST['update'])) {
    $nama_jenis = trim($_POST['nama_jenis']);
    $deskripsi = trim($_POST['deskripsi']);
    $poin = trim($_POST['poin']);
    $kategori_kode = trim($_POST['kategori_kode']);

    if ($nama_jenis != '' && $deskripsi != '' && $poin != '' && $kategori_kode != '') {
        $stmt = $pdo->prepare("
            UPDATE jenis_pelanggaran 
            SET nama_jenis = ?, 
                deskripsi = ?, 
                poin = ?, 
                kategori_kode = ?
            WHERE id_jenis = ?
        ");

        $stmt->execute([
            $nama_jenis,
            $deskripsi,
            $poin,
            $kategori_kode,
            $id
        ]);

        // refresh data terbaru
        $stmt = $pdo->prepare("SELECT * FROM jenis_pelanggaran WHERE id_jenis = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        $popup = true;
        $popup_type = 'success';
        $popup_message = 'Data jenis pelanggaran berhasil diperbarui!';
        $redirect_url = 'index.php';
    } else {
        $popup = true;
        $popup_type = 'error';
        $popup_message = 'Semua data wajib diisi!';

        // isi ulang form dengan data POST terakhir
        $data['nama_jenis'] = $nama_jenis;
        $data['deskripsi'] = $deskripsi;
        $data['poin'] = $poin;
        $data['kategori_kode'] = $kategori_kode;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Jenis Pelanggaran</title>
    <link rel="stylesheet" href="../assets/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>

    <div class="container-add">
        <div class="form-box">
            <h2>Edit Jenis Pelanggaran</h2>

            <form method="POST">
                <label>Nama Jenis</label>
                <input type="text" name="nama_jenis"
                    value="<?= htmlspecialchars($data['nama_jenis']) ?>" required>

                <label>Deskripsi</label>    
                <input type="text" name="deskripsi"
                    value="<?= htmlspecialchars($data['deskripsi']) ?>" required>

                <label>Poin</label>
                <input type="number" name="poin"
                    value="<?= htmlspecialchars($data['poin']) ?>" required>

                <label>Kategori Kode</label>
                <select name="kategori_kode" required>
                    <option value="">-- Pilih Kategori --</option>
                    <option value="SS" <?= ($data['kategori_kode'] == 'SS') ? 'selected' : '' ?>>SS - Seragam Sekolah</option>
                    <option value="KS" <?= ($data['kategori_kode'] == 'KS') ? 'selected' : '' ?>>KS - Kehadiran di Sekolah</option>
                    <option value="PBM" <?= ($data['kategori_kode'] == 'PBM') ? 'selected' : '' ?>>PBM - Proses Belajar Mengajar</option>
                    <option value="PNN" <?= ($data['kategori_kode'] == 'PNN') ? 'selected' : '' ?>>PNN - Pelanggaran Norma-Norma</option>
                    <option value="PB" <?= ($data['kategori_kode'] == 'PB') ? 'selected' : '' ?>>PB - Pelanggaran Berat</option>
                    <option value="KB" <?= ($data['kategori_kode'] == 'KB') ? 'selected' : '' ?>>KB - Kesopanan Berkendaraan</option>
                    <option value="UB" <?= ($data['kategori_kode'] == 'UB') ? 'selected' : '' ?>>UB - Upacara Bendera</option>
                </select>    

                <button type="submit" name="update" class="btn btn-edit">
                    Update
                </button>        
                <a href="index.php" class="btn">Cancel</a>
            </form>        
        </div>
    </div>

    <?php if ($popup): ?>
        <div class="popup-overlay" id="popupOverlay">
            <div class="popup-box <?= $popup_type === 'success' ? 'popup-success' : 'popup-error' ?>">
                <div class="popup-icon">
                    <?= $popup_type === 'success' ? '✓' : '!' ?>
                </div>
                <h3><?= $popup_type === 'success' ? 'Berhasil' : 'Peringatan' ?></h3>
                <p><?= htmlspecialchars($popup_message) ?></p>
                <button type="button" class="popup-btn" onclick="closePopup()">OK</button>
            </div>
        </div>

        <script>
            function closePopup() {
                const redirectUrl = <?= json_encode($redirect_url) ?>;
                if (redirectUrl) {
                    window.location.href = redirectUrl;
                } else {
                    document.getElementById('popupOverlay').style.display = 'none';
                }
            }
        </script>
    <?php endif; ?>

</body>
</html>