<?php
session_start(); // Start the session


// /member.log/DARAJA

// require('../assets/fpdf/fpdf.php');

// Include the database connection file
include 'AGLdbconnection.php';
require('../assets/fpdf/fpdf.php');
require_once('../forms/DBconnection.php');
require('../assets/phpqrcode/qrlib.php'); // Include the phpqrcode library

header("Content-Type: application/json");

// Initialize response array
$response = [
    'success' => false,
    'message' => '',
    'errors' => []
];

// Read and log the callback response
$stkCallbackResponse = file_get_contents('php://input');
$logFile = "callbackEventR.json";
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

        // Insert the data into event_registrations table
        $insertQuery = $conn->prepare("INSERT INTO event_registrations (event_id, event_name, event_location, event_date, member_email, member_name, contact, registration_date, payment_code) 
                                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $insertQuery->bind_param("issssssis", $eventId, $eventName, $eventLocation, $eventDate, $email, $memberName, $UserPhoneNumber, $registrationDate, $TransactionId);
        
        if (!$insertQuery->execute()) {
            $response['errors'][] = "Failed to insert event registration: " . $conn->error;
            $_SESSION['response'] = $response;
            exit;
        }

        // Generate PDF attachment
        $pdfFilePath = '../assets/pdf/Invitation_Card_' . $email . '.pdf'; // Define the path for the PDF
        $pdf = new FPDF('P', 'mm', [127, 178]); // Set custom page size
        $pdf->AddPage();

        // Add content to PDF (from your existing PDF code)
        // Make sure to use $member_email = $email; instead of $member_email = 'maganaadmin@agl.or.ke';
        $member_email = $email;

        $query = "SELECT er.event_name, er.event_date, er.event_location, er.member_name, er.member_email,
                     pm.passport_image, om.logo_image
                  FROM event_registrations er
                  LEFT JOIN personalmembership pm ON er.member_email = pm.email
                  LEFT JOIN organizationmembership om ON er.member_email = om.organization_email
                  WHERE er.member_email = '$member_email'";

        $result = $conn->query($query);

        if ($result->num_rows > 0) {
            $data = $result->fetch_assoc();

            // Extracting the data
            $event_name = $data['event_name'];
            $event_date = $data['event_date'];
            $event_location = $data['event_location'];
            $member_name = $data['member_name'];
            $image = !empty($data['passport_image']) ? $data['passport_image'] : $data['logo_image'];

            // Fill the PDF with content (same as your original PDF generation code)
            // Add your PDF generation code here...

            // Save the PDF
            $pdf->Output('F', $pdfFilePath); // Save PDF file to specified path

        } else {
            $response['errors'][] = "No data found for the member email: $member_email";
            $_SESSION['response'] = $response;
            exit;
        }

        // Send email with registration confirmation and attachment
        $to = $email;
        $subject = "Registration Successful!";
        $message = "
            Dear $memberName,

            Thank you for registering for $eventName! We're excited to have you join us on $eventDate.

            Event Details:
            
            Location: $eventLocation
            Time: 10:00 AM

            Please check your email for more details and any future updates.

            We look forward to seeing you there!

            Warm regards,
            The AGL Team
        ";

        // Set headers for email with attachment
        $boundary = md5(time());
        $headers = "From: events@agl.or.ke\r\n";
        $headers .= "Reply-To: events@agl.or.ke\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: multipart/mixed; boundary=\"{$boundary}\"\r\n";

        // Prepare the email body
        $body = "--{$boundary}\r\n";
        $body .= "Content-Type: text/plain; charset=UTF-8\r\n";
        $body .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
        $body .= $message . "\r\n\r\n";

        // Attach the PDF
        $body .= "--{$boundary}\r\n";
        $body .= "Content-Type: application/pdf; name=\"Invitation_Card.pdf\"\r\n";
        $body .= "Content-Disposition: attachment; filename=\"Invitation_Card.pdf\"\r\n";
        $body .= "Content-Transfer-Encoding: base64\r\n";
        $body .= "\r\n";
        $body .= chunk_split(base64_encode(file_get_contents($pdfFilePath))) . "\r\n\r\n";
        $body .= "--{$boundary}--";

        if (!mail($to, $subject, $body, $headers)) {
            $response['errors'][] = "Failed to send registration confirmation email to $to";
            $_SESSION['response'] = $response;
            exit;
        }

        $response['success'] = true;
        $response['message'] = "Event registration successful and confirmation email with attachment sent.";
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
