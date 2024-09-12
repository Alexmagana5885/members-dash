<?php
session_start(); // Start the session to store response

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

    // Initialize response array
    $_SESSION['response'] = [
        'success' => false,
        'message' => '',
        'errors' => []
    ];

    // Check if the user has already registered for this event
    $checkSql = "SELECT * FROM event_registrations WHERE event_id = ? AND member_email = ?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param("is", $event_id, $member_email);
    $checkStmt->execute();
    $result = $checkStmt->get_result();

    if ($result->num_rows > 0) {
        // The user has already registered for the event
        $_SESSION['response']['message'] = 'You have already registered for this event.';
        $_SESSION['response']['errors'][] = 'You have already registered for this event.';
    } else {
        // If no existing registration, proceed to insert the new registration
        $sql = "INSERT INTO event_registrations (event_id, event_name, event_location, event_date, member_email, member_name, contact) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issssss", $event_id, $event_name, $event_location, $event_date, $member_email, $member_name, $contact);

        // Execute the statement
        if ($stmt->execute()) {
            // Success: Set session response
            $_SESSION['response']['success'] = true;
            $_SESSION['response']['message'] = 'Registration successful!';
        } else {
            // Error: Set error message
            $_SESSION['response']['message'] = 'Registration failed. Please try again.';
            $_SESSION['response']['errors'][] = 'Database error: ' . $stmt->error;
        }

        // Close the statement and the connection
        $stmt->close();
    }

    // Close check statement and connection
    $checkStmt->close();
    $conn->close();

    // Redirect back to the previous page
    $previous_page = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'index.php';
    header("Location: $previous_page");
    exit();
}
?>
