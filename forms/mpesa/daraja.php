<?php

// Include the Daraja API Library
require_once('path/to/Daraja.php');

// Initialize Daraja with your credentials
$daraja = new Daraja([
    'consumerKey' => 'your-consumer-key',
    'consumerSecret' => 'your-consumer-secret',
    'shortcode' => 'your-shortcode',
    'lipaNaMpesaOnlinePasskey' => 'your-passkey',
    'lipaNaMpesaOnlineShortcode' => 'your-lipa-na-mpesa-shortcode',
]);

// Construct the payment request
$lipaNaMpesa = $daraja->lipaNaMpesaOnline([
    'BusinessShortCode' => 'your-business-shortcode',
    'Amount' => '1000', // Specify the amount to be paid
    'PartyA' => '2547xxxxxxx', // Customer's phone number
    'PartyB' => 'your-paybill-number',
    'PhoneNumber' => '2547xxxxxxx', // Customer's phone number
    'CallBackURL' => 'https://your-callback-url.com', // Replace with your callback URL
    'AccountReference' => 'your-account-reference',
    'TransactionDesc' => 'Payment for Order 123',
    'TransactionType' => 'CustomerPayBillOnline',
]);

// Send the request
$response = $daraja->execute($lipaNaMpesa);

// Handle the response
if ($response->getResponseCode() === '0') {
    // Payment request was successful
    $checkoutRequestID = $response->getCheckoutRequestID();
    $responseDescription = $response->getResponseDescription();
    // You can now redirect the customer to the payment page or display a success message.
} else {
    // Payment request failed
    $errorCode = $response->getResponseCode();
    $errorMessage = $response->getResponseDescription();
    // Handle the error as needed.
}