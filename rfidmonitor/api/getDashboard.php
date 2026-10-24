<?php
    include __DIR__ . "/../koneksi/koneksi.php";

    $pengguna = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM pengguna"))['total'];
    $ruangan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM ruangan"))['total'];
    $akses = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM laporan WHERE DATE(waktu) = CURDATE()"))['total'];

    // GRAFIK
    $grafik_query = mysqli_query($conn, "
        SELECT r.nama_ruangan, COUNT(l.id) as total 
        FROM laporan l
        JOIN ruangan r ON l.id_ruangan = r.id
        GROUP BY l.id_ruangan
    ");

    $grafik = [];
    while($g = mysqli_fetch_assoc($grafik_query)){
        $grafik[] = $g;
    }

    // RUANGAN TERFAVORITE
    $bulan = date('m');
    $top = mysqli_fetch_assoc(mysqli_query($conn, "
        SELECT r.nama_ruangan, COUNT(l.id) as total
        FROM laporan l
        JOIN ruangan r ON l.id_ruangan = r.id
        WHERE MONTH(l.waktu) = '$bulan'
        GROUP BY l.id_ruangan
        ORDER BY total DESC
        LIMIT 1
    "));

    // OUTPUT
    echo json_encode([
        "pengguna" => $pengguna,
        "ruangan" => $ruangan,
        "akses" => $akses,
        "bulan_ini" => $top['nama_ruangan'] ?? '-',
        "grafik" => $grafik
    ]);