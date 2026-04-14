<?php
session_start();
include '../config/database.php';
include '../config/auth.php';

checkLogin();
allowRoles(['admin']);

if (!isset($_GET['id']) || $_GET['id'] == '') {
    header("Location: index.php");
    exit;
}

$id = $_GET['id'];

$stmt = $pdo->prepare("DELETE FROM jenis_pelanggaran WHERE id_jenis = ?");
$stmt->execute([$id]);

header("Location: index.php");
exit;
?>