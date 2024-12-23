<?php
session_start();

$response = array();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userOtp = trim($_POST['otp']);  // OTP entered by the user

    if (!empty($userOtp)) {
        // Check if OTP and expiration exist in the session
        if (isset($_SESSION['otp']) && isset($_SESSION['otp_expiration'])) {
            $storedOtp = $_SESSION['otp'];
            $otpExpiration = $_SESSION['otp_expiration'];
            $currentTime = time();

            // Verify OTP and check if it's within the 5-minute expiration window
            if ($userOtp == $storedOtp && $currentTime <= $otpExpiration) {
                // OTP is correct and not expired, redirect user based on role
                if (isset($_SESSION['role'])) {
                    // Define redirect URL based on role
                    switch ($_SESSION['role']) {
                        case 'superadmin':
                            $response['status'] = 'success';
                            $response['redirect'] = 'pages/AGLADMIN.php'; // Superadmin page
                            break;

                        case 'admin':
                            $response['status'] = 'success';
                            $response['redirect'] = 'pages/AGLADMIN.php'; // Admin page
                            break;

                        case 'member':
                            // Check if the user is an organization member
                            if (isset($_SESSION['membership_type']) && $_SESSION['membership_type'] == 'OrganizationMember') {
                                $response['status'] = 'success';
                                $response['redirect'] = 'pages/Organizationpage.php'; // Organization member page
                            } else {
                                $response['status'] = 'success';
                                $response['redirect'] = 'pages/AGLADMIN.php'; // Regular member page
                            }
                            break;

                        default:
                            $response['status'] = 'error';
                            $response['message'] = 'Unknown role in session.';
                            break;
                    }
                    // Clear OTP from session after successful verification
                    unset($_SESSION['otp']);
                    unset($_SESSION['otp_expiration']);
                } else {
                    $response['status'] = 'error';
                    $response['message'] = 'User role not set in session.';
                }
            } else {
                // OTP is incorrect or expired
                $response['status'] = 'error';
                $response['message'] = 'Invalid or expired OTP. Please try again.';
            }
        } else {
            // OTP session data missing
            $response['status'] = 'error';
            $response['message'] = 'OTP session expired or not generated. Please request a new OTP.';
            header("Location: " . $_SERVER['HTTP_REFERER']);
        }
    } else {
        // OTP field is empty
        $response['status'] = 'error';
        $response['message'] = 'Please enter the OTP.';
    }
} else {
    $response['status'] = 'error';
    $response['message'] = 'Invalid request method.';
}

echo json_encode($response);
?>
