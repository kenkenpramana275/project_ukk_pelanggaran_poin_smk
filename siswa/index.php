<?php
session_start();
if (!isset($_SESSION['login'])) header("Location: ../auth/login.php");

include '../config/database.php';
include '../config/auth.php';

$currentFolder = basename(dirname($_SERVER['PHP_SELF']));
$currentPage   = basename($_SERVER['PHP_SELF']);

$role = $_SESSION['role'];

checkLogin();
allowRoles(['admin', 'guru_bk']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Siswa</title>
    <link rel="stylesheet" href="../assets/style.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
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
    <h2>Data Siswa</h2>
    <a href="tambah.php" class="btn btn-tambah">Tambah Siswa</a>

    <table id="usersTable" class="display">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Siswa</th>
                <th>NIS</th>
                <th>Kelas</th>
                <th>Jenis Kelamin</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            $stmt = $pdo->query("
                SELECT 
                    siswa.*, 
                    kelas.tingkat, 
                    kelas.jurusan, 
                    kelas.nama_kelas
                FROM siswa
                JOIN kelas ON siswa.id_kelas = kelas.id_kelas
                ORDER BY siswa.id_siswa
            ");

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $jenis_kelamin = ($row['jenis_kelamin'] == 'L') ? 'Laki-laki' : 'Perempuan';
                $kelas = $row['tingkat'] . " " . $row['jurusan'] . " " . $row['nama_kelas'];
            ?>
                <tr>
                    <td><?= $no; ?></td>
                    <td><?= htmlspecialchars($row['nama']); ?></td>
                    <td><?= htmlspecialchars($row['nis']); ?></td>
                    <td><?= htmlspecialchars($kelas); ?></td>
                    <td><?= $jenis_kelamin; ?></td>
                    <td>
                        <a href="edit.php?id=<?= $row['id_siswa']; ?>" class="btn btn-edit">Edit</a>
                        <a href="hapus.php?id=<?= $row['id_siswa']; ?>" class="btn btn-delete btn-hapus">Delete</a>
                    </td>
                </tr>
            <?php
                $no++;
            }
            ?>
        </tbody>
    </table>
</div>

<!-- Popup Konfirmasi Hapus -->
<div class="popup-overlay" id="deletePopup" style="display: none;">
    <div class="popup-box popup-error">
        <div class="popup-icon">!</div>
        <h3>Konfirmasi Hapus</h3>
        <p>Yakin ingin menghapus data siswa ini?</p>
        <div class="popup-actions">
            <button type="button" class="popup-btn popup-btn-cancel" onclick="closeDeletePopup()">Batal</button>
            <a href="#" id="confirmDeleteBtn" class="popup-btn popup-btn-delete">Ya, Hapus</a>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function () {
        $('#usersTable').DataTable();

        $('.btn-hapus').on('click', function (e) {
            e.preventDefault();
            const deleteUrl = $(this).attr('href');
            $('#confirmDeleteBtn').attr('href', deleteUrl);
            $('#deletePopup').fadeIn(200);
        });
    });

    function closeDeletePopup() {
        $('#deletePopup').fadeOut(200);
    }

    $(document).on('click', '#deletePopup', function(e) {
        if (e.target === this) {
            closeDeletePopup();
        }
    });
</script>

</body>
</html>