<?php
include '../config/database.php';

$popup = false;
$popup_type = '';
$popup_message = '';
$redirect_url = '';

if (isset($_POST['simpan'])) {
    $nama = trim($_POST['nama']);
    $nis = trim($_POST['nis']);
    $id_kelas = $_POST['id_kelas'];
    $alamat = trim($_POST['alamat']);
    $kontak_siswa = trim($_POST['kontak_siswa']);
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $nama_orang_tua = trim($_POST['nama_orang_tua']);
    $kontak_orang_tua = trim($_POST['kontak_orang_tua']);
    $pekerjaan_orang_tua = trim($_POST['pekerjaan_orang_tua']);

    if (
        $nama != "" && $nis != "" && $id_kelas != "" && $alamat != "" &&
        $kontak_siswa != "" && $jenis_kelamin != "" &&
        $nama_orang_tua != "" && $kontak_orang_tua != "" && $pekerjaan_orang_tua != ""
    ) {
        // cek apakah NIS sudah ada
        $cek = $pdo->prepare("SELECT COUNT(*) FROM siswa WHERE nis = ?");
        $cek->execute([$nis]);
        $jumlah = $cek->fetchColumn();

        if ($jumlah > 0) {
            $popup = true;
            $popup_type = 'error';
            $popup_message = 'NIS sudah digunakan!';
        } else {
            $stmt = $pdo->prepare(
                "INSERT INTO siswa 
                (nama, nis, id_kelas, alamat, kontak_siswa, jenis_kelamin, nama_orang_tua, kontak_orang_tua, pekerjaan_orang_tua) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
            );

            $stmt->execute([
                $nama,
                $nis,
                $id_kelas,
                $alamat,
                $kontak_siswa,
                $jenis_kelamin,
                $nama_orang_tua,
                $kontak_orang_tua,
                $pekerjaan_orang_tua
            ]);

            $popup = true;
            $popup_type = 'success';
            $popup_message = 'Data siswa berhasil ditambahkan!';
            $redirect_url = 'index.php';
        }
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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Siswa</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <div class="container-add">
        <div class="form-box">
            <h2>Tambah Siswa</h2>

            <form method="post">
                <label>Nama</label>
                <input name="nama" placeholder="Nama" required value="<?= isset($_POST['nama']) ? htmlspecialchars($_POST['nama']) : '' ?>">

                <label>NIS</label>
                <input name="nis" placeholder="NIS" required value="<?= isset($_POST['nis']) ? htmlspecialchars($_POST['nis']) : '' ?>">

                <label>Kelas</label>
                <select name="id_kelas" required>
                    <option value="">-- Pilih Kelas --</option>
                    <?php
                    $kelas = $pdo->query("SELECT * FROM kelas");
                    foreach ($kelas as $k):
                        $selected = (isset($_POST['id_kelas']) && $_POST['id_kelas'] == $k['id_kelas']) ? 'selected' : '';
                    ?>
                        <option value="<?= $k['id_kelas']; ?>" <?= $selected; ?>>
                            <?= $k['tingkat'] . " " . $k['jurusan'] . " " . $k['nama_kelas']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label>Alamat</label>
                <input name="alamat" placeholder="Alamat" required value="<?= isset($_POST['alamat']) ? htmlspecialchars($_POST['alamat']) : '' ?>">

                <label>Kontak Siswa</label>
                <input name="kontak_siswa" placeholder="Kontak Siswa" required value="<?= isset($_POST['kontak_siswa']) ? htmlspecialchars($_POST['kontak_siswa']) : '' ?>">

                <label>Jenis Kelamin</label>
                <select name="jenis_kelamin" required>
                    <option value="">-- Pilih --</option>
                    <option value="L" <?= (isset($_POST['jenis_kelamin']) && $_POST['jenis_kelamin'] == 'L') ? 'selected' : '' ?>>Laki-laki</option>
                    <option value="P" <?= (isset($_POST['jenis_kelamin']) && $_POST['jenis_kelamin'] == 'P') ? 'selected' : '' ?>>Perempuan</option>
                </select>

                <label>Nama Orang Tua</label>
                <input name="nama_orang_tua" placeholder="Nama Orang Tua" required value="<?= isset($_POST['nama_orang_tua']) ? htmlspecialchars($_POST['nama_orang_tua']) : '' ?>">

                <label>Kontak Orang Tua</label>
                <input name="kontak_orang_tua" placeholder="Kontak Orang Tua" required value="<?= isset($_POST['kontak_orang_tua']) ? htmlspecialchars($_POST['kontak_orang_tua']) : '' ?>">

                <label>Pekerjaan Orang Tua</label>
                <input name="pekerjaan_orang_tua" placeholder="Pekerjaan Orang Tua" required value="<?= isset($_POST['pekerjaan_orang_tua']) ? htmlspecialchars($_POST['pekerjaan_orang_tua']) : '' ?>">

                <button name="simpan" type="submit">Simpan</button>
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