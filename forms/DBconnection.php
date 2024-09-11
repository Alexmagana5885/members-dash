<?php
// Database configuration
$db_host = 'localhost';
$db_username = 'aglorke_wp289'; // Updated username
$db_password = '792pD(.2SP'; // Updated password
$db_name = 'aglorke_wp289'; // Updated database name

// Create a database connection
$conn = new mysqli($db_host, $db_username, $db_password, $db_name);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// require_once('DBconnection.php');

?>
