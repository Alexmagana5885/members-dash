<?php
session_start(); // Start the session

include 'AGLdbconnection.php'; // Include your database connection file

header("Content-Type: application/json");

// Read and log the callback response
$stkCallbackResponse = file_get_contents('php://input');
$logFile = "PremiumMpesastkresponse.json";
file_put_contents($logFile, $stkCallbackResponse . PHP_EOL, FILE_APPEND);

// Decode the JSON response
$data = json_decode($stkCallbackResponse);

// Check if decoding was successful
if (json_last_error() !== JSON_ERROR_NONE) {
    $_SESSION['response'] = [
        'success' => false,
        'message' => 'JSON decoding error: ' . json_last_error_msg()
    ];
    http_response_code(400); // Bad request
    exit;
}

// Extract relevant data from the response
$MerchantRequestID = $data->Body->stkCallback->MerchantRequestID ?? null;
$CheckoutRequestID = $data->Body->stkCallback->CheckoutRequestID ?? null;
$ResultCode = $data->Body->stkCallback->ResultCode ?? null;
$ResultDesc = $data->Body->stkCallback->ResultDesc ?? null;
$Amount = $data->Body->stkCallback->CallbackMetadata->Item[0]->Value ?? null;
$TransactionId = $data->Body->stkCallback->CallbackMetadata->Item[1]->Value ?? null;
$UserPhoneNumber = $data->Body->stkCallback->CallbackMetadata->Item[4]->Value ?? null;

// Check if the transaction was successful
if ($ResultCode == 0) {
    // Retrieve the email associated with the CheckoutRequestID
    $stmt = $conn->prepare("SELECT email FROM mpesa_transactions WHERE CheckoutRequestID = ?");
    $stmt->bind_param('s', $CheckoutRequestID);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $email = $row['email'];

        // Insert the payment details into the member_payments table
        $timestamp = date('Y-m-d H:i:s'); // Current timestamp
        $insertStmt = $conn->prepare("INSERT INTO member_payments (member_email, phone_number, payment_code, amount, timestamp) VALUES (?, ?, ?, ?, ?)");
        $insertStmt->bind_param('sssss', $email, $UserPhoneNumber, $TransactionId, $Amount, $timestamp);
        $insertStmt->execute();
   
        if ($insertStmt->affected_rows > 0) {
            // Send a confirmation email
            $to = $email;
            $subject = "Payment Confirmation";
            $message = "Dear User,\n\nThank you for your Member payment of Ksh $Amount.\n\nTransaction ID: $TransactionId\n\nBest regards,\nAGL Team";
            $headers = "From: payments@agl.or.ke\r\n";
            $headers .= "Reply-To: payments@agl.or.ke\r\n";
            $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

            if (mail($to, $subject, $message, $headers)) {
                $_SESSION['response'] = [
                    'success' => true,
                    'message' => 'Payment processed successfully. Confirmation email sent.'
                ];
            } else {
                // Handle email sending failure
                $_SESSION['response'] = [
                    'success' => false,
                    'message' => 'Failed to send confirmation email.'
                ];
            }
        } else {
            // Handle the case where the insert did not succeed
            $_SESSION['response'] = [
                'success' => false,
                'message' => 'Failed to insert payment details.'
            ];
        }
        $insertStmt->close();
    } else {
        // Handle the case where no email was found
        $_SESSION['response'] = [
            'success' => false,
            'message' => 'No email found for CheckoutRequestID.'
        ];
    }
    $stmt->close();
} else {
    // Handle unsuccessful transaction response
    $_SESSION['response'] = [
        'success' => false,
        'message' => 'Transaction failed: ' . $ResultDesc
    ];
}

$conn->close();
?>
