<?php
// Memulai session agar data login di $_SESSION bisa digunakan
session_start();

// Menghubungkan file ke database
include '../config/database.php';

// Menghubungkan file ke fungsi autentikasi dan otorisasi
include '../config/auth.php';

// Mengecek apakah user sudah login
checkLogin();

// Membatasi akses halaman ini hanya untuk role siswa
allowRoles(['siswa']);

// Mengambil role user yang sedang login
$role = $_SESSION['role'];

// Mengambil id_siswa dari session
// Ini penting agar siswa hanya bisa melihat data miliknya sendiri
$id_siswa = $_SESSION['id_siswa'];

// Mengambil nama folder saat ini dari URL/path
// Bisa dipakai untuk penanda menu aktif
$currentFolder = basename(dirname($_SERVER['PHP_SELF']));

// Mengambil nama file saat ini
$currentPage   = basename($_SERVER['PHP_SELF']);


// ===============================
// AMBIL DATA SISWA + KELAS
// ===============================

// Menyiapkan query untuk mengambil data siswa beserta data kelasnya
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

// Menjalankan query dengan id_siswa dari session
$stmtSiswa->execute([$id_siswa]);

// Mengambil hasil query dalam bentuk array asosiatif
$siswa = $stmtSiswa->fetch(PDO::FETCH_ASSOC);

// Jika data siswa tidak ditemukan
if (!$siswa) {

    // Tampilkan pesan error
    echo "Data siswa tidak ditemukan.";

    // Hentikan program
    exit;
}

// Menggabungkan tingkat, jurusan, dan nama kelas
// agar tampil menjadi format kelas lengkap
$kelasLengkap = $siswa['tingkat'] . " " . $siswa['jurusan'] . " " . $siswa['nama_kelas'];


// ===============================
// AMBIL RIWAYAT PELANGGARAN SISWA
// ===============================

// Menyiapkan query untuk mengambil seluruh riwayat pelanggaran siswa
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

// Menjalankan query dengan id_siswa
$stmt->execute([$id_siswa]);

// Mengambil semua data riwayat pelanggaran siswa
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);


// ===============================
// HITUNG TOTAL POIN
// ===============================

// Nilai awal total poin = 0
$totalPoin = 0;

// Menjumlahkan seluruh poin dari data pelanggaran
foreach ($data as $d) {
    $totalPoin += (int)$d['poin'];
}


// ===============================
// TENTUKAN STATUS BERDASARKAN TOTAL POIN
// ===============================

// Jika total poin 120 atau lebih
if ($totalPoin >= 120) {

    // Status siswa: Surat Pindah
    $status = 'Surat Pindah';

// Jika total poin 75 atau lebih
} elseif ($totalPoin >= 75) {

    // Status siswa: Surat Perjanjian
    $status = 'Surat Perjanjian';

// Jika total poin 30 atau lebih
} elseif ($totalPoin >= 30) {

    // Status siswa: Panggilan Orang Tua
    $status = 'Panggilan Orang Tua';

// Jika kurang dari 30
} else {

    // Status siswa masih aman
    $status = 'Aman';
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <!-- Encoding karakter -->
    <meta charset="UTF-8">

    <!-- Responsive layout -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Judul halaman -->
    <title>Pelanggaran Saya</title>

    <!-- Menghubungkan file CSS utama -->
    <link rel="stylesheet" href="../assets/style.css">

    <!-- Menghubungkan CSS DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">

    <!-- Optimasi koneksi ke Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">

    <!-- Optimasi koneksi ke Google Fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- Menghubungkan font -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>

<!-- ===============================
     SIDEBAR / NAVIGASI
=============================== -->
<div class="nav">

    <!-- Brand / logo aplikasi -->
    <div class="nav-brand">
        <div class="brand-icon">📚</div>
        <span class="brand-text">SISWA<span class="brand-accent">TRACK</span></span>
    </div>

    <!-- Menu navigasi -->
    <div class="nav-links">

        <!-- Judul section menu -->
        <div class="nav-section">MAIN MENU</div>

        <!-- Menu dashboard -->
        <a href="../dashboard.php"
           class="nav-link <?= ($currentPage == 'dashboard.php') ? 'active' : '' ?>">
            <span class="nav-icon">🏠</span>
            <span class="nav-text">Dashboard</span>
        </a>

        <!-- Menu pelanggaran saya -->
        <a href="saya.php" class="nav-link active">
            <span class="nav-icon">📄</span>
            <span class="nav-text">Pelanggaran Saya</span>
        </a>
    </div>

    <!-- Footer sidebar -->
    <div class="nav-footer">

        <!-- Tombol logout -->
        <a href="../auth/logout.php" class="nav-logout">
            <span class="nav-icon">🚪</span>
            <span class="nav-text">Logout</span>
        </a>
    </div>
</div>


<!-- ===============================
     KONTEN UTAMA
=============================== -->
<div class="container">

    <!-- Header halaman -->
    <div class="dashboard-header">
        <div class="header-greeting">

            <!-- Judul halaman -->
            <h1 class="greeting-title">Pelanggaran Saya</h1>

            <!-- Menampilkan nama siswa, NIS, dan kelas -->
            <p class="greeting-user">
                <?= htmlspecialchars($siswa['nama']); ?> | 
                <?= htmlspecialchars($siswa['nis']); ?> | 
                <?= htmlspecialchars($kelasLengkap); ?>
            </p>

            <!-- Menampilkan total poin dan status -->
            <p class="greeting-user">
                Total Poin: <?= $totalPoin; ?> | Status: <?= htmlspecialchars($status); ?>
            </p>
        </div>

        <!-- Dekorasi tambahan -->
        <div class="header-decoration"></div>
    </div>

    <!-- Judul tabel -->
    <h2>Riwayat Pelanggaran</h2>

    <!-- Tabel riwayat pelanggaran -->
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

            <!-- Nomor urut dimulai dari 1 -->
            <?php $no = 1; ?>

            <!-- Loop seluruh data pelanggaran -->
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


<!-- Menghubungkan jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- Menghubungkan JavaScript DataTables -->
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>

<script>
$(document).ready(function () {

    // Mengaktifkan DataTables pada tabel riwayat pelanggaran
    $('#pelanggaranSayaTable').DataTable();
});
</script>

</body>
</html>