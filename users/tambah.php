<?php
session_start();
include '../config/database.php';

if (!isset($_SESSION['role'])) {
    header("Location: ../auth/login.php");
    exit;
}

if ($_SESSION['role'] != 'admin') {
    echo "Akses ditolak!";
    exit;
}

if (isset($_POST['simpan'])) {
    $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
    $stmt->execute([
        $_POST['username'],
        password_hash($_POST['password'], PASSWORD_DEFAULT),
        $_POST['role']
    ]);

    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah User</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<div class="container-add">
    <div class="form-box">
        <h2>Tambah User</h2>

        <form method="post">
            <label>Username</label>
            <input type="text" name="username" required>

            <label>Password</label>
            <input type="password" name="password" required>

            <label>Role</label>
            <select name="role" required>
                <option value="">-- Pilih Role --</option>
                <option value="admin">Admin</option>
                <option value="guru_bk">Guru BK</option>
                <option value="guru_mapel">Guru Mapel</option>
                <option value="siswa">Siswa</option>
            </select>

            <button type="submit" name="simpan" class="btn-tambah">Simpan</button>
            <a href="index.php" class="btn">Kembali</a>
        </form>
    </div>
</div>

</body>
</html>