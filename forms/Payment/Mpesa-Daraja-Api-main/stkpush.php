<?php
// INCLUDE THE ACCESS TOKEN FILE
include 'accessToken.php';
date_default_timezone_set('Africa/Nairobi');

// Function to normalize phone number to '2547...' format
function normalizePhoneNumber($phone) {
    // Remove any spaces or non-digit characters
    $phone = preg_replace('/\s+/', '', $phone);
    
    // Remove the leading '+' if present
    if (strpos($phone, '+') === 0) {
        $phone = substr($phone, 1);
    }

    // If the number starts with '07' or '01', replace '0' with '254'
    if (preg_match('/^0[17]/', $phone)) {
        $phone = '254' . substr($phone, 1);
    }

    // If the number starts with '2547', leave it as is
    if (preg_match('/^2547/', $phone)) {
        // Number is already in the correct format
        return $phone;
    }

    // Return the normalized phone number
    return $phone;
}

// Check if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $phone = $_POST['phone_number']; // Get phone number from form input
    $amount = $_POST['amount'];      // Get amount from form input

    // Normalize phone number
    $normalizedPhone = normalizePhoneNumber($phone);

    // Ensure phone and amount are valid
    if (!empty($normalizedPhone) && !empty($amount)) {
        $processrequestUrl = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
        $callbackurl = 'https://www.agl.or.ke/Daraja/callback.php';
        $passkey = "bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919";
        $BusinessShortCode = '174379'; // Change this to your business shortcode
        $Timestamp = date('YmdHis');

        // ENCRYPT DATA TO GET PASSWORD
        $Password = base64_encode($BusinessShortCode . $passkey . $Timestamp);
        $PartyA = $normalizedPhone;  // Use normalized phone number to receive the STK push
        $PartyB = $BusinessShortCode;  // Business shortcode (recipient)
        $AccountReference = 'AGL';
        $TransactionDesc = 'Membership fee payment';
        $Amount = $amount;

        $stkpushheader = ['Content-Type:application/json', 'Authorization:Bearer ' . $access_token];

        // INITIATE CURL
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

        if ($curl_response === false) {
            echo 'Curl error: ' . curl_error($curl);
        } else {
            $data = json_decode($curl_response);

            if (json_last_error() === JSON_ERROR_NONE) {
                $ResponseCode = $data->ResponseCode ?? null;
                $CheckoutRequestID = $data->CheckoutRequestID ?? null;

                if ($ResponseCode == "0") {
                    echo "The CheckoutRequestID for this transaction is: " . $CheckoutRequestID;
                    
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
?>
