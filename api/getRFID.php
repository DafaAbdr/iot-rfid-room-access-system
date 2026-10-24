<?php
    $file = __DIR__ . "/rfid.txt";

    if(file_exists($file)){
        $data = file_get_contents($file);
        echo $data;

        // KOSONG SETELAH READ
        file_put_contents($file, "");
    }
?>