<?php
 
// Include the Daraja API Library
require_once('path/to/Daraja.php');

// Initialize Daraja with your credentials
$daraja = new Daraja([
    'consumerKey' => 'R6MsFA5Xx6r6j6m1sIxfnDDJHclnYGMBKRTY3oxT0sbPylfz',
    'consumerSecret' => '46FMjLVzxA6mcxaqqtmV6fd8HgHzRXQPGdqAfhqO5RzIRvFshuIUZuMxaI4be0QM',
    'shortcode' => '8209382',
    'lipaNaMpesaOnlinePasskey' => 'your-passkey',
    'lipaNaMpesaOnlineShortcode' => '8209382',
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