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

$popup = false;
$popup_type = '';
$popup_message = '';
$redirect_url = '';

checkLogin();
allowRoles(['admin']);

$id = $_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user) {
    echo "Data user tidak ditemukan!";
    exit;
}

if (isset($_POST['update'])) {
    $id_siswa = ($_POST['role'] == 'siswa' && !empty($_POST['id_siswa'])) ? $_POST['id_siswa'] : null;

    if ($_POST['username'] != '' && $_POST['role'] != '') {
        $stmt = $pdo->prepare("UPDATE users SET username = ?, role = ?, id_siswa = ? WHERE id = ?");
        $stmt->execute([
            $_POST['username'],
            $_POST['role'],
            $id_siswa,
            $id
        ]);

        $popup = true;
        $popup_type = 'success';
        $popup_message = 'User berhasil diupdate!';
        $redirect_url = 'index.php';
    } else {
        $popup = true;
        $popup_type = 'error';
        $popup_message = 'Semua data wajib diisi!';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link rel="stylesheet" href="../assets/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>

<div class="container-add">
    <div class="form-box">
        <h2>Edit User</h2>

        <form method="post">
            <label for="username">Username</label>
            <input type="text" name="username" id="username" value="<?= htmlspecialchars($user['username']) ?>" required>

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

            <button type="submit" name="update" class="btn-edit">Update</button>
            <a href="index.php" class="btn">Kembali</a>
        </form>
    </div>
</div>

<?php if ($popup): ?>
<div class="popup-overlay">
    <div class="popup-box <?= $popup_type === 'success' ? 'popup-success' : 'popup-error' ?>">
        <div class="popup-icon"><?= $popup_type === 'success' ? '✓' : '!' ?></div>
        <h3><?= $popup_type === 'success' ? 'Berhasil' : 'Peringatan' ?></h3>
        <p><?= $popup_message ?></p>
        <button class="popup-btn" onclick="window.location.href='<?= $redirect_url ?>'">OK</button>
    </div>
</div>
<?php endif; ?>

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