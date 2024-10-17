<?php
// Database connection
require_once('DBconnection.php');

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $name = $_POST["name"];
    $email = $_POST["email"];
    $paymentMethod = $_POST["paymentMethod"];
    $paymentCode = $_POST["paymentCode"];
    $timeOfPayment = date('Y-m-d H:i:s'); // Current timestamp for payment

    // Validate and sanitize input data (example)
    $name = filter_var($name, FILTER_SANITIZE_STRING);
    $email = filter_var($email, FILTER_VALIDATE_EMAIL);
    $paymentMethod = filter_var($paymentMethod, FILTER_SANITIZE_STRING);
    $paymentCode = filter_var($paymentCode, FILTER_SANITIZE_STRING);

    if (!$email) {
        echo "Invalid email format.";
        exit();
    }

    // Get member ID based on email
    $stmt = $conn->prepare("SELECT id FROM members WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($memberId);
    $stmt->fetch();

    if ($stmt->num_rows == 0) {
        echo "Member not found.";
        exit();
    }

    $stmt->close();

    // Insert payment details into the membershipPayments table
    $stmt = $conn->prepare("INSERT INTO membershipPayments (member_id, time_of_payment, method_of_payment, payment_code) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $memberId, $timeOfPayment, $paymentMethod, $paymentCode);

    if ($stmt->execute()) {
        echo "Payment details recorded successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>
