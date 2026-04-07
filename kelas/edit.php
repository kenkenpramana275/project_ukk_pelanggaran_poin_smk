<?php
session_start();
include '../config/database.php';
include '../config/auth.php';

checkLogin();
allowRoles(['admin']);

$id = $_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM kelas WHERE id_kelas = ?");
$stmt->execute([$id]);
$kelas = $stmt->fetch();

if(isset($_POST['update'])){
    $stmt = $pdo->prepare("UPDATE kelas SET tingkat=?, jurusan=?, nama_kelas=? WHERE id_kelas=?");
    $stmt->execute([
        $_POST['tingkat'],
        $_POST['jurusan'],
        $_POST['nama_kelas'],
        $id
    ]);

    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Kelas</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<div class="container-add">
  <div class="form-box">
    <h2>Edit Kelas</h2>

    <form method="post">

      <label>Tingkat</label>
      <select name="tingkat">
          <option value="X">X</option>
          <option value="XI">XI</option>
          <option value="XII">XII</option>
      </select>

      <label>Jurusan</label>
      <input name="jurusan" value="<?= htmlspecialchars($kelas['jurusan']); ?>" required>

      <label>Nama Kelas</label>
      <input name="nama_kelas" value="<?= htmlspecialchars($kelas['nama_kelas']); ?>" required>

      <button name="update" class="btn btn-edit">Update</button>
      <a href="index.php" class="btn">Cancel</a>
    </form>
  </div>
</div>

</body>
</html>