<?php

/* Ici on va obtenir les informations sur un produit avec toutes les informations disponibles
 * On limit dans le temps à -25jours
 *
 * */

//init database connection
$db = new Mysqli('localhost','dbusername','password','dbname');

//connect to the db
if ($db->connect_error) {
    header('Content-Type: application/json');
    echo json_encode(array('error' => 'DB excpetion Error'));
    die();
}

// le parametre s'appelle productname
$productname = $_GET['productname'];
// on met un petit coup de mysqlescapestring pour eviter les injections sql
$productname = $db->real_escape_string($productname);

if(empty($productname)){
    header('Content-Type: application/json');
    die(json_encode(array('error' => 'Productname is required')));
}
// on limite à 25 jours
$sql = "SELECT * FROM `tracking` WHERE `productname` = '$productname' AND `date` > DATE_SUB(NOW(), INTERVAL 25 DAY) ORDER BY `date` DESC";


$result = $db->query($sql);

if ($result->num_rows > 0) {
    $rows = array();
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
    echo json_encode($rows);
} else {
    echo json_encode(array('error' => 'No data found'));
}



?>
