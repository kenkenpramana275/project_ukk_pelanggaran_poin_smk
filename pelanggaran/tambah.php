<?php
session_start();
if(!isset($_SESSION['login'])) {
    header("Location: ../auth/login.php");
    exit;
}

include '../config/database.php';
include '../config/auth.php';

checkLogin();
allowRoles(['admin', 'guru_bk', 'guru_mapel']);

/* ambil data siswa */
$stmtSiswa = $pdo->query("SELECT id_siswa, nama FROM siswa ORDER BY nama");
$siswa = $stmtSiswa->fetchAll(PDO::FETCH_ASSOC);

/* ambil data jenis pelanggaran */
$stmtJenis = $pdo->query("SELECT id_jenis, nama_jenis FROM jenis_pelanggaran ORDER BY nama_jenis");
$jenis = $stmtJenis->fetchAll(PDO::FETCH_ASSOC);

/* simpan data */
if (isset($_POST['simpan'])) {

    if ($_POST['id_siswa'] != "" && $_POST['id_jenis'] != "" 
        && $_POST['tanggal'] != "" && $_POST['keterangan'] != "") {

        $stmt = $pdo->prepare(
            "INSERT INTO pelanggaran (id_siswa, id_jenis, tanggal, keterangan)
             VALUES (?, ?, ?, ?)"
        );

        $stmt->execute([
            $_POST['id_siswa'],
            $_POST['id_jenis'],
            $_POST['tanggal'],
            $_POST['keterangan']
        ]);

        if ($_SESSION['role'] == 'guru_mapel') {
            header("Location: ../dashboard.php");
        } else {
            header("Location: index.php");
        }
        exit;

    } else {
        echo "<script>alert('Semua data wajib diisi!');</script>";
    }
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
    <div class="container-add">
  <div class="form-box">
    <h2>Tambah Pelanggaran</h2>

    <form method="post">
      <label>Nama Siswa</label>
      <select name="id_siswa" required>
        <option value="">-- Pilih Siswa --</option>
        <?php foreach ($siswa as $s): ?>
          <option value="<?= $s['id_siswa'] ?>">
            <?= htmlspecialchars($s['nama']) ?>
          </option>
        <?php endforeach; ?>
      </select>

      <label>Jenis Pelanggaran</label>
      <select name="id_jenis" required>
        <option value="">-- Pilih Pelanggaran --</option>
        <?php foreach ($jenis as $j): ?>
          <option value="<?= $j['id_jenis'] ?>">
            <?= htmlspecialchars($j['nama_jenis']) ?>
          </option>
        <?php endforeach; ?>
      </select>

      <label>Tanggal</label>
      <input type="date" name="tanggal" required>

      <label>Keterangan</label>
      <input type="text" name="keterangan" required>

      <button name="simpan">Simpan</button>
    </form>
  </div>
</div>

</body>
</html>