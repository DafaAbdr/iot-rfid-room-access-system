<?php
    session_start();
    include __DIR__ . "/../../koneksi/koneksi.php";

    $nama_admin = $_SESSION['username'] ?? 'Admin';
    $jabatan_admin = $_SESSION['jabatan'] ?? '-';

    // Filter
    $ruangan   = $_GET['ruangan'] ?? '';
    $pengguna  = $_GET['pengguna'] ?? '';
    $status    = $_GET['status'] ?? '';
    $tanggal   = $_GET['tanggal'] ?? '';

    function tanggalIndo($tanggal){
        $hari  = ['Minggu','Senin','Selasa','Rabu','Kamis',"Jum'at",'Sabtu'];
        $bulan = [
            1 => 'Januari','Februari','Maret','April','Mei','Juni',
            'Juli','Agustus','September','Oktober','November','Desember'
        ];
        $split = explode('-', $tanggal);
        $ts = strtotime($tanggal);
        return $hari[(int)date('w', $ts)] . ' ' . $split[2] . ' ' . $bulan[(int)$split[1]] . ' ' . $split[0];
    }

    $query = "
    SELECT l.*, p.nama_pengguna, r.nama_ruangan
    FROM laporan l
    JOIN pengguna p ON l.id_pengguna = p.id
    JOIN ruangan r ON l.id_ruangan = r.id
    WHERE 1=1
    ";

    if($ruangan != ''){
        $query .= " AND l.id_ruangan = '$ruangan'";
    }
    if($pengguna != ''){
        $query .= " AND l.id_pengguna = '$pengguna'";
    }
    if($status == 'Diizinkan'){
        $query .= " AND l.keputusan_akses = 1";
    } elseif($status == 'Ditolak'){
        $query .= " AND l.keputusan_akses = 0";
    }
    if($tanggal != ''){
        $query .= " AND DATE(l.waktu) = '$tanggal'";
    }

    $query .= " ORDER BY l.waktu DESC";

    $data = mysqli_query($conn, $query);
?>

<html>
    <head>
        <title>Cetak Laporan Akses</title>
        <style>
            body {
                font-family: Arial;
                padding: 30px;
                color: #000;
            }

            .header {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 20px;
            }

            .logo {
                width: 60px;
            }

            .title-header {
                text-align: left;
            }

            .title-header h2 {
                margin: 0;
            }

            .title-header p {
                margin: 2px;
                font-size: 14px;
            }

            .line {
                border-top: 3px solid black;
                margin: 15px 0;
            }

            h3 {
                text-align: center;
                margin-top: 10px;
            }

            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 20px;
            }

            th {
                background: #020617;
                color: white;
            }

            @media print {
                th {
                    background: #020617 !important;
                    color: white !important;
                    -webkit-print-color-adjust: exact;
                    print-color-adjust: exact;
                }
            }

            th, td {
                border: 1px solid black;
                padding: 8px;
                text-align: center;
            }

            tr:nth-child(even){
                background: #f2f2f2;
            }

            .status-yes {
                color: green;
                font-weight: bold;
            }

            .status-no {
                color: red;
                font-weight: bold;
            }

            .footer {
                width: 250px;
                margin-left: auto;
                margin-top: 60px;
                text-align: center;
                page-break-inside: avoid;
                break-inside: avoid;
            }

            .footer p {
                margin: 3px 0;
            }
        </style>
    </head>
    <body>
        <div class="header">
            <img src="../../source/LogoMasjid.png" class="logo">
            <div class="title-header">
                <h2>Masjid Ibnu Sabil</h2>
                <p>Sistem Monitoring RFID</p>
                <p>RT.13/RW.7, Pisangan Baru, Matraman, East Jakarta City, Jakarta 13110</p>
            </div>
        </div>
        <div class="line"></div>
        <h3>Laporan Akses RFID</h3>
        <table>
            <tr>
                <th>No</th>
                <th>Pengguna</th>
                <th>Ruangan</th>
                <th>Waktu</th>
                <th>Fuzzy</th>
                <th>Status</th>
            </tr>
            <?php $no=1; while($row = mysqli_fetch_assoc($data)){ ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= $row['nama_pengguna'] ?></td>
                <td><?= $row['nama_ruangan'] ?></td>
                <td><?= $row['waktu'] ?></td>
                <td><?= $row['nilai_fuzzy'] ?></td>
                <td>
                    <?php if($row['keputusan_akses'] == 1){ ?>
                        <span class="status-yes">✔ Diizinkan</span>
                    <?php } else { ?>
                        <span class="status-no">✖ Ditolak</span>
                    <?php } ?>
                </td>
            </tr>
            <?php } ?>
        </table>
        <div class="footer">
            <p>Jakarta, <?= tanggalIndo(date("Y-m-d")) ?></p>
            <p><?= htmlspecialchars($jabatan_admin) ?></p>
            <br><br><br>
            <p><?= htmlspecialchars($nama_admin) ?></p>
        </div>
    </body>
</html>