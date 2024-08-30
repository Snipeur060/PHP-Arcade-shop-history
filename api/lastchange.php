<?php

/*  Cette endpoint d'api permet d'avoir une liste des 20 derniers changements sur l'enssemble des produits
 *
 * */

require_once 'config.php';

// On récupère les 20 derniers changements
$sql = "SELECT * FROM `tracking` ORDER BY `id` DESC LIMIT 20";

// On execute la requête
$result = $db->query($sql);

// On initialise un tableau vide
$lastChanges = [];

// On parcours les résultats
while ($row = $result->fetch_assoc()) {
    // On ajoute chaque ligne dans le tableau
    $lastChanges[] = $row;
}

// On retourne le tableau en json
header("Content-Type: application/json");
echo json_encode($lastChanges);
