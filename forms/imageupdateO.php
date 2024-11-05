<?php
session_start();
require_once('DBconnection.php');
$organization_email = $_SESSION['user_email'];


// Function to sanitize user input
function sanitize_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

$response = ['success' => false, 'message' => '', 'errors' => []];

// File upload directory
$imageDir = "../assets/img/MembersProfile/orgMembers/";


// Get the current timestamp
$timestamp = time();

// Handle profile image upload
$profileImagePath = "";
if ($_FILES['profileImage']['error'] === UPLOAD_ERR_OK) {
    $profileTmpName = $_FILES['profileImage']['tmp_name'];
    $profileExtension = strtolower(pathinfo($_FILES['profileImage']['name'], PATHINFO_EXTENSION));

    if (in_array($profileExtension, ['jpg', 'jpeg', 'png'])) {
        $profileImageName = $organization_email . '_' . $timestamp . '.' . $profileExtension;
        $profileImagePath = $imageDir . $profileImageName;

        // Move the uploaded file to the specified directory
        if (move_uploaded_file($profileTmpName, $profileImagePath)) {
            // Prepare and execute the SQL statement to update the database
            $stmt = $conn->prepare("UPDATE organizationmembership SET logo_image = ? WHERE organization_email = ?");
            $stmt->bind_param("ss", $profileImagePath, $organization_email);

            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = "Profile image updated successfully.";
            } else {
                $response['errors'][] = "Database update failed.";
            }

            $stmt->close(); // Close the statement
        } else {
            $response['errors'][] = "Failed to move uploaded file.";
        }
    } else {
        $response['errors'][] = "Only JPG, JPEG, and PNG files are allowed for the profile image.";
    }
} else {
    $response['errors'][] = "Error uploading profile image.";
}

$conn->close(); // Close the database connection

// Store the response in the session and redirect
$_SESSION['response'] = $response;
header("Location: " . $_SERVER['HTTP_REFERER']);
exit();
?>
