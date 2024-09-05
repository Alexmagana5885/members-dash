<?php
include 'DB_connection.php';
header("Content-Type: application/json");

$logFile = "Mpesastkresponse.json";
$log = fopen($logFile, "a");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $jsonData = $_POST['formData'] ?? null;

    if ($jsonData) {
        $formData = json_decode($jsonData, true);

        if (json_last_error() === JSON_ERROR_NONE) {
            $name = $formData['name'] ?? null;
            $email = $formData['email'] ?? null;
            $phone = $formData['phone'] ?? null;
            $dob = $formData['dob'] ?? null;
            $home_address = $formData['home_address'] ?? null;
            $passport_image = $formData['passport_image'] ?? null;
            $highest_degree = $formData['highest_degree'] ?? null;
            $institution = $formData['institution'] ?? null;
            $start_date = $formData['start_date'] ?? null;
            $graduation_year = $formData['graduation_year'] ?? null;
            $completion_letter = $formData['completion_letter'] ?? null;
            $profession = $formData['profession'] ?? null;
            $experience = $formData['experience'] ?? null;
            $current_company = $formData['current_company'] ?? null;
            $position = $formData['position'] ?? null;
            $work_address = $formData['work_address'] ?? null;
            $password = $formData['password'] ?? null;

            fwrite($log, "Form data: " . json_encode($formData) . "\n");

            $stmt = $db->prepare("INSERT INTO personalmembership_firstpay (
                                    name, email, phone, dob, home_address, passport_image, 
                                    highest_degree, institution, start_date, graduation_year, 
                                    completion_letter, profession, experience, current_company, 
                                    position, work_address, password, registration_date)
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
            $stmt->bind_param('sssssssssssssssss', $name, $email, $phone, $dob, $home_address, $passport_image, 
                                            $highest_degree, $institution, $start_date, $graduation_year, 
                                            $completion_letter, $profession, $experience, $current_company, 
                                            $position, $work_address, $password);

            if ($stmt->execute()) {
                fwrite($log, "User data inserted successfully.\n");
            } else {
                fwrite($log, "Error inserting user data: " . $stmt->error . "\n");
            }
        } else {
            fwrite($log, "JSON form data decode error: " . json_last_error_msg() . "\n");
        }
    } else {
        fwrite($log, "No form data provided.\n");
    }
}

$stkCallbackResponse = file_get_contents('php://input');
fwrite($log, "M-Pesa callback response: " . $stkCallbackResponse . "\n");

$data = json_decode($stkCallbackResponse);

if (json_last_error() === JSON_ERROR_NONE) {
    $MerchantRequestID = $data->Body->stkCallback->MerchantRequestID ?? null;
    $CheckoutRequestID = $data->Body->stkCallback->CheckoutRequestID ?? null;
    $ResultCode = $data->Body->stkCallback->ResultCode ?? null;
    $ResultDesc = $data->Body->stkCallback->ResultDesc ?? null;
    $Amount = $data->Body->stkCallback->CallbackMetadata->Item[0]->Value ?? null;
    $TransactionId = $data->Body->stkCallback->CallbackMetadata->Item[1]->Value ?? null;
    $UserPhoneNumber = $data->Body->stkCallback->CallbackMetadata->Item[4]->Value ?? null;

    if ($ResultCode == 0) {
        $stmt = $db->prepare("UPDATE personalmembership_firstpay 
                              SET payment_number = ?, payment_code = ? 
                              WHERE email = ?");
        $stmt->bind_param('sss', $UserPhoneNumber, $TransactionId, $email);

        if ($stmt->execute()) {
            fwrite($log, "Payment data updated successfully for email: $email.\n");
        } else {
            fwrite($log, "Error updating payment data: " . $stmt->error . "\n");
        }
    } else {
        fwrite($log, "Transaction failed. ResultCode: $ResultCode, ResultDesc: $ResultDesc\n");
    }
} else {
    fwrite($log, "JSON decode error: " . json_last_error_msg() . "\n");
}

fclose($log);
?>
