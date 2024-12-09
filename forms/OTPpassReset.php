<?php
session_start();
require_once 'DBconnection.php';

$response = array();

if (isset($_POST['resetemail'])) {
    $email = $_POST['resetemail'];
    $query = "SELECT * FROM personalmembership WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $personalUser = $stmt->get_result()->fetch_assoc();

    if (!$personalUser) {
        $query = "SELECT * FROM organizationmembership WHERE organization_email = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $organizationUser = $stmt->get_result()->fetch_assoc();
    }

    if (!$personalUser && !$organizationUser) {
        $response['status'] = 'error';
        $response['message'] = 'The email is not registered.';
    } else {
        $otp = rand(100000, 999999);
        $expiryTime = time() + 1800;

        $_SESSION['otp'] = $otp;
        $_SESSION['otp_expiry'] = $expiryTime;
        $_SESSION['otp_email'] = $email;

        $to = $email;
        $subject = "Password Reset OTP";
        $message = "Your OTP code is: $otp. It will expire in 30 minutes.";
        $headers = "From: info@agl.or.ke";

        if (mail($to, $subject, $message, $headers)) {
            $response['status'] = 'success';
            $response['message'] = 'OTP has been sent to your email address.';
            $response['action'] = 'show_new_password_form';
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Failed to send OTP. Please try again later.';
        }
    }
} else {
    $response['status'] = 'error';
    $response['message'] = 'Email is required.';
}

$_SESSION['response'] = $response;
echo json_encode($response);
$conn->close();
?>
