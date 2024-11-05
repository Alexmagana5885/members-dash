<?php
session_start();
require_once('DBconnection.php');
$email = $_SESSION['user_email'];

// Function to sanitize user input
function sanitize_input($data)
{
    return htmlspecialchars(stripslashes(trim($data)));
}

$documentDir = "../assets/Documents/orgMembersDocuments/";


// Get the current timestamp
$timestamp = time();

$response = []; // Initialize response array

$registration_certificatePath = "";
if (isset($_FILES['registration_certificate']) && $_FILES['registration_certificate']['error'] === UPLOAD_ERR_OK) {
    $registration_certificateTmpName = $_FILES['registration_certificate']['tmp_name'];
    $registration_certificateExtension = strtolower(pathinfo($_FILES['registration_certificate']['name'], PATHINFO_EXTENSION));

    if ($registration_certificateExtension === 'pdf') {
        $registration_certificateName = $email . '_' . $timestamp . '.' . $registration_certificateExtension;
        $registration_certificatePath = $documentDir . $registration_certificateName;
        
        if (move_uploaded_file($registration_certificateTmpName, $registration_certificatePath)) {
            // Prepare and execute the update statement
            $stmt = $conn->prepare("UPDATE organizationmembership SET registration_certificate = ? WHERE organization_email = ?");
            $stmt->bind_param("ss", $registration_certificatePath, $email); 

            if ($stmt->execute()) {
                $response['success'] = "registration_certificate letter uploaded and path saved successfully.";
            } else {
                $response['errors'][] = "Failed to update the database.";
            }
            $stmt->close(); // Close the prepared statement
        } else {
            $response['errors'][] = "Failed to move the uploaded file.";
        }
    } else {
        $response['errors'][] = "Only PDF files are allowed for the registration_certificate letter.";
    }
} else {
    $response['errors'][] = "Error uploading registration_certificate letter.";
}

$conn->close(); // Close the database connection

$_SESSION['response'] = $response;

header("Location: " . $_SERVER['HTTP_REFERER']);
exit;
