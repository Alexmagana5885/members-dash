<?php
session_start();
require_once 'DBconnection.php';

// Initialize response array
$response = [
    'success' => false,
    'message' => '',
    'errors' => []
];

if (isset($_POST['ResetCode'], $_POST['UserEmailReset'], $_POST['NewPassWordReset'], $_POST['MembershipType'])) {
    $resetCode = $_POST['ResetCode']; // The OTP entered by the user
    $userEmail = $_POST['UserEmailReset'];
    $newPassword = $_POST['NewPassWordReset'];
    $membershipType = $_POST['MembershipType'];

    // Check if OTP is stored in the session
    if (isset($_SESSION['otp']) && isset($_SESSION['otp_expiry']) && time() <= $_SESSION['otp_expiry']) {
        // Verify the entered OTP against the hashed OTP in the session
        if (password_verify($resetCode, $_SESSION['otp'])) {
            // OTP is valid and not expired, proceed with password reset
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

            // Update the password based on the membership type
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
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Invalid or expired OTP code.';
        }
    } else {
        $response['status'] = 'error';
        $response['message'] = 'OTP has expired or is not set.';
    }
} else {
    $response['status'] = 'error';
    $response['message'] = 'Missing required fields.';
}

echo json_encode($response);
