<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include the FPDF library
require('../assets/fpdf/fpdf.php');

// Include the database connection file
require_once('DBconnection.php');

// Update the query to select from organizationmembership
$query = "
    SELECT 
        id, 
        organization_name, 
        organization_email, 
        contact_person, 
        contact_phone_number, 
        start_date
    FROM organizationmembership
";
$result = $conn->query($query); // Use $conn here

// Check if the query execution was successful
if (!$result) {
    die("Query failed: " . $conn->error); // Use $conn here
}

// Create a class that extends FPDF to include header and footer
class PDF extends FPDF {
    // Header
    function Header() {
        // Logo
        $this->Image('../assets/img/logo.png', 10, 10, 50);
        $this->SetFont('Arial', 'B', 16);
        $this->SetTextColor(54, 125, 201); // Set the text color

        $this->SetXY(65, 15); // Position the text
        $this->Cell(0, 10, 'Organization Information', 0, 1, 'C');
        $this->Ln(10); // Line break

        // Set column headers
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(20, 10, 'ID', 1);
        $this->Cell(50, 10, 'Organization Name', 1);
        $this->Cell(60, 10, 'Email', 1);
        $this->Cell(40, 10, 'Contact Person', 1);
        $this->Cell(30, 10, 'Phone', 1);
        $this->Cell(30, 10, 'Start Date', 1);
        $this->Ln();
    }

    // Footer
    function Footer() {
        $this->SetY(-15); // Position 15mm from bottom
        $this->SetFont('Arial', 'I', 10);
        $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
        $this->Cell(0, 10, 'Visit us at agl.or.ke', 0, 0, 'R'); // Align right
    }
}

// Create a new PDF instance with landscape orientation
$pdf = new PDF('L', 'mm', 'A4'); // L for landscape
$pdf->AddPage();

// Set font for the table body
$pdf->SetFont('Arial', '', 11);

// Loop through the result and output each row in the PDF
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $pdf->Cell(20, 10, htmlspecialchars($row['id']), 1);
        $pdf->Cell(50, 10, htmlspecialchars($row['organization_name']), 1);
        $pdf->Cell(60, 10, htmlspecialchars($row['organization_email']), 1);
        $pdf->Cell(40, 10, htmlspecialchars($row['contact_person']), 1);
        $pdf->Cell(30, 10, htmlspecialchars($row['contact_phone_number']), 1);
        $pdf->Cell(30, 10, htmlspecialchars($row['start_date']), 1);
        $pdf->Ln();
    }

    // Output the PDF as a downloadable file
    $pdf->Output('D', 'organization_information.pdf');
} else {
    // No organizations found, you could output a PDF or message here instead
    echo "No organizations found.";
}

// Close the database connection
$conn->close(); // Use $conn here
?>
