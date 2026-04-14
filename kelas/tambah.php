<?php
session_start();
include '../config/database.php';
include '../config/auth.php';

$popup = false;
$popup_type = '';
$popup_message = '';
$redirect_url = '';

checkLogin();
allowRoles(['admin']);

if (isset($_POST['simpan'])) {
    $tingkat = trim($_POST['tingkat']);
    $jurusan = trim($_POST['jurusan']);
    $nama_kelas = trim($_POST['nama_kelas']);

    if ($tingkat != '' && $jurusan != '' && $nama_kelas != '') {
        // cek apakah kelas sudah ada
        $cek = $pdo->prepare("SELECT COUNT(*) FROM kelas WHERE tingkat = ? AND jurusan = ? AND nama_kelas = ?");
        $cek->execute([$tingkat, $jurusan, $nama_kelas]);
        $jumlah = $cek->fetchColumn();

        if ($jumlah > 0) {
            $popup = true;
            $popup_type = 'error';
            $popup_message = 'Data kelas sudah ada!';
        } else {
            $stmt = $pdo->prepare("INSERT INTO kelas (tingkat, jurusan, nama_kelas) VALUES (?, ?, ?)");
            $stmt->execute([$tingkat, $jurusan, $nama_kelas]);

            $popup = true;
            $popup_type = 'success';
            $popup_message = 'Data kelas berhasil ditambahkan!';
            $redirect_url = 'index.php';
        }
    } else {
        $popup = true;
        $popup_type = 'error';
        $popup_message = 'Semua data wajib diisi!';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tambah Kelas</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<div class="container-add">
    <div class="form-box">
        <h2>Tambah Kelas</h2>

        <form method="post">
            <label>Tingkat</label>
            <select name="tingkat" required>
                <option value="">-- Pilih Tingkat --</option>
                <option value="X" <?= (isset($_POST['tingkat']) && $_POST['tingkat'] == 'X') ? 'selected' : '' ?>>X</option>
                <option value="XI" <?= (isset($_POST['tingkat']) && $_POST['tingkat'] == 'XI') ? 'selected' : '' ?>>XI</option>
                <option value="XII" <?= (isset($_POST['tingkat']) && $_POST['tingkat'] == 'XII') ? 'selected' : '' ?>>XII</option>
            </select>

            <label>Jurusan</label>
            <input 
                type="text" 
                name="jurusan" 
                placeholder="RPL / TKJ"
                value="<?= isset($_POST['jurusan']) ? htmlspecialchars($_POST['jurusan']) : '' ?>" 
                required
            >

            <label>Kelas</label>
            <input 
                type="text" 
                name="nama_kelas" 
                placeholder="1"
                value="<?= isset($_POST['nama_kelas']) ? htmlspecialchars($_POST['nama_kelas']) : '' ?>" 
                required
            >

            <button type="submit" name="simpan" class="btn-tambah">Simpan</button>
        </form>
    </div>
</div>

<?php if ($popup): ?>
    <div class="popup-overlay" id="popupOverlay">
        <div class="popup-box <?= $popup_type === 'success' ? 'popup-success' : 'popup-error' ?>">
            <div class="popup-icon">
                <?= $popup_type === 'success' ? '✓' : '!' ?>
            </div>
            <h3><?= $popup_type === 'success' ? 'Berhasil' : 'Peringatan' ?></h3>
            <p><?= htmlspecialchars($popup_message) ?></p>
            <button type="button" class="popup-btn" onclick="closePopup()">OK</button>
        </div>
    </div>

    <script>
        function closePopup() {
            const redirectUrl = <?= json_encode($redirect_url) ?>;
            if (redirectUrl) {
                window.location.href = redirectUrl;
            } else {
                document.getElementById('popupOverlay').style.display = 'none';
            }
        }
    </script>
<?php endif; ?>

</body>
</html>