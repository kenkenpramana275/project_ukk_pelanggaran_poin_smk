<?php
session_start();
include '../config/database.php';
include '../config/auth.php';

checkLogin();
allowRoles(['admin', 'guru_bk']);

$currentFolder = basename(dirname($_SERVER['PHP_SELF']));
$currentPage   = basename($_SERVER['PHP_SELF']);
$role = $_SESSION['role'];

/* Ambil semua pelanggaran semua siswa */
$stmt = $pdo->query("
    SELECT 
        pelanggaran.id_pelanggaran,
        pelanggaran.tanggal,
        pelanggaran.keterangan,
        siswa.nama,
        siswa.nis,
        kelas.tingkat,
        kelas.jurusan,
        kelas.nama_kelas,
        jenis_pelanggaran.nama_jenis,
        jenis_pelanggaran.poin,
        jenis_pelanggaran.kategori_kode
    FROM pelanggaran
    JOIN siswa ON pelanggaran.id_siswa = siswa.id_siswa
    JOIN kelas ON siswa.id_kelas = kelas.id_kelas
    JOIN jenis_pelanggaran ON pelanggaran.id_jenis = jenis_pelanggaran.id_jenis
    ORDER BY siswa.nama ASC, pelanggaran.tanggal ASC, pelanggaran.id_pelanggaran ASC
");
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Pelanggaran Siswa</title>
    <style>
        body {
            font-family: "Times New Roman", serif;
            color: #000;
            margin: 0;
            padding: 20px;
            font-size: 12px;
            line-height: 1.4;
        }

        .no-print {
            margin-bottom: 15px;
            text-align: center;
        }

        .btn-print {
            display: inline-block;
            padding: 8px 16px;
            background: #111827;
            color: #fff;
            text-decoration: none;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin: 0 4px;
            font-size: 13px;
        }

        .page {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            background: #fff;
            padding: 12mm 16mm;
            box-sizing: border-box;
        }

        .kop {
            /* text-align: center; */
            margin-bottom: 6px;
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
            width: 110px;
            /* margin-top: 10px; */
        }

        .kop-text {
            text-align: center;
            padding-left: 85px;
        }

        .kop-text h2, .kop-text h4, .kop-text p {
            margin: 1px 0;
        }

        .garis {
            border-top: 2px solid #000;
            border-bottom: 1px solid #000;
            height: 2px;
            margin: 6px 0 10px;
        }

        .judul {
            text-align: center;
            font-weight: bold;
            font-size: 15px;
            margin: 8px 0 12px;
            text-transform: uppercase;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .rekap th,
        .rekap td {
            border: 1px solid #000;
            padding: 3px 2px;
            font-size: 10px;
        }

        .rekap th {
            text-align: center;
            vertical-align: middle;
            font-weight: bold;
            /* font-size: 10px; */
        }

        .rekap td {
            vertical-align: middle;
        }

        .center {
            text-align: center;
        }

        .left {
            text-align: left;
        }

        .footer-keterangan {
            margin-top: 8px;
            /* width: 45%; */
            font-size: 11px;
        }

        .footer-keterangan td {
            padding: 1px 4px 1px 0;
            font-size: 11px;
            vertical-align: top;
            border: none;
        }

        .kode {
            width: 30px;
        }

        .rekap th:nth-child(n+6):nth-child(-n+12),
        .rekap td:nth-child(n+6):nth-child(-n+12) {
            width: 18px;
        }

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
                box-shadow: none;
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

    <div class="judul">Rekap Pelanggaran Siswa</div>

    <table class="rekap">
        <thead>
            <tr>
                <th rowspan="2" style="width:5%;">No</th>
                <th rowspan="2" style="width:8%;">Tgl</th>
                <th rowspan="2" style="width:16%;">Nama</th>
                <th rowspan="2" style="width:8%;">NIS</th>
                <th rowspan="2" style="width:10%;">Kelas</th>
                <th colspan="7" style="width:20%;">Jenis Pelanggaran</th>
                <th rowspan="2" style="width:6%;">Poin</th>
                <th rowspan="2" style="width:17%;">Keterangan</th>
            </tr>
            <tr>
                <th>SS</th>
                <th>KS</th>
                <th>PBM</th>
                <th>PNN</th>
                <th>PB</th>
                <th>KB</th>
                <th>UB</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($data)): ?>
                <?php $no = 1; ?>
                <?php foreach ($data as $row): ?>
                    <?php $kelasLengkap = $row['tingkat'] . ' ' . $row['jurusan'] . ' ' . $row['nama_kelas']; ?>
                    <tr>
                        <td class="center"><?= $no++; ?></td>
                        <td class="center"><?= htmlspecialchars(date('d/m/y', strtotime($row['tanggal']))); ?></td>
                        <td class="left"><?= htmlspecialchars($row['nama']); ?></td>
                        <td class="center"><?= htmlspecialchars($row['nis']); ?></td>
                        <td class="center"><?= htmlspecialchars($kelasLengkap); ?></td>

                        <td class="center"><?= ($row['kategori_kode'] == 'SS') ? '✓' : ''; ?></td>
                        <td class="center"><?= ($row['kategori_kode'] == 'KS') ? '✓' : ''; ?></td>
                        <td class="center"><?= ($row['kategori_kode'] == 'PBM') ? '✓' : ''; ?></td>
                        <td class="center"><?= ($row['kategori_kode'] == 'PNN') ? '✓' : ''; ?></td>
                        <td class="center"><?= ($row['kategori_kode'] == 'PB') ? '✓' : ''; ?></td>
                        <td class="center"><?= ($row['kategori_kode'] == 'KB') ? '✓' : ''; ?></td>
                        <td class="center"><?= ($row['kategori_kode'] == 'UB') ? '✓' : ''; ?></td>

                        <td class="center"><?= (int)$row['poin']; ?></td>
                        <td class="left"><?= htmlspecialchars($row['nama_jenis']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="14" class="center">Belum ada data pelanggaran.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <table class="footer-keterangan">
        <tr><td class="kode">SS</td><td>: Seragam Sekolah</td></tr>
        <tr><td class="kode">KS</td><td>: Kehadiran di Sekolah</td></tr>
        <tr><td class="kode">PBM</td><td>: Proses Belajar Mengajar</td></tr>
        <tr><td class="kode">PNN</td><td>: Pelanggaran Norma Norma</td></tr>
        <tr><td class="kode">PB</td><td>: Pelanggaran Berat</td></tr>
        <tr><td class="kode">KB</td><td>: Kesopanan Berkendaraan</td></tr>
        <tr><td class="kode">UB</td><td>: Upacara Bendera</td></tr>
    </table>
</div>

</body>
</html>