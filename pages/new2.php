<?php
// Include the necessary libraries
require('../assets/fpdf/fpdf.php');
require_once('../forms/DBconnection.php');
require_once '../assets/phpqrcode/qrlib.php'; // Include the PHP QR Code library

// Set member email
$member_email = 'maganaadmin@agl.or.ke';

// Query to fetch event and member data
$query = "SELECT er.event_name, er.event_date, er.event_location, er.member_name, er.member_email,
                 pm.passport_image, om.logo_image
          FROM event_registrations er
          LEFT JOIN personalmembership pm ON er.member_email = pm.email
          LEFT JOIN organizationmembership om ON er.member_email = om.organization_email
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

    // Create PDF with smaller size
    $pdf = new FPDF('P', 'mm', [100, 150]); // Set smaller custom page size
    $pdf->AddPage();

    $header_image = '../assets/img/logo.png';
    $page_width = $pdf->GetPageWidth(); // Get the page width

    $pdf->SetFillColor(195, 198, 214); // RGB values for background color
    $pdf->Rect(0, 0, 100, 150, 'F'); // Fills the entire page with the background color

    // Header section
    $pdf->SetFont('Arial', 'B', 12); // Set font for 'AGL'
    $pdf->SetXY(0, 5); // Set position at the top, center
    $pdf->Cell(0, 8, 'Association of Government Librarians', 0, 1, 'C'); // Center 'AGL' text at the top
    $pdf->Ln(5);

    // Add the event location, date, and logo in a single row
    $pdf->SetFont('Arial', '', 8); // Smaller font for location and date
    $pdf->SetXY(5, 20);
    $pdf->Cell(30, 5, 'Location: ' . $event_location, 0, 0, 'L'); // Left-aligned location
    if (file_exists($header_image)) {
        $header_image_width = 20; // Reduced width of the logo image
        $header_image_x = ($page_width - $header_image_width) / 2; // Centered image
        $pdf->Image($header_image, $header_image_x, 15, $header_image_width); // Add logo image
    }
    $pdf->SetXY($page_width - 40, 20); // Adjust X position for right alignment
    $pdf->Cell(30, 5, 'Date: ' . $event_date, 0, 0, 'R'); // Right-aligned date

    // Add a blue line below the header section
    $pdf->SetDrawColor(0, 0, 255); // Set color to blue
    $pdf->SetLineWidth(0.5); // Set line width
    $pdf->Line(5, 30, 95, 30); // Draw the line from (x1, y1) to (x2, y2)

    // Add some space after the header
    $pdf->Ln(10);

    // Set font for event name and center it
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 8, $event_name, 0, 1, 'C'); // Center text
    $pdf->Ln(3);

    // Add member name and center it
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(0, 8, $member_name, 0, 1, 'C');

    // Add more space before the QR code
    $pdf->Ln(5);

    // Prepare the content for the QR code using the user data
    $content = "Name: " . $member_name . "\n" .
               "Email: " . $member_email . "\n" .
               "Location: " . $event_location . "\n" .
               "Event Date: " . $event_date;

    // Generate the QR code image and save it to a file
    $qrCodeFile = 'qr_code.png'; // Path where QR code will be saved
    QRcode::png($content, $qrCodeFile, QR_ECLEVEL_L, 4);

    // Center the QR code image
    if (file_exists($qrCodeFile)) {
        $qr_code_width = 30; // Width of the QR code
        $x_position = ($page_width - $qr_code_width) / 2; // Center the image
        $pdf->Image($qrCodeFile, $x_position, 50, $qr_code_width); // Centered QR code at Y=50
        $pdf->Ln(5); // Space below the QR code
    } else {
        $pdf->Cell(0, 8, 'QR Code not found.', 0, 1, 'C'); // Center error message
        $pdf->Ln(5); // Spacing after error message
    }

    // Add a blue line below the QR code
    $pdf->SetDrawColor(0, 0, 255); // Set color to blue
    $pdf->SetLineWidth(0.5); // Set line width
    $pdf->Line(5, $pdf->GetY() + 5, 95, $pdf->GetY() + 5); // Draw the line below the QR code

    // Add website link in italics below the line
    $pdf->Ln(5); // Add space below the line
    $pdf->SetFont('Arial', 'I', 7); // Set font for the website link
    $pdf->Cell(0, 8, 'https://www.agl.or.ke/', 0, 1, 'C'); // Center website link

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
    $pdf->Rect(0, $pdf->GetY() + 10, 100, 15, 'F'); // Draw a filled rectangle for the background

    // Add member status text in the sky blue section
    $pdf->SetXY(0, $pdf->GetY() + 10); // Set position below the rectangle
    $pdf->SetFont('Arial', 'B', 12); // Set font for member status
    $pdf->Cell(0, 8, $member_status, 0, 1, 'C'); // Center member status text

    // Output the PDF in the browser
    $pdf->Output('I', 'Invitation_Card.pdf');
} else {
    echo "No data found for the member email: $member_email";
}

// Close the database connection
$conn->close();
?>
