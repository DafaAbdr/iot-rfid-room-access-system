<?php
include __DIR__ . '/../../koneksi/koneksi.php';

$id = $_GET['id'];

$hapus = mysqli_query($conn, "DELETE FROM ruangan WHERE id='$id'");

if ($hapus) {
    header("Location: /loginPage/dashboard.php?page=ruangan&status=success&aksi=hapus");
} else {
    header("Location: /loginPage/dashboard.php?page=ruangan&status=error&aksi=hapus");
}

exit;