<?php
// Database configuration
$db_host = 'localhost';
$db_username = 'aglorke_adminM'; // Updated username
$db_password = 'Maglex#588599'; // Updated password
$db_name = 'aglorke_agldatabase'; // Updated database name

// Create a database connection
$conn = new mysqli($db_host, $db_username, $db_password, $db_name);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// require_once('DBconnection.php');

?>


