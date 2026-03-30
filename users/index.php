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

<html>
    <link rel="stylesheet" href="../assets/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
</head>
<head>
    <title>Users</title>
</head>

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

    <a href="users/index.php" class="nav-link">Users</a>

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
            <h1 class="greeting-title">Profile Saya</h1>
            <p class="greeting-user">
                Username: <?= $_SESSION['username']; ?>
            </p>
            <p>Role: <?= $_SESSION['role']; ?></p>
        </div>
        <div class="header-decoration"></div>
    </div>
    <h2>Daftar User</h2> 
    <table border="1" cellpadding="10">
    <tr>
        <th>ID</th>
        <th>Username</th>
        <th>Role</th>
    </tr>

    <?php foreach ($users as $u): ?>
    <tr>
        <td><?= $u['id']; ?></td>
        <td><?= htmlspecialchars($u['username']); ?></td>
        <td><?= $u['role']; ?></td>
    </tr>
    <?php endforeach; ?>
</table>

</div>


<hr>

<!-- FORM TAMBAH USER (HANYA ADMIN) -->
<?php if($role == 'admin'): ?>

<h2>Tambah User</h2>

<form method="POST">
    <label>Username</label><br>
    <input type="text" name="username" required><br><br>

    <label>Password</label><br>
    <input type="text" name="password" required><br><br>

    <label>Role</label><br>
    <select name="role" required>
        <option value="">-- Pilih Role --</option>
        <option value="admin">Admin</option>
        <option value="guru_bk">Guru BK</option>
        <option value="guru_mapel">Guru Mapel</option>
        <option value="siswa">Siswa</option>
    </select><br><br>

    <button type="submit" name="tambah">Tambah User</button>
</form>

<?php endif; ?>

</body>
</html>