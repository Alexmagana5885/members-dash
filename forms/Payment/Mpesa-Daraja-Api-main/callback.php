<?php
include 'AGLdbconnection.php';
header("Content-Type: application/json");

// Read and log the callback response
$stkCallbackResponse = file_get_contents('php://input');
$logFile = "Mpesastkresponse.json";
$log = fopen($logFile, "a");
fwrite($log, $stkCallbackResponse);
fclose($log);

// Decode the JSON response
$data = json_decode($stkCallbackResponse);

// Extract relevant data from the response
$MerchantRequestID = $data->Body->stkCallback->MerchantRequestID;
$CheckoutRequestID = $data->Body->stkCallback->CheckoutRequestID;
$ResultCode = $data->Body->stkCallback->ResultCode;
$ResultDesc = $data->Body->stkCallback->ResultDesc;
$Amount = $data->Body->stkCallback->CallbackMetadata->Item[0]->Value;
$TransactionId = $data->Body->stkCallback->CallbackMetadata->Item[1]->Value;
$UserPhoneNumber = $data->Body->stkCallback->CallbackMetadata->Item[4]->Value;

// Check if the transaction was successful
if ($ResultCode == 0) {

    // Retrieve the email associated with the CheckoutRequestID
    $emailQuery = "SELECT email FROM mpesa_transactions WHERE CheckoutRequestID = '$CheckoutRequestID'";
    $result = mysqli_query($db, $emailQuery);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $email = $row['email'];

        // Update the personalmembership table with payment details
        $updateQuery = "UPDATE personalmembership 
                        SET payment_Number = '$UserPhoneNumber', payment_code = '$TransactionId' 
                        WHERE email = '$email'";
        mysqli_query($db, $updateQuery);
        
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
            mail($to, $subject, $message, $headers);
        } else {
            // Handle the case where the update did not affect any rows
            // This might occur if the email address does not match any row
        }
    } else {
        // Handle the case where no email was found
        // Log or notify the situation as required
    }
}
?>
