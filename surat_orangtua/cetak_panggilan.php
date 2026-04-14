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

/* Data siswa */
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

/* Total poin */
$stmtPoin = $pdo->prepare("
    SELECT COALESCE(SUM(jenis_pelanggaran.poin), 0) AS total_poin
    FROM pelanggaran
    JOIN jenis_pelanggaran ON pelanggaran.id_jenis = jenis_pelanggaran.id_jenis
    WHERE pelanggaran.id_siswa = ?
");
$stmtPoin->execute([$id_siswa]);
$totalPoin = $stmtPoin->fetchColumn();

/* Pelanggaran terakhir */
$stmtLast = $pdo->prepare("
    SELECT 
        pelanggaran.tanggal,
        jenis_pelanggaran.nama_jenis,
        jenis_pelanggaran.poin
    FROM pelanggaran
    JOIN jenis_pelanggaran ON pelanggaran.id_jenis = jenis_pelanggaran.id_jenis
    WHERE pelanggaran.id_siswa = ?
    ORDER BY pelanggaran.tanggal DESC, pelanggaran.id_pelanggaran DESC
    LIMIT 5
");
$stmtLast->execute([$id_siswa]);
$riwayat = $stmtLast->fetchAll(PDO::FETCH_ASSOC);

$pelanggaranTerakhir = [];

foreach ($riwayat as $r) {
    $pelanggaranTerakhir[] = $r['nama_jenis'] . " (" . $r['poin'] . " poin)";
}

$ringkasanPelanggaran = !empty($pelanggaranTerakhir)
    ? implode(', ', $pelanggaranTerakhir)
    : 'Belum ada data pelanggaran.';

$tanggalSurat = date('d-m-Y');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Surat Panggilan Orang Tua</title>
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

        .judul h3 {
            margin: 0;
            text-decoration: underline;
        }

        .meta {
            width: 100%;
            margin-bottom: 10px;
        }

        .meta td {
            padding: 2px 0;
            vertical-align: top;
        }

        .isi {
            text-align: justify;
            line-height: 1.5;
            margin-bottom: 10px;
        }

        .data-siswa {
            width: 100%;
            margin-bottom: 10px;
        }

        .data-siswa td {
            padding: 2px 0;
            vertical-align: top;
        }

        /* .riwayat {
            width: 100%;
            border-collapse: collapse;
            margin: 12px 0 18px;
        }

        .riwayat th,
        .riwayat td {
            border: 1px solid #000;
            padding: 6px;
            font-size: 13px;
        } */

        .ttd {
            width: 100%;
            margin-top: 14px;
            page-break-inside: avoid;
            break-inside: avoid;
        }

        .ttd tr,
        .ttd td {
            page-break-inside: avoid;
            break-inside: avoid;
        }

        .ttd td {
            width: 50%;
            vertical-align: top;
            text-align: center;
        }

        .space-ttd {
            height: 45px;
        }

        /* .underline-name {
            display: inline-block;
            min-width: 200px;
            border-bottom: 1px solid #000;
            text-align: center;
        } */

        @media print {
            .no-print {
                display: none;
            }

            body {
                padding: 0;
                margin: 0;
            }

            .page {
                margin: 0;
                width: auto;
                min-height: auto;
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
        <h3>SURAT PANGGILAN ORANG TUA</h3>
        <p>Nomor: .............. / BK / <?= date('Y'); ?></p>
    </div>

    <table class="meta">
        <tr>
            <td width="70%"> </td>
            <td width="30%">Denpasar, <?= $tanggalSurat; ?></td>
        </tr>
    </table>

    <div class="isi">
        Kepada Yth.<br>
        Bapak / Ibu Orang Tua / Wali Siswa<br>
        di Tempat
        <br><br>
        Dengan hormat,
        <br><br>
        Sehubungan dengan hasil pemantauan kedisiplinan siswa, kami mengundang
        Bapak / Ibu Orang Tua / Wali untuk hadir ke sekolah guna membicarakan
        perkembangan perilaku dan kedisiplinan siswa berikut:
    </div>

    <table class="data-siswa">
        <tr>
            <td width="25%">Nama Siswa</td>
            <td width="2%">:</td>
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
        <tr>
            <td>Total Poin</td>
            <td>:</td>
            <td><?= (int)$totalPoin; ?></td>
        </tr>
    </table>

    <div class="isi">
        Berdasarkan catatan sekolah, siswa yang bersangkutan terakhir melakukan pelanggaran berupa
        <?= htmlspecialchars($ringkasanPelanggaran); ?>.
    </div>

    <div class="isi">
        Mengingat total poin pelanggaran siswa telah mencapai batas panggilan orang tua,
        maka kami mohon kehadiran Bapak / Ibu ke sekolah pada waktu yang akan ditentukan
        oleh pihak sekolah untuk bersama-sama melakukan pembinaan.
        <br><br>
        Demikian surat ini kami sampaikan. Atas perhatian dan kerja sama Bapak / Ibu,
        kami ucapkan terima kasih.
    </div>

    <table class="ttd">
        <tr>
            <td></td>
            <td>
                Hormat kami,<br>
                Guru BK
                <div class="space-ttd"></div>
                (____________________)
            </td>
        </tr>
    </table>
</div>

</body>
</html>