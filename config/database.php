<?php
try {
    // Membuat koneksi ke database MySQL dengan charset utf8mb4
    // agar teks tersimpan dengan lebih aman dan lengkap.
    $pdo = new PDO(
        'mysql:host=localhost;dbname=db_pelanggaran_siswa;charset=utf8mb4',
        'root',
        ''
    );

    // Menampilkan error dalam bentuk exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Mengatur hasil query default menjadi array asosiatif
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // Menghentikan program jika koneksi gagal
    die("DB Error: " . $e->getMessage());
}