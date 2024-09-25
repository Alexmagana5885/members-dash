<?php
require_once('../forms/DBconnection.php');
require('../assets/fpdf/fpdf.php'); 

class PDF extends FPDF {
    // Header
    function Header() {
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, 'Members Information', 0, 1, 'C');
        $this->Ln(5);
    }

    // Footer
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
    }

    // Table header
    function TableHeader() {
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(20, 10, 'ID', 1);
        $this->Cell(50, 10, 'Name', 1);
        $this->Cell(30, 10, 'Phone', 1);
        $this->Cell(50, 10, 'Email', 1);
        // $this->Cell(40, 10, 'Position', 1);
        $this->Cell(40, 10, 'Company', 1);
        $this->Ln();
    }

    // Table rows
    function TableRows($conn) {
        $this->SetFont('Arial', '', 10);
        // Query to get data from personalmembership
        $sql = 'SELECT * FROM personalmembership';
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $this->Cell(20, 10, $row['id'], 1);
                $this->Cell(50, 10, $row['name'], 1);
                $this->Cell(30, 10, $row['phone'], 1);
                $this->Cell(50, 10, $row['email'], 1);
                // $this->Cell(40, 10, $row['position'], 1);
                $this->Cell(40, 10, $row['current_company'], 1);
                $this->Ln();
            }
        }
    }
}

// Create instance of the PDF class
$pdf = new PDF();
$pdf->AddPage();
$pdf->TableHeader(); // Add the table header
$pdf->TableRows($conn); // Add the table rows from the database
$pdf->Output(); // Output the PDF
?>
