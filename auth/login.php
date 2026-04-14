<?php 
// Memulai session agar bisa mengambil data error dari process_login
session_start();


// Variabel untuk menampung pesan error (default kosong)
$error = '';

// Mengecek apakah ada pesan error di session
if (isset($_SESSION['error'])) {

    // Jika ada, ambil pesan error dari session
    $error = $_SESSION['error'];

    // Hapus error dari session agar tidak muncul terus
    unset($_SESSION['error']);
}
?>

<!DOCTYPE html>
<html>
<head>
    <!-- Judul halaman login -->
    <title>Login</title>

    <!-- Menghubungkan file CSS untuk styling -->
    <link rel="stylesheet" href="../assets/style.css">
</head>

<body>

<!-- Wrapper utama untuk layout login (biasanya untuk center posisi) -->
<div class="login-wrapper">

  <!-- Box login (container form) -->
  <div class="login-box">

    <!-- Judul form login -->
    <h2>Login Sistem Poin Pelanggaran</h2>


    <!-- ===============================
         TAMPILKAN PESAN ERROR
    =============================== -->

    <!-- Jika variabel $error tidak kosong -->
    <?php if($error): ?>

      <!-- Tampilkan pesan error -->
      <div class="error-message">
        <?= $error; ?>
      </div>

    <?php endif; ?>


    <!-- ===============================
         FORM LOGIN
    =============================== -->

    <!-- Form dikirim menggunakan method POST ke process_login.php -->
    <form method="post" action="process_login.php">

      <!-- Input untuk username -->
      <!-- required = wajib diisi sebelum submit -->
      <input type="text" name="username" placeholder="Username" required>

      <!-- Input untuk password -->
      <!-- type password agar teks disembunyikan -->
      <input type="password" name="password" placeholder="Password" required>


      <!-- Tombol untuk submit form -->
      <button type="submit">Login</button>

    </form>
  </div>
</div>

</body>
</html>