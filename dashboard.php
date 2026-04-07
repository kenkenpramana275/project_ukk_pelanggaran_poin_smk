<?php 
session_start(); 

if(!isset($_SESSION['login'])) {
    header("Location: auth/login.php");
    exit;
}

include './config/database.php';
include './config/auth.php';

checkLoginRoot();

$role = $_SESSION['role'];

$currentFolder = basename(dirname($_SERVER['PHP_SELF']));
$currentPage   = basename($_SERVER['PHP_SELF']);

/* default */
$total_siswa = 0;
$total_pelanggaran = 0;
$total_poin_siswa = 0;
$status_siswa = 'Aman';

/* ADMIN & GURU BK */
if($role == 'admin' || $role == 'guru_bk'){
    $stmt = $pdo->query("SELECT COUNT(*) FROM siswa");
    $total_siswa = $stmt->fetchColumn();
}

/* ADMIN & GURU BK: total semua pelanggaran */
if($role == 'admin' || $role == 'guru_bk'){
    $stmt2 = $pdo->query("SELECT COUNT(*) FROM pelanggaran");
    $total_pelanggaran = $stmt2->fetchColumn();
}

/* SISWA: hanya data miliknya */
if($role == 'siswa'){
    $id_siswa = $_SESSION['id_siswa'];

    $stmtSiswa = $pdo->prepare("
        SELECT 
            COUNT(pelanggaran.id_pelanggaran) AS total_pelanggaran,
            COALESCE(SUM(jenis_pelanggaran.poin), 0) AS total_poin
        FROM pelanggaran
        JOIN jenis_pelanggaran ON pelanggaran.id_jenis = jenis_pelanggaran.id_jenis
        WHERE pelanggaran.id_siswa = ?
    ");
    $stmtSiswa->execute([$id_siswa]);
    $hasil = $stmtSiswa->fetch(PDO::FETCH_ASSOC);

    $total_pelanggaran = $hasil['total_pelanggaran'];
    $total_poin_siswa = $hasil['total_poin'];

    if ($total_poin_siswa >= 120) {
        $status_siswa = 'Surat Pindah';
    } elseif ($total_poin_siswa >= 75) {
        $status_siswa = 'Surat Perjanjian';
    } elseif ($total_poin_siswa >= 30) {
        $status_siswa = 'Panggilan Orang Tua';
    } else {
        $status_siswa = 'Aman';
    }
}
?>

<?php
// // Total siswa (hanya admin & guru_bk)
// $total_siswa = 0;
// if($role == 'admin' || $role == 'guru_bk'){
//     $stmt = $pdo->query("SELECT COUNT(*) FROM siswa");
//     $total_siswa = $stmt->fetchColumn();
// }

// // Total pelanggaran (semua role boleh lihat)
// $stmt2 = $pdo->query("SELECT COUNT(*) FROM pelanggaran");
// $total_pelanggaran = $stmt2->fetchColumn();
?>



<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistem Pelanggaran Siswa</title>
    <link rel="stylesheet" href="assets/style.css">
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

    <!-- Dashboard -->
    <a href="dashboard.php"
       class="nav-link <?= ($currentPage == 'dashboard.php') ? 'active' : '' ?>">
        <span class="nav-icon">🏠</span>
        <span class="nav-text">Dashboard</span>
    </a>

    <?php if($role == 'admin'): ?>
    <a href="kelas/index.php"
    class="nav-link <?= ($currentFolder == 'kelas') ? 'active' : '' ?>">
        <span class="nav-icon">🏫</span>
        <span class="nav-text">Data Kelas</span>
    </a>
    <?php endif; ?>

    <!-- ADMIN ONLY -->
    <?php if($role == 'admin'): ?>
    <a href="siswa/index.php"
       class="nav-link <?= ($currentFolder == 'siswa') ? 'active' : '' ?>">
        <span class="nav-icon">👥</span>
        <span class="nav-text">Data Siswa</span>
    </a>
    <?php endif; ?>

    <!-- ADMIN & GURU BK -->
    <?php if($role == 'admin' || $role == 'guru_bk'): ?>
    <a href="pelanggaran/index.php"
       class="nav-link <?= ($currentFolder == 'pelanggaran') ? 'active' : '' ?>">
        <span class="nav-icon">⚠️</span>
        <span class="nav-text">Data Pelanggaran</span>
    </a>
    <?php endif; ?>

    <!-- ADMIN ONLY -->
    <?php if($role == 'admin'): ?>
    <a href="jenis_pelanggaran/index.php"
       class="nav-link <?= ($currentFolder == 'jenis_pelanggaran') ? 'active' : '' ?>">
        <span class="nav-icon">📋</span>
        <span class="nav-text">Jenis Pelanggaran</span>
    </a>
    <?php endif; ?>

    <!-- GURU MAPEL -->
    <?php if($role == 'guru_mapel'): ?>
    <a href="pelanggaran/tambah.php"
       class="nav-link">
        <span class="nav-icon">➕</span>
        <span class="nav-text">Input Pelanggaran</span>
    </a>
    <?php endif; ?>

    <?php if($role == 'admin' || $role == 'guru_bk'): ?>
    <a href="cetak_rekap/index.php"
    class="nav-link <?= ($currentFolder == 'cetak_rekap') ? 'active' : '' ?>">
        <span class="nav-icon">🖨️</span>
        <span class="nav-text">Rekap Pelanggaran</span>
    </a>
    <?php endif; ?>

    <!-- SISWA -->
    <?php if($role == 'siswa'): ?>
    <a href="pelanggaran/saya.php"
       class="nav-link">
        <span class="nav-icon">📄</span>
        <span class="nav-text">Pelanggaran Saya</span>
    </a>
    <?php endif; ?>

<?php if($role == 'admin' || $role == 'guru_bk'): ?>
    <div class="nav-section">CETAK SURAT</div>

    <a href="surat_orangtua/index.php"
       class="nav-link <?= ($currentFolder == 'surat_orangtua') ? 'active' : '' ?>">
        <span class="nav-icon">📨</span>
        <span class="nav-text">Panggilan Orang Tua</span>
    </a>

    <a href="surat_perjanjian/index.php"
       class="nav-link <?= ($currentFolder == 'surat_perjanjian') ? 'active' : '' ?>">
        <span class="nav-icon">📝</span>
        <span class="nav-text">Surat Perjanjian</span>
    </a>

    <a href="surat_pindah/index.php"
       class="nav-link <?= ($currentFolder == 'surat_pindah') ? 'active' : '' ?>">
        <span class="nav-icon">📑</span>
        <span class="nav-text">Surat Pindah</span>
    </a>
<?php endif; ?>
</div>

    <div class="nav-footer">
        <?php if($role == 'admin'): ?>
        <a href="users/index.php" class="nav-link">
            <span class="nav-icon">👤</span>
            <span class="nav-text">Users</span>
        </a>
        <?php endif; ?>
        <!-- <a href="users/index.php" class="nav-link">
            <span class="nav-icon">👤</span>
            <span class="nav-text">Users</span>
        </a> -->
        <a href="auth/logout.php" class="nav-logout">
            <span class="nav-icon">🚪</span>
            <span class="nav-text">Logout</span>
        </a>
    </div>
</div>

<div class="main-content">
    <div class="dashboard-header">
        <div class="header-greeting">
            <h1 class="greeting-title">Selamat Datang Kembali!</h1>
            <p class="greeting-user">
                <?= $_SESSION['username']; ?> (<?= $_SESSION['role']; ?>)
            </p>
        </div>
        <div class="header-decoration"></div>
    </div>

<div class="dashboard-stats">

    <?php if($role == 'admin' || $role == 'guru_bk'): ?>
    <div class="stat-card stat-primary">
        <div class="stat-icon">👨‍🎓</div>
        <div class="stat-content">
            <div class="stat-label">Total Siswa</div>
            <div class="stat-value"><?= $total_siswa; ?></div>
        </div>
    </div>
    <?php endif; ?>

    <?php if($role == 'admin' || $role == 'guru_bk'): ?>
    <div class="stat-card stat-warning">
        <div class="stat-icon">📋</div>
        <div class="stat-content">
            <div class="stat-label">Total Pelanggaran</div>
            <div class="stat-value"><?= $total_pelanggaran; ?></div>
        </div>
    </div>
    <?php endif; ?>

    <?php if($role == 'siswa'): ?>
    <div class="stat-card">
        <div class="stat-icon">📄</div>
        <div class="stat-content">
            <div class="stat-label">Jumlah Pelanggaran Saya</div>
            <div class="stat-value"><?= $total_pelanggaran; ?></div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon">🎯</div>
        <div class="stat-content">
            <div class="stat-label">Total Poin Saya</div>
            <div class="stat-value"><?= $total_poin_siswa; ?></div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon">🚨</div>
        <div class="stat-content">
            <div class="stat-label">Status</div>
            <div class="stat-value"><?= htmlspecialchars($status_siswa); ?></div>
        </div>
    </div>
    <?php endif; ?>

</div>

<div class="card-grid">

    <?php if($role == 'admin'): ?>
    <div class="feature-card card-siswa">
        <div class="card-header">
            <div class="card-icon">👥</div>
            <h3 class="card-title">Data Siswa</h3>
        </div>
        <p class="card-description">Kelola data siswa</p>
        <a href="siswa/index.php" class="card-btn">Lihat Data →</a>
    </div>
    <?php endif; ?>

    <?php if($role == 'admin' || $role == 'guru_bk'): ?>
    <div class="feature-card card-pelanggaran">
        <div class="card-header">
            <div class="card-icon">⚠️</div>
            <h3 class="card-title">Data Pelanggaran</h3>
        </div>
        <p class="card-description">Monitor pelanggaran siswa</p>
        <a href="pelanggaran/index.php" class="card-btn">Lihat Data →</a>
    </div>
    <?php endif; ?>

    <?php if($role == 'guru_mapel'): ?>
    <div class="feature-card card-pelanggaran">
        <div class="card-header">
            <div class="card-icon">➕</div>
            <h3 class="card-title">Input Pelanggaran</h3>
        </div>
        <p class="card-description">Input pelanggaran siswa</p>
        <a href="pelanggaran/tambah.php" class="card-btn">Input →</a>
    </div>
    <?php endif; ?>

    <?php if($role == 'siswa'): ?>
    <div class="feature-card card-pelanggaran">
        <div class="card-header">
            <div class="card-icon">📄</div>
            <h3 class="card-title">Pelanggaran Saya</h3>
        </div>
        <p class="card-description">Lihat riwayat pelanggaran dan total poin saya</p>
        <a href="pelanggaran/saya.php" class="card-btn">Lihat Data →</a>
    </div>
    <?php endif; ?>

</div>
</div>

</body>
</html>