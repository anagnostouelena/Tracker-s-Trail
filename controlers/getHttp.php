<?php
    include '/var/www/html/game/repositories/api.php';

    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET");
    header("Access-Control-Allow-Headers: Content-Type");
    header("Access-Control-Allow-Credentials: true");
    header("Content-Type: application/json");


    if ($_SERVER["REQUEST_METHOD"] === "GET") {
    
        $api = new Api();
        $group_name = $_GET['group_name'];
        $point_name = $_GET['point_name'];


        $resultapi=$api-> getApi($group_name,$point_name);
        echo $resultapi;

    }

   
?>
