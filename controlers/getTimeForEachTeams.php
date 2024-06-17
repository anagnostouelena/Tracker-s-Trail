<?php

    include '/var/www/html/game/repositories/time.php';
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET");
    header("Access-Control-Allow-Headers: Content-Type");
    header("Access-Control-Allow-Credentials: true");
    header("Content-Type: application/json");


    if ($_SERVER["REQUEST_METHOD"] === "GET") {
        $timeRepository = new TimeRepository();

        $result=$timeRepository  -> getTimeForEachTeams();
        echo $result;

    }

   
?>
