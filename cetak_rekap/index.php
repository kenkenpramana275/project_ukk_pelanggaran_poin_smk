<?php
session_start();
include '../config/database.php';
include '../config/auth.php';

checkLogin();
allowRoles(['admin', 'guru_bk']);

$currentFolder = basename(dirname($_SERVER['PHP_SELF']));
$currentPage   = basename($_SERVER['PHP_SELF']);
$role = $_SESSION['role'];

/* Ambil data preview (tidak semua, biar ringan) */
$stmt = $pdo->query("
    SELECT 
        siswa.nama,
        siswa.nis,
        kelas.tingkat,
        kelas.jurusan,
        kelas.nama_kelas,
        COUNT(pelanggaran.id_pelanggaran) AS total_pelanggaran,
        COALESCE(SUM(jenis_pelanggaran.poin),0) AS total_poin
    FROM siswa
    LEFT JOIN kelas ON siswa.id_kelas = kelas.id_kelas
    LEFT JOIN pelanggaran ON siswa.id_siswa = pelanggaran.id_siswa
    LEFT JOIN jenis_pelanggaran ON pelanggaran.id_jenis = jenis_pelanggaran.id_jenis
    GROUP BY siswa.id_siswa
    ORDER BY total_poin DESC
    LIMIT 20
");

$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<link rel="stylesheet" href="../assets/style.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">

<body>

<!-- NAVBAR -->
<div class="nav">
    <div class="nav-brand">
        <div class="brand-icon">📚</div>
        <span class="brand-text">SISWA<span class="brand-accent">TRACK</span></span>
    </div>

    <div class="nav-links">

    <div class="nav-section">MAIN MENU</div>
        <a href="../dashboard.php" class="nav-link">
            <span class="nav-icon">🏠</span>
            <span class="nav-text">Dashboard</span>
        </a>

        <?php if($role == 'admin'): ?>
        <a href="../kelas/index.php"
        class="nav-link <?= ($currentFolder == 'kelas') ? 'active' : '' ?>">
            <span class="nav-icon">🏫</span>
            <span class="nav-text">Data Kelas</span>
        </a>
        <?php endif; ?>

        <?php if($role == 'admin'): ?>
        <a href="../siswa/index.php" class="nav-link <?= ($currentFolder == 'siswa') ? 'active' : '' ?>">
            <span class="nav-icon">👥</span>
            <span class="nav-text">Data Siswa</span>
        </a>
        <?php endif; ?>

        <?php if($role == 'admin' || $role == 'guru_bk'): ?>
        <a href="../pelanggaran/index.php" class="nav-link <?= ($currentFolder == 'pelanggaran') ? 'active' : '' ?>">
            <span class="nav-icon">⚠️</span>
            <span class="nav-text">Data Pelanggaran</span>
        </a>

        <?php if($role == 'admin'): ?>
        <a href="../jenis_pelanggaran/index.php" class="nav-link <?= ($currentFolder == 'jenis_pelanggaran') ? 'active' : '' ?>">
            <span class="nav-icon">📋</span>
            <span class="nav-text">Jenis Pelanggaran</span>
        </a>
        <?php endif; ?>

        <?php if($role == 'admin' || $role == 'guru_bk'): ?>
        <a href="../cetak_rekap/index.php"
        class="nav-link active <?= ($currentFolder == 'cetak_rekap') ? 'active' : '' ?>">
            <span class="nav-icon">🖨️</span>
            <span class="nav-text">Rekap Pelanggaran</span>
        </a>
        <?php endif; ?>

        <div class="nav-section">CETAK SURAT</div>

        <a href="../surat_orangtua/index.php" class="nav-link <?= ($currentFolder == 'surat_orangtua') ? 'active' : '' ?>">
            <span class="nav-icon">📨</span>
            <span class="nav-text">Panggilan Orang Tua</span>
        </a>

        <a href="../surat_perjanjian/index.php" class="nav-link <?= ($currentFolder == 'surat_perjanjian') ? 'active' : '' ?>">
            <span class="nav-icon">📝</span>
            <span class="nav-text">Surat Perjanjian</span>
        </a>

        <a href="../surat_pindah/index.php" class="nav-link">
            <span class="nav-icon">📑</span>
            <span class="nav-text">Surat Pindah</span>
        </a>
        <?php endif; ?>
    </div>

    <div class="nav-footer">
        <?php if($role == 'admin'): ?>
        <a href="../users/index.php" class="nav-link">
            <span class="nav-icon">👤</span>
            <span class="nav-text">Users</span>
        </a>
        <?php endif; ?>

        <a href="../auth/logout.php" class="nav-logout">
            <span class="nav-icon">🚪</span>
            <span class="nav-text">Logout</span>
        </a>
    </div>
</div>

<!-- CONTENT -->
<div class="container">

    <h2>Rekap Pelanggaran Siswa</h2>

    <!-- tombol cetak -->
    <a href="cetak_rekap.php" class="btn btn-tambah" target="_blank">Cetak Rekap</a>

    <br><br>

    <!-- preview data -->
    <table>
        <tr>
            <th>No</th>
            <th>Nama</th>
            <th>NIS</th>
            <th>Kelas</th>
            <th>Total Pelanggaran</th>
            <th>Total Poin</th>
        </tr>

        <?php $no = 1; ?>
        <?php foreach($data as $row): ?>
        <?php $kelas = $row['tingkat'] . " " . $row['jurusan'] . " " . $row['nama_kelas']; ?>
        <tr>
            <td><?= $no++; ?></td>
            <td><?= htmlspecialchars($row['nama']); ?></td>
            <td><?= htmlspecialchars($row['nis']); ?></td>
            <td><?= htmlspecialchars($kelas); ?></td>
            <td><?= $row['total_pelanggaran']; ?></td>
            <td><?= $row['total_poin']; ?></td>
        </tr>
        <?php endforeach; ?>

    </table>

</div>

</body>