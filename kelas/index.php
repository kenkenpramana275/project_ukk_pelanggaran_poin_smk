<?php
session_start();
include '../config/database.php';
include '../config/auth.php';

checkLogin();
allowRoles(['admin']);

$data = $pdo->query("SELECT * FROM kelas ORDER BY tingkat, jurusan, nama_kelas")->fetchAll(PDO::FETCH_ASSOC);

$currentFolder = basename(dirname($_SERVER['PHP_SELF']));
$currentPage   = basename($_SERVER['PHP_SELF']);
$role = $_SESSION['role'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Kelas</title>
    <link rel="stylesheet" href="../assets/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>

<div class="nav">
    <div class="nav-brand">
        <div class="brand-icon">📚</div>
        <span class="brand-text">SISWA<span class="brand-accent">TRACK</span></span>
    </div>
    
    <div class="nav-links">
        <div class="nav-section">MAIN MENU</div>

        <a href="../dashboard.php"
           class="nav-link <?= ($currentPage == 'dashboard.php') ? 'active' : '' ?>">
            <span class="nav-icon">🏠</span>
            <span class="nav-text">Dashboard</span>
        </a>

        <?php if ($role == 'admin'): ?>
        <a href="../kelas/index.php"
           class="nav-link <?= ($currentFolder == 'kelas') ? 'active' : '' ?>">
            <span class="nav-icon">🏫</span>
            <span class="nav-text">Data Kelas</span>
        </a>
        <?php endif; ?>

        <?php if ($role == 'admin'): ?>
        <a href="../siswa/index.php"
           class="nav-link <?= ($currentFolder == 'siswa') ? 'active' : '' ?>">
            <span class="nav-icon">👥</span>
            <span class="nav-text">Data Siswa</span>
        </a>
        <?php endif; ?>

        <?php if ($role == 'admin' || $role == 'guru_bk'): ?>
        <a href="../pelanggaran/index.php"
           class="nav-link <?= ($currentFolder == 'pelanggaran') ? 'active' : '' ?>">
            <span class="nav-icon">⚠️</span>
            <span class="nav-text">Data Pelanggaran</span>
        </a>
        <?php endif; ?>

        <?php if ($role == 'admin'): ?>
        <a href="../jenis_pelanggaran/index.php"
           class="nav-link <?= ($currentFolder == 'jenis_pelanggaran') ? 'active' : '' ?>">
            <span class="nav-icon">📋</span>
            <span class="nav-text">Jenis Pelanggaran</span>
        </a>
        <?php endif; ?>

        <?php if ($role == 'guru_mapel'): ?>
        <a href="../pelanggaran/tambah.php" class="nav-link">
            <span class="nav-icon">➕</span>
            <span class="nav-text">Input Pelanggaran</span>
        </a>
        <?php endif; ?>

        <?php if ($role == 'siswa'): ?>
        <a href="../pelanggaran/saya.php"
           class="nav-link <?= ($currentPage == 'saya.php') ? 'active' : '' ?>">
            <span class="nav-icon">📄</span>
            <span class="nav-text">Pelanggaran Saya</span>
        </a>
        <?php endif; ?>

        <?php if ($role == 'admin' || $role == 'guru_bk'): ?>
        <a href="../cetak_rekap/index.php"
           class="nav-link <?= ($currentFolder == 'cetak_rekap') ? 'active' : '' ?>">
            <span class="nav-icon">🖨️</span>
            <span class="nav-text">Rekap Pelanggaran</span>
        </a>
        <?php endif; ?>

        <div class="nav-section">CETAK SURAT</div>

        <?php if ($role == 'admin' || $role == 'guru_bk'): ?>
        <a href="../surat_orangtua/index.php"
           class="nav-link <?= ($currentFolder == 'surat_orangtua') ? 'active' : '' ?>">
            <span class="nav-icon">📨</span>
            <span class="nav-text">Panggilan Orang Tua</span>
        </a>
        <?php endif; ?>

        <?php if ($role == 'admin' || $role == 'guru_bk'): ?>
        <a href="../surat_perjanjian/index.php"
           class="nav-link <?= ($currentFolder == 'surat_perjanjian') ? 'active' : '' ?>">
            <span class="nav-icon">📝</span>
            <span class="nav-text">Surat Perjanjian</span>
        </a>
        <?php endif; ?>

        <?php if ($role == 'admin' || $role == 'guru_bk'): ?>
        <a href="../surat_pindah/index.php"
           class="nav-link <?= ($currentFolder == 'surat_pindah') ? 'active' : '' ?>">
            <span class="nav-icon">📑</span>
            <span class="nav-text">Surat Pindah</span>
        </a>
        <?php endif; ?>
    </div>

    <div class="nav-footer">
        <?php if ($role == 'admin'): ?>
        <a href="../users/index.php" class="nav-link">
            <span class="nav-icon">👤</span>
            <span class="nav-text">Users</span>
        </a>
        <?php endif; ?>

        <a href="../auth/logout.php" class="nav-logout">
            <span class="nav-icon">🚪</span>
            <span class="nav-text">Logout</span>
        </a>
    </div>
</div>

<div class="container">
    <h2>Data Kelas</h2>

    <a href="tambah.php" class="btn btn-tambah">Tambah Kelas</a>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tingkat</th>
                <th>Jurusan</th>
                <th>Nama Kelas</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; ?>
            <?php foreach ($data as $row): ?>
            <tr>
                <td><?= $no++; ?></td>
                <td><?= htmlspecialchars($row['tingkat']); ?></td>
                <td><?= htmlspecialchars($row['jurusan']); ?></td>
                <td><?= htmlspecialchars($row['nama_kelas']); ?></td>
                <td>
                    <a href="edit.php?id=<?= $row['id_kelas']; ?>" class="btn btn-edit">Edit</a>
                    <a href="hapus.php?id=<?= $row['id_kelas']; ?>" class="btn btn-delete btn-hapus">Hapus</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Popup Konfirmasi Hapus -->
<div class="popup-overlay" id="deletePopup" style="display: none;">
    <div class="popup-box popup-error">
        <div class="popup-icon">!</div>
        <h3>Konfirmasi Hapus</h3>
        <p>Yakin ingin menghapus data kelas ini?</p>
        <div class="popup-actions">
            <button type="button" class="popup-btn popup-btn-cancel" onclick="closeDeletePopup()">Batal</button>
            <a href="#" id="confirmDeleteBtn" class="popup-btn popup-btn-delete">Ya, Hapus</a>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const deleteButtons = document.querySelectorAll('.btn-hapus');
        const deletePopup = document.getElementById('deletePopup');
        const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');

        deleteButtons.forEach(function (button) {
            button.addEventListener('click', function (e) {
                e.preventDefault();
                const deleteUrl = this.getAttribute('href');
                confirmDeleteBtn.setAttribute('href', deleteUrl);
                deletePopup.style.display = 'flex';
            });
        });

        deletePopup.addEventListener('click', function (e) {
            if (e.target === deletePopup) {
                closeDeletePopup();
            }
        });
    });

    function closeDeletePopup() {
        document.getElementById('deletePopup').style.display = 'none';
    }
</script>

</body>
</html>