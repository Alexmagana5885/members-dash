<?php
// Include the necessary libraries
require('../assets/fpdf/fpdf.php');
require_once('../forms/DBconnection.php');
require('../assets/phpqrcode/qrlib.php'); // Include the phpqrcode library

// Set member email
// $member_email = 'maganaadmin@agl.or.ke';

// $member_email = $userEmail;
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
    
    // Determine whether to display member photo or organization logo
    $image = !empty($data['passport_image']) ? $data['passport_image'] : $data['logo_image'];

    // Create PDF
    $pdf = new FPDF('P', 'mm', [127, 178]); // Set custom page size
    $pdf->AddPage();

    $header_image = '../assets/img/logo.png'; 
    $page_width = $pdf->GetPageWidth(); // Get the page width

    $pdf->SetFillColor(195, 198, 214); // RGB values
    $pdf->Rect(0, 0, 127, 178, 'F'); // Fills the entire page with the color

    // Add header image and center it
    if (file_exists($header_image)) {
        $header_image_width = 50; // Define the width of the header image
        $x_position = ($page_width - $header_image_width) / 2; // Calculate X to center the image
        $pdf->Image($header_image, $x_position, 5, $header_image_width); // Centered image
    }

    // Add some space after the header
    $pdf->Ln(12); // Adjusted to provide enough space after header image

    // Set font for event name and center it
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, $event_name, 0, 1, 'C'); // Center text with 'C'

    // Add space
    $pdf->Ln(3); // Spacing after event name

    // Add member name and center it
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 10, 'Name: ' . $member_name, 0, 1, 'C'); // Center text with 'C'

    // Add more space before the image
    $pdf->Ln(12); // Spacing before image

    // Add member photo or organization logo and center it
    if (!empty($image)) {
        // Use the full image path from the database
        if (file_exists($image)) {
            $image_width = 40; // Define image width
            $x_position = ($page_width - $image_width) / 2; // Calculate X to center the image
            $pdf->Image($image, $x_position, 70, $image_width); // Centered image at Y=70
            $pdf->Ln(60); // Space below the image based on its height
        } else {
            $pdf->Cell(0, 10, 'Image not found.', 0, 1, 'C'); // Center the error message
            $pdf->Ln(10); // Spacing after error message
        }
    } else {
        $pdf->Ln(50); // Add space if no image is displayed
    }

    // Add event date and center it
    $pdf->Cell(0, 10, 'Event Date: ' . $event_date, 0, 1, 'C'); // Center text

    // Add some space before location
    $pdf->Ln(3);

    // Add event location and center it
    $pdf->Cell(0, 10, 'Location: ' . $event_location, 0, 1, 'C'); // Center text

    // Add a new page for the QR code (back side)
    $pdf->AddPage();

    // Generate QR code for user information
    $qr_content = "Member Name: $member_name\nEvent: $event_name\nDate: $event_date\nLocation: $event_location\nEmail: $member_email";
    $qr_file = '../assets/qr/temp_qrcode.png'; // Path to save the QR code image

    // Generate the QR code and save it as an image
    QRcode::png($qr_content, $qr_file, QR_ECLEVEL_L, 4);

    // Add QR code image to the new page and center it
    if (file_exists($qr_file)) {
        $qr_image_width = 60; // Define QR code image width
        $x_position = ($page_width - $qr_image_width) / 2; // Calculate X to center the image
        $pdf->Image($qr_file, $x_position, 60, $qr_image_width); // Centered QR code at Y=60
    }

    // Output the PDF in the browser
    $pdf->Output('I', 'Invitation_Card.pdf');
} else {
    echo "No data found for the member email: $member_email";
}

// Close the database connection
$conn->close();
?>
