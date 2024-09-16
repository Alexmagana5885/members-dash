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
            if ($stmt) {
                $stmt->bind_param('s', $email);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    if (password_verify($password, $row['password'])) {
                        // Regenerate session ID to prevent session fixation
                        session_regenerate_id(true);

                        $_SESSION['loggedin'] = true;
                        $_SESSION['user_email'] = $email;  // Set the email in 'user_email'
                        
                        // Determine the role
                        if ($email === 'eugeneadmin@agl.or.ke' || $email === 'maganaadmin@agl.or.ke') {
                            $_SESSION['role'] = 'superadmin';
                        } else {
                            // Check membership type and redirection for other users
                            if ($membershipType == 'IndividualMember') {
                                // Check if the user is also in the officialsmembers table
                                $sqlCheck = "SELECT * FROM officialsmembers WHERE personalmembership_email = ?";
                                $stmtCheck = $conn->prepare($sqlCheck);
                                if ($stmtCheck) {
                                    $stmtCheck->bind_param('s', $email);
                                    $stmtCheck->execute();
                                    $resultCheck = $stmtCheck->get_result();

                                    if ($resultCheck->num_rows > 0) {
                                        $_SESSION['role'] = 'admin';
                                        error_log("User set as admin");
                                    } else {
                                        $_SESSION['role'] = 'member';
                                        error_log("User set as member");
                                    }
                                } else {
                                    $response['status'] = 'error';
                                    $response['message'] = 'Database query error (role check).';
                                    echo json_encode($response);
                                    exit();
                                }
                            } elseif ($membershipType == 'OrganizationMember') {
                                $_SESSION['role'] = 'member';
                                error_log("Organization member set as member");
                            }
                        }

                        // Debugging session values
                        error_log("Session role: " . $_SESSION['role']);
                        
                        // Redirect to the appropriate page based on membership type
                        if ($membershipType == 'OrganizationMember') {
                            $response['status'] = 'success';
                            $response['redirect'] = 'pages/Organizationpage.php'; // Redirect to Organization page
                        } else {
                            $response['status'] = 'success';
                            $response['redirect'] = 'pages/AGLADMIN.php'; // Redirect to common page for others
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
