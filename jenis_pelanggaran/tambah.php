<?php
session_start();
include '../config/database.php';

$popup = false;
$popup_type = '';
$popup_message = '';
$redirect_url = '';

/* TAMBAH DATA */
if (isset($_POST['simpan'])) {
    $nama_jenis = trim($_POST['nama_jenis']);
    $deskripsi = trim($_POST['deskripsi']);
    $poin = trim($_POST['poin']);
    $kategori_kode = trim($_POST['kategori_kode']);

    if ($nama_jenis != '' && $deskripsi != '' && $poin != '' && $kategori_kode != '') {
        $stmt = $pdo->prepare(
            "INSERT INTO jenis_pelanggaran (nama_jenis, deskripsi, poin, kategori_kode)
             VALUES (?, ?, ?, ?)"
        );
        $stmt->execute([
            $nama_jenis,
            $deskripsi,
            $poin,
            $kategori_kode,
        ]);

        $popup = true;
        $popup_type = 'success';
        $popup_message = 'Data jenis pelanggaran berhasil ditambahkan!';
        $redirect_url = 'index.php';
    } else {
        $popup = true;
        $popup_type = 'error';
        $popup_message = 'Semua data wajib diisi!';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="../assets/style.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Jenis Pelanggaran</title>
</head>
<body>

<div class="container-add">
    <div class="form-box">
        <h2>Kelola Jenis Pelanggaran</h2>
  
        <form method="post">
            <label>Nama Jenis</label>
            <input
                type="text"
                name="nama_jenis"
                required
                value="<?= isset($_POST['nama_jenis']) ? htmlspecialchars($_POST['nama_jenis']) : '' ?>"
            >

            <label>Deskripsi Pelanggaran</label>
            <input
                type="text"
                name="deskripsi"
                required
                value="<?= isset($_POST['deskripsi']) ? htmlspecialchars($_POST['deskripsi']) : '' ?>"
            >

            <label>Poin Pelanggaran</label>
            <input
                type="number"
                name="poin"
                required
                value="<?= isset($_POST['poin']) ? htmlspecialchars($_POST['poin']) : '' ?>"
            >

            <label>Kategori kode</label>
            <select name="kategori_kode" required>
                <option value="">-- Pilih Kategori --</option>
                <option value="SS" <?= (isset($_POST['kategori_kode']) && $_POST['kategori_kode'] == 'SS') ? 'selected' : '' ?>>SS - Seragam Sekolah</option>
                <option value="KS" <?= (isset($_POST['kategori_kode']) && $_POST['kategori_kode'] == 'KS') ? 'selected' : '' ?>>KS - Kehadiran di Sekolah</option>
                <option value="PBM" <?= (isset($_POST['kategori_kode']) && $_POST['kategori_kode'] == 'PBM') ? 'selected' : '' ?>>PBM - Proses Belajar Mengajar</option>
                <option value="PNN" <?= (isset($_POST['kategori_kode']) && $_POST['kategori_kode'] == 'PNN') ? 'selected' : '' ?>>PNN - Pelanggaran Norma-Norma</option>
                <option value="PB" <?= (isset($_POST['kategori_kode']) && $_POST['kategori_kode'] == 'PB') ? 'selected' : '' ?>>PB - Pelanggaran Berat</option>
                <option value="KB" <?= (isset($_POST['kategori_kode']) && $_POST['kategori_kode'] == 'KB') ? 'selected' : '' ?>>KB - Kesopanan Berkendaraan</option>
                <option value="UB" <?= (isset($_POST['kategori_kode']) && $_POST['kategori_kode'] == 'UB') ? 'selected' : '' ?>>UB - Upacara Bendera</option>
            </select>

            <button name="simpan">Simpan</button>
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