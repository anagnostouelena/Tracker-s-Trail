<?php

    include '/var/www/html/game/repositories/start_stop_time.php';
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET");
    header("Access-Control-Allow-Headers: Content-Type");
    header("Access-Control-Allow-Credentials: true");
    header("Content-Type: application/json");


    if ($_SERVER["REQUEST_METHOD"] === "GET") {
        $startStopTimeRepository = new StartStopTimeRepository();
	$group_status=$_GET['status'];
        $result=$startStopTimeRepository-> duration($group_status);
        echo $result;

    }

   
?>

