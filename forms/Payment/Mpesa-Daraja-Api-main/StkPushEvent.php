<?php
// Start the session
session_start();

// Include the access token file
include 'accessToken.php';
date_default_timezone_set('Africa/Nairobi');

// Initialize response array
$response = ['success' => false, 'message' => '', 'errors' => []];

// Define the normalizePhoneNumber function
function normalizePhoneNumber($phone)
{
    $phone = preg_replace('/\s+/', '', $phone);
    if (strpos($phone, '+') === 0) {
        $phone = substr($phone, 1);
    }
    if (preg_match('/^0[17]/', $phone)) {
        $phone = '254' . substr($phone, 1);
    }
    if (preg_match('/^2547/', $phone)) {
        return $phone;
    }
    return $phone;
}

// Retrieve and normalize form data
$eventId = isset($_POST['event_id']) ? $_POST['event_id'] : '';
$eventName = isset($_POST['event_name']) ? $_POST['event_name'] : '';
$eventLocation = isset($_POST['event_location']) ? $_POST['event_location'] : '';
$eventDate = isset($_POST['event_date']) ? $_POST['event_date'] : '';
$userEmail = isset($_POST['User-email']) ? $_POST['User-email'] : '';
$memberName = isset($_POST['memberName']) ? $_POST['memberName'] : '';
$phone = isset($_POST['phone_number']) ? normalizePhoneNumber($_POST['phone_number']) : '';
$money = isset($_POST['amount']) ? $_POST['amount'] : '';

// Validate inputs
if (empty($phone)) {
    $response['errors'][] = 'Phone number is required.';
}
if (empty($userEmail)) {
    $response['errors'][] = 'Email is required.';
}
if (!empty($response['errors'])) {
    // Set response and redirect
    $_SESSION['response'] = $response;
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}

// Database connection settings
require_once('../../DBconnection.php');

// Check if the user is already registered for the event
$checkSql = "SELECT * FROM event_registrations WHERE event_id = ? AND member_email = ?";
$checkStmt = $conn->prepare($checkSql);
$checkStmt->bind_param("ss", $eventId, $userEmail);
$checkStmt->execute();
$checkResult = $checkStmt->get_result();

if ($checkResult->num_rows > 0) {
    // User is already registered for the event
    $response['errors'][] = 'You have already registered for this event.';
    $_SESSION['response'] = $response;
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}

// If amount is 0, insert into event_registrations and skip STK push
if ($money == 0) {
    $insertSql = "INSERT INTO event_registrations (event_id, event_name, event_location, event_date, member_email, member_name, contact, registration_date, payment_code) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), 00)";
    $insertStmt = $conn->prepare($insertSql);
    $insertStmt->bind_param("sssssss", $eventId, $eventName, $eventLocation, $eventDate, $userEmail, $memberName, $phone);

    if ($insertStmt->execute()) {
        $response['success'] = true;
        $response['message'] = "Registration successful. No payment required.";
    } else {
        $response['errors'][] = "Event Database error: " . $insertStmt->error;
    }

    $insertStmt->close();
} else {
    // Proceed with STK push for non-zero amount
    $processrequestUrl = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
    $callbackurl = 'https://member.log.agl.or.ke/members/forms/Payment/Mpesa-Daraja-Api-main/callbackEventR.php';
    $passkey = "bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919";
    $BusinessShortCode = '174379';
    $Timestamp = date('YmdHis');

    // Encrypt data to get password
    $Password = base64_encode($BusinessShortCode . $passkey . $Timestamp);

    // Define other parameters
    $PartyA = $phone; // Phone number to receive the STK push
    $AccountReference = 'AGL';
    $TransactionDesc = 'Membership Registration fee payment';
    $Amount = $money;

    $stkpushheader = ['Content-Type:application/json', 'Authorization:Bearer ' . $access_token];

    // Initialize cURL
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $processrequestUrl);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $stkpushheader); // Setting custom header

    $curl_post_data = array(
        'BusinessShortCode' => $BusinessShortCode,
        'Password' => $Password,
        'Timestamp' => $Timestamp,
        'TransactionType' => 'CustomerPayBillOnline',
        'Amount' => $Amount,
        'PartyA' => $PartyA,
        'PartyB' => $BusinessShortCode,
        'PhoneNumber' => $PartyA,
        'CallBackURL' => $callbackurl,
        'AccountReference' => $AccountReference,
        'TransactionDesc' => $TransactionDesc
    );

    $data_string = json_encode($curl_post_data);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
    
    // Execute cURL request
    $curl_response = curl_exec($curl);
    if (curl_errno($curl)) {
        $response['errors'][] = "cURL error: " . curl_error($curl);
    }
    curl_close($curl);

    // Log the entire response for debugging
    $data = json_decode($curl_response);
    error_log("cURL Response: " . print_r($data, true));

    // Extract response details
    $CheckoutRequestID = isset($data->CheckoutRequestID) ? $data->CheckoutRequestID : null;
    $ResponseCode = isset($data->ResponseCode) ? $data->ResponseCode : '';
    $ResponseDescription = isset($data->ResponseDescription) ? $data->ResponseDescription : '';

    // Determine status based on response code
    if ($CheckoutRequestID) {
        $status = ($ResponseCode == "0") ? 'Pending' : 'Failed';

        // Insert all data into `EventRegcheckout` table
        $eventSql = "INSERT INTO eventregcheckout (CheckoutRequestID, event_id, event_name, event_location, event_date, email, member_name, phone, amount, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $eventStmt = $conn->prepare($eventSql);
        $eventStmt->bind_param("ssssssssss", $CheckoutRequestID, $eventId, $eventName, $eventLocation, $eventDate, $userEmail, $memberName, $phone, $money, $status);

        if ($eventStmt->execute()) {
            $response['success'] = true;
            $response['message'] = "Kindly enter your Mpesa Pin to complete the payment.";
        } else {
            $response['errors'][] = "Event Database error: " . $eventStmt->error;
        }

        $eventStmt->close();
    } else {
        $response['errors'][] = "Error in transaction processing: " . $ResponseDescription;
    }
}

// Close database connection
$conn->close();

// Store response in session and redirect
$_SESSION['response'] = $response;
header("Location: " . $_SERVER['HTTP_REFERER']);
exit();


// if ($ResponseCode == "0") {
//   echo "The CheckoutRequestID for this transaction is : " . $CheckoutRequestID;
// }

?>
