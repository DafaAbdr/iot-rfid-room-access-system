<?php
session_start();
include __DIR__ . '/../../koneksi/koneksi.php';

$data = mysqli_query($conn, "SELECT * FROM pengguna");

// ambil data login
$id_admin = $_SESSION['id'];
$qAdmin = mysqli_query($conn, "SELECT * FROM admin WHERE id='$id_admin'");
$admin = mysqli_fetch_assoc($qAdmin);
$nama_admin = $admin['username'];
$jabatan_admin = $admin['jabatan'] ?? '-';

// format tanggal Indonesia
date_default_timezone_set('Asia/Jakarta');
$hari = ['Minggu','Senin','Selasa','Rabu','Kamis',"Jum'at",'Sabtu'];
$bulan = [
    1 => 'Januari','Februari','Maret','April','Mei','Juni',
    'Juli','Agustus','September','Oktober','November','Desember'
];

$tanggal = $hari[(int)date('w')] . ', ' . date('d') . ' ' . $bulan[(int)date('m')] . ' ' . date('Y');
?>

<!DOCTYPE html>
<html>
<head>
    <title>Cetak Data Pengguna</title>
    <style>
        body {
            font-family: Arial;
            padding: 30px;
            color: black;
        }

        .kop {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 20px;
            text-align: center;
            margin-bottom: 10px;
        }

        .kop-left img {
            width: 80px;
            height: auto;
        }

        .kop-text h2 {
            margin: 0;
            font-size: 28px;
            font-weight: bold;
        }

        .kop-text p {
            margin: 2px 0;
            font-size: 14px;
        }

        hr {
            border: none;
            border-top: 2px solid black;
            border-bottom: 1px solid black;
            height: 3px;
            margin: 10px 0 20px;
        }

        h3 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid black;
            padding: 10px;
            text-align: center;
            color: black;
        }

        thead th {
            background: #062f36;
            color: white;
        }

        td img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
        }

        .ttd {
            display: none;
        }

        .btn-area {
            text-align: center;
            margin-top: 20px;
        }

        button {
            padding: 10px 20px;
            margin: 5px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        .cetak {
            background: #22c55e;
            color: white;
        }

        .batal {
            background: red;
            color: white;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            thead th {
                color: white !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .ttd {
                display: block;
                width: 250px;
                position: fixed;
                bottom: 30px;
                right: 30px;
                text-align: center;
                font-size: 14px;
            }

            .ttd p {
                margin: 3px 0;
            }
        }
    </style>
</head>
<body>

    <div class="kop">
        <div class="kop-left">
            <img src="../../source/LogoMasjid.png">
        </div>
        <div class="kop-text">
            <h2>Masjid Ibnu Sabil</h2>
            <p>Sistem Monitoring RFID</p>
            <p>RT.13/RW.7, Pisangan Baru, Matraman, East Jakarta City, Jakarta 13110</p>
        </div>
    </div>

    <hr>

    <h3>Data Pengguna</h3>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama</th>
                <th>No RFID</th>
                <th>Role</th>
                <th>Status</th>
                <th>Foto</th>
            </tr>
        </thead>
        <tbody>
        <?php while($row = mysqli_fetch_assoc($data)) { 
            $folder = __DIR__ . '/../../source/profile/';
            $default = '../../source/profile.jpg';

            if (!empty($row['photo']) && file_exists($folder . $row['photo'])) {
                $foto = '../../source/profile/' . $row['photo'];
            } else {
                $foto = $default;
            }
        ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= $row['nama_pengguna'] ?></td>
                <td><?= $row['no_rfid'] ?></td>
                <td><?= $row['role_level'] ?></td>
                <td><?= $row['status'] ?></td>
                <td><img src="<?= $foto ?>"></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>

    <div class="ttd">
        <p>Jakarta, <?= $tanggal ?></p>
        <p><?= htmlspecialchars($jabatan_admin) ?></p>

        <br><br><br>

        <p><b><?= htmlspecialchars($nama_admin) ?></b></p>
    </div>

    <div class="btn-area no-print">
        <button class="cetak" onclick="window.print()">Cetak / Save</button>
        <button class="batal" onclick="window.location.href='/loginPage/dashboard.php?page=user'">
            Batal
        </button>
    </div>

</body>
</html>