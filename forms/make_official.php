<?php
require_once('DBconnection.php');
session_start();

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $email = isset($_POST['email']) ? $conn->real_escape_string($_POST['email']) : '';
    $position = isset($_POST['position']) ? $conn->real_escape_string($_POST['position']) : '';
    $start_date = isset($_POST['start_date']) ? $conn->real_escape_string($_POST['start_date']) : '';
    $number_of_terms = isset($_POST['number_of_terms']) ? (int)$_POST['number_of_terms'] : 0;

    // Prepare the SQL statement
    $sql = "INSERT INTO officialsmembers (personalmembership_email, position, start_date, number_of_terms)
            VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sssi', $email, $position, $start_date, $number_of_terms);

    // Execute the statement
    if ($stmt->execute()) {
        // echo "Member successfully made an official.";
        header("Location: " . $_SERVER['HTTP_REFERER']);
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>
