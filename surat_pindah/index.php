<?php
session_start();
include '../config/database.php';
include '../config/auth.php';

checkLogin();
allowRoles(['admin', 'guru_bk']);

$currentFolder = basename(dirname($_SERVER['PHP_SELF']));
$currentPage   = basename($_SERVER['PHP_SELF']);
$role = $_SESSION['role'];

$stmt = $pdo->query("
    SELECT 
        siswa.id_siswa,
        siswa.nama,
        siswa.nis,
        kelas.tingkat,
        kelas.jurusan,
        kelas.nama_kelas,
        siswa.alamat,
        siswa.nama_orang_tua,
        COALESCE(SUM(jenis_pelanggaran.poin), 0) AS total_poin
    FROM siswa
    LEFT JOIN kelas ON siswa.id_kelas = kelas.id_kelas
    LEFT JOIN pelanggaran ON siswa.id_siswa = pelanggaran.id_siswa
    LEFT JOIN jenis_pelanggaran ON pelanggaran.id_jenis = jenis_pelanggaran.id_jenis
    GROUP BY 
        siswa.id_siswa,
        siswa.nama,
        siswa.nis,
        kelas.tingkat,
        kelas.jurusan,
        kelas.nama_kelas,
        siswa.alamat,
        siswa.nama_orang_tua
    HAVING total_poin >= 120
    ORDER BY total_poin DESC, siswa.nama ASC
");
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Pindah</title>
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
        class="nav-link <?= ($currentFolder == 'cetak_rekap') ? 'active' : '' ?>">
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

        <a href="index.php" class="nav-link active">
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

<div class="container">
    <h2>Daftar Surat Pindah</h2>

    <table id="pindahTable" class="display">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Siswa</th>
                <th>NIS</th>
                <th>Kelas</th>
                <th>Total Poin</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; ?>
            <?php foreach($data as $row): ?>
            <tr>
                <td><?= $no++; ?></td>
                <td><?= htmlspecialchars($row['nama']); ?></td>
                <td><?= htmlspecialchars($row['nis']); ?></td>
                <td>
                <?= htmlspecialchars($row['tingkat'] . " " . $row['jurusan'] . " " . $row['nama_kelas']); ?>
                </td>
                <td><?= (int)$row['total_poin']; ?></td>
                <td>Surat Pindah</td>
                <td>
                    <a href="cetak_pindah.php?id_siswa=<?= $row['id_siswa']; ?>" target="_blank" class="btn">
                        Cetak Surat Pindah
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script>
$(document).ready(function () {
    $('#pindahTable').DataTable();
});
</script>
</body>
</html>