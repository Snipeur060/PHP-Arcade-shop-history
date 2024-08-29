<?php
/*  This part is not accessible to user or on the web
 *  Only plesk / cron job can access this file
 * */

//init database connection
$db = new Mysqli('localhost','dbusername','password','dbname');

//connect to the db
if ($db->connect_error) {
    die("DB excpetion Error");
}

// communicate with the api


$api = 'https://hackclub.com/api/arcade/shop/';

// procédure habituelle pour curl
$ch = curl_init($api);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);


// exécute la requête
$response = curl_exec($ch);

// ferme la session cURL
curl_close($ch);

// Maintenant on a des format [{"name":0,"smallName":0,"description":0,"hours":0,"imageURL":0}, {...}, ...]


$resultarray = json_decode($response, true);


//INSERT INTO `tracking`(`productname`, `hours`, `imageURL`) VALUES ('[value-2]',INT,'[value-4]')

foreach ($resultarray as $key => $value) {
    $productname = $value['name'];
    // on met un petit coup de mysqlescapestring pour eviter les injections sql
    $productname = $db->real_escape_string($productname);
    $hours = $value['hours']; // correspond au prix / nb ticket
    $imageURL = $value['imageURL'];
    $stock = $value['stock'];

    // on ajoute une verification pour savoir s'il y a une difference avec au moins une des valeurs
    // si oui, on ajoute dans la base de données
    // sinon, on ne fait rien
    $sql = "SELECT * FROM `tracking` WHERE `productname` = '$productname' AND `hours` = $hours AND `stock` = $stock AND `imageURL` = '$imageURL' ";
    $result = $db->query($sql);
    if ($result->num_rows > 0) {
        //on passe à la prochaine itération dans le cas où il y a une correspondance exacte
        continue;
    }

    $sql = "INSERT INTO `tracking`(`productname`, `hours`, `imageURL`,`stock`) VALUES ('$productname',$hours,'$imageURL',$stock)";

    $db->query($sql);
}

//close the db connection
$db->close();

header('Status: 200 OK');
header('Content-Type: application/json');
echo json_encode(array('status' => 'success'));
?>
