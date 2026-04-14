<?php
// Memulai session agar data bisa disimpan ke dalam $_SESSION
session_start();

// Menghubungkan file ini ke database
include '../config/database.php';


// Mengambil input username dari form login (method POST)
$username = $_POST['username'];

// Mengambil input password dari form login (method POST)
$password = $_POST['password'];


// ===============================
// AMBIL DATA USER DARI DATABASE
// ===============================

// Menyiapkan query untuk mencari user berdasarkan username
// Tanda ? digunakan untuk parameter agar lebih aman (prepared statement)
$stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");

// Menjalankan query dengan mengisi parameter username
$stmt->execute([$username]);

// Mengambil hasil query dalam bentuk array asosiatif
// Jika user ditemukan, datanya akan disimpan di variabel $user
$user = $stmt->fetch(PDO::FETCH_ASSOC);


// ===============================
// CEK LOGIN (TANPA HASH PASSWORD)
// ===============================

// Mengecek:
// 1. Apakah user ditemukan
// 2. Apakah password yang diinput sama dengan yang di database
if ($user && $password == $user['password']) {

    // Menandai bahwa user sudah login
    $_SESSION['login'] = true;

    // Menyimpan id user ke dalam session
    $_SESSION['id_user'] = $user['id'];

    // Menyimpan username ke dalam session
    $_SESSION['username'] = $user['username'];

    // Menyimpan role user (admin, guru, siswa, dll)
    // Ini penting untuk pembatasan akses halaman
    $_SESSION['role'] = $user['role'];

    // Menyimpan id siswa (jika user adalah siswa)
    // Bisa bernilai NULL jika bukan siswa
    $_SESSION['id_siswa'] = $user['id_siswa'];

    // Jika login berhasil, arahkan ke dashboard
    header("Location: ../dashboard.php");

    // Menghentikan eksekusi setelah redirect
    exit;

} else {
    
    // Jika login gagal, simpan pesan error ke session
    // Pesan ini bisa ditampilkan di halaman login
    $_SESSION['error'] = "Username atau Password anda salah!";

    // Redirect kembali ke halaman login
    header("Location: login.php");

    // Menghentikan eksekusi setelah redirect
    exit;

}
?>