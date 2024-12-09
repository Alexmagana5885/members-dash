<?php
session_start();
require_once 'DBconnection.php'; // Include the database connection

// Initialize response array
$response = array();

// Check if all required POST data is available
if (isset($_POST['ResetCode'], $_POST['UserEmailReset'], $_POST['NewPassWordReset'], $_POST['MembershipType'])) {
    $resetCode = $_POST['ResetCode'];
    $userEmail = $_POST['UserEmailReset'];
    $newPassword = $_POST['NewPassWordReset'];
    $membershipType = $_POST['MembershipType'];

    // Check if OTP is stored in the session
    if (isset($_SESSION['otp']) && $_SESSION['otp'] == $resetCode) {
        // Check if OTP has expired
        if (time() > $_SESSION['otp_expiry']) {
            $response['status'] = 'error';
            $response['message'] = 'OTP has expired.';
        } else {
            // OTP is valid and not expired, proceed with updating password

            // Prepare password for updating (hash it before storing)
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

            // Update password in the appropriate table based on membership type
            if ($membershipType == 'IndividualMember') {
                $query = "UPDATE personalmembership SET password = ? WHERE email = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("ss", $hashedPassword, $userEmail);
            } elseif ($membershipType == 'OrganizationMember') {
                $query = "UPDATE organizationmembership SET password = ? WHERE organization_email = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("ss", $hashedPassword, $userEmail);
            } else {
                $response['status'] = 'error';
                $response['message'] = 'Invalid membership type.';
                echo json_encode($response);
                exit;
            }

            // Execute the query and check if the password was updated successfully
            if ($stmt->execute()) {
                // Clear OTP from session after successful password update
                unset($_SESSION['otp']);
                unset($_SESSION['otp_expiry']);
                unset($_SESSION['otp_email']);

                $response['status'] = 'success';
                $response['message'] = 'Password updated successfully.';
                header("Location: ../index.php");
            } else {
                $response['status'] = 'error';
                $response['message'] = 'Failed to update password. Please try again later.';
                header("Location: " . $_SERVER['HTTP_REFERER']);
            }
        }
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Invalid or expired reset code.';
    }
} else {
    $response['status'] = 'error';
    $response['message'] = 'Missing required fields.';
}

// Return response in JSON format
echo json_encode($response);

// Close the database connection
$conn->close();
?>
