<?php
session_start();
include '../config/database.php';

$username = $_POST['username'];
$password = $_POST['password'];

// Ambil user
$stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
$stmt->execute([$username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Cek tanpa hash
if ($user && $password == $user['password']) {

    $_SESSION['login'] = true;
    $_SESSION['id_user'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['id_siswa'] = $user['id_siswa'];

    header("Location: ../dashboard.php");
    exit;

} else {
    
    header("Location: login.php?error=1");
    exit;

}
?>