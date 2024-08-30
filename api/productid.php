<?php

/* Ici on va obtenir les informations sur un produit avec toutes les informations disponibles
 * On limite au dernier moment
 *
 * */

require_once 'config.php';

$productid = $_GET['id'];
// on met un petit coup de mysqlescapestring pour eviter les injections sql
$productid = $db->real_escape_string($productid);

if(empty($productid)){
    header('Content-Type: application/json');
    die(json_encode(array('error' => 'Product ID is required')));
}
$sql = "SELECT * FROM `tracking` WHERE `id` = $productid";



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
