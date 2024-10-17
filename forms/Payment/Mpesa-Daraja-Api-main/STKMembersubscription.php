<?php
// Start session to store response messages
session_start();

// Include the access token file
include 'accessToken.php';
date_default_timezone_set('Africa/Nairobi');

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

if (empty($phone_number)) {
    $response['errors'][] = 'Phone number is required.';
}
if (empty($userEmail)) {
    $response['errors'][] = 'Email is required.';
}
if (!empty($response['errors'])) {
    $_SESSION['response'] = $response;
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}

$processrequestUrl = 'https://api.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
$callbackurl = 'https://member.log.agl.or.ke/members/forms/Payment/Mpesa-Daraja-Api-main/callback.php';
$passkey = "3d0e12c8f86cede36233aaa2f2be5d5c97eea4c2518fcaf01ff5b5e3a92416d0";
$Timestamp = date('YmdHis');
$BusinessShortCode = '6175135';
$Password = base64_encode($BusinessShortCode . $passkey . $Timestamp);

$phone = $phone_number;
$money = $money_paid;
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
    'TransactionType' => 'CustomerBuyGoodsOnline',
    'Amount' => $Amount,
    'PartyA' => $PartyA,
    'PartyB' => $PartyB,
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

// Check if response contains an error
if (isset($data->errorMessage)) {
    $_SESSION['response'] = [
        'success' => false,
        'message' => $data->errorMessage
    ];
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}

// Continue if no error
$CheckoutRequestID = $data->CheckoutRequestID;
$ResponseCode = $data->ResponseCode;

// Database connection settings
require_once('../../DBconnection.php');

// Determine status based on response code
$status = ($ResponseCode == "0") ? 'Pending' : 'Failed';

// Insert data into database
$sql = "INSERT INTO mpesa_transactions (CheckoutRequestID, email, phone, amount, status) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssss", $CheckoutRequestID, $userEmail, $phone, $money, $status);

if ($stmt->execute()) {
    $_SESSION['response'] = [
        'success' => true,
        'message' => 'Kindly enter your Mpesa Pin to complete the payment'
    ];
} else {
    $_SESSION['response'] = [
        'success' => false,
        'message' => 'Database error: ' . $stmt->error
    ];
}

// Redirect back to the previous page
header("Location: " . $_SERVER['HTTP_REFERER']);

// Close connections
$stmt->close();
$conn->close();
exit();
