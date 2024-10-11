<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start the session
session_start();

// Include the access token file
include 'accessToken.php';
date_default_timezone_set('Africa/Nairobi');

//  response array
$response = ['success' => false, 'message' => '', 'errors' => []];

//  normalizePhoneNumber function
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

// Validate and sanitize input data
$eventId = isset($_POST['event_id']) ? htmlspecialchars($_POST['event_id']) : '';
$eventName = isset($_POST['event_name']) ? htmlspecialchars($_POST['event_name']) : '';
$eventLocation = isset($_POST['event_location']) ? htmlspecialchars($_POST['event_location']) : '';
$eventDate = isset($_POST['event_date']) ? htmlspecialchars($_POST['event_date']) : '';
$userEmail = isset($_POST['User-email']) ? filter_var($_POST['User-email'], FILTER_SANITIZE_EMAIL) : '';
$memberName = isset($_POST['memberName']) ? htmlspecialchars($_POST['memberName']) : '';
$phone = isset($_POST['phone_number']) ? normalizePhoneNumber($_POST['phone_number']) : '';
$money = isset($_POST['amount']) ? floatval($_POST['amount']) : '';

// Validate inputs
if (empty($phone)) {
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

try {
    // db connection 
    require_once('../../DBconnection.php');

    // Check if the user is already registered for the event
    $checkSql = "SELECT * FROM event_registrations WHERE event_id = ? AND member_email = ?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param("ss", $eventId, $userEmail);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows > 0) {
        $response['errors'][] = 'You have already registered for this event.';
        $_SESSION['response'] = $response;
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    }

    // If amount is 0, insert into event_registrations and skip STK push
    if ($money == 0) {
        $insertSql = "INSERT INTO event_registrations (event_id, event_name, event_location, event_date, member_email, member_name, contact, registration_date, payment_code) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), '00')";
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
        // Proceed with STK push for non 0 amount
        $processrequestUrl = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest'; 
        $callbackurl = 'https://member.log.agl.or.ke/members/forms/Payment/Mpesa-Daraja-Api-main/callbackEventR.php'; 
        $passkey = "bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919";
        $BusinessShortCode = '174379';
        $Timestamp = date('YmdHis');
        $Password = base64_encode($BusinessShortCode . $passkey . $Timestamp);

        $PartyA = $phone; 
        $AccountReference = 'AGL';
        $TransactionDesc = 'Membership Registration fee payment';
        $Amount = $money;

        $stkpushheader = ['Content-Type:application/json', 'Authorization:Bearer ' . $access_token];

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $processrequestUrl);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $stkpushheader);

        $curl_post_data = [
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
        ];

        $data_string = json_encode($curl_post_data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);

        $curl_response = curl_exec($curl);
        if (curl_errno($curl)) {
            $response['errors'][] = "cURL error: " . curl_error($curl);
        }
        curl_close($curl);

        $data = json_decode($curl_response);
        error_log("cURL Response: " . print_r($data, true));

        $CheckoutRequestID = isset($data->CheckoutRequestID) ? $data->CheckoutRequestID : null;
        $ResponseCode = isset($data->ResponseCode) ? $data->ResponseCode : '';
        $ResponseDescription = isset($data->ResponseDescription) ? $data->ResponseDescription : '';

        if ($CheckoutRequestID) {
            $status = ($ResponseCode == "0") ? 'Pending' : 'Failed';

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

    $conn->close();
} catch (Exception $e) {
    $response['errors'][] = "Error: " . $e->getMessage();
}

$_SESSION['response'] = $response;
header("Location: " . $_SERVER['HTTP_REFERER']);
exit();
?>
