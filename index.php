<?php
    session_start();

    if (isset($_SESSION['username'])) {
        header("Location: loginPage/dashboard.php");
    } else {
        header("Location: loginPage/auth.php");
    }
    exit;