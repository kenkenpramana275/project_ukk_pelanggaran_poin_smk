<?php 
// Memulai session agar data login di $_SESSION bisa digunakan
session_start(); 

// Mengecek apakah user sudah login atau belum
// Jika belum login, arahkan ke halaman login
if(!isset($_SESSION['login'])) {
    header("Location: auth/login.php");
    exit;
}

// Menghubungkan file dashboard dengan koneksi database
include './config/database.php';

// Menghubungkan file dashboard dengan fungsi auth
include './config/auth.php';

// Mengecek login untuk semua user
checkLogin();

// Mengambil role user yang sedang login dari session
$role = $_SESSION['role'];

// Mengambil nama folder saat ini dari URL/path
// Dipakai untuk menandai menu sidebar mana yang aktif
$currentFolder = basename(dirname($_SERVER['PHP_SELF']));

// Mengambil nama file halaman saat ini
// Dipakai untuk menandai menu aktif, misalnya dashboard.php
$currentPage   = basename($_SERVER['PHP_SELF']);


// ===============================
// NILAI DEFAULT DASHBOARD
// ===============================

// Menyimpan total siswa, default awal = 0
$total_siswa = 0;

// Menyimpan total pelanggaran, default awal = 0
$total_pelanggaran = 0;

// Menyimpan total poin siswa, default awal = 0
$total_poin_siswa = 0;

// Menyimpan status siswa, default awal = Aman
$status_siswa = 'Aman';


// ===============================
// ADMIN & GURU BK
// HITUNG TOTAL SISWA
// ===============================

// Jika role adalah admin atau guru_bk
if($role == 'admin' || $role == 'guru_bk'){
    
    // Query untuk menghitung jumlah seluruh siswa
    $stmt = $pdo->query("SELECT COUNT(*) FROM siswa");

    // Mengambil hasil jumlah siswa
    $total_siswa = $stmt->fetchColumn();
}


// ===============================
// ADMIN & GURU BK
// HITUNG TOTAL PELANGGARAN
// ===============================

// Jika role adalah admin atau guru_bk
if($role == 'admin' || $role == 'guru_bk'){

    // Query untuk menghitung jumlah seluruh pelanggaran
    $stmt2 = $pdo->query("SELECT COUNT(*) FROM pelanggaran");

    // Mengambil hasil jumlah pelanggaran
    $total_pelanggaran = $stmt2->fetchColumn();
}


// ===============================
// SISWA
// AMBIL DATA PELANGGARAN MILIKNYA SENDIRI
// ===============================

