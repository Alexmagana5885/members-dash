<?php
session_start();
require_once 'DBconnection.php'; // Include the database connection

// Initialize response array
$response = array();

// Check if email is provided
if (isset($_POST['resetemail'])) {
    $email = $_POST['resetemail'];

    // Check if email exists in personalmembership table
    $query = "SELECT * FROM personalmembership WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $personalUser = $stmt->get_result()->fetch_assoc();

    // If not found, check in organizationmembership table
    if (!$personalUser) {
        $query = "SELECT * FROM organizationmembership WHERE organization_email = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $organizationUser = $stmt->get_result()->fetch_assoc();
    }

    // If email is not registered in either table
    if (!$personalUser && !$organizationUser) {
        $response['status'] = 'error';
        $response['message'] = 'The email is not registered.';
    } else {
        // Email exists, generate OTP
        $otp = rand(100000, 999999);
        $expiryTime = time() + 1800; // OTP expires in 30 minutes

        // Store OTP and expiry time in session (for verification later)
        $_SESSION['otp'] = $otp;
        $_SESSION['otp_expiry'] = $expiryTime;
        $_SESSION['otp_email'] = $email;

        // Send OTP email
        $to = $email;
        $subject = "Password Reset OTP";
        $message = "Your OTP code is: $otp. It will expire in 30 minutes.";
        $headers = "From: info@agl.or.ke";

        // Send email
        if (mail($to, $subject, $message, $headers)) {
            $response['status'] = 'success';
            $response['message'] = 'OTP has been sent to your email address.';
            header('Location: ../../index.php');
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Failed to send OTP. Please try again later.';
            header('Location: ../../index.php');
        }
    }
} else {
    $response['status'] = 'error';
    $response['message'] = 'Email is required.';
}

// Return response in JSON format
echo json_encode($response);

// Close the database connection
$conn->close();
?>
