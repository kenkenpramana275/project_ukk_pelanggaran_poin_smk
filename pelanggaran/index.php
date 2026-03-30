<?php
session_start(); 
if(!isset($_SESSION['login'])) header("Location: ../auth/login.php");
include '../config/database.php';

$stmt = $pdo->query("
SELECT pelanggaran.*, siswa.nama, jenis_pelanggaran.nama_jenis, jenis_pelanggaran.poin
FROM pelanggaran
JOIN siswa ON pelanggaran.id_siswa = siswa.id_siswa
JOIN jenis_pelanggaran ON pelanggaran.id_jenis = jenis_pelanggaran.id_jenis
ORDER BY pelanggaran.id_pelanggaran
");

$currentFolder = basename(dirname($_SERVER['PHP_SELF']));
$currentPage   = basename($_SERVER['PHP_SELF']);

$role = $_SESSION['role'];
?>

<!DOCTYPE html>
<link rel="stylesheet" href="../assets/style.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
<body>

<div class="nav">
    <div class="nav-brand">
        <div class="brand-icon">📚</div>
        <span class="brand-text">SISWA<span class="brand-accent">TRACK</span></span>
    </div>
    
<div class="nav-links">

    <!-- Dashboard -->
    <a href="../dashboard.php"
       class="nav-link <?= ($currentPage == 'dashboard.php') ? 'active' : '' ?>">
        <span class="nav-icon">🏠</span>
        <span class="nav-text">Dashboard</span>
    </a>

    <!-- ADMIN ONLY -->
    <?php if($role == 'admin'): ?>
    <a href="../siswa/index.php"
       class="nav-link <?= ($currentFolder == 'siswa') ? 'active' : '' ?>">
        <span class="nav-icon">👥</span>
        <span class="nav-text">Data Siswa</span>
    </a>
    <?php endif; ?>

    <!-- ADMIN & GURU BK -->
    <?php if($role == 'admin' || $role == 'guru_bk'): ?>
    <a href="../pelanggaran/index.php"
       class="nav-link <?= ($currentFolder == 'pelanggaran') ? 'active' : '' ?>">
        <span class="nav-icon">⚠️</span>
        <span class="nav-text">Data Pelanggaran</span>
    </a>
    <?php endif; ?>

    <!-- ADMIN ONLY -->
    <?php if($role == 'admin'): ?>
    <a href="../jenis_pelanggaran/index.php"
       class="nav-link <?= ($currentFolder == 'jenis_pelanggaran') ? 'active' : '' ?>">
        <span class="nav-icon">📋</span>
        <span class="nav-text">Jenis Pelanggaran</span>
    </a>
    <?php endif; ?>

    <!-- GURU MAPEL -->
    <?php if($role == 'guru_mapel'): ?>
    <a href="../pelanggaran/tambah.php"
       class="nav-link">
        <span class="nav-icon">➕</span>
        <span class="nav-text">Input Pelanggaran</span>
    </a>
    <?php endif; ?>

    <!-- SISWA -->
    <?php if($role == 'siswa'): ?>
    <a href="../pelanggaran/index.php"
       class="nav-link">
        <span class="nav-icon">📄</span>
        <span class="nav-text">Pelanggaran Saya</span>
    </a>
    <?php endif; ?>

</div>

    <div class="nav-footer">
        <a href="../auth/logout.php" class="nav-logout">
            <span class="nav-icon">🚪</span>
            <span class="nav-text">Logout</span>
        </a>
    </div>
</div>


    <div class="container">
        <h2>Data Pelanggaran</h2>
    <?php if($role == 'admin' || $role == 'guru_bk' || $role == 'guru_mapel'): ?>
    <a href="tambah.php" class="btn btn-tambah">Tambah</a>
    <?php endif; ?>
    <table id="usersTable" class="display">
    <thead>
        <tr>
            <th>No</th>
            <!-- <th>ID Pelanggaran</th>
            <th>ID Jenis</th> -->
            <th>Nama Siswa</th>
            <th>Tanggal</th>
            <th>Keterangan</th>
            <th>Poin</th>
            <!-- <th>Aksi</th> -->
        </tr>
    </thead>
    <tbody>
        <?php
        // Use YOUR PDO connection  
        $no = 1;    

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "<tr>";
    echo "<td>{$no}</td>";
    // echo "<td>{$row['id_pelanggaran']}</td>";
    // echo "<td>{$row['id_jenis']}</td>";
    echo "<td>{$row['nama']}</td>"; // ← tampilkan nama siswa
    echo "<td>{$row['tanggal']}</td>";
    echo "<td>{$row['keterangan']}</td>";
    echo "<td>{$row['poin']}</td>";
    //     echo "<td>
    //     <a href='edit.php?id={$row['id_pelanggaran']}' class='btn btn-edit'>Edit</a>
    //     <a href='hapus.php?id={$row['id_pelanggaran']}' class='btn btn-delete' onclick=\"return confirm('Yakin ingin menghapus data pelanggaran ini?');\">Hapus</a>
    // </td>";
    echo "</tr>";
}
        ?>
    </tbody>
</table></div>
</body>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function () {
        $('#usersTable').DataTable();
    });
</script>
</html>