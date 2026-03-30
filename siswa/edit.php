<?php
include '../config/database.php';
$id = $_GET['id'];


$stmt = $pdo->prepare("SELECT * FROM siswa WHERE id_siswa = ?");
$stmt->execute([$id]);
$siswa = $stmt->fetch();


if (isset($_POST['update'])) {
$stmt = $pdo->prepare("UPDATE siswa SET nama=?, nis=?, kelas=?, alamat=?, nama_orang_tua=?, kontak_orang_tua=? WHERE id_siswa=?");
$stmt->execute([
$_POST['nama'],
$_POST['nis'],
$_POST['kelas'],
$_POST['alamat'],
$_POST['nama_orang_tua'],
$_POST['kontak_orang_tua'],
$id
]);
header("Location: index.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <form method="post">
    <input name="nama" value="<?= $siswa['nama'] ?>" required>
    <input name="nis" value="<?= $siswa['nis'] ?>" required>
    <input name="kelas" value="<?= $siswa['kelas'] ?>" required>
    <input name="alamat" value="<?= $siswa['alamat'] ?>" required>
    <input name="nama_orang_tua" value="<?= $siswa['nama_orang_tua'] ?>" required>
    <input name="kontak_orang_tua" value="<?= $siswa['kontak_orang_tua'] ?>" required>
    <button name="update">Update</button>
</form>
</body>
</html>


