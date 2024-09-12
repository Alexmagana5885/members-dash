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
            // Update successful
            // Optionally, you can send a confirmation email or handle other actions
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
