<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include the FPDF library
require('../assets/fpdf/fpdf.php');

// Include the database connection file
require_once('DBconnection.php');

// Get the email parameter from the URL
$email = isset($_GET['email']) ? $_GET['email'] : '';

if (empty($email)) {
    die('Email parameter missing.');
}

// Query to select data from the organizationmembership table based on the email
$sql = 'SELECT * FROM organizationmembership WHERE organization_email = ?';
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die('No organization found with the given email.');
}

$row = $result->fetch_assoc();

// Create a class that extends FPDF to include header and footer
class PDF extends FPDF {
    // Header
    function Header() {
        $this->Image('../assets/img/logo.png', 10, 10, 50);
        $this->SetFont('Arial', 'B', 16);
        $this->SetTextColor(54, 125, 201);
        $this->SetXY(65, 15);
        $this->Cell(0, 10, 'Organization Details', 0, 1, 'C');
        $this->Ln(10); // Line break
    }

    // Footer
    function Footer() {
        $this->SetY(-15); // Position 15mm from bottom
        $this->SetFont('Arial', 'I', 10);
        $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
        $this->Cell(0, 10, 'Visit us at agl.or.ke', 0, 0, 'R'); // Align right
    }
}

// Create a new PDF instance with portrait orientation
$pdf = new PDF();
$pdf->AddPage();

// Set font for the PDF
$pdf->SetFont('Arial', 'B', 12);
$pdf->SetFillColor(153, 204, 255); // Header background color

// Table header
$pdf->Cell(90, 10, 'Field', 1, 0, 'C', true);
$pdf->Cell(100, 10, 'Details', 1, 1, 'C', true);

// Set font for the table body
$pdf->SetFont('Arial', '', 11);

// Table data
$pdf->Cell(90, 10, 'ID', 1);
$pdf->Cell(100, 10, htmlspecialchars($row['id']), 1, 1);
$pdf->Cell(90, 10, 'Organization Name', 1);
$pdf->Cell(100, 10, htmlspecialchars($row['organization_name']), 1, 1);
$pdf->Cell(90, 10, 'Email', 1);
$pdf->Cell(100, 10, htmlspecialchars($row['organization_email']), 1, 1);
$pdf->Cell(90, 10, 'Contact Person', 1);
$pdf->Cell(100, 10, htmlspecialchars($row['contact_person']), 1, 1);
$pdf->Cell(90, 10, 'Contact Phone Number', 1);
$pdf->Cell(100, 10, htmlspecialchars($row['contact_phone_number']), 1, 1);
$pdf->Cell(90, 10, 'Address', 1);
$pdf->Cell(100, 10, htmlspecialchars($row['organization_address']), 1, 1);
$pdf->Cell(90, 10, 'Country', 1);
$pdf->Cell(100, 10, htmlspecialchars($row['location_country']), 1, 1);
$pdf->Cell(90, 10, 'County', 1);
$pdf->Cell(100, 10, htmlspecialchars($row['location_county']), 1, 1);
$pdf->Cell(90, 10, 'Town', 1);
$pdf->Cell(100, 10, htmlspecialchars($row['location_town']), 1, 1);

// $pdf->Cell(90, 10, 'Logo', 1);
// $pdf->Cell(100, 10, !empty($row['logo_image']) ? htmlspecialchars($row['logo_image']) : 'No logo available', 1, 1);
// $pdf->Cell(90, 10, 'Registration Certificate', 1);
// $pdf->Cell(100, 10, !empty($row['registration_certificate']) ? '<a href="' . htmlspecialchars($row['registration_certificate']) . '">View Certificate</a>' : 'No certificate available', 1, 1);

$pdf->Cell(90, 10, 'What You Do', 1);
$pdf->Cell(100, 10, htmlspecialchars($row['what_you_do']), 1, 1);
// $pdf->Cell(90, 10, 'Number of Employees', 1);
// $pdf->Cell(100, 10, htmlspecialchars($row['number_of_employees']), 1, 1);
$pdf->Cell(90, 10, 'Organization Type', 1);
$pdf->Cell(100, 10, htmlspecialchars($row['organization_type']), 1, 1);
$pdf->Cell(90, 10, 'Date of Registration', 1);
$pdf->Cell(100, 10, htmlspecialchars($row['date_of_registration']), 1, 1);
$pdf->Cell(90, 10, 'Start Date', 1);
$pdf->Cell(100, 10, htmlspecialchars($row['start_date']), 1, 1);

// Optionally, you can also include the email in the PDF content
$pdf->Ln(10);
$pdf->Cell(90, 10, 'Information For:', 1);
$pdf->Cell(100, 10, htmlspecialchars($email), 1, 1);

// Output the PDF as a downloadable file
$pdf->Output('D', 'organization_details_' . urlencode($email) . '.pdf'); // Include email in filename

// Close statement and connection
$stmt->close();
$conn->close();
?>
