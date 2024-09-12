<?php
include 'AGLdbconnection.php'; // Ensure this file contains the proper database connection setup
session_start(); // Start the session to access stored session data
header("Content-Type: application/json");

$logFile = "Mpesastkresponse.json";
$log = fopen($logFile, "a");

// Read the raw M-Pesa callback response
$rawResponse = file_get_contents('php://input');
fwrite($log, "Raw M-Pesa callback response: " . $rawResponse . "\n");

// Decode the M-Pesa callback JSON data
$callbackData = json_decode($rawResponse, true);

if (json_last_error() === JSON_ERROR_NONE) {
    $stkCallback = $callbackData['Body']['stkCallback'] ?? null;

    if ($stkCallback) {
        $MerchantRequestID = $stkCallback['MerchantRequestID'] ?? null;
        $CheckoutRequestID = $stkCallback['CheckoutRequestID'] ?? null;
        $ResultCode = $stkCallback['ResultCode'] ?? null;
        $ResultDesc = $stkCallback['ResultDesc'] ?? null;

        // Extract values from CallbackMetadata
        $amount = null;
        $transactionId = null;
        $phoneNumber = null;

        if (isset($stkCallback['CallbackMetadata']['Item'])) {
            foreach ($stkCallback['CallbackMetadata']['Item'] as $item) {
                if ($item['Name'] == 'Amount') {
                    $amount = $item['Value'];
                } elseif ($item['Name'] == 'MpesaReceiptNumber') {
                    $transactionId = $item['Value'];
                } elseif ($item['Name'] == 'PhoneNumber') {
                    $phoneNumber = $item['Value'];
                }
            }
        }

        // Get email from session
        $email = $_SESSION['email'] ?? null;

        if ($ResultCode == 0 && $email) {
            // Prepare the SQL statement to update payment data using the email from the session
            if ($stmt = $conn->prepare("SELECT * FROM personalmembership WHERE email = ?")) {
                $stmt->bind_param('s', $email);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    // If email exists, update payment data
                    if ($updateStmt = $conn->prepare("UPDATE personalmembership 
                                                       SET payment_number = ?, payment_code = ? 
                                                       WHERE email = ?")) {
                        $updateStmt->bind_param('sss', $phoneNumber, $transactionId, $email);

                        if ($updateStmt->execute()) {
                            fwrite($log, "Payment data updated successfully for email: $email.\n");
                        } else {
                            fwrite($log, "Error updating payment data: " . $updateStmt->error . "\n");
                        }

                        $updateStmt->close();
                    } else {
                        fwrite($log, "Prepare statement error: " . $conn->error . "\n");
                    }
                } else {
                    fwrite($log, "No record found for email: $email.\n");
                }

                $stmt->close();
            } else {
                fwrite($log, "Prepare statement error: " . $conn->error . "\n");
            }
        } else {
            fwrite($log, "Transaction failed. ResultCode: $ResultCode, ResultDesc: $ResultDesc\n");
        }
    } else {
        fwrite($log, "No stkCallback found in response.\n");
    }
} else {
    fwrite($log, "JSON decode error: " . json_last_error_msg() . "\n");
}

fclose($log);
$conn->close();
?>
