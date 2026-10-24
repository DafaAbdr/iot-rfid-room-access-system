<?php
include __DIR__ . '/../../koneksi/koneksi.php';

$id = $_GET['id'];
$data = mysqli_query($conn, "SELECT * FROM pengguna WHERE id='$id'");
$row = mysqli_fetch_assoc($data);

// foto
$folder = __DIR__ . '/../../source/profile/';
$default = '../../source/profile.jpg';

if (!empty($row['photo']) && file_exists($folder . $row['photo'])) {
    $foto = '../../source/profile/' . $row['photo'];
} else {
    $foto = $default;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Cetak Kartu</title>
    <style>
        @page {
            size: 280px 420px; /* ukuran kartu */
            margin: 0;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 280px;
            height: 420px;
            background: #f1f5f9;
            margin: 0;
            padding: 0;
        }

        .card {
            width: 280px;
            height: 420px;
            background: #cbd5e1;
            border-radius: 16px;
            overflow: hidden;
            text-align: center;
            font-family: Arial, sans-serif;
            border: 3px solid #1e293b;
            box-shadow: 0 6px 12px rgba(0,0,0,0.25);
            position: relative;
            box-sizing: border-box;
        }

        /* border dalam */
        .card::before {
            content: "";
            position: absolute;
            top: 8px;
            left: 8px;
            right: 8px;
            bottom: 8px;
            border: 2px dashed rgba(255,255,255,0.7);
            border-radius: 12px;
            pointer-events: none;
        }

        .header {
            background: white;
            padding: 10px;
            border-bottom: 4px solid #eab308;
            position: relative;
            z-index: 2;
        }

        .header img {
            width: 40px;
            vertical-align: middle;
        }

        .header h4 {
            margin: 5px 0 0;
            font-size: 13px;
            color: #1e293b;
        }

        .foto-area {
            background: #eab308;
            padding: 15px;
            position: relative;
            z-index: 2;
        }

        .foto-area img {
            width: 140px;
            height: 180px;
            object-fit: cover;
            border-radius: 6px;
            border: 2px solid white;
        }

        .nama {
            font-weight: bold;
            margin-top: 15px;
            font-size: 16px;
            color: #0f172a;
            position: relative;
            z-index: 2;
        }

        .id {
            font-size: 13px;
            color: #334155;
            margin-top: 8px;
            position: relative;
            z-index: 2;
        }

        @media print {
            body {
                background: white;
            }

            .card {
                box-shadow: none;
            }
        }
    </style>
</head>

<body>
    <div class="card">
        <div class="header">
            <img src="../../source/LogoMasjid.png">
            <h4>Monitoring RFID System</h4>
        </div>

        <div class="foto-area">
            <img src="<?= $foto ?>">
        </div>

        <div class="nama"><?= strtoupper($row['nama_pengguna']) ?></div>
        <div class="id">ID: <?= $row['no_rfid'] ?></div>
    </div>
</body>

<script>
    window.onload = function() {
        window.print();
    }
</script>
</html>