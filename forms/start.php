<?php
session_start();
require_once 'DBconnection.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

   
    if (!empty($email) && !empty($password)) {

        $stmt = $conn->prepare("SELECT email, password FROM personalmembership WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // Verify the provided password with the hashed password in the database
            if (password_verify($password, $user['password'])) {

                $_SESSION['user_email'] = $user['email']; 
                header("Location: ../pages/AGLADMIN.php"); 
            } else {

                echo "Invalid email or password.";
            }
        } else {
  
            echo "Invalid email or password.";
        }


        $stmt->close();
        $conn->close();
    } else {
        echo "Please enter both email and password.";
    }
} else {
    echo "Invalid request method.";
}
?>
