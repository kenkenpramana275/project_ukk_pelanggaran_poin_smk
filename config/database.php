<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=db_pelanggaran_siswa', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("DB Error: " . $e->getMessage());
}