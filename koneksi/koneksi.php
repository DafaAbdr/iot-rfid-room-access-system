<?php
    $conn = mysqli_connect("localhost", "root", "", "rfidmonitor");

    if (!$conn) {
        die("Koneksi gagal: " . mysqli_connect_error());
    }
?>