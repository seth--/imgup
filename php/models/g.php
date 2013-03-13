<?php
//Shows a gallery

error_reporting(0);

if (empty($_CONTROLLER['PATH_ARGS'][1])) {
    header('Location: ' . $_CONTROLLER['BASE_DIR']);
    die();
}

$error = '';
try {
    $dbh = new PDO('sqlite:'.$_CONTROLLER['DB_PATH']);    
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $dbh->prepare('SELECT id, filename FROM images  WHERE gallery = :galleryid');
    $results = $stmt->execute(array('galleryid' => $_CONTROLLER['PATH_ARGS'][1]));
    $gallery_images = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (sizeof($gallery_images) < 1 ) {
        $error = 'Gallery not found';
    }
} catch (PDOException $e) {
    $error = 'DATABASE ERROR';
}


require_once('php/views/header.php');
if($error === ''){
    require_once('php/views/gallery.php');
}else{
    require_once('php/views/error.php');
}
require_once('php/views/footer.php');
