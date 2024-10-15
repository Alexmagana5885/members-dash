<?php
// Start the session
session_start();

// Include the access token file
include 'accessToken.php';
date_default_timezone_set('Africa/Nairobi');

// Initialize response array
$response = ['success' => false, 'message' => '', 'errors' => []];

// Define the normalizePhoneNumber function
function normalizePhoneNumber($phone_number)
{
    $phone_number = preg_replace('/\s+/', '', $phone_number);
    if (strpos($phone_number, '+') === 0) {
        $phone_number = substr($phone_number, 1);
    }
    if (preg_match('/^0[17]/', $phone_number)) {
        $phone_number = '254' . substr($phone_number, 1);
    }
    if (preg_match('/^2547/', $phone_number)) {
        return $phone_number;
    }
    return $phone_number;
}

// Retrieve and normalize form data
$phone_number = isset($_POST['phone_number']) ? normalizePhoneNumber($_POST['phone_number']) : '';
$money_paid = isset($_POST['amount']) ? $_POST['amount'] : '1';
$userEmail = isset($_POST['User-email']) ? $_POST['User-email'] : '';

// Validate inputs
if (empty($phone_number)) {
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
$callbackurl = 'https://member.log.agl.or.ke/members/forms/Payment/Mpesa-Daraja-Api-main/callback.php';
$passkey = "3d0e12c8f86cede36233aaa2f2be5d5c97eea4c2518fcaf01ff5b5e3a92416d0";
$BusinessShortCode = '6175135';

// $processrequestUrl = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest'; 
// $callbackurl = 'https://member.log.agl.or.ke/members/forms/Payment/Mpesa-Daraja-Api-main/callback.php'; 
// $passkey = "bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919";
// $BusinessShortCode = '174379';

$Timestamp = date('YmdHis');

// Encrypt data to get password
$Password = base64_encode($BusinessShortCode . $passkey . $Timestamp);

// $PartyA = $phone; 
// $phone = $phonenumber; 
// $PartyB = '8209382'; 
// $AccountReference = '6175135';
// $TransactionDesc = 'Membership Registration fee payment';
// $Amount = $money;
// $stkpushheader = ['Content-Type:application/json', 'Authorization:Bearer ' . $access_token];



$phone = $phone_number; // phone number to receive the STK push
$money = $money_paid; // amount to be processed
$PartyA = $phone;
$PartyB = '8209382';
$AccountReference = '6175135';
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
        $response['message'] = "Kindly enter your Mpesa Pin to complete the payment ";
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
