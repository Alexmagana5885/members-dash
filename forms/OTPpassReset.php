<?php
// Turn on error reporting (for debugging)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Start session at the very top
session_start();

// Start output buffering to avoid output issues
ob_start();

require_once 'DBconnection.php';

$response = array();

if (isset($_POST['resetemail'])) {
    $email = trim($_POST['resetemail']);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['status'] = 'error';
        $response['message'] = 'Invalid email address.';
    } else if (isset($_SESSION['otp_last_sent']) && (time() - $_SESSION['otp_last_sent']) < 60) {
        $response['status'] = 'error';
        $response['message'] = 'Please wait before requesting a new OTP.';
    } else {
        $_SESSION['otp_last_sent'] = time();

        $query = "SELECT * FROM personalmembership WHERE email = ? 
                  UNION 
                  SELECT * FROM organizationmembership WHERE organization_email = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $email, $email);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();

        if (!$user) {
            $response['status'] = 'error';
            $response['message'] = 'The email is not registered.';
        } else {
            $otp = rand(100000, 999999);
            $_SESSION['otp'] = password_hash($otp, PASSWORD_DEFAULT);
            $_SESSION['otp_expiry'] = time() + 900; 
            $_SESSION['otp_email'] = $email;

            if (!mail($email, "OTP", "Your OTP is $otp", "From: info@agl.or.ke")) {
                $response['status'] = 'error';
                $response['message'] = 'Failed to send OTP.';
            } else {
                $response['status'] = 'success';
                $response['message'] = 'OTP sent to your email.';
            }
        }
    }
} else {
    $response['status'] = 'error';
    $response['message'] = 'Email is required.';
}

$_SESSION['response'] = $response;

header("Location: " . $_SERVER['HTTP_REFERER']);
exit;

$conn->close();
ob_end_flush();
?>
