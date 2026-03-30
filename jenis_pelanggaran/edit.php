<?php
session_start();
include '../config/database.php';

if (!isset($_SESSION['login'])) {
    header("Location: ../auth/login.php");
    exit;
}

// Cek apakah ada ID
if (!isset($_GET['id'])) {
    header("Location: jenis.php");
    exit;
}

$id = $_GET['id'];

// Ambil data berdasarkan ID
$stmt = $pdo->prepare("SELECT * FROM jenis_pelanggaran WHERE id_jenis = ?");
$stmt->execute([$id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

// Jika data tidak ditemukan
if (!$data) {
    header("Location: jenis.php");
    exit;
}

// PROSES UPDATE
if (isset($_POST['update'])) {

    $stmt = $pdo->prepare("
        UPDATE jenis_pelanggaran 
        SET nama_jenis = ?, poin = ?
        WHERE id_jenis = ?
    ");

    $stmt->execute([
        $_POST['nama_jenis'],
        $_POST['poin'],
        $id
    ]);

    header("Location: jenis.php");
    exit;
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <div class="container">
    <h2>Edit Jenis Pelanggaran</h2>

    <form method="POST">
        <label>Nama Jenis</label><br>
        <input type="text" name="nama_jenis"
               value="<?= htmlspecialchars($data['nama_jenis']) ?>" required>
        <br><br>

        <label>Poin</label><br>
        <input type="number" name="poin"
               value="<?= $data['poin'] ?>" required>
        <br><br>

        <button type="submit" name="update" class="btn btn-edit">
            Update
        </button>

        <a href="jenis.php" class="btn">Batal</a>
    </form>
</div>
</body>
</html>