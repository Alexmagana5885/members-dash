<?php
session_start();
require_once 'DBconnection.php';

$response = array();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['resetemail'])) {
    $email = trim($_POST['resetemail']);

    if (!empty($email)) {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            if (!isset($_SESSION['otp_last_sent']) || (time() - $_SESSION['otp_last_sent']) >= 10) {
                $_SESSION['otp_last_sent'] = time();

                $query = "SELECT email FROM personalmembership WHERE email = ? 
                          UNION 
                          SELECT organization_email FROM organizationmembership WHERE organization_email = ?";
                $stmt = $conn->prepare($query);

                if ($stmt) {
                    $stmt->bind_param("ss", $email, $email);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        // OTP generation and session setup
                        $otp = rand(100000, 999999);
                        $_SESSION['otp'] = $otp;
                        $_SESSION['otp_expiry'] = time() + 900; // 15 minutes expiry
                        $_SESSION['otp_email'] = $email;
                        $_SESSION['user_email'] = $email;

                        $subject = "Password Reset OTP";
                        $message = "Your OTP code is: $otp. It will expire in 15 minutes.";
                        $headers = "From: info@agl.or.ke";

                        if (mail($email, $subject, $message, $headers)) {
                            $response['status'] = 'success';
                            $response['message'] = 'OTP has been sent to your email.';
                            header('Location: ../pages/PasswordReset.php');
                        } else {
                            $response['status'] = 'error';
                            $response['message'] = 'Failed to send OTP. Please try again.';
                        }
                    } else {
                        $response['status'] = 'error';
                        $response['message'] = 'Email not found.';
                    }
                } else {
                    $response['status'] = 'error';
                    $response['message'] = 'Database query error (main query).';
                }
            } else {
                $response['status'] = 'error';
                $response['message'] = 'Please wait before requesting a new OTP.';
            }
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Invalid email address.';
        }
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Please fill in all fields.';
    }
}

// Return the response as JSON
echo json_encode($response);
?>
