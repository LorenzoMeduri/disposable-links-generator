<?php

    /*ini_set('display_errors', 1);
    error_reporting(E_ALL);*/

    $dir_path = dirname(__DIR__);
    require_once($dir_path.'/config/conn.php');
    $hash = $_GET['hash'];

    $sql = "SELECT * FROM links WHERE hash = '$hash' AND expiration >= NOW() AND remaining_uses > 0";
    $rec = mysqli_query($conn,$sql);
    $array=mysqli_fetch_array($rec);

    $file = $array['file_path'];

    if($file != ''){
        header("Content-Disposition: attachment; filename=".basename($file));
        header("Content-Type: application/octet-stream");
        header("Content-Length: " . filesize($file));
        header("Connection: close");

        if(readfile($file) != false){
            $sql = "UPDATE links SET remaining_uses = (remaining_uses - 1) WHERE hash = '$hash'";
            $rec = mysqli_query($conn,$sql);
        }


    }else{
        echo '
        <html>
        <head>
            <style>
                body {
                    text-align: center;
                }
            </style>
        </head>
        <body>
            <h1> INVALID LINK </h1>
        </body>
        </html>';    
    }



?>