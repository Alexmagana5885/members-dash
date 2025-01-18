<?php
session_start(); // Start the session

include 'AGLdbconnection.php';
header("Content-Type: application/json");

$stkCallbackResponse = file_get_contents('php://input');
$logFile = "PremiumMpesastkresponse.json";
file_put_contents($logFile, $stkCallbackResponse . PHP_EOL, FILE_APPEND);

$data = json_decode($stkCallbackResponse);

if (json_last_error() !== JSON_ERROR_NONE) {
    $_SESSION['response'] = [
        'success' => false,
        'message' => 'JSON decoding error: ' . json_last_error_msg()
    ];
    http_response_code(400);
    exit;
}
// Extract relevant data from the response
$MerchantRequestID = $data['Body']['stkCallback']['MerchantRequestID'] ?? null;
$CheckoutRequestID = $data['Body']['stkCallback']['CheckoutRequestID'] ?? null;
$ResultCode = $data['Body']['stkCallback']['ResultCode'] ?? null;
$ResultDesc = $data['Body']['stkCallback']['ResultDesc'] ?? null;
$Amount = $data['Body']['stkCallback']['CallbackMetadata']['Item'][0]['Value'] ?? null;
$TransactionId = $data['Body']['stkCallback']['CallbackMetadata']['Item'][1]['Value'] ?? null;
$UserPhoneNumber = $data['Body']['stkCallback']['CallbackMetadata']['Item'][4]['Value'] ?? null;

// Check if the transaction was successful
if ($ResultCode == 0) {
    // Retrieve the email associated with the CheckoutRequestID
    $stmt = $conn->prepare("SELECT email FROM mpesa_transactions WHERE CheckoutRequestID = ?");
    $stmt->bind_param('s', $CheckoutRequestID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $email = $row['email'];

        // Insert the payment details into the member_payments table
        $timestamp = date('Y-m-d H:i:s'); // Current timestamp
        $insertStmt = $conn->prepare("INSERT INTO member_payments (member_email, phone_number, payment_code, amount, timestamp) VALUES (?, ?, ?, ?, ?)");
        $insertStmt->bind_param('sssss', $email, $UserPhoneNumber, $TransactionId, $Amount, $timestamp);
        $insertStmt->execute();

        if ($insertStmt->affected_rows > 0) {
            // Generate a custom invoice ID
            $stmtInvoice = $conn->prepare("SELECT MAX(id) as max_id FROM invoices");
            $stmtInvoice->execute();
            $resultInvoice = $stmtInvoice->get_result();
            $rowInvoice = $resultInvoice->fetch_assoc();
            $lastId = $rowInvoice['max_id'] ?? 0; // Default to 0 if no invoices exist
            $customId = "AGLP" . str_pad($lastId + 1, 6, "0", STR_PAD_LEFT);

            // Insert data into the invoices table
            $paymentDescription = "Membership Premium Payment";
            $amountBilled = 3600.00;

            $insertInvoice = $conn->prepare("INSERT INTO invoices (id, payment_description, amount_billed, amount_paid, user_email, invoice_date) VALUES (?, ?, ?, ?, ?, ?)");
            $insertInvoice->bind_param('ssddss', $customId, $paymentDescription, $amountBilled, $Amount, $email, $timestamp);
            $insertInvoice->execute();

            if ($insertInvoice->affected_rows > 0) {
                // Send a confirmation email
                $to = $email;
                $subject = "Payment Confirmation";
                $message = "Dear User,\n\nThank you for your Member payment of Ksh $Amount.\n\nTransaction ID: $TransactionId\n\nYour Invoice ID: $customId\n\nBest regards,\nAGL Team";
                $headers = "From: payments@agl.or.ke\r\n";
                $headers .= "Reply-To: payments@agl.or.ke\r\n";
                $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

                if (mail($to, $subject, $message, $headers)) {
                    $_SESSION['response'] = [
                        'success' => true,
                        'message' => 'Payment processed successfully. Invoice created and confirmation email sent.'
                    ];
                } else {
                    // Handle email sending failure
                    $_SESSION['response'] = [
                        'success' => false,
                        'message' => 'Failed to send confirmation email.'
                    ];
                }
            } else {
                // Handle the case where the invoice insert did not succeed
                $_SESSION['response'] = [
                    'success' => false,
                    'message' => 'Failed to create invoice.'
                ];
            }
            $insertInvoice->close();
        } else {
            // Handle the case where the payment insert did not succeed
            $_SESSION['response'] = [
                'success' => false,
                'message' => 'Failed to insert payment details.'
            ];
        }
        $insertStmt->close();
    } else {
        // Handle the case where no email was found
        $_SESSION['response'] = [
            'success' => false,
            'message' => 'No email found for CheckoutRequestID.'
        ];
    }
    $stmt->close();
} else {
    // Handle unsuccessful transaction response
    $_SESSION['response'] = [
        'success' => false,
        'message' => 'Transaction failed: ' . $ResultDesc
    ];
}


$conn->close();
