<?php
include '../config/database.php';
$id = $_GET['id'];
$stmt = $pdo->prepare("DELETE FROM siswa WHERE id_siswa = ?");
$stmt->execute([$id]);
header("Location: index.php");
?>