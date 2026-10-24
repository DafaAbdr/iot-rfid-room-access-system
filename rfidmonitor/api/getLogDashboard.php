<?php
    include __DIR__ . "/../koneksi/koneksi.php";

    $filter = $_GET['filter'] ?? 'hari';

    $where = "";

    if($filter == "hari"){
        $where = "DATE(l.waktu) = CURDATE()";
    }
    else if($filter == "minggu"){
        $where = "YEARWEEK(l.waktu, 1) = YEARWEEK(CURDATE(), 1)";
    }
    else if($filter == "bulan"){
        $where = "MONTH(l.waktu) = MONTH(CURDATE()) AND YEAR(l.waktu) = YEAR(CURDATE())";
    }

    $query = mysqli_query($conn, "
        SELECT 
            l.id,
            p.nama_pengguna,
            r.nama_ruangan,
            l.waktu,
            l.nilai_fuzzy,
            l.keputusan_akses
        FROM laporan l
        JOIN pengguna p ON l.id_pengguna = p.id
        JOIN ruangan r ON l.id_ruangan = r.id
        WHERE $where
        ORDER BY l.waktu DESC
        LIMIT 50
    ");

    $data = [];

    while($row = mysqli_fetch_assoc($query)){
        $data[] = $row;
    }

    echo json_encode($data);