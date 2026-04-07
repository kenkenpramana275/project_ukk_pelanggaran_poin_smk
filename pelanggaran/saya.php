<?php
session_start();
include '../config/database.php';
include '../config/auth.php';

checkLogin();
allowRoles(['siswa']);

$role = $_SESSION['role'];
$id_siswa = $_SESSION['id_siswa'];

$currentFolder = basename(dirname($_SERVER['PHP_SELF']));
$currentPage   = basename($_SERVER['PHP_SELF']);

/* data siswa + kelas */
$stmtSiswa = $pdo->prepare("
    SELECT 
        siswa.*,
        kelas.tingkat,
        kelas.jurusan,
        kelas.nama_kelas
    FROM siswa
    JOIN kelas ON siswa.id_kelas = kelas.id_kelas
    WHERE siswa.id_siswa = ?
");
$stmtSiswa->execute([$id_siswa]);
$siswa = $stmtSiswa->fetch(PDO::FETCH_ASSOC);

if (!$siswa) {
    echo "Data siswa tidak ditemukan.";
    exit;
}

$kelasLengkap = $siswa['tingkat'] . " " . $siswa['jurusan'] . " " . $siswa['nama_kelas'];

/* riwayat pelanggaran */
$stmt = $pdo->prepare("
    SELECT 
        pelanggaran.id_pelanggaran,
        pelanggaran.tanggal,
        pelanggaran.keterangan,
        jenis_pelanggaran.nama_jenis,
        jenis_pelanggaran.poin,
        jenis_pelanggaran.kategori_kode
    FROM pelanggaran
    JOIN jenis_pelanggaran ON pelanggaran.id_jenis = jenis_pelanggaran.id_jenis
    WHERE pelanggaran.id_siswa = ?
    ORDER BY pelanggaran.tanggal DESC, pelanggaran.id_pelanggaran DESC
");
$stmt->execute([$id_siswa]);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* total poin */
$totalPoin = 0;
foreach ($data as $d) {
    $totalPoin += (int)$d['poin'];
}

/* status */
if ($totalPoin >= 120) {
    $status = 'Surat Pindah';
} elseif ($totalPoin >= 75) {
    $status = 'Surat Perjanjian';
} elseif ($totalPoin >= 30) {
    $status = 'Panggilan Orang Tua';
} else {
    $status = 'Aman';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pelanggaran Saya</title>
    <link rel="stylesheet" href="../assets/style.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>

<div class="nav">
    <div class="nav-brand">
        <div class="brand-icon">📚</div>
        <span class="brand-text">SISWA<span class="brand-accent">TRACK</span></span>
    </div>

    <div class="nav-links">
        <div class="nav-section">MAIN MENU</div>
        <a href="../dashboard.php"
           class="nav-link <?= ($currentPage == 'dashboard.php') ? 'active' : '' ?>">
            <span class="nav-icon">🏠</span>
            <span class="nav-text">Dashboard</span>
        </a>

        <a href="saya.php" class="nav-link active">
            <span class="nav-icon">📄</span>
            <span class="nav-text">Pelanggaran Saya</span>
        </a>
    </div>

    <div class="nav-footer">
        <a href="../auth/logout.php" class="nav-logout">
            <span class="nav-icon">🚪</span>
            <span class="nav-text">Logout</span>
        </a>
    </div>
</div>

<div class="container">
    <div class="dashboard-header">
        <div class="header-greeting">
            <h1 class="greeting-title">Pelanggaran Saya</h1>
            <p class="greeting-user">
                <?= htmlspecialchars($siswa['nama']); ?> | 
                <?= htmlspecialchars($siswa['nis']); ?> | 
                <?= htmlspecialchars($kelasLengkap); ?>
            </p>
            <p class="greeting-user">
                Total Poin: <?= $totalPoin; ?> | Status: <?= htmlspecialchars($status); ?>
            </p>
        </div>
        <div class="header-decoration"></div>
    </div>

    <h2>Riwayat Pelanggaran</h2>

    <table id="pelanggaranSayaTable" class="display">
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Jenis Pelanggaran</th>
                <th>Kategori</th>
                <th>Poin</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; ?>
            <?php foreach($data as $row): ?>
            <tr>
                <td><?= $no++; ?></td>
                <td><?= htmlspecialchars($row['tanggal']); ?></td>
                <td><?= htmlspecialchars($row['nama_jenis']); ?></td>
                <td><?= htmlspecialchars($row['kategori_kode']); ?></td>
                <td><?= (int)$row['poin']; ?></td>
                <td><?= htmlspecialchars($row['keterangan']); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script>
$(document).ready(function () {
    $('#pelanggaranSayaTable').DataTable();
});
</script>
</body>
</html>