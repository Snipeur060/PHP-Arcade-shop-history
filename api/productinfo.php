<?php

/* Ici on va obtenir les informations sur un produit avec toutes les informations disponibles
 * On limite au dernier moment
 *
 * */

require_once 'config.php';

// le parametre s'appelle productname
$productname = $_GET['productname'];
// on met un petit coup de mysqlescapestring pour eviter les injections sql
$productname = $db->real_escape_string($productname);

if(empty($productname)){
    header('Content-Type: application/json');
    die(json_encode(array('error' => 'Productname is required')));
}
// on limite à la derniere entré
$sql = "SELECT * FROM `tracking` WHERE `productname` = '$productname' ORDER BY `id` DESC LIMIT 1";



$result = $db->query($sql);


if ($result->num_rows > 0) {
    $rows = array();
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
    header('Content-Type: application/json');
    echo json_encode($rows);
} else {
    header('Content-Type: application/json');
    echo json_encode(array('error' => 'No data found'));
}



?>
