<?php
session_start();
include '../config/database.php';
include '../config/auth.php';

if (!isset($_SESSION['role'])) {
    header("Location: ../auth/login.php");
    exit;
}

if ($_SESSION['role'] != 'admin') {
    echo "Akses ditolak!";
    exit;
}

checkLogin();
allowRoles(['admin']);

$id = $_GET['id'];

$stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
$stmt->execute([$id]);

header("Location: index.php");
exit;
?>