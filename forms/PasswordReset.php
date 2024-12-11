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
            $response['status'] = 'error';
            $response['message'] = 'Please fill in all fields.';
            echo json_encode($response);
            exit;
        }

        // Check if OTP exists and is valid
        if (!isset($_SESSION['otp']) || !isset($_SESSION['otp_expiry']) || time() > $_SESSION['otp_expiry']) {
            $response['status'] = 'error';
            $response['message'] = 'OTP has expired or is not set.';
            echo json_encode($response);
            exit;
        }

        if (!password_verify($resetCode, $_SESSION['otp'])) {
            $response['status'] = 'error';
            $response['message'] = 'Invalid or expired OTP code.';
            echo json_encode($response);
            exit;
        }

        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        // Get user email from session
        $userEmail = $_SESSION['user_email'] ?? null;

        if (!$userEmail) {
            $response['status'] = 'error';
            $response['message'] = 'User email is not available in the session.';
            echo json_encode($response);
            exit;
        }

        // Determine table and email column based on membership type
        if ($membershipType === 'IndividualMember') {
            $query = "SELECT email FROM personalmembership WHERE email = ?";
            $emailColumn = 'email';
        } elseif ($membershipType === 'OrganizationMember') {
            $query = "SELECT organization_email FROM organizationmembership WHERE organization_email = ?";
            $emailColumn = 'organization_email';
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Invalid membership type.';
            echo json_encode($response);
            exit;
        }

        $stmt = $conn->prepare($query);
        if (!$stmt) {
            $response['status'] = 'error';
            $response['message'] = 'Database query preparation failed.';
            echo json_encode($response);
            exit;
        }

        $stmt->bind_param("s", $userEmail);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows === 0) {
            $response['status'] = 'error';
            $response['message'] = 'The email provided is not registered.';
            echo json_encode($response);
            exit;
        }

        // Update password based on membership type
        if ($membershipType === 'IndividualMember') {
            $updateQuery = "UPDATE personalmembership SET password = ? WHERE email = ?";
        } else {
            $updateQuery = "UPDATE organizationmembership SET password = ? WHERE organization_email = ?";
        }

        $stmt = $conn->prepare($updateQuery);
        if (!$stmt) {
            $response['status'] = 'error';
            $response['message'] = 'Database query preparation failed.';
            echo json_encode($response);
            exit;
        }

        $stmt->bind_param("ss", $hashedPassword, $userEmail);

        if (!$stmt->execute()) {
            $response['status'] = 'error';
            $response['message'] = 'Failed to update password. Please try again later.';
            echo json_encode($response);
            exit;
        }

        // Clear OTP and session variables
        unset($_SESSION['otp'], $_SESSION['otp_expiry'], $_SESSION['otp_email'], $_SESSION['user_email']);

        // Set response for success
        $response['status'] = 'success';
        $response['message'] = 'Password updated successfully.';
        $response['redirect'] = 'index.php'


    } else {
        $response['status'] = 'error';
        $response['message'] = 'Missing required fields.';
    }
} catch (Exception $e) {
    // Handle exceptions and log error
    $response['status'] = 'error';
    $response['message'] = 'An error occurred while processing your request.';
    $response['errors'][] = $e->getMessage();

    // Log detailed error for internal tracking
    error_log("Error: " . $e->getMessage(), 3, 'error_log.txt');
} finally {
    // Return the JSON response
    echo json_encode($response);
    exit;
}
