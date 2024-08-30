<?php
session_start();
require_once 'DBconnection.php'; // Include your database connection settings

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare a SQL statement to fetch the user with the provided email
    $stmt = $conn->prepare("SELECT id, password FROM personalmembership WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // Verify the provided password with the hashed password in the database
        if (password_verify($password, $user['password'])) {
            // Password is correct, start a session for the user
            $_SESSION['user_id'] = $user['id'];
            header("Location:../pages/AGLADMIN.php"); // Redirect to a secure page after successful login
            exit();
        } else {
            // Invalid password
            echo "Invalid email or password.";
        }
    } else {
        // No user found with the provided email
        echo "Invalid email or password.";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request method.";
}
?>
