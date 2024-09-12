<?php
session_start(); // Start the session

include 'AGLdbconnection.php';
header("Content-Type: application/json");

// Initialize response array
$response = [
    'success' => false,
    'message' => '',
    'errors' => []
];

// Read and log the callback response
$stkCallbackResponse = file_get_contents('php://input');
$logFile = "Mpesastkresponse.json";
$log = fopen($logFile, "a");
if ($log === false) {
    $response['errors'][] = "Failed to open log file: $logFile";
    $_SESSION['response'] = $response;
    exit;
} else {
    fwrite($log, $stkCallbackResponse);
    fclose($log);
}

// Decode the JSON response
$data = json_decode($stkCallbackResponse);

if (json_last_error() !== JSON_ERROR_NONE) {
    $response['errors'][] = "Failed to decode JSON: " . json_last_error_msg();
    $_SESSION['response'] = $response;
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
    $emailQuery = "SELECT email FROM mpesa_transactions WHERE CheckoutRequestID = '$CheckoutRequestID'";
    $result = mysqli_query($db, $emailQuery);
    
    if (!$result) {
        $response['errors'][] = "Database query failed: " . mysqli_error($db);
        $_SESSION['response'] = $response;
        exit;
    }
    
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $email = $row['email'];

        // Update the personalmembership table with payment details
        $updateQuery = "UPDATE personalmembership 
                        SET payment_Number = '$UserPhoneNumber', payment_code = '$TransactionId' 
                        WHERE email = '$email'";
        if (!mysqli_query($db, $updateQuery)) {
            $response['errors'][] = "Database update failed: " . mysqli_error($db);
            $_SESSION['response'] = $response;
            exit;
        }
        
        if (mysqli_affected_rows($db) > 0) {
            // Email details
            $to = $email;
            $subject = "Payment Confirmation";
            $message = "
                Dear Member,

                Thank you for your recent payment towards your membership with AGL. We are pleased to inform you that your payment has been successfully processed.

                Payment Amount: $Amount
                Transaction Date: " . date('Y-m-d H:i:s') . "

                Best regards,
                AGL Team
            ";
            $headers = "From: payments@agl.or.ke\r\n";
            $headers .= "Reply-To: payments@agl.or.ke\r\n";
            $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
            
            // Send email
            if (!mail($to, $subject, $message, $headers)) {
                $response['errors'][] = "Failed to send email to $to";
                $_SESSION['response'] = $response;
                exit;
            }

            $response['success'] = true;
            $response['message'] = "Payment successfully processed and confirmation email sent.";
        } else {
            $response['errors'][] = "No rows affected during database update for email: $email";
        }
    } else {
        $response['errors'][] = "No email found for CheckoutRequestID: $CheckoutRequestID";
    }
} else {
    $response['errors'][] = "Transaction failed with ResultCode: $ResultCode and ResultDesc: $ResultDesc";
}

$_SESSION['response'] = $response;
?>
