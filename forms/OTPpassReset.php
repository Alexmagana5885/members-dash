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

        // Select only the email from both tables
        $query = "SELECT email FROM personalmembership WHERE email = ? 
                  UNION 
                  SELECT organization_email FROM organizationmembership WHERE organization_email = ?";
        
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

            $to = $email;
            $subject = "Password Reset OTP";
            $message = "Your OTP code is: $otp. It will expire in 15 minutes.";
            $headers = "From: info@agl.or.ke\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

            if (mail($to, $subject, $message, $headers)) {
                $response['status'] = 'success';
                $response['message'] = 'OTP has been sent to your email address.';
                $response['action'] = 'show_new_password_form';
            } else {
                $response['status'] = 'error';
                $response['message'] = 'Failed to send OTP. Please try again later.';
            }
        }
    }
} else {
    $response['status'] = 'error';
    $response['message'] = 'Email is required.';
}

$_SESSION['response'] = $response;

// header("Location: " . $_SERVER['HTTP_REFERER']);
exit;

$conn->close();
ob_end_flush();
?>
