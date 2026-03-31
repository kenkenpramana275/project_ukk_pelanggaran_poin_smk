<?php
session_start();
include '../config/database.php';

if (!isset($_SESSION['login'])) {
    header("Location: ../auth/login.php");
    exit;
}

$role = $_SESSION['role'];

/* TAMBAH USER (HANYA ADMIN) */
if (isset($_POST['tambah']) && $role == 'admin') {

    $username = $_POST['username'];
    $password = $_POST['password'];
    $role_user = $_POST['role'];

    $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
    $stmt->execute([$username, $password, $role_user]);

    header("Location: index.php");
    exit;
}

if ($role != 'admin' && isset($_POST['tambah'])) {
    die("Akses ditolak!");
}

/* AMBIL DATA USER */
$stmt = $pdo->query("SELECT * FROM users ORDER BY id ASC");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users</title>

    <link rel="stylesheet" href="../assets/style.css">
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

        <a href="../dashboard.php"
           class="nav-link <?= ($currentPage == 'dashboard.php') ? 'active' : '' ?>">
            <span class="nav-icon">🏠</span>
            <span class="nav-text">Dashboard</span>
        </a>

        <?php if($role == 'admin'): ?>
        <a href="../siswa/index.php"
           class="nav-link <?= ($currentFolder == 'siswa') ? 'active' : '' ?>">
            <span class="nav-icon">👥</span>
            <span class="nav-text">Data Siswa</span>
        </a>
        <?php endif; ?>

        <?php if($role == 'admin' || $role == 'guru_bk'): ?>
        <a href="../pelanggaran/index.php"
           class="nav-link <?= ($currentFolder == 'pelanggaran') ? 'active' : '' ?>">
            <span class="nav-icon">⚠️</span>
            <span class="nav-text">Data Pelanggaran</span>
        </a>
        <?php endif; ?>

        <?php if($role == 'admin'): ?>
        <a href="../jenis_pelanggaran/index.php"
           class="nav-link <?= ($currentFolder == 'jenis_pelanggaran') ? 'active' : '' ?>">
            <span class="nav-icon">📋</span>
            <span class="nav-text">Jenis Pelanggaran</span>
        </a>
        <?php endif; ?>

        <?php if($role == 'guru_mapel'): ?>
        <a href="../pelanggaran/tambah.php" class="nav-link">
            <span class="nav-icon">➕</span>
            <span class="nav-text">Input Pelanggaran</span>
        </a>
        <?php endif; ?>

        <?php if($role == 'siswa'): ?>
        <a href="../pelanggaran/index.php" class="nav-link">
            <span class="nav-icon">📄</span>
            <span class="nav-text">Pelanggaran Saya</span>
        </a>
        <?php endif; ?>
    </div>

    <div class="nav-footer">
        <a href="users/index.php" class="nav-link active">
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
    <div class="dashboard-header">
        <div class="header-greeting">
            <h1 class="greeting-title">Profile Saya</h1>
            <p class="greeting-user">Username: <?= htmlspecialchars($_SESSION['username']); ?></p>
            <p class="greeting-user">Role: <?= htmlspecialchars($_SESSION['role']); ?></p>
        </div>
        <div class="header-decoration"></div>
    </div>

    <h2>Daftar User</h2>

    <?php if($role == 'admin'): ?>
        <a href="tambah.php" class="btn btn-tambah">+ Tambah User</a>
    <?php endif; ?>

<table>
    <tr>
        <th>ID</th>
        <th>Username</th>
        <th>Role</th>
        <?php if ($role == 'admin'): ?>
            <th>Aksi</th>
        <?php endif; ?>
    </tr>

    <?php foreach ($users as $u): ?>
    <tr>
        <td><?= $u['id']; ?></td>
        <td><?= htmlspecialchars($u['username']); ?></td>
        <td><?= htmlspecialchars($u['role']); ?></td>

        <?php if ($role == 'admin'): ?>
        <td>
            <a href="edit.php?id=<?= $u['id']; ?>" class="btn btn-edit">Edit</a>
            <a href="hapus.php?id=<?= $u['id']; ?>" class="btn btn-delete" onclick="return confirm('Yakin ingin menghapus user ini?')">Hapus</a>
        </td>
        <?php endif; ?>
    </tr>
    <?php endforeach; ?>
</table>

</body>
</html>