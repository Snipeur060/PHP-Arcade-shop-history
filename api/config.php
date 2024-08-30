<?php

//init database connection
$db = new Mysqli('localhost','userdb','pass','dbname');

//connect to the db
if ($db->connect_error) {
    header('Content-Type: application/json');
    echo json_encode(array('error' => 'DB excpetion Error'));
    die();
}
