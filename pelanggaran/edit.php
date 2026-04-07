<?php
session_start();
include '../config/database.php';
include '../config/auth.php';

checkLogin();
allowRoles(['admin', 'guru_bk']);

$role = $_SESSION['role'];

$currentFolder = basename(dirname($_SERVER['PHP_SELF']));
$currentPage   = basename($_SERVER['PHP_SELF']);

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "ID pelanggaran tidak ditemukan.";
    exit;
}

$id = $_GET['id'];

/* Ambil data pelanggaran yang akan diedit */
$stmt = $pdo->prepare("
    SELECT * FROM pelanggaran
    WHERE id_pelanggaran = ?
");
$stmt->execute([$id]);
$pelanggaran = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$pelanggaran) {
    echo "Data pelanggaran tidak ditemukan.";
    exit;
}

/* Ambil data siswa */
$stmtSiswa = $pdo->query("
    SELECT id_siswa, nama, nis
    FROM siswa
    ORDER BY nama ASC
");
$dataSiswa = $stmtSiswa->fetchAll(PDO::FETCH_ASSOC);

/* Ambil data jenis pelanggaran */
$stmtJenis = $pdo->query("
    SELECT id_jenis, nama_jenis, poin
    FROM jenis_pelanggaran
    ORDER BY nama_jenis ASC
");
$dataJenis = $stmtJenis->fetchAll(PDO::FETCH_ASSOC);

/* Proses update */
if (isset($_POST['update'])) {
    $stmtUpdate = $pdo->prepare("
        UPDATE pelanggaran
        SET id_siswa = ?, id_jenis = ?, tanggal = ?, keterangan = ?
        WHERE id_pelanggaran = ?
    ");

    $stmtUpdate->execute([
        $_POST['id_siswa'],
        $_POST['id_jenis'],
        $_POST['tanggal'],
        $_POST['keterangan'],
        $id
    ]);

    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pelanggaran</title>
    <link rel="stylesheet" href="../assets/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>

<div class="container-add">
    <div class="form-box">
        <h2>Edit Pelanggaran</h2>

        <form method="POST">
            <label for="id_siswa">Nama Siswa</label>
            <select name="id_siswa" id="id_siswa" required>
                <option value="">-- Pilih Siswa --</option>
                <?php foreach ($dataSiswa as $s): ?>
                    <option value="<?= $s['id_siswa']; ?>" <?= ($pelanggaran['id_siswa'] == $s['id_siswa']) ? 'selected' : ''; ?>>
                        <?= htmlspecialchars($s['nama']) . ' - ' . htmlspecialchars($s['nis']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="id_jenis">Jenis Pelanggaran</label>
            <select name="id_jenis" id="id_jenis" required>
                <option value="">-- Pilih Jenis Pelanggaran --</option>
                <?php foreach ($dataJenis as $j): ?>
                    <option value="<?= $j['id_jenis']; ?>" <?= ($pelanggaran['id_jenis'] == $j['id_jenis']) ? 'selected' : ''; ?>>
                        <?= htmlspecialchars($j['nama_jenis']) . ' (' . $j['poin'] . ' poin)'; ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="tanggal">Tanggal</label>
            <input type="date" name="tanggal" id="tanggal" value="<?= htmlspecialchars($pelanggaran['tanggal']); ?>" required>

            <label for="keterangan">Keterangan</label>
            <input type="text" name="keterangan" id="keterangan" value="<?= htmlspecialchars($pelanggaran['keterangan']); ?>" required>

            <button type="submit" name="update" class="btn-edit">Update</button>
            <a href="index.php" class="btn">Kembali</a>
        </form>
    </div>
</div>

</body>
</html>