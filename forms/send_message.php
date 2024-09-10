<?php
session_start();
require_once('DBconnection.php');

// Get form data
$sender_name = $_POST['sender_name'];
$sender_email = $_POST['sender_email'];
$recipient_choice = $_POST['recipient'];
$subject = $_POST['subject'];
$message_content = $_POST['message'];

// Default 'from' email
$from_email = 'info@agl.or.ke';

// Initialize response array
$response = array('success' => false, 'message' => '', 'errors' => array());

// Determine recipients based on user choice
$recipients = [];

if ($recipient_choice === 'all_members') {
    // Fetch emails from both tables
    $sql_organization = "SELECT organization_email FROM organizationmembership";
    $sql_personal = "SELECT email FROM personalmembership";

    $result_organization = $conn->query($sql_organization);
    $result_personal = $conn->query($sql_personal);

    if ($result_organization->num_rows > 0) {
        while ($row = $result_organization->fetch_assoc()) {
            $recipients[] = $row['organization_email'];
        }
    }

    if ($result_personal->num_rows > 0) {
        while ($row = $result_personal->fetch_assoc()) {
            $recipients[] = $row['email'];
        }
    }

    // Record message in memberMessages table
    $stmt = $conn->prepare("INSERT INTO membermessages (sender_name, sender_email, recipient_group, subject, message) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $sender_name, $sender_email, $recipient_choice, $subject, $message_content);
    $stmt->execute();
    $stmt->close();

} elseif ($recipient_choice === 'officials_only') {
    // Fetch emails of officials
    $sql_officials = "SELECT personalmembership.email 
                      FROM personalmembership 
                      JOIN officialsmembers ON personalmembership.email = officialsmembers.personalmembership_email";

    $result_officials = $conn->query($sql_officials);

    if ($result_officials->num_rows > 0) {
        while ($row = $result_officials->fetch_assoc()) {
            $recipients[] = $row['email'];
        }
    }

    // Record message in officialMessages table
    $stmt = $conn->prepare("INSERT INTO officialMessages (sender_name, sender_email, recipient_group, subject, message) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $sender_name, $sender_email, $recipient_choice, $subject, $message_content);
    $stmt->execute();
    $stmt->close();
}

// Send emails
if (!empty($recipients)) {
    foreach ($recipients as $recipient_email) {
        $headers = "From: $from_email\r\n";
        $headers .= "Reply-To: $sender_email\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

        // Send the email
        if (mail($recipient_email, $subject, nl2br($message_content), $headers)) {
            // You could store successful emails sent if needed
        } else {
            // Capture email sending failure
            $response['errors'][] = "Failed to send message to $recipient_email.";
        }
    }

    // If there are no errors, mark the response as successful
    if (empty($response['errors'])) {
        $response['success'] = true;
        $response['message'] = 'All emails sent successfully.';
    } else {
        $response['message'] = 'Some emails could not be sent.';
    }
} else {
    $response['message'] = 'No recipients found for the selected group.';
}

// Store the response in the session
$_SESSION['response'] = $response;

// Redirect back to the referring page
header("Location: " . $_SERVER['HTTP_REFERER']);
exit;

// Close the database connection
$conn->close();
?>
