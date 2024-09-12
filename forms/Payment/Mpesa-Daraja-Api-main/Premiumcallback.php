<?php
include 'AGLdbconnection.php';
header("Content-Type: application/json");

// Read and log the callback response
$stkCallbackResponse = file_get_contents('php://input');
$logFile = "PremiumMpesastkresponse.json";
file_put_contents($logFile, $stkCallbackResponse . PHP_EOL, FILE_APPEND);

// Decode the JSON response
$data = json_decode($stkCallbackResponse);

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
        $insertStmt = $conn->prepare("INSERT INTO member_payments (member_email, phone_number, payment_code, amount, timestamp) 
                                      VALUES (?, ?, ?, ?, NOW())");
        $insertStmt->bind_param('ssss', $email, $UserPhoneNumber, $TransactionId, $Amount);
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
                // Email sent successfully
            } else {
                // Handle email sending failure
            }
        } else {
            // Handle the case where the insert did not succeed
            // This might occur if there was an issue with the database
        }
        $insertStmt->close();
    } else {
        // Handle the case where no email was found
        // Log or notify the situation as required
    }
    $stmt->close();
}

$conn->close();
?>
