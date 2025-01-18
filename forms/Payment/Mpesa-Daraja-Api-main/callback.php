<?php
session_start();

include 'AGLdbconnection.php';
header("Content-Type: application/json");

$stkCallbackResponse = file_get_contents('php://input');
$logFile = "MemberReg.json";
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

$MerchantRequestID = $data->Body->stkCallback->MerchantRequestID ?? null;
$CheckoutRequestID = $data->Body->stkCallback->CheckoutRequestID ?? null;
$ResultCode = $data->Body->stkCallback->ResultCode ?? null;
$ResultDesc = $data->Body->stkCallback->ResultDesc ?? null;
$Amount = $data->Body->stkCallback->CallbackMetadata->Item[0]->Value ?? null;
$TransactionId = $data->Body->stkCallback->CallbackMetadata->Item[1]->Value ?? null;
$UserPhoneNumber = $data->Body->stkCallback->CallbackMetadata->Item[4]->Value ?? null;

if ($ResultCode == 0) {
    $stmt = $conn->prepare("SELECT email FROM mpesa_transactions WHERE CheckoutRequestID = ?");
    $stmt->bind_param('s', $CheckoutRequestID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $email = $row['email'];

        $timestamp = date('Y-m-d H:i:s');
        $insertStmt = $conn->prepare("INSERT INTO member_payments (member_email, phone_number, payment_code, amount, timestamp) VALUES (?, ?, ?, ?, ?)");
        $insertStmt->bind_param('sssss', $email, $UserPhoneNumber, $TransactionId, $Amount, $timestamp);
        $insertStmt->execute();

        if ($insertStmt->affected_rows > 0) {

            // $lastIdQuery = "SELECT id FROM invoices ORDER BY id DESC LIMIT 1";
            // $lastIdResult = $conn->query($lastIdQuery);

            // if ($lastIdResult->num_rows > 0) {
            //     $row = $lastIdResult->fetch_assoc();
            //     $lastId = intval(substr($row['id'], strrpos($row['id'], '/') + 1));
            //     $customId = 'AGLP/' . str_pad($lastId + 1, 6, '0', STR_PAD_LEFT);
            // } else {
            //     $customId = 'AGLP/000001'; 
            // }

            // $paymentDescription = "Membership Registration Payment";
            // $amountBilled = 2000.00;

            // $insertInvoice = $conn->prepare("INSERT INTO invoices (id, payment_description, amount_billed, amount_paid, user_email, invoice_date) VALUES (?, ?, ?, ?, ?, ?)");
            // $insertInvoice->bind_param('ssddss', $customId, $paymentDescription, $amountBilled, $Amount, $email, $timestamp);
            // $insertInvoice->execute();

            // Insert data into the invoices table
            $paymentDescription = "Membership Registration Payment";
            $amountBilled = 2000.00;

            $insertInvoice = $conn->prepare("INSERT INTO invoices (payment_description, amount_billed, amount_paid, user_email, invoice_date) VALUES (?, ?, ?, ?, ?)");
            $insertInvoice->bind_param('ssddss', $paymentDescription, $amountBilled, $Amount, $email, $timestamp);
            $insertInvoice->execute();




            // Check which table the email exists in
            $checkPersonal = $conn->prepare("SELECT email FROM personalmembership WHERE email = ?");
            $checkPersonal->bind_param('s', $email);
            $checkPersonal->execute();
            $personalResult = $checkPersonal->get_result();

            if ($personalResult->num_rows > 0) {
                $updateStmt = $conn->prepare("UPDATE personalmembership SET payment_Number = ?, payment_code = ?, payment_date = ? WHERE email = ?");
            } else {
                $checkOrg = $conn->prepare("SELECT organization_email FROM organizationmembership WHERE organization_email = ?");
                $checkOrg->bind_param('s', $email);
                $checkOrg->execute();
                $orgResult = $checkOrg->get_result();

                if ($orgResult->num_rows > 0) {
                    $updateStmt = $conn->prepare("UPDATE organizationmembership SET payment_Number = ?, payment_code = ?, payment_date = ? WHERE organization_email = ?");
                } else {
                    $_SESSION['response'] = [
                        'success' => false,
                        'message' => 'Email not found in either membership table.'
                    ];
                    http_response_code(404);
                    exit;
                }
            }

            $updateStmt->bind_param('ssss', $UserPhoneNumber, $TransactionId, $timestamp, $email);
            $updateStmt->execute();

            if ($updateStmt->affected_rows > 0) {
                $to = $email;
                $subject = "Payment Confirmation";
                $message = "Greetings,\n\nThank you for your Member payment of Ksh $Amount.\n\nTransaction ID: $TransactionId\n\nBest regards,\nAGL Team";
                $headers = "From: payments@agl.or.ke\r\n";
                $headers .= "Reply-To: payments@agl.or.ke\r\n";
                $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

                if (mail($to, $subject, $message, $headers)) {
                    $_SESSION['response'] = [
                        'success' => true,
                        'message' => 'Payment processed successfully. Confirmation email sent.'
                    ];
                } else {
                    $_SESSION['response'] = [
                        'success' => false,
                        'message' => 'Failed to send confirmation email.'
                    ];
                }
            } else {
                $_SESSION['response'] = [
                    'success' => false,
                    'message' => 'Failed to update membership with payment details.'
                ];
            }

            $updateStmt->close();
        } else {
            $_SESSION['response'] = [
                'success' => false,
                'message' => 'Failed to insert payment details.'
            ];
        }
        $insertStmt->close();
    } else {
        $_SESSION['response'] = [
            'success' => false,
            'message' => 'No email found for CheckoutRequestID.'
        ];
    }
    $stmt->close();
} else {
    $_SESSION['response'] = [
        'success' => false,
        'message' => 'Transaction failed: ' . $ResultDesc
    ];
}

$conn->close();
