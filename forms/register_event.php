<?php
// Include your database connection file
include 'DBconnection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the form data
    $event_id = htmlspecialchars($_POST['event_id']);
    $event_name = htmlspecialchars($_POST['event_name']);
    $event_location = htmlspecialchars($_POST['event_location']); 
    $event_date = htmlspecialchars($_POST['event_date']);         
    $member_email = htmlspecialchars($_POST['memberEmail']);
    $member_name = htmlspecialchars($_POST['memberName']);
    $contact = htmlspecialchars($_POST['contact']);

    // Prepare the SQL statement to insert data into the event_registrations table
    $sql = "INSERT INTO event_registrations (event_id, event_name, event_location, event_date, member_email, member_name, contact) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";

    // Prepare and bind
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issssss", $event_id, $event_name, $event_location, $event_date, $member_email, $member_name, $contact);

    // Execute the statement
    if ($stmt->execute()) {
        echo "Registration successful!";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the statement and the connection
    $stmt->close();
    $conn->close();
}
?>
