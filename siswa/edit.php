<?php
include '../config/database.php';
$id = $_GET['id'];


$stmt = $pdo->prepare("SELECT * FROM siswa WHERE id_siswa = ?");
$stmt->execute([$id]);
$siswa = $stmt->fetch();


if (isset($_POST['update'])) {
$stmt = $pdo->prepare("UPDATE siswa SET nama=?, nis=?, id_kelas=?, alamat=?, kontak_siswa=?, jenis_kelamin=?, nama_orang_tua=?, kontak_orang_tua=?, pekerjaan_orang_tua=? WHERE id_siswa=?");
$stmt->execute([
$_POST['nama'],
$_POST['nis'],
$_POST['id_kelas'],
$_POST['alamat'],
$_POST['kontak_siswa'],
$_POST['jenis_kelamin'],
$_POST['nama_orang_tua'],
$_POST['kontak_orang_tua'],
$_POST['pekerjaan_orang_tua'],

$id
]);
header("Location: index.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data Siswa</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <div class="container-add">
        <div class="form-box">
            <h2>Edit Siswa</h2>
                <form method="post">

                    <label>Nama</label>
                    <input name="nama" value="<?= $siswa['nama'] ?>" required>
                    <label>NIS</label>
                    <input name="nis" value="<?= $siswa['nis'] ?>" required>
                    <label>Kelas</label>
                    <select name="id_kelas" required>
                        <option value="">-- Pilih Kelas --</option>
                        <?php
                        $kelas = $pdo->query("SELECT * FROM kelas ORDER BY tingkat, jurusan, nama_kelas");
                        foreach ($kelas as $k):
                        ?>
                            <option value="<?= $k['id_kelas']; ?>" <?= $siswa['id_kelas'] == $k['id_kelas'] ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($k['tingkat'] . " " . $k['jurusan'] . " " . $k['nama_kelas']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <label>Alamat</label>
                    <input name="alamat" value="<?= $siswa['alamat'] ?>" required>
                    <label>Kontak Siswa</label>
                    <input name="kontak_siswa" value="<?= $siswa['kontak_siswa'] ?>" required>
                    <label>Jenis Kelamin</label>
                    <select name="jenis_kelamin" required>
                        <option value="L" <?= $siswa['jenis_kelamin'] == 'L' ? 'selected' : '' ?>>Laki-laki</option>
                        <option value="P" <?= $siswa['jenis_kelamin'] == 'P' ? 'selected' : '' ?>>Perempuan</option>
                    </select>
                    <label>Nama Orang Tua</label>
                    <input name="nama_orang_tua" value="<?= $siswa['nama_orang_tua'] ?>" required>
                    <label>Kontak Orang Tua</label>
                    <input name="kontak_orang_tua" value="<?= $siswa['kontak_orang_tua'] ?>" required>
                    <label>Pekerjaan Orang Tua</label>
                    <input name="pekerjaan_orang_tua" value="<?= $siswa['pekerjaan_orang_tua'] ?>" required>
                    
                    <button name="update" class="btn btn-edit">Update</button>
                    <a href="index.php" class="btn">Cancel</a>

                </form>
        </div>
    </div>


</form>
</body>
</html>


