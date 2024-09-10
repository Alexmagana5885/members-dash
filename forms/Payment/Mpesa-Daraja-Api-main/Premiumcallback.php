<?php
include 'AGLdbconnection.php';
header("Content-Type: application/json");

// Read and log the callback response
$stkCallbackResponse = file_get_contents('php://input');
$logFile = "PremiumMpesastkresponse.json";
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
$Amount = $data->Body->stkCallback->CallbackMetadata->Item[3]->Value;
$TransactionId = $data->Body->stkCallback->CallbackMetadata->Item[1]->Value;
$UserPhoneNumber = $data->Body->stkCallback->CallbackMetadata->Item[4]->Value;
$MemberEmail = $data->Body->stkCallback->CallbackMetadata->Item[0]->Value; // Assuming email is at index 0

// Check if the transaction was successful
if ($ResultCode == 0) {
    // Prepare and execute the SQL query to insert the data into member_payments table
    $stmt = $db->prepare("INSERT INTO member_payments (member_email, phone_number, payment_code, amount) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssd", $MemberEmail, $UserPhoneNumber, $TransactionId, $Amount);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Payment recorded successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to record payment']);
    }

    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Transaction failed']);
}

// Close the database connection
$db->close();
?>
