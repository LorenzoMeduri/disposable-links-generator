<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Files</title>
</head>
<body>
    <?php
        /* to display errors 
           DEVELOPMENT ONLY  */
        ini_set('display_errors', 1);
        error_reporting(E_ALL);

        $dir_path = dirname(__DIR__);
        $config_path = $dir_path.'/config/config.ini';
        $ini = parse_ini_file($config_path);

        $path = $dir_path.'/files/';
        $files = scandir($path);    //directory scan
        
        /* file upload */
        if(isset($_POST['submitUpload'])){
            if(isset($_FILES['fileToUpload'])){
                $destination = $path.basename($_FILES['fileToUpload']['name']);
                if(file_exists($destination)){
                    echo '<script> alert("The file already exists"); </script>';
                }else{
                    if(move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $destination)){
                        echo '<script> alert("File successfully uploaded"); </script>';
                        header("location:index.php");
                    }
                }
            }
        }

        require_once($dir_path.'/config/conn.php');

        echo '<table>';
            echo '<tr><th></th><th>Name</th><th>Size</th><th>Link</th><th>Expiration</th><th>Uses</th><th>Path</th> </tr>';

            foreach($files as $file){

                if($file[0] != '.' && $file != 'index.php' && $file != 'conn.php' && $file != 'style.css' && $file != 'linkGenerator.php'){    //excluding hidden and web files
                    $file_path = $path.$file;
                    $file_size = filesize($file_path);

                    if($file_size < 1000){
                        $file_size = $file_size." bytes";
                    }elseif($file_size >= 1000 && $file_size < 1000000){
                        $file_size = round(($file_size/1000), 2)." Kb";
                    }elseif($file_size >= 1000000 && $file_size < 1000000000){
                        $file_size = round(($file_size/1000000), 2)." Mb";
                    }else{
                        $file_size = round(($file_size/1000000000), 2)." Gb";
                    }
                    
                    /* query to find the number of links of this file */
                    $sql2 = "SELECT COUNT(*) AS count FROM links WHERE file_path = '$file_path'";
                    $rec2 = mysqli_query($conn,$sql2);
                    $array2=mysqli_fetch_array($rec2);

                    $count = $array2['count'];

                    /* form start for the delete button */
                    echo '<form method="POST">';
                    
                    /* if there are no links for a file the number of rows is set to 1 */
                    if($count == 0){    
                        $count = 1;
                    }

                    /* prints name and size of the file
                       and set the number of rows it occupy with $count
                       + ADD button to add a link                       */
                    echo '<tr><td rowspan ="'.$count.'"><a href="linkGenerator.php?file='.$file.'">ADD</a></td><td rowspan ="'.$count.'">'.$file.'</td><td rowspan ="'.$count.'">'.$file_size.'</td>';

                    /* query to find all data about the file */
                    $sql = "SELECT * FROM links WHERE file_path = '$file_path'";
                    $rec = mysqli_query($conn,$sql);

                    /* prints links' data */
                    for($i=0;$i<$array2['count'];$i++){
                        $array=mysqli_fetch_array($rec);

                        /* if the delete button has been pressed it deletes the link from the db */           /*PP*/
                        if(isset($_POST[$array['hash']])){
                            $deleteQuery = "DELETE FROM links WHERE hash = '".$array['hash']."'";
                            mysqli_query($conn,$deleteQuery);
                            /* refresh so that the deleted link doesn't show up */
                            header("location:index.php");
                        }

                        if($i != 0){
                            echo '<tr>';
                        }

                        echo '<td>'.$ini['base_url'].'?hash='.$array['hash'].'</td><td>'.$array['expiration'].'</td><td>'.$array['remaining_uses'].'</td><td>'.$array['file_path'].'</td>
                        <td><button type="submit" name="'.$array['hash'].'">DELETE</button></td></tr>';
                    }

                    echo '</form>';
                }
            }
        echo '</table>';
    ?>

    <form id="uploadForm" method="POST" enctype="multipart/form-data">
        <input type="file" name="fileToUpload">
        <input type="submit" name="submitUpload" value="UPLOAD">
    </form>

</body>
</html>

