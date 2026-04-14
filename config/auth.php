<?php
// Mengecek apakah session sudah aktif atau belum
// Jika belum aktif, maka jalankan session_start()
// Ini penting agar $_SESSION bisa digunakan di semua file
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


// ===============================
// FUNGSI CEK LOGIN UMUM
// ===============================
function checkLogin() {

    // Mengecek apakah session 'login' ada atau tidak
    // Jika tidak ada, berarti user belum login
    if (!isset($_SESSION['login'])) {

        // Redirect user ke halaman login
        header("Location: ../auth/login.php");

        // Menghentikan eksekusi program setelah redirect
        exit;
    }
}


// ===============================
// FUNGSI CEK LOGIN KHUSUS ADMIN (ROOT)
// ===============================
function checkLoginRoot() {

    // Mengecek dua hal:
    // 1. Apakah user sudah login
    // 2. Apakah role user adalah 'admin'
    if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {

        // Jika bukan admin, tampilkan pesan
        echo "Akses khusus admin!";

        // Hentikan program
        exit;
    }
}


// ===============================
// FUNGSI BATASI AKSES BERDASARKAN ROLE
// ===============================
function allowRoles($roles = []) {

    // Mengecek apakah session 'role' tersedia
    // Jika tidak ada, berarti ada kesalahan pada sistem login
    if (!isset($_SESSION['role'])) {

        // Tampilkan pesan error
        echo "Role tidak ditemukan!";

        // Hentikan program
        exit;
    }

    // Mengecek apakah role user termasuk dalam daftar yang diizinkan
    // in_array digunakan untuk mencari nilai di dalam array
    if (!in_array($_SESSION['role'], $roles)) {

        // Jika role tidak sesuai, tampilkan pesan
        echo "Akses ditolak!";

        // Hentikan program
        exit;
    }
}