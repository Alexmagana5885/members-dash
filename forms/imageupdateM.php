<?php
session_start();
require_once('DBconnection.php');
$email = $_SESSION['user_email'];

// Function to sanitize user input
function sanitize_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

$response = ['success' => false, 'message' => '', 'errors' => []];

// File upload directory
$imageDir = "../assets/img/MembersProfile/";

// Get the current timestamp
$timestamp = time();

// Handle passport image upload
$passportImagePath = "";
if ($_FILES['passport']['error'] === UPLOAD_ERR_OK) {
    $passportTmpName = $_FILES['passport']['tmp_name'];
    $passportExtension = strtolower(pathinfo($_FILES['passport']['name'], PATHINFO_EXTENSION));

    if (in_array($passportExtension, ['jpg', 'jpeg', 'png'])) {
        $passportImageName = $email . '_' . $timestamp . '.' . $passportExtension;
        $passportImagePath = $imageDir . $passportImageName;

        // Move the uploaded file to the specified directory
        if (move_uploaded_file($passportTmpName, $passportImagePath)) {
            // Prepare and execute the SQL statement to update the database
            $stmt = $conn->prepare("UPDATE personalmembership SET passport_image = ? WHERE email = ?");
            $stmt->bind_param("ss", $passportImagePath, $email);

            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = "Passport image updated successfully.";
            } else {
                $response['errors'][] = "Database update failed.";
            }

            $stmt->close(); // Close the statement
        } else {
            $response['errors'][] = "Failed to move uploaded file.";
        }
    } else {
        $response['errors'][] = "Only JPG, JPEG, and PNG files are allowed for the passport image.";
    }
} else {
    $response['errors'][] = "Error uploading passport image.";
}

$conn->close(); // Close the database connection

// Store the response in the session and redirect
$_SESSION['response'] = $response;
header("Location: " . $_SERVER['HTTP_REFERER']);
exit();
