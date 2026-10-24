<?php
    session_start();
    include '../koneksi/koneksi.php';

    $type = $_POST['type'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $jabatan = $_POST['jabatan'] ?? '';

    // ID GENERATOR
    function generateID($conn) {
        $query = mysqli_query($conn, "SELECT id FROM admin ORDER BY id DESC LIMIT 1");
        $data = mysqli_fetch_assoc($query);

        if (!$data) return "A001";

        $lastID = $data['id'];
        $number = (int) substr($lastID, 1);
        $number++;

        return "A" . str_pad($number, 3, "0", STR_PAD_LEFT);
    }

    // REGISTER
    if ($type == "register") {

        $cek = mysqli_query($conn, "SELECT * FROM admin WHERE username='$username'");
        if (mysqli_num_rows($cek) > 0) {
            header("Location: auth.php?error=register");
            exit;
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $id = generateID($conn);

        $fotoName = $_FILES['foto']['name'];
        $tmp      = $_FILES['foto']['tmp_name'];

        $folder = "../source/profile/";

        if (!is_dir($folder)) {
            mkdir($folder, 0777, true);
        }

        $ext = strtolower(pathinfo($fotoName, PATHINFO_EXTENSION));

        $allowed = ['jpg','jpeg','png'];
        if (!in_array($ext, $allowed)) {
            header("Location: auth.php?error=upload");
            exit;
        }

        $fotoBaru = $id . "." . $ext;

        if (move_uploaded_file($tmp, $folder . $fotoBaru)) {

            $insert = mysqli_query($conn, "INSERT INTO admin (id, username, password, jabatan, photo) 
            VALUES ('$id', '$username', '$hashedPassword', '$jabatan', '$fotoBaru')");

            if ($insert) {
                header("Location: auth.php?success=register");
            } else {
                header("Location: auth.php?error=register");
            }

        } else {
            header("Location: auth.php?error=upload");
        }

        exit;
    }

    // LOGIN
    else if ($type == "login") {

        $query = mysqli_query($conn, "SELECT * FROM admin WHERE username='$username'");
        $data = mysqli_fetch_assoc($query);

        if ($data && password_verify($password, $data['password'])) {

            $_SESSION['username'] = $data['username'];
            $_SESSION['id'] = $data['id'];
            $_SESSION['foto'] = $data['photo'];
            $_SESSION['jabatan'] = $data['jabatan'];

            // remember me
            if (isset($_POST['remember'])) {
                setcookie("username", $username, time() + (86400 * 30), "/");
                setcookie("password", $password, time() + (86400 * 30), "/");
            } else {
                setcookie("username", "", time() - 3600, "/");
                setcookie("password", "", time() - 3600, "/");
            }

            header("Location: dashboard.php");
            exit;

        } else {
            header("Location: auth.php?error=login");
            exit;
        }
    }
?>