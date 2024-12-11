<?php
session_start();
require_once 'DBconnection.php';

// Initialize response array
$response = array();

try {
    if (isset($_POST['ResetCode'], $_POST['NewPassWordReset'], $_POST['MembershipType'])) {
        $resetCode = trim($_POST['ResetCode']); 
        $newPassword = trim($_POST['NewPassWordReset']);
        $membershipType = trim($_POST['MembershipType']);

        if (empty($resetCode) || empty($newPassword) || empty($membershipType)) {
            throw new Exception('Please fill in all fields.');
        }

        // Check if OTP exists and is valid
        if (!isset($_SESSION['otp']) || !isset($_SESSION['otp_expiry']) || time() > $_SESSION['otp_expiry']) {
            throw new Exception('OTP has expired or is not set.');
        }

        if (!password_verify($resetCode, $_SESSION['otp'])) {
            throw new Exception('Invalid or expired OTP code.');
        }

        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        // Get user email from session
        $userEmail = $_SESSION['user_email'] ?? null;

        if (!$userEmail) {
            throw new Exception('User email is not available in the session.');
        }

        // Determine table and email column based on membership type
        if ($membershipType === 'IndividualMember') {
            $query = "SELECT email FROM personalmembership WHERE email = ?";
            $emailColumn = 'email';
        } elseif ($membershipType === 'OrganizationMember') {
            $query = "SELECT organization_email FROM organizationmembership WHERE organization_email = ?";
            $emailColumn = 'organization_email';
        } else {
            throw new Exception('Invalid membership type.');
        }

        $stmt = $conn->prepare($query);
        if (!$stmt) {
            throw new Exception('Database query preparation failed: ' . $conn->error);
        }

        $stmt->bind_param("s", $userEmail);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows === 0) {
            throw new Exception('The email provided is not registered.');
        }

        // Update password based on membership type
        if ($membershipType === 'IndividualMember') {
            $updateQuery = "UPDATE personalmembership SET password = ? WHERE email = ?";
        } else {
            $updateQuery = "UPDATE organizationmembership SET password = ? WHERE organization_email = ?";
        }

        $stmt = $conn->prepare($updateQuery);
        if (!$stmt) {
            throw new Exception('Database query preparation failed: ' . $conn->error);
        }

        $stmt->bind_param("ss", $hashedPassword, $userEmail);

        if (!$stmt->execute()) {
            throw new Exception('Failed to update password. Please try again later.');
        }

        // Clear OTP and session variables
        unset($_SESSION['otp'], $_SESSION['otp_expiry'], $_SESSION['otp_email'], $_SESSION['user_email']);

        // Set response for success
        $response['status'] = 'success';
        $response['message'] = 'Password updated successfully.';
    } else {
        throw new Exception('Missing required fields.');
    }
} catch (Exception $e) {
    // Handle exceptions and log error
    $errorMessage = $e->getMessage();
    $response['status'] = 'error';
    $response['message'] = 'An error occurred while processing your request.';
    $response['errors'][] = $errorMessage;

    // Log detailed error for internal tracking
    error_log("Error: " . $errorMessage, 3, 'error_log.txt');
} finally {
    // Return the JSON response
    echo json_encode($response);
    exit;
}
