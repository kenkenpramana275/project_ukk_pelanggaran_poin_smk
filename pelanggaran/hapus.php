<?php
session_start();
if(!isset($_SESSION['login'])) header("Location: ../auth/login.php");
include '../config/database.php';

$id = $_GET['id'];

$stmt = $pdo->prepare("DELETE FROM pelanggaran WHERE id_pelanggaran = ?");
$stmt->execute([$id]);

header("Location: index.php");
exit;
?>
