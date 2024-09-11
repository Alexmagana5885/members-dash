<?php
session_start();

// Get the response data from the session
$response = isset($_SESSION['response']) ? $_SESSION['response'] : null;

// Clear the session response
unset($_SESSION['response']);

// Output response data as JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
