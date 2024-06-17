<?php

//    include '/var/www/html/game/repositories/teamsRepository.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json");
header("Content-Type: image/png");

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    $teamsRepository = new TeamsRepository();
$group_name = $_GET['group_name'];

    // Καλούμε τη μέθοδο που δημιουργεί το QR code
    $teamsRepository->getQR($group_name); 

    // Διαβάζουμε και επιστρέφουμε το αρχείο εικόνας QR
    readfile('repositories/qrcode.png'); 
}
?>
