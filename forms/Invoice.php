<?php
require('../assets/fpdf/fpdf.php');

require_once('AGLDBconnect.php');
ob_start();
class PDF extends FPDF
{
    function Header()
    {
        $this->SetFillColor(110, 193, 228);
        $this->Rect(0, 0, $this->w, 10, 'F');
    }

    function InvoiceHeader($date)

    {
        global $conn;
        $invoice_date = date('Y-m-d', strtotime($date));

        $query = "SELECT id, invoice_date FROM invoices WHERE DATE(invoice_date) = '$invoice_date' ORDER BY id DESC LIMIT 1";
        $result = $conn->query($query);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $invoice_id = $row['id'];
            $invoice_date = $row['invoice_date'];
        } else {
            die("No invoice data found for the specified date.");
        }


        $formatted_date = date('d/m/Y', strtotime($invoice_date));
        // $invoice_number = sprintf('AGL%06d', $invoice_id);
        $invoice_number = $invoice_id;
        $this->SetFillColor(230, 234, 240);
        $this->Rect(0, 10, $this->w, 40, 'F');

        // logo
        $this->SetXY(10, 15);
        $this->Image('../assets/img/logo.png', 10, 15, 50);

        // Invoice details
        $this->SetFont('Arial', '', 10);
        $this->SetTextColor(85, 85, 85);
        $this->SetXY(10, 40);
        $this->Cell(50, 5, 'info@agl.or.ke', 0, 1);
        $this->Cell(50, 5, '253-722-605-048', 0, 1);

        // Title
        $this->SetXY(120, 15);
        $this->SetFont('Arial', 'B', 14);
        $this->SetTextColor(24, 49, 90);
        $this->Cell(70, 10, 'INVOICE', 0, 1, 'R');

        // Date
        $this->SetFont('Arial', '', 10);
        $this->SetTextColor(0, 0, 0);
        $this->SetXY(120, 25);
        $this->Cell(70, 5, $formatted_date, 0, 1, 'R');

        $this->Line(120, 30, 200, 30);

        // Invoice number
        $this->SetFont('Arial', 'B', 10);
        $this->SetTextColor(24, 49, 90);
        $this->SetXY(120, 32);
        $this->Cell(70, 5, 'INVOICE NO.', 0, 1, 'R');

        $this->Line(120, 37, 200, 37);

