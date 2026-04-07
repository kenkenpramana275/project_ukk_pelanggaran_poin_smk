<?php include '../config/database.php'; 

if (isset($_POST['simpan'])) {
    $stmt = $pdo->prepare(
        "INSERT INTO siswa (nama, nis, id_kelas, alamat, kontak_siswa, jenis_kelamin, nama_orang_tua, kontak_orang_tua, pekerjaan_orang_tua) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
    );
    $stmt->execute([
        $_POST['nama'],
        $_POST['nis'],
        $_POST['id_kelas'],
        $_POST['alamat'],
        $_POST['kontak_siswa'],
        $_POST['jenis_kelamin'],
        $_POST['nama_orang_tua'],
        $_POST['kontak_orang_tua'],
        $_POST['pekerjaan_orang_tua'],
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
    <div class="container-add">
  <div class="form-box">
    <h2>Tambah Siswa</h2>

    <form method="post">
      <label>Nama</label>
      <input name="nama" placeholder="Nama" required>

      <label>NIS</label>
      <input name="nis" placeholder="NIS" required>

      <label>Kelas</label>
      <select name="id_kelas" required>
          <option value="">-- Pilih Kelas --</option>
          <?php
          $kelas = $pdo->query("SELECT * FROM kelas");
          foreach($kelas as $k):
          ?>
              <option value="<?= $k['id_kelas']; ?>">
                  <?= $k['tingkat'] . " " . $k['jurusan'] . " " . $k['nama_kelas']; ?>
              </option>
          <?php endforeach; ?>
      </select>

      <label>Alamat</label>
      <input name="alamat" placeholder="Alamat" required>

      <label>Kontak Siswa</label>
      <input name="kontak_siswa" placeholder="Kontak Siswa" required>

      <label>Jenis Kelamin</label>
      <select name="jenis_kelamin" required>
          <option value="">-- Pilih --</option>
          <option value="L">Laki-laki</option>
          <option value="P">Perempuan</option>
      </select>

      <label>Nama Orang Tua</label>
      <input name="nama_orang_tua" placeholder="Nama Orang Tua" required>

      <label>Kontak Orang Tua</label>
      <input name="kontak_orang_tua" placeholder="Kontak Orang Tua" required>

      <label>Pekerjaan Orang Tua</label>
      <input name="pekerjaan_orang_tua" placeholder="Pekerjaan Orang Tua" required>

      <button name="simpan">Simpan</button>
    </form>
  </div>
</div>

<?php
if(isset($_POST['simpan'])){
  mysqli_query($conn, "INSERT INTO siswa(nama,nis,kelas,alamat,kontak_siswa,jenis_kelamin,nama_orang_tua,kontak_orang_tua, pekerjaan_orang_tua)
  VALUES('$_POST[nama]','$_POST[nis]','$_POST[kelas]','$_POST[alamat]','$_POST[kontak_siswa]','$_POST[jenis_kelamin]','$_POST[nama_orang_tua]','$_POST[kontak_orang_tua]','$_POST[pekerjaan_orang_tua]')");
  header("Location: index.php");
}
?>

</body>
</html>


