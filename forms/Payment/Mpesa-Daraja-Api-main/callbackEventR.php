<?php
session_start(); // Start the session

// Include the database connection file
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
$logFile = "EventRcallback.json";
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

    // Retrieve the email associated with the CheckoutRequestID from the eventregcheckout table
    $checkoutQuery = $conn->prepare("SELECT email, member_name, event_id, event_name, event_location, event_date FROM eventregcheckout WHERE CheckoutRequestID = ?");
    $checkoutQuery->bind_param("s", $CheckoutRequestID);
    $checkoutQuery->execute();
    $checkoutResult = $checkoutQuery->get_result();

    if (!$checkoutResult) {
        $response['errors'][] = "Database query failed: " . $conn->error;
        $_SESSION['response'] = $response;
        exit;
    }

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

        // Insert the data into event_registrations table
        $insertQuery = $conn->prepare("INSERT INTO event_registrations (event_id, event_name, event_location, event_date, member_email, member_name, contact, registration_date, payment_code) 
                                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $insertQuery->bind_param("issssssis", $eventId, $eventName, $eventLocation, $eventDate, $email, $memberName, $UserPhoneNumber, $registrationDate, $TransactionId);
        
        if (!$insertQuery->execute()) {
            $response['errors'][] = "Failed to insert event registration: " . $conn->error;
            $_SESSION['response'] = $response;
            exit;
        }

        // Send email to confirm registration
        $to = $email;
        $subject = "Registration Successful!";
        $message = "
            Dear $memberName,

            Thank you for registering for $eventName! We're excited to have you join us on $eventDate.

            Event Details:
            Location: $eventLocation
            Time: 10:00 AM

            Please check your email for more details and any future updates. If you have any questions, feel free to reach out at support@agl.or.ke.

            We look forward to seeing you there!

            Warm regards,
            The AGL Team
        ";
        $headers = "From: events@agl.or.ke\r\n";
        $headers .= "Reply-To: events@agl.or.ke\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

        if (!mail($to, $subject, $message, $headers)) {
            $response['errors'][] = "Failed to send registration confirmation email to $to";
            $_SESSION['response'] = $response;
            exit;
        }

        $response['success'] = true;
        $response['message'] = "Event registration successful, payment processed, and confirmation email sent.";
    } else {
        $response['errors'][] = "No records found for CheckoutRequestID: $CheckoutRequestID";
        $_SESSION['response'] = $response;
        exit;
    }
} else {
    $response['errors'][] = "Transaction failed with ResultCode: $ResultCode and ResultDesc: $ResultDesc";
}

$_SESSION['response'] = $response;
?>
