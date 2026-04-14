<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: ../auth/login.php");
    exit;
}

include '../config/database.php';
include '../config/auth.php';

checkLogin();
allowRoles(['admin', 'guru_bk', 'guru_mapel']);

$popup = false;
$popup_type = '';
$popup_message = '';
$redirect_url = '';

// ambil data siswa
$stmtSiswa = $pdo->query("SELECT id_siswa, nama FROM siswa ORDER BY nama");
$siswa = $stmtSiswa->fetchAll(PDO::FETCH_ASSOC);

// ambil data jenis pelanggaran
$stmtJenis = $pdo->query("SELECT id_jenis, nama_jenis FROM jenis_pelanggaran ORDER BY nama_jenis");
$jenis = $stmtJenis->fetchAll(PDO::FETCH_ASSOC);

// proses simpan
if (isset($_POST['simpan'])) {
    $id_siswa = trim($_POST['id_siswa']);
    $id_jenis = trim($_POST['id_jenis']);
    $tanggal = trim($_POST['tanggal']);
    $keterangan = trim($_POST['keterangan']);

    if ($id_siswa != "" && $id_jenis != "" && $tanggal != "" && $keterangan != "") {
        $stmt = $pdo->prepare(
            "INSERT INTO pelanggaran (id_siswa, id_jenis, tanggal, keterangan)
             VALUES (?, ?, ?, ?)"
        );

        $stmt->execute([
            $id_siswa,
            $id_jenis,
            $tanggal,
            $keterangan
        ]);

        $popup = true;
        $popup_type = 'success';
        $popup_message = 'Data pelanggaran berhasil ditambahkan!';

        if ($_SESSION['role'] == 'guru_mapel') {
            $redirect_url = '../dashboard.php';
        } else {
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
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Data Pelanggaran</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<div class="container-add">
    <div class="form-box">
        <h2>Tambah Pelanggaran</h2>

        <form method="post">
            <label>Nama Siswa</label>
            <select name="id_siswa" required>
                <option value="">-- Pilih Siswa --</option>
                <?php foreach ($siswa as $s): ?>
                    <option value="<?= $s['id_siswa'] ?>" <?= (isset($_POST['id_siswa']) && $_POST['id_siswa'] == $s['id_siswa']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($s['nama']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label>Jenis Pelanggaran</label>
            <select name="id_jenis" required>
                <option value="">-- Pilih Pelanggaran --</option>
                <?php foreach ($jenis as $j): ?>
                    <option value="<?= $j['id_jenis'] ?>" <?= (isset($_POST['id_jenis']) && $_POST['id_jenis'] == $j['id_jenis']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($j['nama_jenis']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label>Tanggal</label>
            <input type="date" name="tanggal" required value="<?= isset($_POST['tanggal']) ? htmlspecialchars($_POST['tanggal']) : '' ?>">

            <label>Keterangan</label>
            <input type="text" name="keterangan" required value="<?= isset($_POST['keterangan']) ? htmlspecialchars($_POST['keterangan']) : '' ?>">

            <button type="submit" name="simpan">Simpan</button>
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