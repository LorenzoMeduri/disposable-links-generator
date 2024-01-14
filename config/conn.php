<?php
    session_start();
    $ini = parse_ini_file("config.ini");
    $conn = mysqli_connect($ini['db_ip'],$ini['db_user'],$ini['db_password'],$ini['db_name']) or die("Can't connect to database");
?>