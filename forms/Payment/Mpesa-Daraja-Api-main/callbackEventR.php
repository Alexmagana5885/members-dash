<?php
session_start(); // Start the session

// Include the database connection file

require_once('../../DBconnection.php');
require('../../assets/fpdf/fpdf.php');
require_once('../../forms/DBconnection.php');
require('../../assets/phpqrcode/qrlib.php');


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
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $invitationCardPath = ''; // Initialize invitation card path
        $insertQuery->bind_param("issssssiss", $eventId, $eventName, $eventLocation, $eventDate, $email, $memberName, $UserPhoneNumber, $registrationDate, $TransactionId, $invitationCardPath);

        if (!$insertQuery->execute()) {
            $response['errors'][] = "Failed to insert event registration: " . $conn->error;
            $_SESSION['response'] = $response;
            exit;
        }


        // PDF generation
        // Determine the file path for the PDF
        $pdfDirectory = '../../assets/Documents/EventCards/'; // Directory to save PDFs
        $pdfFilename = $email . '_' . str_replace(' ', '_', $eventName) . '.pdf'; // Name of the PDF file
        $pdfFilePath = $pdfDirectory . $pdfFilename; // Complete path to save PDF

        // Create PDF
        $pdf = new FPDF('P', 'mm', [100, 150]); // Set smaller custom page size
        $pdf->AddPage();

        // Set fill color and draw background rectangle
        $pdf->SetFillColor(195, 198, 214); // RGB values for background color
        $pdf->Rect(0, 0, 100, 150, 'F'); // Fills the entire page with the background color

        // Add header image
        $header_image = '../../assets/img/logo.png';
        $page_width = $pdf->GetPageWidth(); // Get the page width

        // Add header image
        if (file_exists($header_image)) {
            $header_image_width = 35; // Reduced width of the logo image
            $header_image_x = ($page_width - $header_image_width) / 2; // Centered image
            $pdf->Image($header_image, $header_image_x, 5, $header_image_width); // Adjusted logo position to Y=5
        }

        // Header section (Association of Government Librarians text)
        $pdf->SetFont('Arial', 'B', 12); // Set font for 'AGL'
        $pdf->SetXY(0, 25);
        $pdf->Cell(0, 3, 'Association of Government Librarians', 0, 1, 'R'); // Center 'AGL' text below the logo
        $pdf->Ln(5);

        // Add a blue line below the header section
        $pdf->SetDrawColor(0, 0, 255); // Set color to blue
        $pdf->SetLineWidth(0.5); // Set line width
        $pdf->Line(5, 40, 95, 40); // Draw the line from (x1, y1) to (x2, y2)


        // Add some space after the header
        $pdf->Ln(10);


        // Set font for event name and center it
        $pdf->SetFont('Arial', 'B', 10); // Bold for emphasis
        $pdf->Cell(0, 2, $event_name, 0, 2, 'C'); // Center text
        $pdf->Ln(1); // Reduced space

        // Add member name and center it
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 8, $member_name, 0, 1, 'C');

        // Add more space before the QR code
        $pdf->Ln(5);

        // Generate QR code with a unique filename
        $sanitizedEmail = preg_replace('/[^a-zA-Z0-9_]/', '_', $email); // Sanitize email for filename
        $sanitizedEventName = preg_replace('/[^a-zA-Z0-9_]/', '_', $eventName); // Sanitize event name for filename
        $qr_filename = $sanitizedEmail . '_' . $sanitizedEventName . '.png'; // Create unique filename
        $qr_file = '../../assets/img/qrcodes/' . $qr_filename; // Set the file path for the QR code

        $qr_content = "Member Name: $memberName\nEvent: $eventName\nDate: $eventDate\nLocation: $eventLocation\nEmail: $email";
        QRcode::png($qr_content, $qr_file, QR_ECLEVEL_L, 4); // Generate the QR code and save it to the specified path

        // Add QR code to PDF
        if (file_exists($qr_file)) {
            $qr_image_width = 35;
            $x_position = ($pdf->GetPageWidth() - $qr_image_width) / 2;
            $pdf->Image($qr_file, $x_position, 60, $qr_image_width);
        }

        // Add a blue line below the QR code
        $pdf->SetDrawColor(0, 0, 255); // Set color to blue
        $pdf->SetLineWidth(0.5); // Set line width
        $pdf->Line(5, $pdf->GetY() + 5, 95, $pdf->GetY() + 5); // Draw the line below the QR code

        $cellHeight = 5;

        // Set the font for location and date
        $pdf->SetFont('Arial', '', 9); // Smaller font for location and date

        // Set the Y position for the cells
        $pdf->SetXY(5, $pdf->GetY() + 10);

        // Create a cell for the event location
        $pdf->Cell(90, $cellHeight, $event_location, 0, 1, 'L'); // Left-aligned location with increased width

        // Create a cell for the event date with the same width
        $pdf->SetXY($page_width - 95, $pdf->GetY() - 5); // Adjust X position for right alignment
        $pdf->Cell(90, $cellHeight, $event_date, 0, 1, 'R'); // Right-aligned date with the same width

        // Determine member status
        $status_query = "SELECT position FROM officialsmembers WHERE personalmembership_email = '$member_email'";
        $status_result = $conn->query($status_query);

        $member_status = 'Member'; // Default status
        if ($status_result->num_rows > 0) {
            $status_data = $status_result->fetch_assoc();
            $member_status = $status_data['position']; // Set status to the position found
        }

        // Add sky blue background for member status
        $pdf->SetFillColor(135, 206, 250); // Sky blue color
        $pdf->Rect(0, $pdf->GetY() + 10, 100, 20, 'F'); // Adjust the height of the rectangle for status and link

        // Add member status text in the sky blue section
        $pdf->SetXY(0, $pdf->GetY() + 10); // Set position below the rectangle
        $pdf->SetFont('Arial', 'B', 12); // Set font for member status
        $pdf->Cell(0, 8, $member_status, 0, 1, 'C'); // Center member status text

        // Add website link below member status
        $pdf->SetFont('Arial', 'I', 7); // Set font for the website link
        $pdf->Cell(0, 5, 'https://www.agl.or.ke/', 0, 1, 'C'); // Center website link


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
