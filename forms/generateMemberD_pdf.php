<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

require('../assets/fpdf/fpdf.php');
require_once('../forms/DBconnection.php'); // Include your DB connection script

// Get the member's email from the URL
$email = isset($_GET['email']) ? $conn->real_escape_string($_GET['email']) : '';

if (!$email) {
    die('Email not provided.');
}

// Query to fetch the member's details from the database
$sql = "SELECT * FROM personalmembership WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();

// Check if member exists
if ($result->num_rows === 0) {
    die('No member found with the provided email.');
}

$member = $result->fetch_assoc();

// Check if the logo image exists
$logoPath = '../assets/img/logo.png';
if (!file_exists($logoPath)) {
    die('Logo image not found.');
}

// Create instance of FPDF
$pdf = new FPDF();
$pdf->AddPage();

// Set the logo in the header
$pdf->Image($logoPath, 10, 10, 50);

$pdf->SetXY(65, 15); // Position the text
$pdf->SetFont('Arial', 'B', 16);
$pdf->SetTextColor(54, 125, 201);

$pdf->Cell(0, 10, 'Association of Government Librarians');

// Function to check for null values and sanitize data
function checkNull($value) {
    return isset($value) && $value !== '' ? htmlspecialchars($value) : 'Null';
}

// Line break after header
$pdf->Ln(20);

// Set font for the member details
$pdf->SetFont('Arial', '', 12);
$pdf->SetTextColor(0, 0, 0); // Reset text color to black

// Function to output member data with MultiCell for dynamic height
function addMemberData($pdf, $label, $data) {
    // Label cell (fixed width, right-aligned)
    $pdf->Cell(50, 10, $label, 1);
    
    // Dynamic cell (allows text wrapping)
    $pdf->MultiCell(0, 10, $data, 1);
}

// Add member data to the PDF with null checking
addMemberData($pdf, 'ID', checkNull($member['id']));
addMemberData($pdf, 'Name', checkNull($member['name']));
addMemberData($pdf, 'Phone Number', checkNull($member['phone']));
addMemberData($pdf, 'Email', checkNull($member['email']));
// addMemberData($pdf, 'Date of Birth', checkNull($member['dob']));
addMemberData($pdf, 'Home Address', checkNull($member['home_address']));
addMemberData($pdf, 'Highest Degree', checkNull($member['highest_degree']));
addMemberData($pdf, 'Institution', checkNull($member['institution']));
addMemberData($pdf, 'Start Date', checkNull($member['start_date']));
addMemberData($pdf, 'Graduation Year', checkNull($member['graduation_year']));
addMemberData($pdf, 'Profession', checkNull($member['profession']));
addMemberData($pdf, 'Experience', checkNull($member['experience']));
addMemberData($pdf, 'Current Company', checkNull($member['current_company']));
addMemberData($pdf, 'Position', checkNull($member['position']));
addMemberData($pdf, 'Work Address', checkNull($member['work_address']));
addMemberData($pdf, 'Payment Number', checkNull($member['payment_Number']));
addMemberData($pdf, 'Payment Code', checkNull($member['payment_code']));
addMemberData($pdf, 'Registration Date', checkNull($member['registration_date']));

// Add footer with link
$pdf->SetY(-10); // Position 10mm from bottom
$pdf->SetFont('Arial', 'I', 10);
$pdf->Cell(0, 10, 'Visit us at agl.or.ke', 0, 0, 'C');

// Output the PDF as a download
$pdf->Output('D', 'member_details.pdf');  // 'D' forces download

// Close the database connection
$conn->close();
?>
