<?php
session_start();
require_once 'DBconnection.php';

$response = array();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $membershipType = trim($_POST['MembershipType']);

    if (!empty($email) && !empty($password) && !empty($membershipType)) {
        // Determine the table and email column based on MembershipType
        $table = '';
        $emailColumn = '';

        if ($membershipType == 'IndividualMember') {
            $table = 'personalmembership';
            $emailColumn = 'email';
        } elseif ($membershipType == 'OrganizationMember') {
            $table = 'organizationmembership';
            $emailColumn = 'organization_email';
        }

        if ($table && $emailColumn) {
            $sql = "SELECT $emailColumn, password FROM $table WHERE $emailColumn = ?";
            $stmt = $conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param('s', $email);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    if (password_verify($password, $row['password'])) {
                        session_regenerate_id(true);
                        $_SESSION['loggedin'] = true;
                        $_SESSION['user_email'] = $email;
                        
                        // Set role based on email or membership type
                        if ($email === 'eugeneadmin@agl.or.ke' || $email === 'maganaalex634@gmail.com') {
                            $_SESSION['role'] = 'superadmin';
                        } else {
                            // Check for admin role in officialsmembers table
                            if ($membershipType == 'IndividualMember') {
                                $sqlCheck = "SELECT * FROM officialsmembers WHERE personalmembership_email = ?";
                                $stmtCheck = $conn->prepare($sqlCheck);
                                if ($stmtCheck) {
                                    $stmtCheck->bind_param('s', $email);
                                    $stmtCheck->execute();
                                    $resultCheck = $stmtCheck->get_result();

                                    $_SESSION['role'] = $resultCheck->num_rows > 0 ? 'admin' : 'member';
                                } else {
                                    $response['status'] = 'error';
                                    $response['message'] = 'Database query error (role check).';
                                    echo json_encode($response);
                                    exit();
                                }
                            } else {
                                $_SESSION['role'] = 'member';
                            }
                        }

                        // Generate OTP and set expiration
                        $otp = rand(100000, 999999);
                        $_SESSION['otp'] = $otp;
                        $_SESSION['otp_expiration'] = time() + 300; // 5 minutes from now

                        // Send OTP to email 
                        $subject = "Your OTP Code";
                        $message = "Your OTP code is: $otp. It will expire in 5 minutes.";
                        $headers = "From: noreply@agl.or.ke";

                        if (mail($email, $subject, $message, $headers)) {
                            $response['status'] = 'otp_sent';
                            $response['message'] = 'OTP has been sent to your email.';
                        } else {
                            $response['status'] = 'error';
                            $response['message'] = 'Failed to send OTP. Please try again.';
                        }
                    } else {
                        $response['status'] = 'error';
                        $response['message'] = 'Invalid password.';
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
            $response['message'] = 'Invalid Membership Type.';
        }
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Please fill in all fields.';
    }
}

echo json_encode($response);
?>
