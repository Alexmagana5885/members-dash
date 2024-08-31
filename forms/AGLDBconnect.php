<?php
// Database configuration
$db_host = '162.55.31.172';
$db_username = 'aglorke';
$db_password = ')0EqcQmN4W4q.4'; // Make sure to replace with the actual password
$db_name = 'aglorke_wp289';

// Create a database connection
$conn = new mysqli($db_host, $db_username, $db_password, $db_name);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    echo "Connected successfully!";
}

// require_once('DBconnection.php');

?>
