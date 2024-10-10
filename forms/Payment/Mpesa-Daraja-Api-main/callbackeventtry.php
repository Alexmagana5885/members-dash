<?php
session_start(); // Start the session

require_once('../../DBconnection.php');

header("Content-Type: application/json");

$stkCallbackResponse = file_get_contents('php://input');
$logFile = "callbackEventR.json";
$log = fopen($logFile, "a");

if ($log) {
    fwrite($log, $stkCallbackResponse);
    fclose($log);
} else {
    // Log file write error
    $_SESSION['error'] = "Unable to write to the log file.";
    exit(json_encode(["status" => "error", "message" => $_SESSION['error']]));
}

$data = json_decode($stkCallbackResponse);
if (!$data) {
    $_SESSION['error'] = "Invalid JSON data received.";
    exit(json_encode(["status" => "error", "message" => $_SESSION['error']]));
}

// Extracting the necessary fields from the response
$MerchantRequestID = $data->Body->stkCallback->MerchantRequestID ?? null;
$CheckoutRequestID = $data->Body->stkCallback->CheckoutRequestID ?? null;
$ResultCode = $data->Body->stkCallback->ResultCode ?? null;
$ResultDesc = $data->Body->stkCallback->ResultDesc ?? null;
$Amount = $data->Body->stkCallback->CallbackMetadata->Item[0]->Value ?? null;
$TransactionId = $data->Body->stkCallback->CallbackMetadata->Item[1]->Value ?? null;
$UserPhoneNumber = $data->Body->stkCallback->CallbackMetadata->Item[4]->Value ?? null;

// Check if critical fields are present
if (!$MerchantRequestID || !$CheckoutRequestID || !$ResultCode || !$Amount || !$TransactionId || !$UserPhoneNumber) {
    $_SESSION['error'] = "Missing important transaction details.";
    exit(json_encode(["status" => "error", "message" => $_SESSION['error']]));
}

// Check if the transaction was successful
if ($ResultCode == 0) {

    // Prepare the query to get email and event details
    $checkoutQuery = $conn->prepare("SELECT email, member_name, event_id, event_name, event_location, event_date FROM eventregcheckout WHERE CheckoutRequestID = ?");
    if (!$checkoutQuery) {
        $_SESSION['error'] = "Database query preparation failed.";
        exit(json_encode(["status" => "error", "message" => $_SESSION['error']]));
    }

    $checkoutQuery->bind_param("s", $CheckoutRequestID);
    $checkoutQuery->execute();
    $checkoutResult = $checkoutQuery->get_result();

    if ($checkoutResult->num_rows > 0) {
        $checkoutData = $checkoutResult->fetch_assoc();

        // Extract necessary information
        $email = $checkoutData['email'];
        $memberName = $checkoutData['member_name'];
        $eventId = $checkoutData['event_id'];
        $eventName = $checkoutData['event_name'];
        $eventLocation = $checkoutData['event_location'];
        $eventDate = $checkoutData['event_date'];
        $registrationDate = date('Y-m-d H:i:s');

        // Insert data into event_registrations table
        $insertQuery = $conn->prepare("INSERT INTO event_registrations (event_id, event_name, event_location, event_date, member_email, member_name, contact, registration_date, payment_code, invitation_card)  
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $invitationCardPath = ''; // Initialize invitation card path

        if ($insertQuery) {
            $insertQuery->bind_param("issssssiss", $eventId, $eventName, $eventLocation, $eventDate, $email, $memberName, $UserPhoneNumber, $registrationDate, $TransactionId, $invitationCardPath);
            if ($insertQuery->execute()) {
                // Success, set session variables
                $_SESSION['success'] = "Registration successful.";
            } else {
                $_SESSION['error'] = "Error inserting registration data.";
            }
        } else {
            $_SESSION['error'] = "Database insert query preparation failed.";
        }
    } else {
        $_SESSION['error'] = "No matching event found for the CheckoutRequestID.";
    }
} else {
    $_SESSION['error'] = "Transaction failed. Result Description: $ResultDesc";
}

// Return response
if (isset($_SESSION['error'])) {
    exit(json_encode(["status" => "error", "message" => $_SESSION['error']]));
} else {
    exit(json_encode(["status" => "success", "message" => $_SESSION['success']]));
}
?>
