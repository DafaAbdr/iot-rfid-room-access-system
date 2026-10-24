<?php
    include __DIR__ . '/../../koneksi/koneksi.php';

    $id = $_POST['id'];
    $nama = $_POST['nama_pengguna'];
    $rfid = $_POST['no_rfid'];
    $role = $_POST['role_level'];
    $status = $_POST['status'];

    // AMBIL DATA LAMA
    $dataLama = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM pengguna WHERE id='$id'"));
    $fotoLama = $dataLama['photo'];

    $folder = __DIR__ . '/../../source/profile/';

    // CEK FOTO
    if (!empty($_FILES['photo']['name'])) {

        $namaFile = time() . '_' . $_FILES['photo']['name'];
        $tmp = $_FILES['photo']['tmp_name'];

        move_uploaded_file($tmp, $folder . $namaFile);

        if (!empty($fotoLama) && file_exists($folder . $fotoLama)) {
            unlink($folder . $fotoLama);
        }

        $query = "UPDATE pengguna SET
                    nama_pengguna='$nama',
                    no_rfid='$rfid',
                    role_level='$role',
                    status='$status',
                    photo='$namaFile'
                WHERE id='$id'";

    } else {
        $query = "UPDATE pengguna SET
                    nama_pengguna='$nama',
                    no_rfid='$rfid',
                    role_level='$role',
                    status='$status'
                WHERE id='$id'";
    }

    $update = mysqli_query($conn, $query);

    // AKSI
    if ($update) {
        header("Location: /loginPage/dashboard.php?page=user&status=success&aksi=edit");
    } else {
        header("Location: /loginPage/dashboard.php?page=user&status=error&aksi=edit");
    }

    exit;
?>