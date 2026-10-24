<?php
    include __DIR__ . '/../../koneksi/koneksi.php';

    function generateID($conn) {
        $q = mysqli_query($conn, "SELECT id FROM pengguna ORDER BY id DESC LIMIT 1");
        $d = mysqli_fetch_assoc($q);
        if (!$d) return "P001";

        $num = (int) substr($d['id'],1);
        $num++;
        return "P".str_pad($num,3,"0",STR_PAD_LEFT);
    }

    $id = generateID($conn);
    $nama = $_POST['nama_pengguna'];
    $rfid = $_POST['no_rfid'];
    $role = $_POST['role_level'];
    $status = $_POST['status'];

    // Upload Foto
    $photoName = "profile.jpg";

    if(isset($_FILES['photo']) && $_FILES['photo']['error'] == 0){

        $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
        $photoName = "user_" . time() . "." . $ext;

        move_uploaded_file(
            $_FILES['photo']['tmp_name'], 
            __DIR__ . '/../../source/profile/' . $photoName
        );
    }

    // INSERT
    mysqli_query($conn, "INSERT INTO pengguna 
    (id, nama_pengguna, no_rfid, role_level, status, photo)
    VALUES 
    ('$id','$nama','$rfid','$role','$status','$photoName')");

    header("Location: /loginPage/dashboard.php?page=user&status=success&aksi=tambah");
exit;