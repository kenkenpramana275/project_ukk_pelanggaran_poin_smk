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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>

    <div class="container-add">
        <div class="form-box">
            <h2>Edit Jenis Pelanggaran</h2>

            <form method="POST">
                <label>Nama Jenis</label>
                <input type="text" name="nama_jenis"
                    value="<?= htmlspecialchars($data['nama_jenis']) ?>" required>

                <label>Poin</label>
                <input type="number" name="poin"
                    value="<?= $data['poin'] ?>" required>

                <button type="submit" name="update" class="btn btn-edit">
                    Update
                </button>        
                <a href="index.php" class="btn">Batal</a>
            </form>        
        </div>
    </div>

</body>
</html>