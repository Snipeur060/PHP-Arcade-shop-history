<?php
/* Le but est d'utiliser un string pour faire une recherche dans la base de données
 * */

//init database connection
$db = new Mysqli('localhost','userdb','passs','dbname');

//connect to the db
if ($db->connect_error) {
    header('Content-Type: application/json');
    echo json_encode(array('error' => 'DB excpetion Error'));
    die();
}

// le parametre s'appelle search
$search = $_GET['search'];
// on met un petit coup de mysqlescapestring pour eviter les injections sql
$search = $db->real_escape_string($search);

if(empty($search)){
    header('Content-Type: application/json');
    die(json_encode(array('error' => 'Search is required')));
}

// on est flexible avec les % et regroupe par productname comme ça pas de doublon mais on group avec la derniere id et on prend toujours la derniere entrée

$sql = "SELECT * FROM `tracking` t1 WHERE t1.`productname` LIKE '%$search%' AND t1.`id` = ( SELECT MAX(t2.`id`) FROM `tracking` t2 WHERE t2.`productname` = t1.`productname` )";

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
