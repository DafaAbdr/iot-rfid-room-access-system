<?php
    session_start();

    include __DIR__ . '/../../koneksi/koneksi.php';

    $nama_admin = $_SESSION['username'] ?? 'Admin';
    $jabatan_admin = $_SESSION['jabatan'] ?? '-';

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

    $data = mysqli_query($conn, "
        SELECT l.*, p.nama_pengguna, r.nama_ruangan
        FROM laporan l
        JOIN pengguna p ON l.id_pengguna = p.id
        JOIN ruangan r ON l.id_ruangan = r.id
        ORDER BY l.waktu DESC
    ");
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Cetak Laporan</title>
        <style>
            body {
                font-family: Arial;
                padding: 30px;
            }

            .kop {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 15px;
            }

            .kop-left img {
                width: 70px;
            }

            .kop-text h2 {
                margin: 0;
            }

            .kop-text p {
                margin: 2px 0;
                font-size: 14px;
            }

            hr {
                border: 1px solid black;
                margin: 10px 0 20px;
            }

            h3 {
                text-align: center;
                margin-bottom: 20px;
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

            .footer {
                width: 250px;
                margin-left: auto;
                margin-top: 60px;
                text-align: center;
                color: black;
                page-break-inside: avoid;
                break-inside: avoid;
            }

            .footer p {
                margin: 3px 0;
            }

            @media print {
                thead th {
                    -webkit-print-color-adjust: exact;
                    print-color-adjust: exact;
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
        <h3>Laporan Keseluruhan Akses RFID</h3>
        <table>
            <thead>
            <tr>
                <th>No</th>
                <th>Pengguna</th>
                <th>Ruangan</th>
                <th>Waktu</th>
                <th>Fuzzy</th>
                <th>Status</th>
            </tr>
            </thead>
            <tbody>
                <?php $no=1; while($row = mysqli_fetch_assoc($data)) { ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= $row['nama_pengguna'] ?></td>
                    <td><?= $row['nama_ruangan'] ?></td>
                    <td><?= $row['waktu'] ?></td>
                    <td><?= $row['nilai_fuzzy'] ?></td>
                    <td><?= $row['keputusan_akses'] == 1 ? 'Diterima' : 'Ditolak' ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
        <div class="footer">
            <p>Jakarta, <?= tanggalIndo(date("Y-m-d")) ?></p>
            <p><?= htmlspecialchars($jabatan_admin) ?></p>
            <br><br><br>
            <p>( <?= htmlspecialchars($nama_admin) ?> )</p>
        </div>
    </body>
    <script>
        window.onload = function(){
            window.print();
        }
    </script>
</html>