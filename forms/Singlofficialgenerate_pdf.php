<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include the FPDF library
require('../assets/fpdf/fpdf.php');

// Include the database connection file
require_once('../forms/DBconnection.php');

// Get the email parameter from the URL
$email = isset($_GET['email']) ? $_GET['email'] : '';

if (empty($email)) {
    die('Email parameter missing.');
}

// Query to select data from both personalmembership and officialsmembers tables based on the email
$sql = "SELECT p.*, o.position AS official_position, o.start_date AS official_start_date, o.number_of_terms
        FROM personalmembership p
        JOIN officialsmembers o ON p.email = o.personalmembership_email
        WHERE p.email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die('No member found with the given email.');
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
        $this->Cell(0, 10, 'Member Details', 0, 1, 'C');
        $this->Ln(10); // Line break
    }

    // Footer
    function Footer() {
        $this->SetY(-15); // Position 15mm from bottom
        $this->SetFont('Arial', 'I', 10);
        $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
        
        // Additional footer information
        $this->SetY(-20); // Move up for the additional footer content
        $this->Cell(0, 10, 'Visit us at agl.or.ke', 0, 0, 'C');
    }
}

// Create a new PDF instance
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
$pdf->Cell(90, 10, 'Name', 1);
$pdf->Cell(100, 10, htmlspecialchars($row['name']), 1, 1);
$pdf->Cell(90, 10, 'Email', 1);
$pdf->Cell(100, 10, htmlspecialchars($row['email']), 1, 1);
$pdf->Cell(90, 10, 'Phone', 1);
$pdf->Cell(100, 10, htmlspecialchars($row['phone']), 1, 1);
// $pdf->Cell(90, 10, 'Date of Birth', 1);
// $pdf->Cell(100, 10, htmlspecialchars($row['dob']), 1, 1);
$pdf->Cell(90, 10, 'Home Address', 1);
$pdf->Cell(100, 10, htmlspecialchars($row['home_address']), 1, 1);
$pdf->Cell(90, 10, 'Highest Degree', 1);
$pdf->Cell(100, 10, htmlspecialchars($row['highest_degree']), 1, 1);
$pdf->Cell(90, 10, 'Institution', 1);
$pdf->Cell(100, 10, htmlspecialchars($row['institution']), 1, 1);
$pdf->Cell(90, 10, 'Start Date', 1);
$pdf->Cell(100, 10, htmlspecialchars($row['start_date']), 1, 1);
$pdf->Cell(90, 10, 'Graduation Year', 1);
$pdf->Cell(100, 10, htmlspecialchars($row['graduation_year']), 1, 1);
$pdf->Cell(90, 10, 'Profession', 1);
$pdf->Cell(100, 10, htmlspecialchars($row['profession']), 1, 1);
$pdf->Cell(90, 10, 'Current Company', 1);
$pdf->Cell(100, 10, htmlspecialchars($row['current_company']), 1, 1);
$pdf->Cell(90, 10, 'Position', 1);
$pdf->Cell(100, 10, htmlspecialchars($row['official_position']), 1, 1);
$pdf->Cell(90, 10, 'Work Address', 1);
$pdf->Cell(100, 10, htmlspecialchars($row['work_address']), 1, 1);
// $pdf->Cell(90, 10, 'Payment Method', 1);
// $pdf->Cell(100, 10, htmlspecialchars($row['payment_method']), 1, 1);
$pdf->Cell(90, 10, 'Payment Code', 1);
$pdf->Cell(100, 10, htmlspecialchars($row['payment_code']), 1, 1);
$pdf->Cell(90, 10, 'Registration Date', 1);
$pdf->Cell(100, 10, htmlspecialchars($row['registration_date']), 1, 1);
$pdf->Cell(90, 10, 'Official Start Date', 1);
$pdf->Cell(100, 10, htmlspecialchars($row['official_start_date']), 1, 1);
$pdf->Cell(90, 10, 'Number of Terms', 1);
$pdf->Cell(100, 10, htmlspecialchars($row['number_of_terms']), 1, 1);

// Output the PDF as a downloadable file
$pdf->Output('D', 'member_details_' . urlencode($email) . '.pdf'); // Include email in filename

// Close statement and connection
$stmt->close();
$conn->close();
?>
