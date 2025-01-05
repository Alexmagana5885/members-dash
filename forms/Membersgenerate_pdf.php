<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include the FPDF library
require('../assets/fpdf/fpdf.php');

// Include the database connection file
require_once('DBconnection.php');

// Change $mysqli to $conn
$query = "SELECT id, name, phone, email, current_company FROM personalmembership";
$result = $conn->query($query); // Use $conn here

// Check if the query execution was successful
if (!$result) {
    die("Query failed: " . $conn->error); // Use $conn here
}

class PDF extends FPDF {
    // Header
    function Header() {
        // Logo
        $this->Image('../assets/img/logo.png', 10, 10, 50);
        $this->SetFont('Arial', 'B', 16);
        $this->SetTextColor(54, 125, 201); // Set the text color

        $this->SetXY(65, 15); // Position the text

        $this->Cell(0, 10, 'Members Information', 0, 1, 'C');
        $this->Ln(10); // Line break

        // Set column headers
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(30, 10, 'ID', 1); // Width increased by 10
        $this->Cell(85, 10, 'Name', 1); // Width increased by 10
        $this->Cell(40, 10, 'Phone', 1); // Width increased by 10
        $this->Cell(75, 10, 'Email', 1); // Width increased by 10
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

$pdf = new PDF('L'); // 'L' sets the orientation to landscape
$pdf->AddPage();

// Set font for the table body
$pdf->SetFont('Arial', '', 11);

// Loop through the result and output each row in the PDF
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $pdf->Cell(30, 10, htmlspecialchars($row['id']), 1); // Width increased by 10

        $xPos = $pdf->GetX(); // Save the current X position
        $yPos = $pdf->GetY(); // Save the current Y position
        $pdf->MultiCell(85, 10, htmlspecialchars($row['name']), 1); // Width increased by 10
        $pdf->SetXY($xPos + 85, $yPos); // Move to the right after MultiCell

        $pdf->Cell(40, 10, htmlspecialchars($row['phone']), 1); // Width increased by 10

        $xPos = $pdf->GetX(); // Save the current X position
        $yPos = $pdf->GetY(); // Save the current Y position
        $pdf->MultiCell(75, 10, htmlspecialchars($row['email']), 1); // Width increased by 10
        $pdf->SetXY($xPos + 75, $yPos); // Move to the right after MultiCell

        $pdf->Ln();
    }

    // Output the PDF as a downloadable file
    $pdf->Output('D', 'members_information.pdf');
} else {
    // No members found, you could output a PDF or message here instead
    echo "No members found.";
}

// Close the database connection
$conn->close(); // Use $conn here
?>
