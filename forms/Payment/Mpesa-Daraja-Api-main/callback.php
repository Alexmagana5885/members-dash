<?php
include 'DB_connection.php'; // Ensure this file contains the proper database connection setup
header("Content-Type: application/json");

$logFile = "Mpesastkresponse.json";
$log = fopen($logFile, "a");

// Read the M-Pesa callback response
$stkCallbackResponse = file_get_contents('php://input');
fwrite($log, "M-Pesa callback response: " . $stkCallbackResponse . "\n");

$data = json_decode($stkCallbackResponse);

if (json_last_error() === JSON_ERROR_NONE) {
    $stkCallback = $data->Body->stkCallback ?? null;

    $MerchantRequestID = $stkCallback->MerchantRequestID ?? null;
    $CheckoutRequestID = $stkCallback->CheckoutRequestID ?? null;
    $ResultCode = $stkCallback->ResultCode ?? null;
    $ResultDesc = $stkCallback->ResultDesc ?? null;
    $Amount = $stkCallback->CallbackMetadata->Item[0]->Value ?? null;
    $TransactionId = $stkCallback->CallbackMetadata->Item[1]->Value ?? null;
    $UserPhoneNumber = $stkCallback->CallbackMetadata->Item[4]->Value ?? null;
    $UserEmail = $stkCallback->CallbackMetadata->Item[3]->Value ?? null;

    if ($ResultCode == 0) {
        // Update payment data in the database
        $stmt = $db->prepare("UPDATE personalmembership 
                              SET payment_number = ?, payment_code = ? 
                              WHERE email = ?");
        if ($stmt) {
            $stmt->bind_param('sss', $UserPhoneNumber, $TransactionId, $UserEmail);

            if ($stmt->execute()) {
                fwrite($log, "Payment data updated successfully for email: $UserEmail.\n");
            } else {
                fwrite($log, "Error updating payment data: " . $stmt->error . "\n");
            }

            $stmt->close();
        } else {
            fwrite($log, "Prepare statement error: " . $db->error . "\n");
        }
    } else {
        fwrite($log, "Transaction failed. ResultCode: $ResultCode, ResultDesc: $ResultDesc\n");
    }
} else {
    fwrite($log, "JSON decode error: " . json_last_error_msg() . "\n");
}

fclose($log);
?>
