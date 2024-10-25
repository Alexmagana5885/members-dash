
<?php
// Start the session
session_start();

include '../../../config.php';
echo $passkey; // Outputs: localhost
echo $BusinessShortCode; // Outputs: example_db

include 'accessToken.php'; 
date_default_timezone_set('Africa/Nairobi');

$response = ['success' => false, 'message' => '', 'errors' => []];

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
// $passkey = "3d0e12c8f86cede36233aaa2f2be5d5c97eea4c2518fcaf01ff5b5e3a92416d0";
// $BusinessShortCode = '6175135';
$Timestamp = date('YmdHis');

$passkey = $config['passkey'];
$businessShortCode = $config['business_short_code'];
$Password = base64_encode($BusinessShortCode . $passkey . $Timestamp); 

$phone = $phone_number; 
$money = $money_paid;
$PartyA = $phone; 
$PartyB = '8209382'; 
$AccountReference = '6175135';
$TransactionDesc = 'Membership Registration fee payment'; 
$Amount = $money;

$stkpushheader = ['Content-Type:application/json', 'Authorization:Bearer ' . $access_token];

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


$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $processrequestUrl);
curl_setopt($curl, CURLOPT_HTTPHEADER, $stkpushheader); 
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
$curl_response = curl_exec($curl);

if (curl_errno($curl)) {
    $response['errors'][] = 'cURL error: ' . curl_error($curl);
    $_SESSION['response'] = $response;
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}

curl_close($curl);


$data = json_decode($curl_response);
$CheckoutRequestID = isset($data->CheckoutRequestID) ? $data->CheckoutRequestID : null;
$ResponseCode = isset($data->ResponseCode) ? $data->ResponseCode : '';

require_once('../../DBconnection.php');
$status = ($ResponseCode == "0") ? 'Pending' : 'Failed';

if ($CheckoutRequestID) {
    $sql = "INSERT INTO mpesa_transactions (CheckoutRequestID, email, phone, amount, status) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $CheckoutRequestID, $userEmail, $phone, $money, $status);

    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = "Kindly enter your Mpesa Pin to complete the payment";
    } else {
        $response['errors'][] = "Database error: " . $stmt->error;
    }

    $stmt->close();
} else {
    $response['errors'][] = "Error in transaction processing. Please try again.";
}

$conn->close();

$_SESSION['response'] = $response;
// header("Location: " . $_SERVER['HTTP_REFERER']);
exit();
