<?php
// Start the session at the beginning
session_start();

// INCLUDE THE ACCESS TOKEN FILE
include 'accessToken.php';
date_default_timezone_set('Africa/Nairobi');

// DB Connection (replace with your actual DB credentials)
require_once('../../DBconnection.php');

// Function to normalize phone number to '2547...' format
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

// Check if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $email = $_POST['User-email'] ?? null;
    $phone = $_POST['phone_number'] ?? null;
    $amount = $_POST['amount'] ?? null;
    $referringPage = $_POST['referringPage'] ?? 'index.html'; // Default to 'index.html' if no referring page is provided

    // Store the email in the session for later use
    $_SESSION['email'] = $email;

    // Normalize phone number
    $normalizedPhone = normalizePhoneNumber($phone);

    // Ensure phone and amount are valid
    if (!empty($normalizedPhone) && !empty($amount)) {
        $processrequestUrl = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
        $callbackurl = 'https://member.log.agl.or.ke/DARAJA/callback.php';
        $passkey = "bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919";
        $BusinessShortCode = '174379';
        $Timestamp = date('YmdHis');

        $Password = base64_encode($BusinessShortCode . $passkey . $Timestamp);
        $PartyA = $normalizedPhone;
        $PartyB = $BusinessShortCode;
        $AccountReference = 'AGL';
        $TransactionDesc = 'Membership fee payment';
        $Amount = $amount;

        $stkpushheader = ['Content-Type:application/json', 'Authorization:Bearer ' . $access_token];

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $processrequestUrl);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $stkpushheader);

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

        if ($curl_response === false) {
            echo 'Curl error: ' . curl_error($curl);
        } else {
            $data = json_decode($curl_response);

            if (json_last_error() === JSON_ERROR_NONE) {
                $ResponseCode = $data->ResponseCode ?? null;
                $CheckoutRequestID = $data->CheckoutRequestID ?? null;

                if ($ResponseCode == "0") {
                    // Store the email and CheckoutRequestID in the database
                    $sql = "INSERT INTO mpesa_transactions (CheckoutRequestID, email, phone, amount, status) 
                            VALUES ('$CheckoutRequestID', '$email', '$PartyA', '$Amount', 'pending')";

                    if ($conn->query($sql) === TRUE) {
                        echo "Transaction data stored successfully.";
                    } else {
                        echo "Error storing transaction: " . $conn->error;
                    }

                    // Prepare JSON data to send to callback
                    $jsonDataToSend = json_encode([
                        'MerchantRequestID' => $data->MerchantRequestID ?? null,
                        'CheckoutRequestID' => $CheckoutRequestID,
                        'ResultCode' => $ResponseCode,
                        'Amount' => $Amount,
                        'MpesaReceiptNumber' => $data->MpesaReceiptNumber ?? null,
                        'PhoneNumber' => $PartyA,
                        'Email' => $email // Add the email to the JSON data
                    ]);

                    // Send JSON as form data to callback.php using cURL
                    $ch = curl_init($callbackurl);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, [
                        'jsonData' => $jsonDataToSend // Send JSON data as a form field
                    ]);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, [
                        'Content-Type: application/x-www-form-urlencoded'
                    ]);

                    $callbackResponse = curl_exec($ch);

                    if ($callbackResponse === false) {
                        echo 'Callback curl error: ' . curl_error($ch);
                    } else {
                        echo 'Callback response: ' . $callbackResponse;
                    }

                    curl_close($ch);

                    // Redirect back to the referring page
                    header("Location: " . $_SERVER['HTTP_REFERER']);
                    exit;
                } else {
                    echo "Error response from M-Pesa API. Response: " . $curl_response;
                }
            } else {
                echo "JSON decode error: " . json_last_error_msg();
            }
        }

        curl_close($curl);
    } else {
        echo "Phone number and amount are required.";
    }
}

// Close the database connection
$conn->close();
?>
