<?php
// Start the session
session_start();

// Include the access token file
include 'accessToken.php';
date_default_timezone_set('Africa/Nairobi');

// Initialize response array
$response = ['success' => false, 'message' => '', 'errors' => []];

// Define the normalizePhoneNumber function
function normalizePhoneNumber($phone) {
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

// Define variables
$processrequestUrl = 'https://api.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
// $callbackurl = 'https://member.log.agl.or.ke/DARAJA/callback.php';
$callbackurl = 'https://member.log.agl.or.ke/members/forms/Payment/Mpesa-Daraja-Api-main/callback.php';
$passkey = "3d0e12c8f86cede36233aaa2f2be5d5c97eea4c2518fcaf01ff5b5e3a92416d0";
$BusinessShortCode = '6175135';
$Timestamp = date('YmdHis');

// Encrypt data to get password
$Password = base64_encode($BusinessShortCode . $passkey . $Timestamp);

// Define other parameters
$PartyA = $phone; 
$PartyB = $BusinessShortCode; 
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
$curl_response = curl_exec($curl);
curl_close($curl);

// Decode and handle the response
$data = json_decode($curl_response);
$CheckoutRequestID = isset($data->CheckoutRequestID) ? $data->CheckoutRequestID : null;
$ResponseCode = isset($data->ResponseCode) ? $data->ResponseCode : '';

// Database connection settings
require_once('../../DBconnection.php');

// Determine status based on response code
$status = ($ResponseCode == "0") ? 'Pending' : 'Failed';

// Insert data into the database only if the transaction was initiated
if ($CheckoutRequestID) {
    $sql = "INSERT INTO mpesa_transactions (CheckoutRequestID, email, phone, amount, status) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $CheckoutRequestID, $userEmail, $phone, $money, $status);

    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = "Kindly enter your Mpesa Pin to complete the payment " ;
    } else {
        $response['errors'][] = "Database error: " . $stmt->error;
    }

    $stmt->close();
} else {
    $response['errors'][] = "Error in transaction processing. Please try again.";
}

// Close database connection
$conn->close();

// Store response in session and redirect
$_SESSION['response'] = $response;
header("Location: " . $_SERVER['HTTP_REFERER']);
exit();
?>
