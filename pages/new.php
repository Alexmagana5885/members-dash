<?php
// include 'Payment/Mpesa-Daraja-Api-main/DBconnection.php';
include '../DBconnection.php';
header("Content-Type: application/json");

// Log file for debugging
$logFile = "Mpesastkresponse.json";
$log = fopen($logFile, "a");

// Retrieve and decode JSON data from the POST request (assume form data sent here)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Read the form data
    $jsonData = $_POST['formData'] ?? null;

    if ($jsonData) {
        $formData = json_decode($jsonData, true);

        if (json_last_error() === JSON_ERROR_NONE) {
            // Extract user data from the form
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

            // Log form data
            fwrite($log, "Form data: " . json_encode($formData) . "\n");

            // INSERT INTO DATABASE (store initial user data)
            $insertQuery = "INSERT INTO personalmembership (
                            name, email, phone, dob, home_address, passport_image, 
                            highest_degree, institution, start_date, graduation_year, 
                            completion_letter, profession, experience, current_company, 
                            position, work_address, password, registration_date)
                            VALUES (
                            '$name', '$email', '$phone', '$dob', '$home_address', '$passport_image', 
                            '$highest_degree', '$institution', '$start_date', '$graduation_year', 
                            '$completion_letter', '$profession', '$experience', '$current_company', 
                            '$position', '$work_address', '$password', NOW())";

            if (mysqli_query($db, $insertQuery)) {
                fwrite($log, "User data inserted successfully.\n");
            } else {
                fwrite($log, "Error inserting user data: " . mysqli_error($db) . "\n");
            }
        } else {
            fwrite($log, "JSON form data decode error: " . json_last_error_msg() . "\n");
        }
    } else {
        fwrite($log, "No form data provided.\n");
    }

    // Read M-Pesa response from callback
    $stkCallbackResponse = file_get_contents('php://input');
    fwrite($log, "M-Pesa callback response: " . $stkCallbackResponse . "\n");

    // Decode M-Pesa response JSON
    $data = json_decode($stkCallbackResponse);

    if (json_last_error() === JSON_ERROR_NONE) {
        // Extract necessary details from the response
        $MerchantRequestID = $data->Body->stkCallback->MerchantRequestID ?? null;
        $CheckoutRequestID = $data->Body->stkCallback->CheckoutRequestID ?? null;
        $ResultCode = $data->Body->stkCallback->ResultCode ?? null;
        $ResultDesc = $data->Body->stkCallback->ResultDesc ?? null;
        $Amount = $data->Body->stkCallback->CallbackMetadata->Item[0]->Value ?? null;
        $TransactionId = $data->Body->stkCallback->CallbackMetadata->Item[1]->Value ?? null;
        $UserPhoneNumber = $data->Body->stkCallback->CallbackMetadata->Item[4]->Value ?? null;

        // CHECK IF THE TRANSACTION WAS SUCCESSFUL
        if ($ResultCode == 0) {
            // Update `payment_code` and `payment_number` in `personalmembership` table
            $updateQuery = "UPDATE personalmembership 
                            SET payment_number = '$UserPhoneNumber', payment_code = '$TransactionId' 
                            WHERE email = '$email'"; // Email should match the user

            if (mysqli_query($db, $updateQuery)) {
                fwrite($log, "Payment data updated successfully for email: $email.\n");
            } else {
                fwrite($log, "Error updating payment data: " . mysqli_error($db) . "\n");
            }
        } else {
            fwrite($log, "Transaction failed. ResultCode: $ResultCode, ResultDesc: $ResultDesc\n");
        }
    } else {
        fwrite($log, "JSON decode error: " . json_last_error_msg() . "\n");
    }
}

// Close the log file
fclose($log);
?>
