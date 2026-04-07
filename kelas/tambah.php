<?php
session_start();
include '../config/database.php';
include '../config/auth.php';

checkLogin();
allowRoles(['admin']);

if(isset($_POST['simpan'])){
    $stmt = $pdo->prepare("INSERT INTO kelas (tingkat, jurusan, nama_kelas) VALUES (?, ?, ?)");
    $stmt->execute([
      $_POST['tingkat'],
      $_POST['jurusan'],
      $_POST['nama_kelas']
    ]);

    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tambah Kelas</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<div class="container-add">
  <div class="form-box">
    <h2>Tambah Kelas</h2>

    <form method="post">
      <label>Tingkat</label>
      <select name="tingkat">
          <option value="X">X</option>
          <option value="XI">XI</option>
          <option value="XII">XII</option>
      </select>

      <label>Jurusan</label>
      <input name="jurusan" placeholder="RPL / TKJ">

      <label>Kelas</label>
      <input name="nama_kelas" placeholder="1">

      <button name="simpan" class="btn-tambah">Simpan</button>
    </form>
  </div>
</div>

</body>
</html>