<?php
// DATABASE CONNECTION (Replace with your actual connection details)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "your_database";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// READ THE MPESA RESPONSE
$response = file_get_contents('php://input');
$mpesaResponse = json_decode($response, true);

// EXTRACT THE RELEVANT FIELDS FROM THE MPESA RESPONSE
$CheckoutRequestID = $mpesaResponse['Body']['stkCallback']['CheckoutRequestID'] ?? null;
$ResultCode = $mpesaResponse['Body']['stkCallback']['ResultCode'] ?? null;
$ResultDesc = $mpesaResponse['Body']['stkCallback']['ResultDesc'] ?? null;
$MpesaReceiptNumber = $mpesaResponse['Body']['stkCallback']['CallbackMetadata']['Item'][1]['Value'] ?? null;
$PhoneNumber = $mpesaResponse['Body']['stkCallback']['CallbackMetadata']['Item'][4]['Value'] ?? null;
$Amount = $mpesaResponse['Body']['stkCallback']['CallbackMetadata']['Item'][0]['Value'] ?? null;

if ($ResultCode == "0") {
    // PAYMENT SUCCESSFUL - UPDATE TRANSACTION DETAILS IN THE DATABASE
    $stmt = $conn->prepare("UPDATE mpesa_transactions SET mpesa_receipt_number=?, result_code=?, result_description=? WHERE checkout_request_id=?");
    $stmt->bind_param("ssss", $MpesaReceiptNumber, $ResultCode, $ResultDesc, $CheckoutRequestID);
    $stmt->execute();
    $stmt->close();

    // Optionally retrieve the email and send a confirmation email
    $result = $conn->query("SELECT email FROM mpesa_transactions WHERE checkout_request_id='$CheckoutRequestID'");
    $row = $result->fetch_assoc();
    $userEmail = $row['email'];

    // Here you can send an email to $userEmail confirming the transaction
    echo "Payment successful for user with email: " . $userEmail;
} else {
    // PAYMENT FAILED - UPDATE TRANSACTION DETAILS IN THE DATABASE
    $stmt = $conn->prepare("UPDATE mpesa_transactions SET result_code=?, result_description=? WHERE checkout_request_id=?");
    $stmt->bind_param("sss", $ResultCode, $ResultDesc, $CheckoutRequestID);
    $stmt->execute();
    $stmt->close();

    echo "Payment failed: " . $ResultDesc;
}

$conn->close();
?>
