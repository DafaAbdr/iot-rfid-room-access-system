<?php
    session_start();
    include __DIR__ . "/../../koneksi/koneksi.php";

    // ambil data admin login
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

    $tanggal = $hari[(int)date('w')] . ' ' . date('d') . ' ' . $bulan[(int)date('m')] . ' ' . date('Y');

    $id = $_GET['id'] ?? '';

    if($id == ''){
        echo "<h2>ID tidak ditemukan</h2>";
        exit;
    }

    $data = mysqli_query($conn, "
    SELECT l.*, p.nama_pengguna, p.role_level, r.nama_ruangan, r.sensitivitas
    FROM laporan l
    JOIN pengguna p ON l.id_pengguna = p.id
    JOIN ruangan r ON l.id_ruangan = r.id
    WHERE l.id='$id'
    ");

    $d = mysqli_fetch_assoc($data);

    if(!$d){
        echo "<h2>Data tidak ditemukan</h2>";
        exit;
    }

    // ================= FUZZY =================
    function rendah($x){
        if($x <= 0) return 1;
        if($x >= 5) return 0;
        return (5 - $x) / 5;
    }

    function tinggi($x){
        if($x <= 4) return 0;
        if($x >= 10) return 1;
        return ($x - 4) / 6;
    }

    $role = $d['role_level'];
    $sens = $d['sensitivitas'];

    $role_rendah = rendah($role);
    $role_tinggi = tinggi($role);
    $sens_rendah = rendah($sens);
    $sens_tinggi = tinggi($sens);

    // ================= RULE BASE (baru) =================
    // R1 : Role Rendah AND Sensitivitas Rendah  -> Diizinkan
    // R2 : Role Rendah AND Sensitivitas Tinggi  -> Ditolak
    // R3 : Role Tinggi AND Sensitivitas Rendah  -> Diizinkan
    // R4 : Role Tinggi AND Sensitivitas Tinggi  -> Diizinkan
    $r1 = min($role_rendah, $sens_rendah); // Diizinkan
    $r2 = min($role_rendah, $sens_tinggi); // Ditolak
    $r3 = min($role_tinggi, $sens_rendah); // Diizinkan
    $r4 = min($role_tinggi, $sens_tinggi); // Diizinkan

    // AGREGASI
    $izin = max($r1, $r3, $r4);
    $tolak = $r2;

    // ================= DEFUZZIFIKASI =================

    // ===== IZIN (5–10)
    $z1 = 0;
    $L1 = 0;
    $L2 = 0;
    $C1 = 0;
    $C2 = 0;
    if($izin > 0){

        // titik potong
        $z1 = 5 + (5 * $izin);

        // luas area
        $L1 = (($z1 - 5) * $izin) / 2;     // segitiga
        $L2 = ((10 - $z1) * $izin);        // persegi panjang

        // centroid
        $C1 = (5 + $z1 + $z1) / 3;         // segitiga
        $C2 = ($z1 + 10) / 2;              // persegi panjang (FIX PENTING)

        // hasil akhir
        $nilai_izin = (($L1 * $C1) + ($L2 * $C2)) / ($L1 + $L2);

    } else {
        $nilai_izin = 0;
    }
    $z2 = 0;
    // ===== TOLAK (0–5)
    if($tolak > 0){

        $z2 = 5 - (5 * $tolak);

        $L3 = ($z2 * $tolak);
        $L4 = ((5 - $z2) * $tolak) / 2;

        $C3 = ($z2 + 0) / 2;
        $C4 = ($z2 + 5 + 5) / 3;

        $nilai_tolak = (($L3 * $C3) + ($L4 * $C4)) / ($L3 + $L4);

    } else {
        $nilai_tolak = 0;
    }

    // ================= PILIH NILAI =================
    $nilai = ($izin >= $tolak) ? $nilai_izin : $nilai_tolak;

    // ================= KEPUTUSAN =================
    $keputusan = ($nilai >= 5) ? "Diizinkan" : "Ditolak";
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Analisis Akses</title>
        <style>
            body{
                font-family: 'Segoe UI', Tahoma, sans-serif;
                background: #0f172a;
                margin: 0;
                padding: 20px;
                color: #e2e8f0;
            }

            .title{
                font-size: 26px;
                font-weight: 600;
                margin-bottom: 20px;
                color: #f8fafc;
            }

            .kop{
                display:flex;
                justify-content:center;
                margin-bottom:10px;
            }

            .kop-inner{
                display:flex;
                align-items:center;
                gap:15px;
            }

            .logo{
                width:70px;
            }

            .kop-text{
                text-align:left;
            }

            .kop-text h2{
                margin:0;
                font-size:18px;
                font-weight:bold;
            }

            .kop-text p{
                margin:2px 0;
                font-size:13px;
            }

            .garis{
                border-top:2px solid #ccc;
                border-bottom:1px solid #ccc;
                height:3px;
                margin:10px 0 20px;
            }

            .card{
                background: #1e293b;
                border-radius: 10px;
                padding: 18px;
                margin-bottom: 18px;
                border: 1px solid rgba(255,255,255,0.05);
            }

            .card h3{
                margin-bottom: 10px;
                font-size: 16px;
                border-bottom: 1px solid rgba(255,255,255,0.1);
                padding-bottom: 6px;
                color: #f1f5f9;
            }

            .card p{
                margin: 6px 0;
                font-size: 14px;
            }

            .rumus{
                background:#0f172a;
                padding:8px;
                border-radius:6px;
                font-family: monospace;
                font-size:13px;
            }

            .hasil{
                border-left:4px solid #22c55e;
            }

            .yes{
                color:#16a34a;
                font-weight:bold;
            }

            .no{
                color:#dc2626;
                font-weight:bold;
            }

            .btn-print{
                background: #2563eb;
                color: white;
                border: none;
                padding: 10px 16px;
                border-radius: 6px;
                cursor: pointer;
                font-size: 14px;
            }

            .btn-print:hover{
                background: #1d4ed8;
            }

            .ttd{
                display:none;
            }

            @media print{

                body{
                    background:white;
                    color:black;
                    font-family:"Times New Roman";
                    padding:30px;
                }

                .kop{
                justify-content:center;
                }

                .kop-inner{
                    align-items:center;
                }

                .title{
                    display:none;
                }

                .card{
                    background:white;
                    border:none;
                    box-shadow:none;
                    padding:0;
                }

                .card h3{
                    border:none;
                    font-size:16px;
                    margin-top:15px;
                    color: #000;
                }

                .card:nth-of-type(5){
                    page-break-before: always;
                    break-before: page;
                }

                .rumus{
                    background:none;
                    border:1px solid #000;
                    padding:5px;
                }

                .btn-print{
                    display:none;
                }

                .yes, .no{
                    color:black;
                }

                .garis{
                    border-top:2px solid black;
                    border-bottom:1px solid black;
                }

                .ttd{
                    display:block;
                    width:250px;
                    margin-left:auto;
                    text-align:center;
                    margin-top:10px;
                    font-size:15px;
                    line-height:1.5;
                }
            }
        </style>
    </head>
    <body>
        <h1 class="title">🔍 Analisis Akses</h1>
        <div id="printArea">
            <div class="kop">
            <div class="kop-inner">
                <img src="../../source/LogoMasjid.png" class="logo">
                <div class="kop-text">
                    <h2>MASJID IBNU SABIL</h2>
                    <p>Sistem Monitoring RFID</p>
                    <p>RT.13/RW.7, Pisangan Baru, Matraman, Jakarta Timur 13110</p>
                </div>
            </div>
        </div>
        <hr class="garis">
        <h3 style="text-align:center;">Laporan Analisis Akses RFID</h3>
        <div class="card">
            <h3>Data Akses</h3>
            <p><b>ID Laporan:</b> <?= $d['id'] ?></p>
            <p><b>Nama:</b> <?= $d['nama_pengguna'] ?></p>
            <p><b>Ruangan:</b> <?= $d['nama_ruangan'] ?></p>
            <p><b>Waktu:</b> <?= $d['waktu'] ?></p>
        </div>
        <div class="card">
            <h3>Fuzzifikasi</h3>
            <p><b>Rumus:</b></p>
            <p class="rumus">
                μ rendah(x) = (5 - x) / 5  <br>
                μ tinggi(x) = (x - 4) / 6
            </p>
            <p><b>Role (<?= $role ?>)</b></p>
            <p>μ rendah = <?= round($role_rendah,3) ?></p> 
            <p>μ tinggi = <?= round($role_tinggi,3) ?></p>
            <p><b>Sensitivitas (<?= $sens ?>)</b></p>
            <p>μ rendah = <?= round($sens_rendah,3) ?></p>
            <p>μ tinggi = <?= round($sens_tinggi,3) ?></p>
        </div>
        <div class="card">
            <h3>Inferensi (Metode Min)</h3>
            <p><b>Rumus:</b></p>
            <p class="rumus">
                α = min(μ1, μ2)
            </p>

            <p><b>R1:</b> IF Role Rendah AND Sens Rendah &rarr; Diizinkan</p>
            <p class="rumus">
                α1 = min(<?= round($role_rendah,3) ?> , <?= round($sens_rendah,3) ?>)
                = <?= round($r1,3) ?>
            </p>
            <p><b>R2:</b> IF Role Rendah AND Sens Tinggi &rarr; Ditolak</p>
            <p class="rumus">
                α2 = min(<?= round($role_rendah,3) ?> , <?= round($sens_tinggi,3) ?>)
                = <?= round($r2,3) ?>
            </p>
            <p><b>R3:</b> IF Role Tinggi AND Sens Rendah &rarr; Diizinkan</p>
            <p class="rumus">
                α3 = min(<?= round($role_tinggi,3) ?> , <?= round($sens_rendah,3) ?>)
                = <?= round($r3,3) ?>
            </p>
            <p><b>R4:</b> IF Role Tinggi AND Sens Tinggi &rarr; Diizinkan</p>
            <p class="rumus">
                α4 = min(<?= round($role_tinggi,3) ?> , <?= round($sens_tinggi,3) ?>)
                = <?= round($r4,3) ?>
            </p>
        </div>

        <div class="card">
            <h3>Komposisi (Max)</h3>

            <p class="rumus">
                μ<sub>izin</sub> = max(α1, α3, α4)
                = max(<?= round($r1,2) ?> , <?= round($r3,2) ?> , <?= round($r4,2) ?>)
                = <?= round($izin,2) ?>
            </p>

            <p class="rumus">
                μ<sub>tolak</sub> = α2
                = <?= round($tolak,2) ?>
            </p>
        </div>

        <div class="card">
            <h3>Titik Potong (α-cut)</h3>

            <p><b>Untuk Izin:</b></p>
            <p class="rumus">
                α = (z - 5) / 5
                <br>
                z = 5 + (5 × α)
                <br>
                z = 5 + (5 × <?= round($izin,2) ?>)
                = <?= round($z1,2) ?>
            </p>

            <p><b>Untuk Tolak:</b></p>
            <p class="rumus">
                α = (5 - z) / 5
                <br>
                z = 5 - (5 × α)
                <br>
                z = 5 - (5 × <?= round($tolak,2) ?>)
                = <?= round($z2,2) ?>
            </p>
        </div>

        <div class="card">
            <h3>Defuzzifikasi (Centroid)</h3>

            <p class="rumus">
            z1 = 5 + (5 × α) = <?= round($z1,2) ?>
            </p>

            <p>L1 = <?= round($L1,3) ?></p>
            <p>z1 = <?= round($C1,2) ?> (segitiga)</p>

            <p>L2 = <?= round($L2,3) ?></p>
            <p>z2 = <?= round($C2,2) ?> (persegi panjang)</p>

            <p class="rumus">
            Z = (L1×z1 + L2×z2) / (L1 + L2)
            </p>

            <p><b>= <?= round($nilai,2) ?></b></p>
        </div>
        <div class="card hasil">
            <h3>Hasil Akhir</h3>
            <p>izin = <?= round($izin,2) ?></p>
            <p>tolak = <?= round($tolak,2) ?></p>
            <p>Nilai Fuzzy = <b><?= round($nilai,2) ?></b></p>
            <p>Keputusan: 
                <span class="<?= ($keputusan=='Diizinkan')?'yes':'no' ?>">
                    <?= $keputusan ?>
                </span>
            </p>
            <hr>
            <p style="font-size:13px;">
                Sistem memberikan keputusan <b><?= $keputusan ?></b> berdasarkan metode fuzzy Mamdani.
            </p>
        </div>
        <br>
        <div class="ttd">
            <p>Jakarta, <?= $tanggal ?></p>
            <p><?= htmlspecialchars($jabatan_admin) ?></p>

            <br><br><br>

            <p><b><?= htmlspecialchars($nama_admin) ?></b></p>
        </div>
        <button onclick="window.print()" class="btn-print">🖨️ Cetak</button>
    </body>
</html>