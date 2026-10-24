<?php
    include __DIR__ . '/../../koneksi/koneksi.php';

    $id = $_GET['id'];

    $data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT photo FROM pengguna WHERE id='$id'"));

    if($data){
        $photo = $data['photo'];
        if($photo != "profile.jpg" && file_exists(__DIR__ . '/../../source/profile/' . $photo)){
            unlink(__DIR__ . '/../../source/profile/' . $photo);
        }
    }

    $hapus = mysqli_query($conn, "DELETE FROM pengguna WHERE id='$id'");

    if($hapus){
        header("Location: /loginPage/dashboard.php?page=user&status=success&aksi=hapus");
    } else {
        header("Location: /loginPage/dashboard.php?page=user&status=error&aksi=hapus");
    }
exit;