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

if (!isset($_GET['id']) || $_GET['id'] == '') {
    header("Location: index.php");
    exit;
}

$id = $_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM kelas WHERE id_kelas = ?");
$stmt->execute([$id]);
$kelas = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$kelas) {
    header("Location: index.php");
    exit;
}

if (isset($_POST['update'])) {
    $tingkat = trim($_POST['tingkat']);
    $jurusan = trim($_POST['jurusan']);
    $nama_kelas = trim($_POST['nama_kelas']);

    if ($tingkat != '' && $jurusan != '' && $nama_kelas != '') {
        // cek duplikat, kecuali data yang sedang diedit
        $cek = $pdo->prepare("
            SELECT COUNT(*) 
            FROM kelas 
            WHERE tingkat = ? AND jurusan = ? AND nama_kelas = ? AND id_kelas != ?
        ");
        $cek->execute([$tingkat, $jurusan, $nama_kelas, $id]);
        $jumlah = $cek->fetchColumn();

        if ($jumlah > 0) {
            $popup = true;
            $popup_type = 'error';
            $popup_message = 'Data kelas sudah digunakan!';
        } else {
            $stmt = $pdo->prepare("
                UPDATE kelas 
                SET tingkat = ?, jurusan = ?, nama_kelas = ?
                WHERE id_kelas = ?
            ");
            $stmt->execute([
                $tingkat,
                $jurusan,
                $nama_kelas,
                $id
            ]);

            // refresh data terbaru
            $stmt = $pdo->prepare("SELECT * FROM kelas WHERE id_kelas = ?");
            $stmt->execute([$id]);
            $kelas = $stmt->fetch(PDO::FETCH_ASSOC);

            $popup = true;
            $popup_type = 'success';
            $popup_message = 'Data kelas berhasil diperbarui!';
            $redirect_url = 'index.php';
        }
    } else {
        $popup = true;
        $popup_type = 'error';
        $popup_message = 'Semua data wajib diisi!';
    }

    // kalau error, tampilkan data terakhir dari form
    if ($popup_type == 'error') {
        $kelas['tingkat'] = $tingkat;
        $kelas['jurusan'] = $jurusan;
        $kelas['nama_kelas'] = $nama_kelas;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Kelas</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<div class="container-add">
    <div class="form-box">
        <h2>Edit Kelas</h2>

        <form method="post">
            <label>Tingkat</label>
            <select name="tingkat" required>
                <option value="">-- Pilih Tingkat --</option>
                <option value="X" <?= $kelas['tingkat'] == 'X' ? 'selected' : ''; ?>>X</option>
                <option value="XI" <?= $kelas['tingkat'] == 'XI' ? 'selected' : ''; ?>>XI</option>
                <option value="XII" <?= $kelas['tingkat'] == 'XII' ? 'selected' : ''; ?>>XII</option>
            </select>

            <label>Jurusan</label>
            <input 
                type="text" 
                name="jurusan" 
                value="<?= htmlspecialchars($kelas['jurusan']); ?>" 
                required
            >

            <label>Nama Kelas</label>
            <input 
                type="text" 
                name="nama_kelas" 
                value="<?= htmlspecialchars($kelas['nama_kelas']); ?>" 
                required
            >

            <button type="submit" name="update" class="btn btn-edit">Update</button>
            <a href="index.php" class="btn">Cancel</a>
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