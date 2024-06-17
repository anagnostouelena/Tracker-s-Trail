<?php
    include '/var/www/html/game/repositories/routiesRepository.php';
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Allow-Headers: Content-Type");
    header("Access-Control-Allow-Credentials: true");
    header("Content-Type: application/json");
    
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $json_data = file_get_contents('php://input');
    
        $datajson = json_decode($json_data, true);

        if ($datajson === null) {
            http_response_code(400); 
            echo json_encode(['error' => 'Invalid JSON']);
        }

        print_r($datajson);
        $routeRepo = new routiesRepository();

        $routeRepo->deleteRoute($datajson);


    } else {
        echo "Invalid request method";
    }
    


?>
