<?php
    if(isset($_POST['rfid'])){
        $rfid = $_POST['rfid'];

        file_put_contents(__DIR__ . "/rfid.txt", $rfid);

        echo "OK";
    }
?>