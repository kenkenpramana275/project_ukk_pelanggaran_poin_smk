<?php
include '../config/database.php';

$popup = false;
$popup_type = '';
$popup_message = '';
$redirect_url = '';

if (!isset($_GET['id']) || $_GET['id'] == '') {
    header("Location: index.php");
    exit;
}

$id = $_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM siswa WHERE id_siswa = ?");
$stmt->execute([$id]);
$siswa = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$siswa) {
    header("Location: index.php");
    exit;
}

if (isset($_POST['update'])) {
    $nama = trim($_POST['nama']);
    $nis = trim($_POST['nis']);
    $id_kelas = trim($_POST['id_kelas']);
    $alamat = trim($_POST['alamat']);
    $kontak_siswa = trim($_POST['kontak_siswa']);
    $jenis_kelamin = trim($_POST['jenis_kelamin']);
    $nama_orang_tua = trim($_POST['nama_orang_tua']);
    $kontak_orang_tua = trim($_POST['kontak_orang_tua']);
    $pekerjaan_orang_tua = trim($_POST['pekerjaan_orang_tua']);

    if (
        $nama != "" && $nis != "" && $id_kelas != "" && $alamat != "" &&
        $kontak_siswa != "" && $jenis_kelamin != "" &&
        $nama_orang_tua != "" && $kontak_orang_tua != "" && $pekerjaan_orang_tua != ""
    ) {
        // cek apakah NIS sudah dipakai siswa lain
        $cek = $pdo->prepare("SELECT COUNT(*) FROM siswa WHERE nis = ? AND id_siswa != ?");
        $cek->execute([$nis, $id]);
        $jumlah = $cek->fetchColumn();

        if ($jumlah > 0) {
            $popup = true;
            $popup_type = 'error';
            $popup_message = 'NIS sudah digunakan oleh siswa lain!';
        } else {
            $stmt = $pdo->prepare("
                UPDATE siswa 
                SET nama = ?, nis = ?, id_kelas = ?, alamat = ?, kontak_siswa = ?, 
                    jenis_kelamin = ?, nama_orang_tua = ?, kontak_orang_tua = ?, pekerjaan_orang_tua = ?
                WHERE id_siswa = ?
            ");

            $stmt->execute([
                $nama,
                $nis,
                $id_kelas,
                $alamat,
                $kontak_siswa,
                $jenis_kelamin,
                $nama_orang_tua,
                $kontak_orang_tua,
                $pekerjaan_orang_tua,
                $id
            ]);

            // refresh data siswa setelah update
            $stmt = $pdo->prepare("SELECT * FROM siswa WHERE id_siswa = ?");
            $stmt->execute([$id]);
            $siswa = $stmt->fetch(PDO::FETCH_ASSOC);

            $popup = true;
            $popup_type = 'success';
            $popup_message = 'Data siswa berhasil diperbarui!';
            $redirect_url = 'index.php';
        }
    } else {
        $popup = true;
        $popup_type = 'error';
        $popup_message = 'Semua data wajib diisi!';
    }

    // kalau gagal, isi form pakai data POST terakhir
    if ($popup_type == 'error') {
        $siswa['nama'] = $nama;
        $siswa['nis'] = $nis;
        $siswa['id_kelas'] = $id_kelas;
        $siswa['alamat'] = $alamat;
        $siswa['kontak_siswa'] = $kontak_siswa;
        $siswa['jenis_kelamin'] = $jenis_kelamin;
        $siswa['nama_orang_tua'] = $nama_orang_tua;
        $siswa['kontak_orang_tua'] = $kontak_orang_tua;
        $siswa['pekerjaan_orang_tua'] = $pekerjaan_orang_tua;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data Siswa</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <div class="container-add">
        <div class="form-box">
            <h2>Edit Siswa</h2>

            <form method="post">
                <label>Nama</label>
                <input name="nama" value="<?= htmlspecialchars($siswa['nama']) ?>" required>

                <label>NIS</label>
                <input name="nis" value="<?= htmlspecialchars($siswa['nis']) ?>" required>

                <label>Kelas</label>
                <select name="id_kelas" required>
                    <option value="">-- Pilih Kelas --</option>
                    <?php
                    $kelas = $pdo->query("SELECT * FROM kelas ORDER BY tingkat, jurusan, nama_kelas");
                    foreach ($kelas as $k):
                    ?>
                        <option value="<?= $k['id_kelas']; ?>" <?= $siswa['id_kelas'] == $k['id_kelas'] ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($k['tingkat'] . " " . $k['jurusan'] . " " . $k['nama_kelas']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label>Alamat</label>
                <input name="alamat" value="<?= htmlspecialchars($siswa['alamat']) ?>" required>

                <label>Kontak Siswa</label>
                <input name="kontak_siswa" value="<?= htmlspecialchars($siswa['kontak_siswa']) ?>" required>

                <label>Jenis Kelamin</label>
                <select name="jenis_kelamin" required>
                    <option value="">-- Pilih --</option>
                    <option value="L" <?= $siswa['jenis_kelamin'] == 'L' ? 'selected' : '' ?>>Laki-laki</option>
                    <option value="P" <?= $siswa['jenis_kelamin'] == 'P' ? 'selected' : '' ?>>Perempuan</option>
                </select>

                <label>Nama Orang Tua</label>
                <input name="nama_orang_tua" value="<?= htmlspecialchars($siswa['nama_orang_tua']) ?>" required>

                <label>Kontak Orang Tua</label>
                <input name="kontak_orang_tua" value="<?= htmlspecialchars($siswa['kontak_orang_tua']) ?>" required>

                <label>Pekerjaan Orang Tua</label>
                <input name="pekerjaan_orang_tua" value="<?= htmlspecialchars($siswa['pekerjaan_orang_tua']) ?>" required>

                <button type="submit" name="update" class="btn btn-edit">Update</button>
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