<?PHP
$_CONTROLLER['DB_PATH'] = __DIR__.'/db.sqlite'; // configure this in controller.php too

try {
    $dbh = new PDO('sqlite:'.$_CONTROLLER['DB_PATH']);    
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $dbh->prepare('SELECT id FROM galleries WHERE delete_timestamp < ?');
    $results = $stmt->execute(array(time()));
    $to_delete = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($to_delete as $gallery){
        if(delete_gallery($gallery['id'], $dbh)) {
            echo "Deleted gallery " . $gallery['id'] . "\n";
        }else{
            echo "Unable to delete the gallery " . $gallery['id'] . "\n";
        }
    }
} catch (PDOException $e) {
    echo "DATABASE ERROR\n\n";
    print_r($e);
}


function delete_image($id, $dbh) {
    if ((!unlink(dirname(__FILE__) . '/uploads/' . $id)) and file_exists(dirname(__FILE__) . '/uploads/' . $id)) {
        return false;
    }
    $stmt = $dbh->prepare('DELETE FROM images WHERE id = ?');
    return $stmt->execute(array($id));
}

function delete_gallery($id, $dbh) {
    $stmt = $dbh->prepare('SELECT images.id FROM images WHERE gallery = ?');
    $results = $stmt->execute(array($id));
    $to_delete = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $success = true;
    foreach ($to_delete as $image){
        if(!delete_image($image['id'], $dbh)) {
            echo "Unable to delete the image " . $image['id'] . "\n";
            $success = false;
        }
    }

    if ($success) {
        $stmt = $dbh->prepare('DELETE FROM galleries WHERE id = ?');
        $success = $stmt->execute(array($id));
    }

    return $success;
}