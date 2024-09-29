<?php
session_start(); // Start the session

// Include the database connection file
include 'AGLdbconnection.php';
require '../members/assets/fpdf/fpdf.php'; 

header("Content-Type: application/json");

// Initialize response array
$response = [
    'success' => false,
    'message' => '',
    'errors' => []
];

// Read and log the callback response
$stkCallbackResponse = file_get_contents('php://input');
$logFile = "EventRcallback.json";
$log = fopen($logFile, "a");
if ($log === false) {
    $response['errors'][] = "Failed to open log file: $logFile";
    $_SESSION['response'] = $response;
    exit;
} else {
    fwrite($log, $stkCallbackResponse);
    fclose($log);
} 

// Decode the JSON response
$data = json_decode($stkCallbackResponse);

if (json_last_error() !== JSON_ERROR_NONE) {
    $response['errors'][] = "Failed to decode JSON: " . json_last_error_msg();
    $_SESSION['response'] = $response;
    exit;
}

// Extract relevant data from the response
$MerchantRequestID = $data->Body->stkCallback->MerchantRequestID ?? null;
$CheckoutRequestID = $data->Body->stkCallback->CheckoutRequestID ?? null;
$ResultCode = $data->Body->stkCallback->ResultCode ?? null;
$ResultDesc = $data->Body->stkCallback->ResultDesc ?? null;
$Amount = $data->Body->stkCallback->CallbackMetadata->Item[0]->Value ?? null;
$TransactionId = $data->Body->stkCallback->CallbackMetadata->Item[1]->Value ?? null;
$UserPhoneNumber = $data->Body->stkCallback->CallbackMetadata->Item[4]->Value ?? null;

// Check if the transaction was successful
if ($ResultCode == 0) {

    // Retrieve the email associated with the CheckoutRequestID from the eventregcheckout table
    $checkoutQuery = $conn->prepare("SELECT email, member_name, event_id, event_name, event_location, event_date FROM eventregcheckout WHERE CheckoutRequestID = ?");
    $checkoutQuery->bind_param("s", $CheckoutRequestID);
    $checkoutQuery->execute();
    $checkoutResult = $checkoutQuery->get_result();

    if (!$checkoutResult) {
        $response['errors'][] = "Database query failed: " . $conn->error;
        $_SESSION['response'] = $response;
        exit;
    }

    if ($checkoutResult->num_rows > 0) {
        $checkoutData = $checkoutResult->fetch_assoc();

        // Extract necessary information
        $email = $checkoutData['email'];
        $memberName = $checkoutData['member_name'];
        $eventId = $checkoutData['event_id'];
        $eventName = $checkoutData['event_name'];
        $eventLocation = $checkoutData['event_location'];
        $eventDate = $checkoutData['event_date'];
        $registrationDate = date('Y-m-d H:i:s');

        // Check if the email exists in personalmembership or organizationmembership
        $personalQuery = $conn->prepare("SELECT passport_image, name FROM personalmembership WHERE email = ?");
        $personalQuery->bind_param("s", $email);
        $personalQuery->execute();
        $personalResult = $personalQuery->get_result();

        $organizationQuery = $conn->prepare("SELECT logo_image, organization_name FROM organizationmembership WHERE organization_email = ?");
        $organizationQuery->bind_param("s", $email);
        $organizationQuery->execute();
        $organizationResult = $organizationQuery->get_result();

        $image = '';
        $name = '';
        
        // Check if the email is in the personalmembership table
        if ($personalResult->num_rows > 0) {
            $personalData = $personalResult->fetch_assoc();
            $image = $personalData['passport_image'];
            $name = $personalData['name'];
        } 
        // Check if the email is in the organizationmembership table
        elseif ($organizationResult->num_rows > 0) {
            $organizationData = $organizationResult->fetch_assoc();
            $image = $organizationData['logo_image'];
            $name = $organizationData['organization_name'];
        }

        // Insert the data into event_registrations table
        $insertQuery = $conn->prepare("INSERT INTO event_registrations (event_id, event_name, event_location, event_date, member_email, member_name, contact, registration_date, payment_code) 
                                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $insertQuery->bind_param("issssssis", $eventId, $eventName, $eventLocation, $eventDate, $email, $memberName, $UserPhoneNumber, $registrationDate, $TransactionId);
        
        if (!$insertQuery->execute()) {
            $response['errors'][] = "Failed to insert event registration: " . $conn->error;
            $_SESSION['response'] = $response;
            exit;
        }

        // Create the PDF file
        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);

        // Add logo image
        if ($image) {
            $pdf->Image($image, 10, 10, 30); 
        }

        // Add event name
        $pdf->Cell(0, 40, $eventName, 0, 1, 'C');

        // Add participant or organization name
        $pdf->Cell(0, 10, $name, 0, 1, 'C');

        // Add event date
        $pdf->Cell(0, 10, "Event Date: $eventDate", 0, 1, 'C');

        // Save the PDF to a file
        $pdfFileName = "event_registration_$TransactionId.pdf";
        $pdf->Output("F", $pdfFileName);

        // Send email with PDF attachment
        $to = $email;
        $subject = "Registration Successful!";
        $message = "
            Dear $memberName,

            Thank you for registering for $eventName! We're excited to have you join us on $eventDate.

            Event Details:
            Location: $eventLocation
            Time: 10:00 AM

            Please check your email for more details and any future updates. Attached is your event registration confirmation.

            We look forward to seeing you there!

            Warm regards,
            The AGL Team
        ";
        $headers = "From: events@agl.or.ke\r\n";
        $headers .= "Reply-To: events@agl.or.ke\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

        // Attach the PDF to the email
        $file = chunk_split(base64_encode(file_get_contents($pdfFileName)));
        $uid = md5(uniqid(time()));

        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: multipart/mixed; boundary=\"".$uid."\"\r\n\r\n";
        $message = "--".$uid."\r\n";
        $message .= "Content-Type: text/plain; charset=UTF-8\r\n";
        $message .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
        $message .= $message."\r\n\r\n";
        $message .= "--".$uid."\r\n";
        $message .= "Content-Type: application/octet-stream; name=\"".$pdfFileName."\"\r\n"; 
        $message .= "Content-Transfer-Encoding: base64\r\n";
        $message .= "Content-Disposition: attachment; filename=\"".$pdfFileName."\"\r\n\r\n";
        $message .= $file."\r\n\r\n";
        $message .= "--".$uid."--";

        if (!mail($to, $subject, $message, $headers)) {
            $response['errors'][] = "Failed to send registration confirmation email to $to";
            $_SESSION['response'] = $response;
            exit;
        }

        $response['success'] = true;
        $response['message'] = "Event registration successful, payment processed, and confirmation email sent with PDF.";
    } else {
        $response['errors'][] = "No records found for CheckoutRequestID: $CheckoutRequestID";
        $_SESSION['response'] = $response;
        exit;
    }
} else {
    $response['errors'][] = "Transaction failed with ResultCode: $ResultCode, Description: $ResultDesc";
    $_SESSION['response'] = $response;
    exit;
}

// Save response in session
$_SESSION['response'] = $response;
?>
