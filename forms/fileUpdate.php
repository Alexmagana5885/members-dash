<?php
session_start();
require_once('DBconnection.php');
$email = $_SESSION['user_email'];

// Function to sanitize user input
function sanitize_input($data)
{
    return htmlspecialchars(stripslashes(trim($data)));
}

$documentDir = "../assets/Documents/MembersDocuments/";

// Get the current timestamp
$timestamp = time();

$response = []; // Initialize response array

$completionLetterPath = "";
if (isset($_FILES['completionLetter']) && $_FILES['completionLetter']['error'] === UPLOAD_ERR_OK) {
    $completionTmpName = $_FILES['completionLetter']['tmp_name'];
    $completionExtension = strtolower(pathinfo($_FILES['completionLetter']['name'], PATHINFO_EXTENSION));

    if ($completionExtension === 'pdf') {
        $completionLetterName = $email . '_' . $timestamp . '.' . $completionExtension;
        $completionLetterPath = $documentDir . $completionLetterName;
        
        if (move_uploaded_file($completionTmpName, $completionLetterPath)) {
            // Prepare and execute the update statement
            $stmt = $conn->prepare("UPDATE personalmembership SET completion_letter = ? WHERE email = ?");
            $stmt->bind_param("ss", $completionLetterPath, $email);
            
            if ($stmt->execute()) {
                $response['success'] = "Completion letter uploaded and path saved successfully.";
            } else {
                $response['errors'][] = "Failed to update the database.";
            }
            $stmt->close(); // Close the prepared statement
        } else {
            $response['errors'][] = "Failed to move the uploaded file.";
        }
    } else {
        $response['errors'][] = "Only PDF files are allowed for the completion letter.";
    }
} else {
    $response['errors'][] = "Error uploading completion letter.";
}

$conn->close(); // Close the database connection

$_SESSION['response'] = $response;
header("Location: " . $_SERVER['HTTP_REFERER']);
exit;
