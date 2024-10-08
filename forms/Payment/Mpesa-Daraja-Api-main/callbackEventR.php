<?php
session_start(); // Start the session

require_once('../../DBconnection.php');
require('../../../assets/fpdf/fpdf.php');
require('../../../assets/phpqrcode/qrlib.php');

header("Content-Type: application/json");

// Initialize response array
$response = [
    'success' => false,
    'message' => '',
    'errors' => []
];

// Define directories for PDFs and QR codes
$pdfDirectory = '../../../../members/assets/Documents/EventCards/';
$qrCodeDirectory = '../../../../members/assets/img/qrcodes/';

// Create directories if not existing
if (!is_dir($pdfDirectory) && !mkdir($pdfDirectory, 0777, true)) {
    $response['errors'][] = "Failed to create PDF directory: $pdfDirectory";
    $_SESSION['response'] = $response;
    exit;
}

if (!is_dir($qrCodeDirectory) && !mkdir($qrCodeDirectory, 0777, true)) {
    $response['errors'][] = "Failed to create QR code directory: $qrCodeDirectory";
    $_SESSION['response'] = $response;
    exit;
}

// Read and log the callback response
$stkCallbackResponse = file_get_contents('php://input');
$logFile = "callbackEventR.json";
if (file_put_contents($logFile, $stkCallbackResponse, FILE_APPEND) === false) {
    $response['errors'][] = "Failed to open log file: $logFile";
    $_SESSION['response'] = $response;
    exit;
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

// If the transaction was successful, proceed
if ($ResultCode == 0) {
    // Retrieve the email associated with the CheckoutRequestID
    $checkoutQuery = $conn->prepare("SELECT email, member_name, event_id, event_name, event_location, event_date FROM eventregcheckout WHERE CheckoutRequestID = ?");
    $checkoutQuery->bind_param("s", $CheckoutRequestID);
    $checkoutQuery->execute();
    $checkoutResult = $checkoutQuery->get_result();

    if ($checkoutResult->num_rows > 0) {
        $checkoutData = $checkoutResult->fetch_assoc();
        $email = $checkoutData['email'];
        $memberName = $checkoutData['member_name'];
        $eventId = $checkoutData['event_id'];
        $eventName = $checkoutData['event_name'];
        $eventLocation = $checkoutData['event_location'];
        $eventDate = $checkoutData['event_date'];
        $registrationDate = date('Y-m-d H:i:s');

        // Insert the registration data
        $invitationCardPath = ''; // Path will be updated later
        $insertQuery = $conn->prepare("INSERT INTO event_registrations (event_id, event_name, event_location, event_date, member_email, member_name, contact, registration_date, payment_code, invitation_card)  
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $insertQuery->bind_param("ssssssssss", $eventId, $eventName, $eventLocation, $eventDate, $email, $memberName, $UserPhoneNumber, $registrationDate, $TransactionId, $invitationCardPath);

        if (!$insertQuery->execute()) {
            $response['errors'][] = "Failed to insert event registration: " . $conn->error;
            $_SESSION['response'] = $response;
            exit;
        }

        // Create PDF and QR Code
        $pdfFilename = $email . '_' . str_replace(' ', '_', $eventName) . '.pdf';
        $pdfFilePath = $pdfDirectory . $pdfFilename;
        $pdf = new FPDF('P', 'mm', [127, 178]); // Custom page size
        $pdf->AddPage();
        $pdf->SetFillColor(195, 198, 214); // Background color
        $pdf->Rect(0, 0, 100, 150, 'F'); // Fill entire page

        // Header image and text
        $headerImage = '../../../assets/img/logo.png';
        if (file_exists($headerImage)) {
            $pdf->Image($headerImage, ($pdf->GetPageWidth() - 35) / 2, 5, 35); // Centered logo
        }
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetXY(0, 25);
        $pdf->Cell(0, 3, 'Association of Government Librarians', 0, 1, 'R');
        $pdf->Ln(5);

        // Line after header
        $pdf->SetDrawColor(0, 0, 255);
        $pdf->SetLineWidth(0.5);
        $pdf->Line(5, 40, 95, 40);
        $pdf->Ln(10);

        // Event details
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(0, 2, $eventName, 0, 2, 'C');
        $pdf->Ln(1);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 8, $memberName, 0, 1, 'C');
        $pdf->Ln(5);

        // Generate and add QR code
        $qrContent = "Member Name: $memberName\nEvent: $eventName\nDate: $eventDate\nLocation: $eventLocation\nEmail: $email";
        $qrFilename = preg_replace('/[^a-zA-Z0-9_]/', '_', $email) . '_' . preg_replace('/[^a-zA-Z0-9_]/', '_', $eventName) . '.png';
        $qrFile = $qrCodeDirectory . $qrFilename;
        QRcode::png($qrContent, $qrFile, QR_ECLEVEL_L, 4);

        if (file_exists($qrFile)) {
            $pdf->Image($qrFile, ($pdf->GetPageWidth() - 60) / 2, 60, 60);
        }

        // Save the PDF
        $pdf->Output('F', $pdfFilePath);

        // Update the event registration with the invitation card path
        $updateQuery = $conn->prepare("UPDATE event_registrations SET invitation_card = ? WHERE member_email = ? AND event_id = ?");
        $updateQuery->bind_param("ssi", $pdfFilePath, $email, $eventId);
        if (!$updateQuery->execute()) {
            $response['errors'][] = "Failed to update invitation card path: " . $conn->error;
            $_SESSION['response'] = $response;
            exit;
        }

        $response['success'] = true;
        $response['message'] = 'Registration and invitation card created successfully.';
        $_SESSION['response'] = $response;
    } else {
        $response['errors'][] = "No data found for CheckoutRequestID.";
        $_SESSION['response'] = $response;
    }
} else {
    $response['message'] = "Transaction failed: $ResultDesc";
    $_SESSION['response'] = $response;
}

echo json_encode($response);
?>
