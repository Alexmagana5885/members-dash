<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require('../assets/fpdf/fpdf.php');

// Check if the logo image exists
$logoPath = '../assets/img/logo.png';
if (!file_exists($logoPath)) {
    die('Logo image not found.');
}

// Member data (you can fetch this from the database in a real implementation)
$member = [
    'id' => '123',
    'name' => 'John Doe',
    'phone' => '123-456-7890',
    'email' => 'john.doe@example.com',
    'dob' => '1990-01-01',
    'home_address' => '123 Main St, Hometown',
    'passport_image' => 'passport.jpg',
    'highest_degree' => 'Master\'s Degree',
    'institution' => 'University of XYZ',
    'start_date' => '2010-09-01',
    'graduation_year' => '2014',
    'completion_letter' => 'completion.pdf',
    'profession' => 'Software Engineer',
    'experience' => '5 years',
    'current_company' => 'ABC Corp',
    'position' => 'Senior Developer',
    'work_address' => '456 Work St, City',
    'payment_Number' => 'MP12345',
    'payment_code' => 'XYZ67890',
    'password' => '********',
    'registration_date' => '2020-01-01'
];

// Create instance of FPDF
$pdf = new FPDF();
$pdf->AddPage();

// Set the logo in the header
$pdf->Image($logoPath, 10, 10, 50); 


$pdf->SetXY(65, 15); // Position the text
$pdf->SetFont('Arial', 'B', 16);
$pdf->SetTextColor(54, 125, 201); 

$pdf->Cell(0, 10, 'Association of Government Librarians');

// Line break after header
$pdf->Ln(20);

// Set font for the member details
$pdf->SetFont('Arial', '', 12);
$pdf->SetTextColor(0, 0, 0); // Reset text color to black

// Function to output member data
function addMemberData($pdf, $label, $data) {
    $pdf->Cell(50, 10, $label, 1);
    $pdf->Cell(0, 10, $data, 1, 1);
}

// Add member data to the PDF
addMemberData($pdf, 'ID', $member['id']);
addMemberData($pdf, 'Name', $member['name']);
addMemberData($pdf, 'Phone Number', $member['phone']);
addMemberData($pdf, 'Email', $member['email']);
addMemberData($pdf, 'Date of Birth', $member['dob']);
addMemberData($pdf, 'Home Address', $member['home_address']);
addMemberData($pdf, 'Highest Degree', $member['highest_degree']);
addMemberData($pdf, 'Institution', $member['institution']);
addMemberData($pdf, 'Start Date', $member['start_date']);
addMemberData($pdf, 'Graduation Year', $member['graduation_year']);
addMemberData($pdf, 'Profession', $member['profession']);
addMemberData($pdf, 'Experience', $member['experience']);
addMemberData($pdf, 'Current Company', $member['current_company']);
addMemberData($pdf, 'Position', $member['position']);
addMemberData($pdf, 'Work Address', $member['work_address']);
addMemberData($pdf, 'Payment Number', $member['payment_Number']);
addMemberData($pdf, 'Payment Code', $member['payment_code']);
addMemberData($pdf, 'Registration Date', $member['registration_date']);

// Add footer with link
$pdf->SetY(-10); // Position 15mm from bottom
$pdf->SetFont('Arial', 'I', 10);
$pdf->Cell(0, 10, 'Visit us at agl.or.ke', 0, 0, 'C');

// Output the PDF as a download
$pdf->Output('D', 'member_details.pdf');  // 'D' forces download
?>
