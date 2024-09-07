<?php
// Database configuration
$db_host = 'localhost'; // From DB_HOST
$db_username = 'aglorke_wp289'; // From DB_USER
$db_password = '792pD(.2SP'; // From DB_PASSWORD
$db_name = 'aglorke_wp289'; // From DB_NAME

// Create a database connection
$conn = new mysqli($db_host, $db_username, $db_password, $db_name);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    // echo "Connection successful!";
}

// You can now use $conn to interact with your database
?>
