<?php
session_start();
include '../config/database.php';
include '../config/auth.php';

checkLogin();
allowRoles(['admin', 'guru_bk']);

if (!isset($_GET['id_siswa']) || empty($_GET['id_siswa'])) {
    echo "ID siswa tidak ditemukan.";
    exit;
}

$id_siswa = $_GET['id_siswa'];

$stmtSiswa = $pdo->prepare("
    SELECT siswa.*, kelas.tingkat, kelas.jurusan, kelas.nama_kelas
    FROM siswa
    JOIN kelas ON siswa.id_kelas = kelas.id_kelas
    WHERE id_siswa = ?
");
$stmtSiswa->execute([$id_siswa]);
$siswa = $stmtSiswa->fetch(PDO::FETCH_ASSOC);
$kelasLengkap = $siswa['tingkat'] . " " . $siswa['jurusan'] . " " . $siswa['nama_kelas'];

if (!$siswa) {
    echo "Data siswa tidak ditemukan.";
    exit;
}

$stmtPoin = $pdo->prepare("
    SELECT COALESCE(SUM(jenis_pelanggaran.poin), 0) AS total_poin
    FROM pelanggaran
    JOIN jenis_pelanggaran ON pelanggaran.id_jenis = jenis_pelanggaran.id_jenis
    WHERE pelanggaran.id_siswa = ?
");
$stmtPoin->execute([$id_siswa]);
$totalPoin = $stmtPoin->fetchColumn();

$nomorSurat = "548/SMK TI/BG/XII/" . date('Y');
$tanggalIndonesia = date('d') . ' ' . [
    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
][(int)date('n')] . ' ' . date('Y');

$alasanPindah = "Mengikuti kebijakan sekolah berdasarkan akumulasi poin pelanggaran yang telah mencapai {$totalPoin} poin dan untuk kelengkapan administrasi telah diselesaikan.";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Keterangan Pindah Sekolah</title>
    <style>
        body {
            font-family: "Times New Roman", serif;
            color: #000;
            margin: 0;
            padding: 20px;
            font-size: 14px;
            line-height: 1.4;
        }

        .page {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            background: #fff;
            padding: 10mm 14mm;
            box-sizing: border-box;
        }

        .no-print {
            text-align: center;
            margin-bottom: 15px;
        }

        .btn-print {
            /* display: inline-block; */
            display: inline-flex;
            align-items: center;
            justify-content: center;

            width: 80px;
            height: 38px;

            /* padding: 8px 16px; */
            
            padding: 0;

            background: #111827;
            color: #fff;
            text-decoration: none;

            border: none;
            border-radius: 5px;
            cursor: pointer;

            /* margin: 0 4px; */
            font-size: 13px;
            font-family: inherit;
        }

        .kop {
            /* text-align: center; */
            margin-bottom: 8px;
        }

        .kop-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .kop-logo {
            position: absolute;
            left: 0;
        }

        .kop-logo img {
            width: 120px;
            margin-top: 10px;
        }

        .kop-text {
            text-align: center;
            padding-left: 90px;
        }

        .kop-text h2, .kop-text h4, .kop-text p {
            margin: 2px 0;
        }

        .garis {
            border-top: 2px solid #000;
            border-bottom: 1px solid #000;
            height: 2px;
            margin: 8px 0 14px;
        }

        .judul {
            text-align: center;
            margin-bottom: 12px;
        }

        .judul h3,
        .judul h4 {
            margin: 0;
            text-transform: uppercase;
        }

        .isi {
            text-align: justify;
            line-height: 1.5;
            margin-bottom: 10px;
        }

        .identitas {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0 14px;
        }

        .identitas td {
            padding: 2px 0;
            vertical-align: top;
        }

        .ttd {
            width: 100%;
            margin-top: 28px;
        }

        .ttd td {
            width: 50%;
            vertical-align: top;
            text-align: center;
        }

        .space-ttd {
            height: 80px;
        }

        .underline-name {
            display: inline-block;
            min-width: 260px;
            border-bottom: 1px solid #000;
            text-align: center;
        }

        @media print {
            .no-print {
                display: none;
            }

            body {
                margin: 0;
                padding: 0;
            }

            .page {
                margin: 0;
                width: auto;
                min-height: auto;
                margin: 0;
                padding: 12mm 16mm;
            }

            @page {
                size: A4 portrait;
                margin: 8mm;
            }
        }
    </style>
</head>
<body>

<div class="no-print">
    <button onclick="window.print()" class="btn-print">Print</button>
    <a href="index.php" class="btn-print" style="background:#6b7280;">Kembali</a>
</div>

<div class="page">
    <div class="kop">
        <div class="kop-wrapper">
            <div class="kop-logo">
                <img src="../assets/logo.png" alt="Logo Sekolah">
            </div>
            <div class="kop-text">
                <h4>SEKOLAH MENENGAH KEJURUAN TEKNOLOGI INFORMASI BALI GLOBAL</h3>
                <h2>SMK TI BALI GLOBAL DENPASAR</h2>
                <p>Jl. Tukad Citarum No.44 Denpasar, Telp. (0361) 249434, FAX. (0361) 248269 </p>
                <p>website : www.smkti-baliglobal.sch.id | email : admin@smkti-baliglobal.sch.id</p>
            </div>
        </div>
    </div>

    <div class="garis"></div>

    <div class="judul">
        <h3>KETERANGAN PINDAH SEKOLAH</h3>
        <h4>No : <?= $nomorSurat; ?></h4>
    </div>

    <div class="isi">
        Yang bertanda tangan dibawah ini Kepala SMK TI BALI GLOBAL Denpasar, kecamatan Denpasar Selatan,
        Kota Denpasar, Provinsi Bali, menerangkan bahwa :
    </div>

    <table class="identitas">
        <tr>
            <td width="28%">Nama Siswa</td>
            <td width="3%">:</td>
            <td><?= htmlspecialchars($siswa['nama']); ?></td>
        </tr>
        <tr>
            <td>Kelas</td>
            <td>:</td>
            <td><?= htmlspecialchars($kelasLengkap); ?></td>
        </tr>
        <tr>
            <td>NIS</td>
            <td>:</td>
            <td><?= htmlspecialchars($siswa['nis']); ?></td>
        </tr>
        <tr>
            <td>Jenis Kelamin</td>
            <td>:</td>
            <td><?php $jk = $siswa['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan';?>
                <?= htmlspecialchars($jk); ?></td>
        </tr>
        <tr>
            <td>Alamat</td>
            <td>:</td>
            <td><?= htmlspecialchars($siswa['alamat'] ?? '-'); ?></td>
        </tr>
    </table>

    <div class="isi">
        Sesuai dengan surat permohonan pindah sekolah dari Orang tua / Wali siswa
    </div>

    <table class="identitas">
        <tr>
            <td width="28%">Nama</td>
            <td width="3%">:</td>
            <td><?= htmlspecialchars($siswa['nama_orang_tua'] ?? '-'); ?></td>
        </tr>
        <tr>
            <td>Alamat</td>
            <td>:</td>
            <td><?= htmlspecialchars($siswa['alamat'] ?? '-'); ?></td>
        </tr>
    </table>

    <div class="isi">
        Telah mengajukan surat permohonan pindah ke sekolah lain, dengan alasan
        <?= htmlspecialchars($alasanPindah); ?>
    </div>

    <div class="isi">
        Demikian surat pindah ini dibuat untuk dipergunakan sebagaimana mestinya.
    </div>

    <table class="ttd">
        <tr>
            <td></td>
            <td>
                Denpasar, <?= $tanggalIndonesia; ?><br>
                Kepala SMK TI Bali Global Denpasar
                <div class="space-ttd"></div>
                <span class="underline-name">Drs. I Gusti Made Murjana, M.Pd</span>
            </td>
        </tr>
    </table>
</div>

</body>
</html>