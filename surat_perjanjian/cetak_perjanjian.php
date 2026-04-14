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

$stmtMasalah = $pdo->prepare("
    SELECT 
        jenis_pelanggaran.nama_jenis,
        pelanggaran.tanggal,
        jenis_pelanggaran.poin
    FROM pelanggaran
    JOIN jenis_pelanggaran ON pelanggaran.id_jenis = jenis_pelanggaran.id_jenis
    WHERE pelanggaran.id_siswa = ?
    ORDER BY pelanggaran.tanggal DESC, pelanggaran.id_pelanggaran DESC
    LIMIT 3
");
$stmtMasalah->execute([$id_siswa]);
$masalahList = $stmtMasalah->fetchAll(PDO::FETCH_ASSOC);

$masalahText = [];
foreach ($masalahList as $m) {
    $masalahText[] = $m['nama_jenis'] . " (" . $m['poin'] . " poin)";
}
$masalahGabung = implode(', ', $masalahText);

$tanggalSurat = date('d-m-Y');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Surat Perjanjian Siswa</title>
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
            font-weight: bold;
            font-size: 17px;
            margin-bottom: 12px;
            text-transform: uppercase;
        }

        .section {
            margin-bottom: 12px;
        }

        .identitas {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .identitas td {
            padding: 1px 0;
            vertical-align: top;
            font-size: 14px;
        }

        .ttd {
            width: 100%;
            margin-top: 18px;
        }

        .ttd td {
            width: 50%;
            vertical-align: top;
            text-align: center;
            padding-top: 8px;
        }

        .space-ttd {
            height: 50px;
        }

        .underline-name {
            display: inline-block;
            min-width: 220px;
            border-bottom: 1px solid #000;
            text-align: center;
        }

        .center {
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
                <p>Jl. Tukad Citarum No.44 Denpasar, Telp. (0361) 249434, FAX. (0361) 248269</p>
                <p>website : www.smkti-baliglobal.sch.id | email : admin@smkti-baliglobal.sch.id</p>
            </div>
        </div>
    </div>

    <div class="garis"></div>

    <div class="judul">SURAT PERNYATAAN SISWA</div>

    <div class="section">
        Yang bertandatangan di bawah ini :
    </div>

    <table class="identitas">
        <tr>
            <td width="28%">Nama</td>
            <td width="3%">:</td>
            <td><?= htmlspecialchars($siswa['nama']); ?></td>
        </tr>
        <tr>
            <td>NIS</td>
            <td>:</td>
            <td><?= htmlspecialchars($siswa['nis']); ?></td>
        </tr>
        <tr>
            <td>Kelas</td>
            <td>:</td>
            <td><?= htmlspecialchars($kelasLengkap); ?></td>
        </tr>
        <!-- <tr>
            <td>Program Keahlian</td>
            <td>:</td>
            <td>-</td>
        </tr> -->
        <tr>
            <td>Masalah</td>
            <td>:</td>
            <td><?= htmlspecialchars($masalahGabung); ?>. Total poin: <?= (int)$totalPoin; ?></td>
        </tr>
    </table>

    <table class="identitas" style="margin-top: 14px;">
        <tr>
            <td width="28%">Nama Orang Tua</td>
            <td width="3%">:</td>
            <td><?= htmlspecialchars($siswa['nama_orang_tua'] ?? '-'); ?></td>
        </tr>
        <tr>
            <td>Pekerjaan</td>
            <td>:</td>
            <td><?= htmlspecialchars($siswa['pekerjaan_orang_tua'] ?? '-'); ?></td>
        </tr>
        <tr>
            <td>Alamat Rumah</td>
            <td>:</td>
            <td><?= htmlspecialchars($siswa['alamat'] ?? '-'); ?></td>
        </tr>
        <tr>
            <td>No. Hp./Telp.</td>
            <td>:</td>
            <td><?= htmlspecialchars($siswa['kontak_orang_tua'] ?? '-'); ?></td>
        </tr>
    </table>

    <div class="section" style="text-align: justify;">
        Menyatakan dan berjanji akan bersungguh-sungguh berubah dan bersedia menaati aturan dan
        tata tertib sekolah. Apabila selama masa pembinaan tidak mengalami perubahan, maka siswa
        yang bersangkutan dikembalikan kepada orang tua/wali.
        <br>
        Demikian surat pernyataan ini saya buat dengan sesungguhnya tanpa ada tekanan dari siapapun.
    </div>

    <table class="ttd">
        <tr>
            <td style="text-align:left;">
                Mengetahui,<br>
                Orang Tua/Wali siswa
                <div class="space-ttd"></div>
                <span class="underline-name"></span>
            </td>
            <td>
                Denpasar, <?= $tanggalSurat; ?><br>
                Siswa yang bersangkutan
                <div class="space-ttd"></div>
                <span class="underline-name"></span>
            </td>
        </tr>

        <tr>
            <td style="padding-top: 24px; text-align:left;">
                Guru Bimbingan Konseling
                <div class="space-ttd"></div>
                <span class="underline-name">Ni Putu Chintya Pradnya Suari, S.Pd</span>
            </td>
            <td style="padding-top: 24px;">
                Guru Wali Kelas
                <div class="space-ttd"></div>
                <span class="underline-name"></span>
            </td>
        </tr>

        <tr>
            <td colspan="2" class="center" style="padding-top: 24px;">
                Mengetahui<br>
                Wakasek Kesiswaan
                <div class="space-ttd"></div>
                <span class="underline-name">Bagus Putu Eka Wijaya, S.Kom</span>
            </td>
        </tr>
    </table>
</div>

</body>
</html>