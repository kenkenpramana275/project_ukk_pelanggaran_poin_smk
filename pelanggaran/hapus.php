<?php
// Memulai session
session_start();

// Menghubungkan ke database
include '../config/database.php';

// Menghubungkan ke file auth
include '../config/auth.php';

// Mengecek login user
checkLogin();

// Membatasi akses hanya untuk admin dan guru_bk
allowRoles(['admin', 'guru_bk']);

// Mengecek apakah parameter id tersedia
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "ID pelanggaran tidak ditemukan.";
    exit;
}

// Mengambil id dari URL
$id = $_GET['id'];

// Menyiapkan query hapus
$stmt = $pdo->prepare("DELETE FROM pelanggaran WHERE id_pelanggaran = ?");

// Menjalankan query hapus
$stmt->execute([$id]);

// Redirect kembali ke index
header("Location: index.php");
exit;
?>