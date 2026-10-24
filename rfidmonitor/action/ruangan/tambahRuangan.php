<?php
    include __DIR__ . '/../../koneksi/koneksi.php';

    function generateID($conn) {
        $query = mysqli_query($conn, "SELECT id FROM ruangan ORDER BY id DESC LIMIT 1");
        $data = mysqli_fetch_assoc($query);
        if (!$data) return "R001";
        $last = $data['id'];
        $num = (int) substr($last, 1);
        $num++;
        return "R" . str_pad($num, 3, "0", STR_PAD_LEFT);
    }

    $id = generateID($conn);
    $nama = $_POST['nama_ruangan'];
    $detail = $_POST['detail_ruangan'];
    $sensitivitas = $_POST['sensitivitas'];

    // VALIDASI
    if ($sensitivitas === "" || !is_numeric($sensitivitas)) {
        header("Location: /loginPage/dashboard.php?page=ruangan&status=error&aksi=tambah");
        exit;
    }

    // INSERT
    $insert = mysqli_query($conn, "INSERT INTO ruangan (id, nama_ruangan, detail_ruangan, sensitivitas)
    VALUES ('$id','$nama','$detail','$sensitivitas')");

    // AKSI
    if ($insert) {
        header("Location: /loginPage/dashboard.php?page=ruangan&status=success&aksi=tambah");
    } else {
        header("Location: /loginPage/dashboard.php?page=ruangan&status=error&aksi=tambah");
    }
    exit;
?>