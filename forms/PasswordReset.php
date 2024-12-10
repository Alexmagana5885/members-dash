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

    // Check if OTP is stored in the session and not expired
    if (isset($_SESSION['otp']) && isset($_SESSION['otp_expiry']) && time() <= $_SESSION['otp_expiry']) {
        
        // Verify the entered OTP against the hashed OTP in the session
        if (password_verify($resetCode, $_SESSION['otp'])) {
            
            // OTP is valid and not expired, proceed with password reset
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

            if ($membershipType == 'IndividualMember') {
                $query = "SELECT email FROM personalmembership WHERE email = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("s", $userEmail);
            } elseif ($membershipType == 'OrganizationMember') {
                $query = "SELECT organization_email FROM organizationmembership WHERE organization_email = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("s", $userEmail);
            } else {
                $_SESSION['errors'][] = 'Invalid membership type.';
                header("Location: " . $_SERVER['HTTP_REFERER']);
                exit;
            }

            // Check if the email exists in the database
            $stmt->execute();
            $stmt->store_result();
            
            if ($stmt->num_rows > 0) {
                // Update the password
                if ($membershipType == 'IndividualMember') {
                    $query = "UPDATE personalmembership SET password = ? WHERE email = ?";
                } else {
                    $query = "UPDATE organizationmembership SET password = ? WHERE organization_email = ?";
                }
                
                $stmt = $conn->prepare($query);
                $stmt->bind_param("ss", $hashedPassword, $userEmail);

                if ($stmt->execute()) {
                    // Clear OTP from session after successful password update
                    unset($_SESSION['otp']);
                    unset($_SESSION['otp_expiry']);
                    unset($_SESSION['otp_email']);
                    
                    $_SESSION['success'] = 'Password updated successfully.';
                    header("Location: ../index.php");
                    exit;
                } else {
                    $_SESSION['errors'][] = 'Failed to update password. Please try again later.';
                    header("Location: " . $_SERVER['HTTP_REFERER']);
                    exit;
                }
            } else {
                $_SESSION['errors'][] = 'The email provided is not registered.';
                header("Location: " . $_SERVER['HTTP_REFERER']);
                exit;
            }
        } else {
            $_SESSION['errors'][] = 'Invalid or expired OTP code.';
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit;
        }
    } else {
        $_SESSION['errors'][] = 'OTP has expired or is not set.';
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit;
    }
} else {
    $_SESSION['errors'][] = 'Missing required fields.';
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit;
}
?>
