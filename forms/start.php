<?php
session_start();
require_once 'DBconnection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $membershipType = trim($_POST['MembershipType']);

    if (!empty($email) && !empty($password) && !empty($membershipType)) {
        if ($membershipType == 'Default') {
            echo "Please select a valid Membership Type.";
        } else {
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

                // Check for errors in preparing the statement
                if (!$stmt) {
                    die("Failed to prepare SQL statement: " . $conn->error);
                }

                // Bind parameters and execute the query
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows === 1) {
                    $user = $result->fetch_assoc();
                    
                    // Verify the provided password with the hashed password in the database
                    if (password_verify($password, $user['password'])) {
                        $_SESSION['user_email'] = $user[$emailColumn]; 
                        header("Location: ../pages/AGLADMIN.php"); 
                    } else {
                        echo "Invalid email or password.";
                    }
                } else {
                    echo "Invalid email or password.";
                }

                $stmt->close();
            } else {
                echo "Invalid membership type.";
            }
        }
        $conn->close();
    } else {
        echo "Please enter all required fields.";
    }
} else {
    echo "Invalid request method.";
}
?>
