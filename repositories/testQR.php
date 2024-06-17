<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json");

include '/var/www/html/game/phpqrcode-2010100721_1.1.4/phpqrcode/qrlib.php'; // Include the QR Code library

$backColor = 0xFFFF00; // Background color (in hexadecimal)
$foreColor = 0xFF00FF; // Foreground color (in hexadecimal)
$textLabel = "Hello!"; // Text label

// Create a QR Code and export it to PNG
QRcode::png("scanMe", "test.png", "L", 4, 4, false, $backColor, $foreColor);

// Load the QR code image using the Imagick library
$qrCodeImage = new Imagick("test.png");

// Create a new image with the QR code and the text label
$newImage = new Imagick();
$newImage->newImage($qrCodeImage->getImageWidth(), $qrCodeImage->getImageHeight() + 20, "none"); // Create a new image with the same size as the QR code image
$newImage->setImageBackgroundColor("#FFFFFF"); // Set the background color
$newImage->drawImage(new ImagickDraw()); // Draw the QR code image on the new image
$newImage->annotateImage(new ImagickDraw(), 0, $qrCodeImage->getImageHeight() + 10, 0, $textLabel); // Add the text label to the new image

// Save the new image as an SVG image
$newImage->setImageFormat("svg");
$newImage->writeImage("test.svg");
?>
