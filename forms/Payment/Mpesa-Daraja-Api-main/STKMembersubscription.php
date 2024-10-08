<?php
// Start session to store response messages
session_start();

// Include the access token file
include 'accessToken.php';
date_default_timezone_set('Africa/Nairobi');

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
$phone = isset($_POST['phone_number']) ? normalizePhoneNumber($_POST['phone_number']) : '';
$money = isset($_POST['amount']) ? $_POST['amount'] : '1';
$userEmail = isset($_POST['User-email']) ? $_POST['User-email'] : '';

// Define variables

$processrequestUrl = 'https://api.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
$callbackurl = 'https://member.log.agl.or.ke/members/forms/Payment/Mpesa-Daraja-Api-main/Premiumcallback.php';
$passkey = "3d0e12c8f86cede36233aaa2f2be5d5c97eea4c2518fcaf01ff5b5e3a92416d0";
$BusinessShortCode = '6175135';
$Timestamp = date('YmdHis');

// $processrequestUrl = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest'; 
// $callbackurl = 'https://member.log.agl.or.ke/members/forms/Payment/Mpesa-Daraja-Api-main/callbackEventR.php';
// $passkey = "bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919";
// $BusinessShortCode = '174379';
// $Timestamp = date('YmdHis');
 
// Encrypt data to get password
$Password = base64_encode($BusinessShortCode . $passkey . $Timestamp);

// Define other parameters
$PartyA = $phone; // Phone number to receive the STK push
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
?>
