<?php
function random_file_name($length){
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $random_string = '';
    for ($i = 0; $i < $length; $i++) {
        $random_string .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $random_string;
}

error_reporting(0);
$error = '';
if (isset($_FILES['file']) and is_array($_FILES['file']) and isset($_POST['duration']) and is_numeric($_POST['duration'])) {
    $allowed_mime_types = array('image/gif','image/jpeg','image/png');
    $allowed_extensions = array('gif', 'jpg', 'jpeg', 'png');

    for ($i=0; $i < sizeof($_FILES['file']['name']); $i++) {
        if (!(isset($_FILES['file']['type'][$i]) and isset($_FILES['file']['name'][$i]) and isset($_FILES['file']['tmp_name'][$i]) and isset($_FILES['file']['error'][$i]))) {
            $error = 'HACKING ATTEMPT';
        }elseif (($_FILES['file']['error'][$i] ===  UPLOAD_ERR_OK) and ((!in_array($_FILES['file']['type'][$i], $allowed_mime_types)) or (!in_array(strtolower(pathinfo($_FILES['file']['name'][$i], PATHINFO_EXTENSION)), $allowed_extensions)) )) {
            $error = 'File ' . htmlentities($_FILES['file']['name'][$i], ENT_QUOTES, 'utf-8') . ' not allowed';
            $i = sizeof($_FILES['file']); //end loop
        }
    }

    if($error === ''){
        try {
            $dbh = new PDO('sqlite:'.$_CONTROLLER['DB_PATH']);
            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $dbh->exec('CREATE TABLE IF NOT EXISTS images (
                        id TEXT PRIMARY KEY NOT NULL,
                        mimetype TEXT NOT NULL,
                        filename TEXT NOT NULL,
                        gallery TEXT NOT NULL)');
            $stmt = $dbh->prepare('INSERT INTO images (id, mimetype, filename, gallery)
                                   VALUES (:id, :mimetype, :filename, :gallery)');
         
            // Bind parameters to statement variables
            $stmt->bindParam(':id', $id, PDO::PARAM_STR);
            $stmt->bindParam(':mimetype', $mimetype, PDO::PARAM_STR);
            $stmt->bindParam(':filename', $filename, PDO::PARAM_STR);
            $stmt->bindParam(':gallery', $gallery_id, PDO::PARAM_STR);

            $gallery_id = random_file_name(32);
            $uploaded_ids = array();
            for ($i=0; $i < sizeof($_FILES['file']['name']); $i++) {
                if (($error === '') and ($_FILES['file']['error'][$i] === UPLOAD_ERR_OK )) {
                    $id = random_file_name(32);
                    while (file_exists('upload/' . $id)){
                        $id = random_file_name(32);
                    }

                    if (move_uploaded_file($_FILES['file']['tmp_name'][$i], 'uploads/' . $id)) {
                        $mimetype = $_FILES['file']['type'][$i];
                        $filename = basename($_FILES['file']['name'][$i]);
                        $stmt->execute();

                        $uploaded_ids[] = $id;
                    } else {
                        $error = 'HACKING ATTEMPT';
                    }
                }
            }

            if($error === ''){
                if (sizeof($uploaded_ids) > 0) {
                    $dbh->exec('CREATE TABLE IF NOT EXISTS galleries (
                                id TEXT PRIMARY KEY NOT NULL,
                                delete_timestamp INTEGER NOT NULL)');

                    $stmt = $dbh->prepare('INSERT INTO galleries (id, delete_timestamp)
                                           VALUES (?, ?)');
                    $stmt->execute(array($gallery_id, time() + $_POST['duration']));
                }else{
                    $error = 'Where are the files?';
                }
            }
        } catch (PDOException $e) {
            $error = 'Database error';
        }
    }
}else{
    header('Location: ' . $_CONTROLLER['BASE_DIR']);
    die();
}

if($error === ''){
    header('Location: ' . $_CONTROLLER['BASE_DIR'] . '/g/' . $gallery_id);
    die();
}else{
    require_once('php/views/header.php');
    require_once('php/views/error.php');
    require_once('php/views/footer.php');
}
