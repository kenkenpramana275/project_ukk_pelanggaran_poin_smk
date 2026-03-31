<?php
session_start();
include '../config/database.php';

if (!isset($_SESSION['login'])) {
    header("Location: ../auth/login.php");
    exit;
}

/* TAMBAH DATA */
if (isset($_POST['simpan'])) {
    $stmt = $pdo->prepare(
        "INSERT INTO jenis_pelanggaran (nama_jenis, poin)
         VALUES (?, ?)"
    );
    $stmt->execute([
        $_POST['nama_jenis'],
        $_POST['poin']
    ]);

    header("Location: jenis.php");
    exit;
}

/* HAPUS DATA */
if (isset($_GET['hapus'])) {
    $stmt = $pdo->prepare(
        "DELETE FROM jenis_pelanggaran WHERE id_jenis = ?"
    );
    $stmt->execute([$_GET['hapus']]);

    header("Location: jenis.php");
    exit;
}

/* AMBIL DATA */
$stmt = $pdo->query("SELECT * FROM jenis_pelanggaran ORDER BY id_jenis DESC");
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
<html lang="en">
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
        <a href="../users/index.php" class="nav-link">
            <span class="nav-icon">👤</span>
            <span class="nav-text">Users</span>
        </a>
            <a href="../auth/logout.php" class="nav-logout">
                <span class="nav-icon">🚪</span>
                <span class="nav-text">Logout</span>
            </a>
        </div>
    </div>

<div class="container">
  <h2>Jenis Pelanggaran</h2>
    <a href="tambah.php" class="btn btn-tambah">Tambah</a>
    <br>
  <table border="1">
    <tr>
      <th>Nama Jenis</th>
      <th>Poin</th>
      <th>Aksi</th>
    </tr>

    <?php foreach ($data as $row): ?>
    <tr>
      <td><?= htmlspecialchars($row['nama_jenis']) ?></td>
      <td><?= $row['poin'] ?></td>
        <td>
            <a href="edit.php?id=<?= $row['id_jenis']; ?>" class="btn btn-edit">Edit</a>
            <a href="index.php?hapus=<?= $row['id_jenis']; ?>"
            onclick="return confirm('Yakin hapus?')"
            class="btn btn-delete">
            Hapus
            </a>
        </td>
    </tr>
    <?php endforeach; ?>

  </table>
</div>
</body>
</html>