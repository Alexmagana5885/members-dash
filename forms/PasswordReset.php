<?php
session_start();
require_once 'DBconnection.php';

// Set the response headers to return JSON
header('Content-Type: application/json');

// Initialize response array
$response = [
    'success' => false,
    'message' => '',
    'errors' => []
];

try {
    if (isset($_POST['ResetCode'], $_POST['UserEmailReset'], $_POST['NewPassWordReset'], $_POST['MembershipType'])) {
        $resetCode = trim($_POST['ResetCode']); 
        $userEmail = trim($_POST['UserEmailReset']);
        $newPassword = trim($_POST['NewPassWordReset']);
        $membershipType = trim($_POST['MembershipType']);

        if (empty($resetCode) || empty($userEmail) || empty($newPassword) || empty($membershipType)) {
            throw new Exception('All fields are required.');
        }

        if (!isset($_SESSION['otp']) || !isset($_SESSION['otp_expiry']) || time() > $_SESSION['otp_expiry']) {
            throw new Exception('OTP has expired or is not set.');
        }

        if (!password_verify($resetCode, $_SESSION['otp'])) {
            throw new Exception('Invalid or expired OTP code.');
        }

        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

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

        unset($_SESSION['otp'], $_SESSION['otp_expiry'], $_SESSION['otp_email']);
        
        $response['success'] = true;
        $response['message'] = 'Password updated successfully.';
    } else {
        throw new Exception('Missing required fields.');
    }
} catch (Exception $e) {
    $errorMessage = $e->getMessage();
    error_log("Error: " . $errorMessage, 3, 'error_log.txt');
    $response['errors'][] = $errorMessage;
    $response['message'] = 'An error occurred while processing your request.';
} finally {
    echo json_encode($response);
    exit;
}
?>