// Jika role yang login adalah siswa
if($role == 'siswa'){

    // Mengambil id_siswa dari session
    $id_siswa = $_SESSION['id_siswa'];

    // Menyiapkan query untuk:
    // - menghitung total pelanggaran milik siswa
    // - menjumlahkan total poin pelanggaran siswa
    $stmtSiswa = $pdo->prepare("
        SELECT 
            COUNT(pelanggaran.id_pelanggaran) AS total_pelanggaran,
            COALESCE(SUM(jenis_pelanggaran.poin), 0) AS total_poin
        FROM pelanggaran
        JOIN jenis_pelanggaran ON pelanggaran.id_jenis = jenis_pelanggaran.id_jenis
        WHERE pelanggaran.id_siswa = ?
    ");

    // Menjalankan query dengan id_siswa
    $stmtSiswa->execute([$id_siswa]);

    // Mengambil hasil query dalam bentuk array asosiatif
    $hasil = $stmtSiswa->fetch(PDO::FETCH_ASSOC);

    // Menyimpan total pelanggaran siswa ke variabel
    $total_pelanggaran = $hasil['total_pelanggaran'];

    // Menyimpan total poin siswa ke variabel
    $total_poin_siswa = $hasil['total_poin'];

    // Menentukan status siswa berdasarkan total poin
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
// Bagian ini adalah kode lama / cadangan yang sedang dikomentari
// Fungsinya untuk menghitung total siswa dan total pelanggaran
// Namun sekarang sudah digantikan oleh blok logika di atas

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
    <!-- Mengatur karakter agar mendukung huruf standar UTF-8 -->
    <meta charset="UTF-8">

    <!-- Membuat tampilan responsive di layar HP / tablet -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Judul halaman -->
    <title>Dashboard - Sistem Pelanggaran Siswa</title>

    <!-- Menghubungkan file CSS utama -->
    <link rel="stylesheet" href="assets/style.css">

    <!-- Optimasi koneksi ke Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">

    <!-- Optimasi koneksi ke Google Fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- Menghubungkan font Outfit dan Space Mono -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>

<!-- ===============================
     SIDEBAR / NAVIGASI
=============================== -->
<div class="nav">

    <!-- Bagian brand/logo aplikasi -->
    <div class="nav-brand">
        <div class="brand-icon">📚</div>
        <span class="brand-text">SISWA<span class="brand-accent">TRACK</span></span>
    </div>
    
    <!-- Kumpulan link navigasi -->
    <div class="nav-links">

        <!-- Judul section menu -->
        <div class="nav-section">MAIN MENU</div>

        <!-- Menu Dashboard -->
        <a href="dashboard.php"
           class="nav-link <?= ($currentPage == 'dashboard.php') ? 'active' : '' ?>">
            <span class="nav-icon">🏠</span>
            <span class="nav-text">Dashboard</span>
        </a>

        <!-- Menu Data Kelas, hanya untuk admin -->
        <?php if($role == 'admin'): ?>
        <a href="kelas/index.php"
           class="nav-link <?= ($currentFolder == 'kelas') ? 'active' : '' ?>">
            <span class="nav-icon">🏫</span>
            <span class="nav-text">Data Kelas</span>
        </a>
        <?php endif; ?>

        <!-- Menu Data Siswa, hanya untuk admin -->
        <?php if($role == 'admin'): ?>
        <a href="siswa/index.php"
           class="nav-link <?= ($currentFolder == 'siswa') ? 'active' : '' ?>">
            <span class="nav-icon">👥</span>
            <span class="nav-text">Data Siswa</span>
        </a>
        <?php endif; ?>

        <!-- Menu Data Pelanggaran, untuk admin dan guru BK -->
        <?php if($role == 'admin' || $role == 'guru_bk'): ?>
        <a href="pelanggaran/index.php"
           class="nav-link <?= ($currentFolder == 'pelanggaran') ? 'active' : '' ?>">
            <span class="nav-icon">⚠️</span>
            <span class="nav-text">Data Pelanggaran</span>
        </a>
        <?php endif; ?>

        <!-- Menu Jenis Pelanggaran, hanya admin -->
        <?php if($role == 'admin'): ?>
        <a href="jenis_pelanggaran/index.php"
           class="nav-link <?= ($currentFolder == 'jenis_pelanggaran') ? 'active' : '' ?>">
            <span class="nav-icon">📋</span>
            <span class="nav-text">Jenis Pelanggaran</span>
        </a>
        <?php endif; ?>

        <!-- Menu Input Pelanggaran, khusus guru mapel -->
        <?php if($role == 'guru_mapel'): ?>
        <a href="pelanggaran/tambah.php"
           class="nav-link">
            <span class="nav-icon">➕</span>
            <span class="nav-text">Input Pelanggaran</span>
        </a>
        <?php endif; ?>

        <!-- Menu Rekap Pelanggaran, untuk admin dan guru BK -->
        <?php if($role == 'admin' || $role == 'guru_bk'): ?>
        <a href="cetak_rekap/index.php"
           class="nav-link <?= ($currentFolder == 'cetak_rekap') ? 'active' : '' ?>">
            <span class="nav-icon">🖨️</span>
            <span class="nav-text">Rekap Pelanggaran</span>
        </a>
        <?php endif; ?>

        <!-- Menu khusus siswa untuk melihat pelanggaran miliknya -->
        <?php if($role == 'siswa'): ?>
        <a href="pelanggaran/saya.php"
           class="nav-link">
            <span class="nav-icon">📄</span>
            <span class="nav-text">Pelanggaran Saya</span>
        </a>
        <?php endif; ?>

        <!-- Menu cetak surat, hanya admin dan guru BK -->
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

    <!-- Footer sidebar -->
    <div class="nav-footer">

        <!-- Menu users, hanya admin -->
        <?php if($role == 'admin'): ?>
        <a href="users/index.php" class="nav-link">
            <span class="nav-icon">👤</span>
            <span class="nav-text">Users</span>
        </a>
        <?php endif; ?>

        <!-- Kode lama menu users yang sedang dinonaktifkan -->
        <!--
        <a href="users/index.php" class="nav-link">
            <span class="nav-icon">👤</span>
            <span class="nav-text">Users</span>
        </a>
        -->

        <!-- Tombol logout -->
        <a href="auth/logout.php" class="nav-logout">
            <span class="nav-icon">🚪</span>
            <span class="nav-text">Logout</span>
        </a>
    </div>
</div>

<!-- ===============================
     KONTEN UTAMA DASHBOARD
=============================== -->
<div class="main-content">

    <!-- Header dashboard -->
    <div class="dashboard-header">
        <div class="header-greeting">

            <!-- Judul sapaan -->
            <h1 class="greeting-title">Selamat Datang Kembali, <?= $_SESSION['username']; ?>!</h1>

            <!-- Menampilkan username dan role yang sedang login -->
            <!-- <p class="greeting-user">

            </p> -->
        </div>

        <!-- Dekorasi tambahan -->
        <div class="header-decoration"></div>
    </div>

    <!-- ===============================
         KARTU STATISTIK
    =============================== -->
    <div class="dashboard-stats">

        <!-- Total siswa untuk admin dan guru BK -->
        <?php if($role == 'admin' || $role == 'guru_bk'): ?>
        <div class="stat-card stat-primary">
            <div class="stat-icon">👨‍🎓</div>
            <div class="stat-content">
                <div class="stat-label">Total Siswa</div>
                <div class="stat-value"><?= $total_siswa; ?></div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Total pelanggaran untuk admin dan guru BK -->
        <?php if($role == 'admin' || $role == 'guru_bk'): ?>
        <div class="stat-card stat-warning">
            <div class="stat-icon">📋</div>
            <div class="stat-content">
                <div class="stat-label">Total Pelanggaran</div>
                <div class="stat-value"><?= $total_pelanggaran; ?></div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Statistik untuk siswa -->
        <?php if($role == 'siswa'): ?>

        <!-- Jumlah pelanggaran milik siswa -->
        <div class="stat-card">
            <div class="stat-icon">📄</div>
            <div class="stat-content">
                <div class="stat-label">Jumlah Pelanggaran Saya</div>
                <div class="stat-value"><?= $total_pelanggaran; ?></div>
            </div>
        </div>

        <!-- Total poin milik siswa -->
        <div class="stat-card">
            <div class="stat-icon">🎯</div>
            <div class="stat-content">
                <div class="stat-label">Total Poin Saya</div>
                <div class="stat-value"><?= $total_poin_siswa; ?></div>
            </div>
        </div>

        <!-- Status sanksi siswa berdasarkan poin -->
        <div class="stat-card">
            <div class="stat-icon">🚨</div>
            <div class="stat-content">
                <div class="stat-label">Status</div>
                <div class="stat-value"><?= htmlspecialchars($status_siswa); ?></div>
            </div>
        </div>
        <?php endif; ?>

    </div>

    <!-- ===============================
         KARTU FITUR / QUICK ACCESS
    =============================== -->
    <div class="card-grid">

        <!-- Kartu Data Siswa untuk admin -->
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

        <!-- Kartu Data Pelanggaran untuk admin dan guru BK -->
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

        <!-- Kartu Input Pelanggaran untuk guru mapel -->
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

        <!-- Kartu Pelanggaran Saya untuk siswa -->
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