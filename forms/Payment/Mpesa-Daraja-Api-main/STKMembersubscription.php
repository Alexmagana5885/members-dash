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

// Validate and sanitize form data
$phone_number = isset($_POST['phone_number']) ? normalizePhoneNumber(filter_var($_POST['phone_number'], FILTER_SANITIZE_STRING)) : '';
$money_paid = isset($_POST['amount']) ? filter_var($_POST['amount'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION) : '1';
$userEmail = isset($_POST['User-email']) ? filter_var($_POST['User-email'], FILTER_VALIDATE_EMAIL) : '';

$response = ['errors' => []];

// Validate required fields
if (empty($phone_number)) {
    $response['errors'][] = 'Phone number is required.';
}
if (empty($userEmail)) {
    $response['errors'][] = 'Valid email is required.';
}
if (!empty($response['errors'])) {
    $_SESSION['response'] = $response;
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}

// Set M-Pesa API credentials
$processrequestUrl = 'https://api.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
$callbackurl = 'https://member.log.agl.or.ke/members/forms/Payment/Mpesa-Daraja-Api-main/callback.php';
$passkey = getenv('MPESA_PASSKEY'); 
$BusinessShortCode = getenv('MPESA_BUSINESS_SHORTCODE'); 

$Timestamp = date('YmdHis');
$Password = base64_encode($BusinessShortCode . $passkey . $Timestamp);

// Prepare M-Pesa API request data
$PartyA = $phone_number;
$PartyB = '8209382'; // Till number
$AccountReference = $BusinessShortCode;
$TransactionDesc = 'Membership Registration fee payment';
$Amount = $money_paid;

$stkpushheader = ['Content-Type:application/json', 'Authorization:Bearer ' . $access_token];

// Initialize cURL and send request to M-Pesa API
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $processrequestUrl);
curl_setopt($curl, CURLOPT_HTTPHEADER, $stkpushheader);
$curl_post_data = json_encode([
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
]);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $curl_post_data);
curl_setopt($curl, CURLOPT_TIMEOUT, 30); // Set a timeout for the request

$curl_response = curl_exec($curl);

// Check for cURL errors
if ($curl_error = curl_error($curl)) {
    $_SESSION['response'] = [
        'success' => false,
        'message' => 'cURL error: ' . $curl_error
    ];
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}

curl_close($curl);

// Decode and handle M-Pesa API response
$data = json_decode($curl_response);

if (isset($data->errorMessage)) {
    $_SESSION['response'] = [
        'success' => false,
        'message' => $data->errorMessage
    ];
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}

$CheckoutRequestID = $data->CheckoutRequestID ?? null;
$ResponseCode = $data->ResponseCode ?? null;

if (!$CheckoutRequestID || !$ResponseCode) {
    $_SESSION['response'] = [
        'success' => false,
        'message' => 'Invalid response from M-Pesa. Please try again.'
    ];
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}

// Determine transaction status
$status = ($ResponseCode === "0") ? 'Pending' : 'Failed';

// Database connection settings
require_once('../../DBconnection.php');

try {
    // Insert transaction data into the database
    $sql = "INSERT INTO mpesa_transactions (CheckoutRequestID, email, phone_number, amount, status) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $CheckoutRequestID, $userEmail, $phone_number, $money_paid, $status);

    if ($stmt->execute()) {
        $_SESSION['response'] = [
            'success' => true,
            'message' => 'Kindly enter your Mpesa Pin to complete the payment'
        ];
    } else {
        throw new Exception('Database error: ' . $stmt->error);
    }
} catch (Exception $e) {
    $_SESSION['response'] = [
        'success' => false,
        'message' => $e->getMessage()
    ];
}

// Redirect to the previous page
header("Location: " . $_SERVER['HTTP_REFERER']);

// Close connections
$stmt->close();
$conn->close();
exit();
