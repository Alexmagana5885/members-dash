<?php
session_start(); // Start the session

// Include the database connection file
include '../forms/AGLdbconnection.php';
require('../members/assets/fpdf/fpdf.php');
require_once('../members/forms/DBconnection.php');
require('../members/assets/phpqrcode/qrlib.php'); // Include the phpqrcode library

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
        $insertQuery = $conn->prepare("INSERT INTO event_registrations (event_id, event_name, event_location, event_date, member_email, member_name, contact, registration_date, payment_code, invitation_card)  
                                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $invitationCardPath = ''; // Initialize invitation card path
        $insertQuery->bind_param("issssssis", $eventId, $eventName, $eventLocation, $eventDate, $email, $memberName, $UserPhoneNumber, $registrationDate, $TransactionId);
        
        if (!$insertQuery->execute()) {
            $response['errors'][] = "Failed to insert event registration: " . $conn->error;
            $_SESSION['response'] = $response;
            exit;
        }

        // PDF generation
        // Determine the file path for the PDF
        $pdfDirectory = '../members/assets/Documents/EventCards/'; // Directory to save PDFs
        $pdfFilename = $email . '_' . str_replace(' ', '_', $eventName) . '.pdf'; // Name of the PDF file
        $pdfFilePath = $pdfDirectory . $pdfFilename; // Complete path to save PDF
        
        // Create PDF
        $pdf = new FPDF('P', 'mm', [127, 178]); // Set custom page size
        $pdf->AddPage();
        
        // Set fill color and draw background rectangle
        $pdf->SetFillColor(195, 198, 214);
        $pdf->Rect(0, 0, 127, 178, 'F');
        
        // Add header image
        $header_image = '../members/assets/img/logo.png';
        if (file_exists($header_image)) {
            $header_image_width = 50;
            $x_position = ($pdf->GetPageWidth() - $header_image_width) / 2;
            $pdf->Image($header_image, $x_position, 5, $header_image_width);
        }
        $pdf->Ln(12); // Spacing after header image

        // Add event name
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, $eventName, 0, 1, 'C');
        $pdf->Ln(3); // Spacing

        // Add member name
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 10, 'Name: ' . $memberName, 0, 1, 'C');
        $pdf->Ln(12); // Spacing

        // Add event date and location
        $pdf->Cell(0, 10, 'Event Date: ' . $eventDate, 0, 1, 'C');
        $pdf->Ln(3);
        $pdf->Cell(0, 10, 'Location: ' . $eventLocation, 0, 1, 'C');
        
        // Generate QR code with a unique filename
        $sanitizedEmail = preg_replace('/[^a-zA-Z0-9_]/', '_', $email); // Sanitize email for filename
        $sanitizedEventName = preg_replace('/[^a-zA-Z0-9_]/', '_', $eventName); // Sanitize event name for filename
        $qr_filename = $sanitizedEmail . '_' . $sanitizedEventName . '.png'; // Create unique filename
        $qr_file = '../members/assets/img/qrcodes/' . $qr_filename; // Set the file path for the QR code
        
        $qr_content = "Member Name: $memberName\nEvent: $eventName\nDate: $eventDate\nLocation: $eventLocation\nEmail: $email";
        QRcode::png($qr_content, $qr_file, QR_ECLEVEL_L, 4); // Generate the QR code and save it to the specified path
        
        // Add QR code to PDF
        if (file_exists($qr_file)) {
            $qr_image_width = 60;
            $x_position = ($pdf->GetPageWidth() - $qr_image_width) / 2;
            $pdf->Image($qr_file, $x_position, 60, $qr_image_width);
        }
 
        // Output the PDF to the file
        $pdf->Output('F', $pdfFilePath); // Save the PDF to the specified file path

        // Update the invitation_card field with the PDF path
        $updateQuery = $conn->prepare("UPDATE event_registrations SET invitation_card = ? WHERE member_email = ? AND event_id = ?");
        $updateQuery->bind_param("ssi", $pdfFilePath, $email, $eventId);
        
        if (!$updateQuery->execute()) {
            $response['errors'][] = "Failed to update invitation card path: " . $conn->error;
            $_SESSION['response'] = $response;
            exit;
        }

        // Send email with registration confirmation (as already implemented)
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
        $headers = "From: events@agl.or.ke\r\n";
        $headers .= "Reply-To: events@agl.or.ke\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

        if (!mail($to, $subject, $message, $headers)) {
            $response['errors'][] = "Failed to send registration confirmation email to $to";
            $_SESSION['response'] = $response;
            exit;
        }

        $response['success'] = true;
        $response['message'] = "Event registration successful, PDF generated, and confirmation email sent.";
    } else {
        $response['errors'][] = "No records found for CheckoutRequestID: $CheckoutRequestID";
    }
} else {
    $response['errors'][] = "Transaction failed: $ResultDesc";
}

// Save the response to the session
$_SESSION['response'] = $response;

// Return the JSON response
echo json_encode($response);
?>
