<?php
session_start();
include '../config/database.php';

/* TAMBAH DATA */
if (isset($_POST['simpan'])) {
    $stmt = $pdo->prepare(
        "INSERT INTO jenis_pelanggaran (nama_jenis, deskripsi, poin, kategori_kode)
         VALUES (?, ?, ?, ?)"
    );
    $stmt->execute([
        $_POST['nama_jenis'],
        $_POST['deskripsi'],
        $_POST['poin'],
        $_POST['kategori_kode'],
    ]);

    header("Location: index.php");
    exit;
}

?>

<!DOCTYPE html>
<link rel="stylesheet" href="..\\/assets/style.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
<html lang="en">
<body>

<div class="container-add">
  <div class="form-box">
  <h2>Kelola Jenis Pelanggaran</h2>
  
    <form method="post">
      <label>Nama Jenis</label>
      <input type="text" name="nama_jenis" required>

      <label>Deskripsi Pelanggaran</label>
      <input type="text" name="deskripsi" required>

      <label>Poin Pelanggaran</label>
      <input type="number" name="poin" required>

      <label>Kategori kode</label>
      <select name="kategori_kode" required>
          <option value="">-- Pilih Kategori --</option>
          <option value="SS">SS - Seragam Sekolah</option>
          <option value="KS">KS - Kehadiran di Sekolah</option>
          <option value="PBM">PBM - Proses Belajar Mengajar</option>
          <option value="PNN">PNN - Pelanggaran Norma-Norma</option>
          <option value="PB">PB - Pelanggaran Berat</option>
          <option value="KB">KB - Kesopanan Berkendaraan</option>
          <option value="UB">UB - Upacara Bendera</option>
      </select>

      <button name="simpan">Simpan</button>
    </form>
    
  </div>
</div>
</body>
</html>