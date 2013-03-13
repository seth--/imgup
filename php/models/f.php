<?php
//Shows a file

error_reporting(0);

if (empty($_CONTROLLER['PATH_ARGS'][1])) {
    header('Location: ' . $_CONTROLLER['BASE_DIR']);
    die();
}

$error = '';
try {
    $dbh = new PDO('sqlite:'.$_CONTROLLER['DB_PATH']);    
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $dbh->prepare('SELECT mimetype FROM images WHERE id = :id');
    $results = $stmt->execute(array('id' => $_CONTROLLER['PATH_ARGS'][1]));
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die();
}

if ($data !== FALSE){
    header('Content-type: ' . str_replace("\n", '', $data['mimetype']));
    readfile('uploads/' . $_CONTROLLER['PATH_ARGS'][1]);
}