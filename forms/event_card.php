<?php
// Include the necessary libraries
include 'DBconnection.php';
require('../assets/fpdf/fpdf.php');
require_once '../assets/phpqrcode/qrlib.php';


// event_card.php


$member_email = $_POST['email'];
$event_id = $_POST['eventName'];

// echo "Member Email: " . $member_email . "<br>";
// echo "Event ID: " . $event_id;

$qrDir = '../assets/img/qrcodes/';
if (!is_dir($qrDir)) {
    mkdir($qrDir, 0755, true); // Create the directory with proper permissions
}

// Query to fetch event and member data
$query = "SELECT er.event_name, er.event_date, er.event_location, er.member_name, er.member_email,
       er.invitation_card, er.contact, er.payment_code
FROM event_registrations er
WHERE er.member_email = '$member_email' AND er.event_id = '$event_id'
 ";
$result = $conn->query($query);


// Execute the query
// $result = $conn->query($query);

// Check if any records were returned
if ($result->num_rows > 0) {
    // Fetch the data
    $data = $result->fetch_assoc();

    $event_name = $data['event_name'];
    $event_date = $data['event_date'];
    $event_location = $data['event_location'];
    $member_name = $data['member_name'];

    // Query to check if the member email exists in personalmembership table
    $stmtPersonal = $conn->prepare("SELECT name, phone, home_address, highest_degree, institution, graduation_year, profession, experience, current_company, position, work_address FROM personalmembership WHERE email = ?");
    $stmtPersonal->bind_param("s", $member_email);
    $stmtPersonal->execute();
    $userResultPersonal = $stmtPersonal->get_result();
    $userDataPersonal = $userResultPersonal->fetch_assoc();

    // Query to check if the member email exists in organizationmembership table
    $stmtOrg = $conn->prepare("SELECT organization_name, contact_person, contact_phone_number, organization_address, location_country, location_county, location_town, organization_type, what_you_do FROM organizationmembership WHERE organization_email = ?");
    $stmtOrg->bind_param("s", $member_email);
    $stmtOrg->execute();
    $userResultOrg = $stmtOrg->get_result();
    $userDataOrg = $userResultOrg->fetch_assoc();

    // Fetch the user data
    // $userData = $userResult->fetch_assoc();

    if ($userDataPersonal) {
        // Personal member data found
        $content = "Name: " . $userDataPersonal['name'] . "\n" .
            "Phone: " . $userDataPersonal['phone'] . "\n" .
            "Address: " . $userDataPersonal['home_address'] . "\n" .
            "Degree: " . $userDataPersonal['highest_degree'] . "\n" .
            "Institution: " . $userDataPersonal['institution'] . "\n" .
            "Graduation Year: " . $userDataPersonal['graduation_year'] . "\n" .
            "Profession: " . $userDataPersonal['profession'] . "\n" .
            "Experience: " . $userDataPersonal['experience'] . "\n" .
            "Current Company: " . $userDataPersonal['current_company'] . "\n" .
            "Position: " . $userDataPersonal['position'] . "\n" .
            "Work Address: " . $userDataPersonal['work_address'];
    } elseif ($userDataOrg) {
        // Organization member data found
        $content = "Organization Name: " . $userDataOrg['organization_name'] . "\n" .
            "Contact Person: " . $userDataOrg['contact_person'] . "\n" .
            "Phone: " . $userDataOrg['contact_phone_number'] . "\n" .
            "Address: " . $userDataOrg['organization_address'] . "\n" .
            "Country: " . $userDataOrg['location_country'] . "\n" .
            "County: " . $userDataOrg['location_county'] . "\n" .
            "Town: " . $userDataOrg['location_town'] . "\n" .
            "Organization Type: " . $userDataOrg['organization_type'] . "\n" .
            "What You Do: " . $userDataOrg['what_you_do'];
    } else {
        echo 'No user found with that email address.';
        exit;
    }

    // Generate the QR code image and save it to a file
    $qrCodeFile = $qrDir . 'qr_code_' . md5($member_email) . '.png';
    QRcode::png($content, $qrCodeFile, QR_ECLEVEL_L, 4);


    // Create PDF with smaller size
    $pdf = new FPDF('P', 'mm', [100, 150]); // Set smaller custom page size
    $pdf->AddPage();

    $header_image = '../assets/img/logo.png';
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

    $pdf->SetFont('Arial', 'B', 8); 
    $pdf->Cell(0, 1, $event_name, 0, 2, 'C');
    $pdf->Ln(1); 

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

    // Output the PDF

    $pdfDir = '../assets/Documents/EventCards/'; // Specify your desired directory

    // Create the directory if it doesn't exist
    if (!is_dir($pdfDir)) {
        mkdir($pdfDir, 0755, true); // Create the directory with proper permissions
    }

    $sanitized_event_name = preg_replace('/[^A-Za-z0-9\-]/', '_', $event_name);
    $pdfFileName = $sanitized_event_name . '_' . $member_email . '.pdf';


    $pdf->Output('D', $pdfFileName);
} else {
    echo "No event registration found for this member.";
}

// Close the database connection
$conn->close();
