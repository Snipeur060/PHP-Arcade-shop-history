<?php
/*  This part is not accessible to user or on the web
 *  Only plesk / cron job can access this file
 * */

//init database connection
$db = new Mysqli('localhost','userdb','pass','dbname');

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

    if($stock === NULL){
        $stock = 9999;
    }



    // on va faire une verification simple on prend le nom du produit dans la db (la derniere entrée) et on compare avec le nom du produit actuel
    $sql = "SELECT * FROM `tracking` WHERE `productname` = '$productname' ORDER BY `id` DESC LIMIT 1";


    $result = $db->query($sql);
    if($result->num_rows > 0){
        $row = $result->fetch_assoc();
        // ça nous permet de remettre les stocks correctement si on fait +1 -1
        //on fait juste une verif pour Wacome si l'image est https://cloud-c6nos84m4-hack-club-bot.vercel.app/061zj7gvrvsl._ac_sx679_.png on pass
        if($row['imageURL'] == 'https://cloud-c6nos84m4-hack-club-bot.vercel.app/061zj7gvrvsl._ac_sx679_.png' or $row['imageURL'] == 'https://cloud-bi9lc9poq-hack-club-bot.vercel.app/0intuos.png'){
            // on ne fait rien
            continue;
        }
        if($row['imageURL']=="https://cloud-g0bjmr0sz-hack-club-bot.vercel.app/013in.png" or $row['imageURL']=="https://cloud-elfpck7gj-hack-club-bot.vercel.app/1screenshot_2024-06-14_at_07.39.22.png" or $row["imageURL"] == 'https://cloud-elfpck7gj-hack-club-bot.vercel.app/1screenshot_2024-06-14_at_07.39.22.png'){
            // on ne fait rien
            continue;
        }
        if($row['hours'] == $hours && $row['stock'] == $stock){
            // on ne fait rien
            continue;
        }
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
