<?php
session_start();
include '../config/database.php';
include '../config/auth.php';

checkLogin();
allowRoles(['admin']);

$id = $_GET['id'];

$stmt = $pdo->prepare("DELETE FROM kelas WHERE id_kelas = ?");
$stmt->execute([$id]);

header("Location: index.php");