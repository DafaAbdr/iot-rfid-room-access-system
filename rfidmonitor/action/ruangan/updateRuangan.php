<?php
    include __DIR__ . '/../../koneksi/koneksi.php';

    $id = $_POST['id'];
    $nama = $_POST['nama_ruangan'];
    $detail = $_POST['detail_ruangan'];
    $sensitivitas = $_POST['sensitivitas'];
    // VALIDASI
    if ($sensitivitas === "" || !is_numeric($sensitivitas)) {
        header("Location: /loginPage/dashboard.php?page=ruangan&status=error&aksi=edit");
        exit;
    }

    // UPDATE
    $update = mysqli_query($conn, "UPDATE ruangan SET 
        nama_ruangan='$nama',
        detail_ruangan='$detail',
        sensitivitas='$sensitivitas'
    WHERE id='$id'");

    // AKSI
    if ($update) {
        header("Location: /loginPage/dashboard.php?page=ruangan&status=success&aksi=edit");
    } else {
        header("Location: /loginPage/dashboard.php?page=ruangan&status=error&aksi=edit");
    }

    exit;
?>