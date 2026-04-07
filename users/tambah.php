<?php
session_start();
include '../config/database.php';
include '../config/auth.php';

checkLogin();
allowRoles(['admin']);

if (!isset($_SESSION['role'])) {
    header("Location: ../auth/login.php");
    exit;
}

if ($_SESSION['role'] != 'admin') {
    echo "Akses ditolak!";
    exit;
}

if (isset($_POST['simpan'])) {
    $id_siswa = ($_POST['role'] == 'siswa' && !empty($_POST['id_siswa'])) ? $_POST['id_siswa'] : null;

    $stmt = $pdo->prepare("INSERT INTO users (username, password, role, id_siswa) VALUES (?, ?, ?, ?)");
    $stmt->execute([
        $_POST['username'],
        $_POST['password'],
        $_POST['role'],
        $id_siswa
    ]);

    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah User</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<div class="container-add">
    <div class="form-box">
        <h2>Tambah User</h2>

        <form method="post">
            <label>Username</label>
            <input type="text" name="username" required>

            <label>Password</label>
            <input type="password" name="password" required>

            <label for="role">Role</label>
            <select name="role" id="role" required>
                <option value="">-- Pilih Role --</option>
                <option value="admin" <?= (isset($user['role']) && $user['role'] == 'admin') ? 'selected' : '' ?>>Admin</option>
                <option value="guru_bk" <?= (isset($user['role']) && $user['role'] == 'guru_bk') ? 'selected' : '' ?>>Guru BK</option>
                <option value="guru_mapel" <?= (isset($user['role']) && $user['role'] == 'guru_mapel') ? 'selected' : '' ?>>Guru Mapel</option>
                <option value="siswa" <?= (isset($user['role']) && $user['role'] == 'siswa') ? 'selected' : '' ?>>Siswa</option>
            </select>
            
            <div id="siswaField">
            <label for="id_siswa">Pilih Siswa</label>
            <select name="id_siswa" id="id_siswa">
                <option value="">-- Pilih Data Siswa --</option>
                <?php
                $stmtSiswa = $pdo->query("SELECT id_siswa, nama, nis FROM siswa ORDER BY nama");
                while ($s = $stmtSiswa->fetch(PDO::FETCH_ASSOC)) {
                    $selected = (isset($user['id_siswa']) && $user['id_siswa'] == $s['id_siswa']) ? 'selected' : '';
                                echo "<option value='{$s['id_siswa']}' {$selected}>"
                . htmlspecialchars($s['nama']) . " - " . htmlspecialchars($s['nis']) .
                "</option>";
                }
                ?>
            </select>
            </div>
            <button type="submit" name="simpan" class="btn-tambah">Simpan</button>
            <a href="index.php" class="btn">Kembali</a>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const roleSelect = document.getElementById('role');
    const siswaField = document.getElementById('siswaField');
    const siswaSelect = document.getElementById('id_siswa');

    function toggleSiswaField() {
        if (roleSelect.value === 'siswa') {
            siswaField.style.display = 'block';
            siswaSelect.setAttribute('required', 'required');
        } else {
            siswaField.style.display = 'none';
            siswaSelect.value = '';
            siswaSelect.removeAttribute('required');
        }
    }

    toggleSiswaField();
    roleSelect.addEventListener('change', toggleSiswaField);
});
</script>
</body>
</html>