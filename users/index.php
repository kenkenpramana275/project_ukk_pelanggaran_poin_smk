<?php
session_start();
include '../config/database.php';
include '../config/auth.php';

if (!isset($_SESSION['login'])) {
    header("Location: ../auth/login.php");
    exit;
}

checkLogin();
allowRoles(['admin']);

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

    <div class="nav-section">MAIN MENU</div>

        <a href="../dashboard.php"
           class="nav-link <?= ($currentPage == 'dashboard.php') ? 'active' : '' ?>">
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

        <?php if($role == 'admin' || $role == 'guru_bk'): ?>
        <a href="../cetak_rekap/index.php"
        class="nav-link <?= ($currentFolder == 'cetak_rekap') ? 'active' : '' ?>">
            <span class="nav-icon">🖨️</span>
            <span class="nav-text">Rekap Pelanggaran</span>
        </a>
        <?php endif; ?>

        <?php if($role == 'siswa'): ?>
        <a href="../pelanggaran/index.php" class="nav-link">
            <span class="nav-icon">📄</span>
            <span class="nav-text">Pelanggaran Saya</span>
        </a>
        <?php endif; ?>

        <div class="nav-section">CETAK SURAT</div>

        <?php if($role == 'admin' || $role == 'guru_bk'): ?>
        <a href="../surat_orangtua/index.php"
        class="nav-link <?= ($currentFolder == 'surat_orangtua') ? 'active' : '' ?>">
            <span class="nav-icon">📨</span>
            <span class="nav-text">Panggilan Orang Tua</span>
        </a>
        <?php endif; ?>

        <?php if($role == 'admin' || $role == 'guru_bk'): ?>
        <a href="../surat_perjanjian/index.php"
        class="nav-link <?= ($currentFolder == 'surat_perjanjian') ? 'active' : '' ?>">
            <span class="nav-icon">📝</span>
            <span class="nav-text">Surat Perjanjian</span>
        </a>
        <?php endif; ?>

        <?php if($role == 'admin' || $role == 'guru_bk'): ?>
        <a href="../surat_pindah/index.php"
        class="nav-link <?= ($currentFolder == 'surat_pindah') ? 'active' : '' ?>">
            <span class="nav-icon">📑</span>
            <span class="nav-text">Surat Pindah</span>
        </a>
        <?php endif; ?>
    </div>

    <div class="nav-footer">
        <?php if($role == 'admin'): ?>
        <a href="../users/index.php" class="nav-link active">
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
            <a href="hapus.php?id=<?= $u['id']; ?>" class="btn btn-delete btn-hapus">Hapus</a>
        </td>
        <?php endif; ?>
    </tr>
    <?php endforeach; ?>
</table>

<!-- Popup Konfirmasi Hapus -->
<div class="popup-overlay" id="deletePopup" style="display: none;">
    <div class="popup-box popup-error">
        <div class="popup-icon">!</div>
        <h3>Konfirmasi Hapus</h3>
        <p>Yakin ingin menghapus user ini?</p>
        <div class="popup-actions">
            <button type="button" class="popup-btn popup-btn-cancel" id="cancelDeleteBtn">Batal</button>
            <a href="#" id="confirmDeleteBtn" class="popup-btn popup-btn-delete">Ya, Hapus</a>
        </div>
    </div>
</div>

<script>
document.addEventListener('click', function(e) {
    const target = e.target.closest('.btn-hapus');
    if (target) {
        e.preventDefault();
        const deleteUrl = target.getAttribute('href');
        document.getElementById('confirmDeleteBtn').setAttribute('href', deleteUrl);
        document.getElementById('deletePopup').style.display = 'flex';
    }
});

document.getElementById('cancelDeleteBtn').addEventListener('click', function() {
    closeDeletePopup();
});

document.getElementById('deletePopup').addEventListener('click', function(e) {
    if (e.target === this) {
        closeDeletePopup();
    }
});

function closeDeletePopup() {
    document.getElementById('deletePopup').style.display = 'none';
}
</script>

</body>
</html>