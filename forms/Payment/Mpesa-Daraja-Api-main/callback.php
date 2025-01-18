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

        // Calculate total payments made in the last 1 year
        $oneYearAgo = date('Y-m-d H:i:s', strtotime('-1 year'));
        $totalPaymentsQuery = $conn->prepare("SELECT SUM(amount) AS total_paid FROM member_registration_payments WHERE member_email = ? AND timestamp >= ?");
        $totalPaymentsQuery->bind_param('ss', $email, $oneYearAgo);
        $totalPaymentsQuery->execute();
        $paymentResult = $totalPaymentsQuery->get_result();
        $totalPaid = 0;

        if ($paymentResult && $paymentRow = $paymentResult->fetch_assoc()) {
            $totalPaid = $paymentRow['total_paid'] ?? 0;
        }

        $amountBilled = 2000.00 - $totalPaid;

        if ($amountBilled <= 0) {
            $amountBilled = 0.00; // No further billing required
        }

        $timestamp = date('Y-m-d H:i:s');
        $insertStmt = $conn->prepare("INSERT INTO member_payments (member_email, phone_number, payment_code, amount, timestamp) VALUES (?, ?, ?, ?, ?)");
        $insertStmt->bind_param('sssss', $email, $UserPhoneNumber, $TransactionId, $Amount, $timestamp);
        $insertStmt->execute();

        if ($insertStmt->affected_rows > 0) {
            // Insert into invoices table
            $paymentDescription = "Membership Registration Payment";
            $insertInvoice = $conn->prepare("INSERT INTO invoices (payment_description, amount_billed, amount_paid, user_email, invoice_date) VALUES (?, ?, ?, ?, ?)");
            $insertInvoice->bind_param('sddss', $paymentDescription, $amountBilled, $Amount, $email, $timestamp);
            $insertInvoice->execute();

            // Insert into member_registration_payments table
            $insertRegistrationPayment = $conn->prepare("INSERT INTO member_registration_payments (member_email, phone_number, payment_code, amount, timestamp) VALUES (?, ?, ?, ?, ?)");
            $insertRegistrationPayment->bind_param('sssss', $email, $UserPhoneNumber, $TransactionId, $Amount, $timestamp);
            $insertRegistrationPayment->execute();

            if ($insertRegistrationPayment->affected_rows > 0) {
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
                    $subject = "Membership Registration Payment";
                    $message = "Dear User,\n\nThank you for your Registration payment of Ksh $Amount.\n\nTransaction ID: $TransactionId\n\nKindly Download your invoice from the portal\n\nBest regards,\nAGL Team";
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
                    'message' => 'Failed to insert into member_registration_payments.'
                ];
            }
            $insertRegistrationPayment->close();
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
