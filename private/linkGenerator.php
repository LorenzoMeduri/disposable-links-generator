<html>
<?php
    ini_set('display_errors', 1);
    error_reporting(E_ALL);

    function createLink($file, $hours_to_expiration, $remaining_uses){
        $hash = sha1($file.uniqid().time());
        
        $config_path = __DIR__.'/../config/config.ini';
        $ini = parse_ini_file($config_path);
        require_once(__DIR__.'/../config/conn.php');
        
        $path = __DIR__."/".$file;

        date_default_timezone_set('Europe/Rome');
        $expirationDate = date('Y-m-d H:i:s', strtotime("+ $hours_to_expiration hours"));
        
        $sql = "INSERT INTO links (hash, expiration, remaining_uses, file_path) VALUES ('".$hash."', '".$expirationDate."' , ".$remaining_uses.", '".$path."')";
        $rec = mysqli_query($conn,$sql);
        
        return $ini['base_url']."?hash=".$hash;
    }

    $file = $_GET['file'];
    createLink($file, 24, 5);

    header('location:index.php');
?>
</html>