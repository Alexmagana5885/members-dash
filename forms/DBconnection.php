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


Error: (XID ekgkka) “/usr/local/cpanel/3rdparty/bin/git” reported error
 code “1” when it ended: error: The following untracked working 
 tree files would be overwritten by merge:
  forms/Payment/Mpesa-Daraja-Api-main/PremiumMpesastkresponse.json
 forms/Payment/Mpesa-Daraja-Api-main/callbackEventR.json 
 Please move or remove them before you merge. Aborting