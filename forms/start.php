<?php
session_start();
require_once 'DBconnection.php';

$response = array();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $membershipType = trim($_POST['MembershipType']);

    if (!empty($email) && !empty($password) && !empty($membershipType)) {
        // Determine the table and email column to query based on MembershipType
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
            // Prepare the SQL statement
            $sql = "SELECT $emailColumn, password FROM $table WHERE $emailColumn = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                if (password_verify($password, $row['password'])) {
                    // Check if the email is one of the admin emails
                    if ($email === 'eugeneadmin@agl.or.ke' || $email === 'maganaadmin@agl.or.ke') {
                        $_SESSION['loggedin'] = true;
                        $_SESSION['email'] = $email;
                        $response['status'] = 'success';
                        $response['redirect'] = 'pages/AGLADMIN.php';
                    } else {
                        // Check membership type and redirection for other users
                        if ($membershipType == 'IndividualMember') {
                            // Check if the user is also in the officialsmembers table
                            $sqlCheck = "SELECT * FROM officialsmembers WHERE personalmembership_email = ?";
                            $stmtCheck = $conn->prepare($sqlCheck);
                            $stmtCheck->bind_param('s', $email);
                            $stmtCheck->execute();
                            $resultCheck = $stmtCheck->get_result();

                            if ($resultCheck->num_rows > 0) {
                                $_SESSION['loggedin'] = true;
                                $_SESSION['email'] = $email;
                                $response['status'] = 'success';
                                $response['redirect'] = 'pages/AdminMember.php';
                            } else {
                                $_SESSION['loggedin'] = true;
                                $_SESSION['email'] = $email;
                                $response['status'] = 'success';
                                $response['redirect'] = 'pages/MembersPortal.php';
                            }
                        } elseif ($membershipType == 'OrganizationMember') {
                            $_SESSION['loggedin'] = true;
                            $_SESSION['email'] = $email;
                            $response['status'] = 'success';
                            $response['redirect'] = 'pages/MembersPortal.php';
                        }
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
            $response['message'] = 'Invalid Membership Type.';
        }
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Please fill in all fields.';
    }
}

echo json_encode($response);
?>