        $this->SetFont('Arial', '', 10);
        $this->SetTextColor(0, 0, 0);
        $this->SetXY(120, 40);
        $this->Cell(70, 5, $invoice_number, 0, 1, 'R');
    }

    function BillAndPayTo($user_email, $user_type)
    {
        global $conn;
        $name = $user_id = $address = $phone = $email = '';
        if ($user_type == 'personal') {
            $query = "SELECT * FROM personalmembership WHERE email = ?";
            $stmt = $conn->prepare($query);
            if ($stmt === false) {
                die("Error preparing the query: " . $conn->error);
            }
            $stmt->bind_param('s', $user_email);
            if (!$stmt->execute()) {
                die("Error executing the query: " . $stmt->error);
            }
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $user_data = $result->fetch_assoc();
                $name = $user_data['name'];
                $user_id = $user_data['id'];
                $address = $user_data['home_address'];
                $phone = $user_data['phone'];
                $email = $user_data['email'];
            } else {
                echo "No personal user found.";
            }
        } else if ($user_type == 'organization') {
            $query = "SELECT * FROM officialsmembers WHERE personalmembership_email = ?";
            $stmt = $conn->prepare($query);
            if ($stmt === false) {
                die("Error preparing the query: " . $conn->error);
            }
            $stmt->bind_param('s', $user_email);
            if (!$stmt->execute()) {
                die("Error executing the query: " . $stmt->error);
            }
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $org_data = $result->fetch_assoc();
                $name = $org_data['organization_name'];
                $user_id = $org_data['id'];
                $address = 'Organization Address';
                $phone = 'Organization Phone';
                $email = $user_email;
            } else {
                echo "No organization user found.";
            }
        }

        // $stmt->close();
        $this->SetXY(10, 55);
        $this->SetFont('Arial', 'B', 12);
        $this->SetTextColor(24, 49, 90);
        $this->Cell(90, 10, 'BILL TO', 0, 1);
        $this->Line(10, 65, 100, 65);

        $this->SetFont('Arial', '', 10);
        $this->SetTextColor(85, 85, 85);
        $this->SetXY(10, 68);
        $this->Cell(90, 5, $name, 0, 1);
        $this->Cell(90, 5, $user_id, 0, 1);
        $this->Cell(90, 5, $address, 0, 1);
        $this->Cell(90, 5, $phone, 0, 1);
        $this->Cell(90, 5, $email, 0, 1);

        $this->SetXY(110, 55);
        $this->SetFont('Arial', 'B', 12);
        $this->SetTextColor(24, 49, 90);
        $this->Cell(90, 10, 'PAY TO', 0, 1, 'R');
        $this->Line(110, 65, 200, 65);

        $this->SetFont('Arial', '', 10);
        $this->SetTextColor(85, 85, 85);
        $this->SetXY(110, 68);
        $this->Cell(90, 5, 'ASSOCIATION OF GOVERNMENT LIBRARIANS', 0, 1, 'R');
        $this->Cell(190, 5, 'info@agl.or.ke', 0, 1, 'R');
        $this->Cell(190, 5, '254-722-605-048', 0, 1, 'R');
    }

    function ItemsTable($user_email, $date)
    {
        global $conn;
        $total_billed = 0;
        $total_paid = 0;
        $query = "SELECT * FROM invoices WHERE user_email = ? AND invoice_date = ?";
        $stmt = $conn->prepare($query);
        if ($stmt === false) {
            die("Error preparing the query: " . $conn->error);
        }
        $stmt->bind_param('ss', $user_email, $date);
        if (!$stmt->execute()) {
            die("Error executing the query: " . $stmt->error);
        }
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $this->SetXY(10, 105);

            $this->SetFillColor(110, 193, 228);
            $this->SetTextColor(255, 255, 255);
            $this->SetFont('Arial', 'B', 10);

            $this->Cell(90, 10, 'Description', 1, 0, 'L', true);
            $this->Cell(50, 10, 'Amount Billed', 1, 0, 'L', true);
            $this->Cell(50, 10, 'Amount Paid', 1, 1, 'L', true);

            $this->SetFont('Arial', '', 10);
            $this->SetTextColor(85, 85, 85);

            while ($row = $result->fetch_assoc()) {
                $this->Cell(90, 10, $row['payment_description'], 1);
                $this->Cell(50, 10, 'Ksh ' . number_format($row['amount_billed'], 2), 1);
                $this->Cell(50, 10, 'Ksh ' . number_format($row['amount_paid'], 2), 1, 1, 'R');


                $total_billed += $row['amount_billed'];
                $total_paid += $row['amount_paid'];
            }

            for ($i = 0; $i < 3; $i++) {
                $this->Cell(90, 10, '', 1);
                $this->Cell(50, 10, '', 1);
                $this->Cell(50, 10, '', 1, 1);
            }
        } else {
            $this->Cell(190, 10, 'No invoice data available for this user on this date.', 1, 1, 'C');
        }

        $stmt->close();

        return [$total_billed, $total_paid];
    }


    function RemarksSection($total_billed, $total_paid)
    {
        $discount = 0;
        $balance_due = $total_billed - $total_paid;

        $this->SetXY(10, 160);
        $this->SetFont('Arial', 'B', 12);
        $this->SetTextColor(24, 49, 90);
        $this->Cell(190, 10, 'Remarks / Payment Instructions', 0, 1, 'L');

        $this->SetFont('Arial', '', 10);
        $this->SetTextColor(85, 85, 85);
        $this->MultiCell(190, 6, "CHEQUE:\n    ALL PAYMENTS SHOULD BE MADE\n    TO ASSOCIATION OF GOVERNMENT LIBRARIANS,\n    KCB BANK, KICC BRANCH, ACCOUNT: 1238906532,\n    AND DEPOSIT SLIP SENT VIA EMAIL\n    TO info@agl.or.ke\n\nMPESA:\n    TILL: 8209382", 0, 'L');

        $this->Ln(10);

        $this->SetXY(120, 160);
        $this->SetFont('Arial', '', 10);
        $this->SetTextColor(85, 85, 85);

        $this->Cell(80, 5, 'TOTAL BILLED: Ksh ' . number_format($total_billed, 2), 0, 1, 'R');
        $this->Cell(190, 5, 'DISCOUNT: Ksh ' . number_format($discount, 2), 0, 1, 'R');
        $this->Cell(190, 5, 'TOTAL PAYMENT: Ksh ' . number_format($total_paid, 2), 0, 1, 'R');
        $this->Cell(190, 5, str_repeat('_', 30), 0, 1, 'R');

        $this->SetFont('Arial', 'B', 12);
        $this->SetTextColor(24, 49, 90);
        $this->Cell(190, 10, 'Balance Due: Ksh ' . number_format($balance_due, 2), 0, 1, 'R');
    }

    function NoteSection()
    {
        $this->SetXY(10, 270);

        $this->SetFont('Arial', 'I', 10);
        $this->SetTextColor(255, 0, 0);
        $this->MultiCell(190, 5, "NOTE:    This is a system-generated invoice and does not require a physical signature.", 0, 'L');
    }

    function FooterLineSection()
    {
        $this->SetY(-10);

        $this->SetFillColor(110, 193, 228);

        $this->Rect(0, $this->GetY(), $this->w, 10, 'F');
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_email = $_POST['user_email'] ?? '';
    $user_type = $_POST['membership_type'] ?? '';
    $date = $_POST['date'] ?? '';
    if (!empty($user_email) && !empty($user_type) && !empty($date)) {
        $pdf = new PDF();
        $pdf->AddPage();
        $pdf->InvoiceHeader($date,);
        $pdf->BillAndPayTo($user_email, $user_type);
        list($total_billed, $total_paid) = $pdf->ItemsTable($user_email, $date);
        $pdf->RemarksSection($total_billed, $total_paid);
        $pdf->NoteSection();
        $pdf->FooterLineSection();
        $pdf->Output();
    } else {
        echo "Error: Missing required input data.";
    }
} else {
    echo "Invalid request method.";
}
