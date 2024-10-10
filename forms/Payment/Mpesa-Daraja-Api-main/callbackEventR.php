<?php
session_start(); // Start the session

// Include the database connection file
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
    
    if (!$checkoutQuery->execute()) {
        $response['errors'][] = "Database query failed: " . $conn->error;
        $_SESSION['response'] = $response;
        exit;
    }

    $checkoutResult = $checkoutQuery->get_result();
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

        // Create directories for QR codes and PDF cards if they don't exist
        $qrDir = '../../../../assets/img/qrcodes/';
        $PDFDir = '../../../../assets/Documents/EventCards/';

        if (!is_dir($qrDir)) {
            mkdir($qrDir, 0755, true); // Create the directory with proper permissions
        }

        if (!is_dir($PDFDir)) {
            mkdir($PDFDir, 0755, true); // Create the directory with proper permissions
        }

        // Query to fetch event and member data
        $query = "SELECT er.event_name, er.event_date, er.event_location, er.member_name, er.member_email,
                     pm.passport_image
              FROM event_registrations er
              LEFT JOIN personalmembership pm ON er.member_email = pm.email
              WHERE er.member_email = ?";

        // Execute the query
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if any records were returned
        if ($result->num_rows > 0) {
            // Fetch the data
            $data = $result->fetch_assoc();

            $event_name = $data['event_name'];
            $event_date = $data['event_date'];
            $event_location = $data['event_location'];
            $member_name = $data['member_name'];

            // Prepare and execute the query to get user data for the QR code
            $stmt = $conn->prepare("SELECT name, phone, home_address, highest_degree, institution, graduation_year, profession, experience, current_company, position, work_address FROM personalmembership WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $userResult = $stmt->get_result();

            // Fetch the user data
            $userData = $userResult->fetch_assoc();
            if ($userData) {
                // Prepare the content for the QR code
                $content = "Name: " . $userData['name'] . "\n" .
                    "Phone: " . $userData['phone'] . "\n" .
                    "Address: " . $userData['home_address'] . "\n" .
                    "Degree: " . $userData['highest_degree'] . "\n" .
                    "Institution: " . $userData['institution'] . "\n" .
                    "Graduation Year: " . $userData['graduation_year'] . "\n" .
                    "Profession: " . $userData['profession'] . "\n" .
                    "Experience: " . $userData['experience'] . "\n" .
                    "Current Company: " . $userData['current_company'] . "\n" .
                    "Position: " . $userData['position'] . "\n" .
                    "Work Address: " . $userData['work_address'];

                // Generate the QR code image and save it to a file
                $qrCodeFile = $qrDir . 'qr_code_' . md5($email) . '.png'; // Unique filename based on email
                QRcode::png($content, $qrCodeFile, QR_ECLEVEL_L, 4);
            } else {
                $response['errors'][] = 'No user found with that email address.';
                $_SESSION['response'] = $response;
                exit;
            }

            // Create PDF with smaller size
            $pdf = new FPDF('P', 'mm', [100, 150]); // Set smaller custom page size
            $pdf->AddPage();

            // Header image
            $header_image = '../../../assets/img/logo.png';
            $page_width = $pdf->GetPageWidth(); // Get the page width

            $pdf->SetFillColor(195, 198, 214); // RGB values for background color
            $pdf->Rect(0, 0, 100, 150, 'F'); // Fills the entire page with the background color

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
            $pdf->Cell(0, 2, $event_name, 0, 2, 'C'); // Centered event name
            $pdf->Ln(2);

            // Add details in normal font
            $pdf->SetFont('Arial', '', 8);
            $pdf->Cell(0, 2, 'Date: ' . $event_date, 0, 2, 'C');
            $pdf->Cell(0, 2, 'Location: ' . $event_location, 0, 2, 'C');
            $pdf->Ln(3); // Extra line for spacing

            // Add member information
            $pdf->Cell(0, 2, 'Member: ' . $member_name, 0, 2, 'C');
            $pdf->Cell(0, 2, 'Email: ' . $email, 0, 2, 'C');

            // Add QR code image to the PDF
            $pdf->Ln(5); // Add space before the QR code
            $pdf->Image($qrCodeFile, 30, $pdf->GetY(), 40); // QR code image with size

            // Output PDF to a file
            $pdfFileName = $PDFDir . 'EventCard_' . md5($email) . '.pdf'; // Unique filename based on email
            $pdf->Output('F', $pdfFileName);

            // Update the invitation card path in the database
            $invitationCardPath = $pdfFileName;
            $updateQuery = $conn->prepare("UPDATE event_registrations SET invitation_card = ? WHERE member_email = ?");
            $updateQuery->bind_param("ss", $invitationCardPath, $email);

            if (!$updateQuery->execute()) {
                $response['errors'][] = "Failed to update invitation card path: " . $conn->error;
            } else {
                $response['success'] = true;
                $response['message'] = "Payment successful, registration completed!";
                $response['invitation_card_path'] = $invitationCardPath;
            }

            // Close the statements
            $stmt->close();
            $checkoutQuery->close();
            $insertQuery->close();
            $updateQuery->close();
        } else {
            $response['errors'][] = 'No registrations found for the provided email.';
        }
    } else {
        $response['errors'][] = 'No checkout information found for this CheckoutRequestID.';
    }
} else {
    $response['errors'][] = "Payment failed with Result Code: $ResultCode and Description: $ResultDesc";
}

// Send the response back to the user
$_SESSION['response'] = $response;
header('Location: ../../payment_response.php');
exit();
?>
