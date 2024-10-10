<?php
session_start(); // Start the session

// Include the database connection file

require_once('../../DBconnection.php');
require('../../../assets/fpdf/fpdf.php');
require('../../../assets/phpqrcode/qrlib.php');

// require_once('../members/forms/DBconnection.php');
// require('../members/assets/fpdf/fpdf.php');
// require('../members/assets/phpqrcode/qrlib.php');

header("Content-Type: application/json");

// Initialize response array
$response = [
    'success' => false,
    'message' => '',
    'errors' => []
];

// Read and log the callback response

// $stkCallbackResponse = file_get_contents('php://input');
// $logFile = "callbackEventR.json";
// $log = fopen($logFile, "a");
// if ($log === false) {
//     $response['errors'][] = "Failed to open log file: $logFile";
//     $_SESSION['response'] = $response;
//     exit;
// } else {
//     fwrite($log, $stkCallbackResponse);
//     fclose($log);
// }

// $data = json_decode($stkCallbackResponse);


$stkCallbackResponse = file_get_contents('php://input');
$logFile = "callbackEventR.json";
file_put_contents($logFile, $stkCallbackResponse . PHP_EOL, FILE_APPEND);

// Decode the JSON response
$data = json_decode($stkCallbackResponse);

// if (json_last_error() !== JSON_ERROR_NONE) {
//     $response['errors'][] = "Failed to decode JSON: " . json_last_error_msg();
//     $_SESSION['response'] = $response;
//     exit;
// }

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

        // invitation card

        // Set member email 
        $member_email = $email;
        // $member_email = 'maganaadmin@agl.or.ke';

        // Create directory for QR codes if it doesn't exist

        $qrDir = '../../../../assets/img/qrcodes/';
        $PDFDir = '../../../../assets/Documents/EventCards/';

        // $qrDir = '../members/assets/testqr/';
        // $PDFDir = '../members/assets/testpdf/';



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
          WHERE er.member_email = '$member_email'";

        // Execute the query
        $result = $conn->query($query);

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
            $stmt->bind_param("s", $member_email);
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
                $qrCodeFile = $qrDir . 'qr_code_' . md5($member_email) . '.png'; // Unique filename based on email
                QRcode::png($content, $qrCodeFile, QR_ECLEVEL_L, 4);
            } else {
                echo 'No user found with that email address.';
                exit;
            }

            // Create PDF with smaller size
            $pdf = new FPDF('P', 'mm', [100, 150]); // Set smaller custom page size
            $pdf->AddPage();

            // $header_image = '../members/assets/img/logo.png';

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
            $pdf->Cell(0, 2, $event_name, 0, 2, 'C'); // Center text
            $pdf->Ln(1); // Reduced space

            // Add member name and center it
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(0, 8, $member_name, 0, 1, 'C');

            // Add more space before the QR code
            $pdf->Ln(5);

            // QR code image logic
            if (file_exists($qrCodeFile)) {
                $qrCodeWidth = 35; // Width of the QR code image
                $x_position = ($page_width - $qrCodeWidth) / 2; // Center the image
                $pdf->Image($qrCodeFile, $x_position, 55, $qrCodeWidth); // Centered image at Y=55
                $pdf->Ln(10); // Space below the QR code
            } else {
                $pdf->Cell(0, 8, 'QR code not found.', 0, 1, 'C'); // Center error message
                $pdf->Ln(5); // Spacing after error message
            }
            $pdf->Ln(20);
            // Add a blue line below the QR code
            $pdf->SetDrawColor(0, 0, 255); // Set color to blue
            $pdf->SetLineWidth(0.5); // Set line width
            $pdf->Line(5, $pdf->GetY() + 5, 95, $pdf->GetY() + 5); // Draw the line below the QR code

            // Set the same height for both cells
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

            // Sanitize the event name
            $sanitized_event_name = str_replace(' ', '_', $event_name);

            // Create the PDF file name using the event name and member email
            $pdfFilePath = $PDFDir . $sanitized_event_name . '_' . md5($member_email) . '.pdf'; // Define the file path and name

            // Save the PDF to a file ('F' stands for File)
            $pdf->Output($pdfFilePath, 'F');

            // Update the database with the PDF file path
            $updateQuery = $conn->prepare("UPDATE event_registrations SET invitation_card = ? WHERE member_email = ? AND event_id = ?");
            $updateQuery->bind_param("ssi", $pdfFilePath, $member_email, $event_id);

            if (!$updateQuery->execute()) {
                $response['errors'][] = "Failed to update invitation card path: " . $conn->error;
                $_SESSION['response'] = $response;
                exit;
            }
        } else {
            echo "No event registration found for this member.";
        }




        // Send email with registration confirmation (as already implemented)
        $to = $email;
        $subject = "Registration Successful!";
        $message = "
            Dear $memberName,

            Thank you for registering for $eventName! We're excited to have you join us on $eventDate.
            
            Location: $eventLocation

            Kindly download your invitation card from the portal.

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
