<?php
// Include the PHP QR Code library
require_once '../assets/phpqrcode/qrlib.php';

// Set the content for the QR code
$content = "Hello Alex";

// Generate the QR code image and save it to a file
$qrCodeFile = 'qr_code.png';
QRcode::png($content, $qrCodeFile, QR_ECLEVEL_L, 4);

// Display the generated QR code
echo '<h1>Your QR Code</h1>';
echo '<img src="' . $qrCodeFile . '" alt="QR Code" />';
?>
