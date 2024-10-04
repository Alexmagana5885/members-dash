<?php
require('../assets/fpdf/fpdf.php');
require_once('../forms/DBconnection.php');
require('../assets/phpqrcode/qrlib.php');

$member_email = 'maganaadmin@agl.or.ke';

$query = "SELECT er.event_name, er.event_date, er.event_location, er.member_name, er.member_email,
                 pm.passport_image, om.logo_image
          FROM event_registrations er
          LEFT JOIN personalmembership pm ON er.member_email = pm.email
          LEFT JOIN organizationmembership om ON er.member_email = om.organization_email
          WHERE er.member_email = '$member_email'";

$result = $conn->query($query);

if ($result->num_rows > 0) {
    $data = $result->fetch_assoc();
    
    $event_name = $data['event_name'];
    $event_date = $data['event_date'];
    $event_location = $data['event_location'];
    $member_name = $data['member_name'];
    
    $image = !empty($data['passport_image']) ? $data['passport_image'] : $data['logo_image'];

    $pdf = new FPDF('P', 'mm', [127, 178]);
    $pdf->AddPage();

    $header_image = '../assets/img/logo.png'; 
    $page_width = $pdf->GetPageWidth();

    $pdf->SetFillColor(195, 198, 214);
    $pdf->Rect(0, 0, 127, 178, 'F');

    if (file_exists($header_image)) {
        $header_image_width = 50;
        $x_position = ($page_width - $header_image_width) / 2;
        $pdf->Image($header_image, $x_position, 5, $header_image_width);
    }

    $pdf->Ln(12);

    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, $event_name, 0, 1, 'C');

    $pdf->Ln(3);

    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 10, 'Name: ' . $member_name, 0, 1, 'C');

    $pdf->Ln(12);

    if (!empty($image)) {
        if (file_exists($image)) {
            $image_width = 40;
            $x_position = ($page_width - $image_width) / 2;
            $pdf->Image($image, $x_position, 70, $image_width);
            $pdf->Ln(60);
        } else {
            $pdf->Cell(0, 10, 'Image not found.', 0, 1, 'C');
            $pdf->Ln(10);
        }
    } else {
        $pdf->Ln(50);
    }

    $pdf->Cell(0, 10, 'Event Date: ' . $event_date, 0, 1, 'C');
    $pdf->Ln(3);
    $pdf->Cell(0, 10, 'Location: ' . $event_location, 0, 1, 'C');

    $pdf->AddPage();
    $pdf->SetFillColor(195, 198, 214);
    $pdf->Rect(0, 0, 127, 178, 'F');

    $qr_content = "Member Name: $member_name\nEvent: $event_name\nDate: $event_date\nLocation: $event_location\nEmail: $member_email";
    
    $qr_directory = '../assets/qr/';

    if (!is_dir($qr_directory)) {
        mkdir($qr_directory, 0755, true);
    }
    
    $qr_file = $qr_directory . 'temp_qrcode.png'; 
    QRcode::png($qr_content, $qr_file, QR_ECLEVEL_L, 8);

    if (file_exists($qr_file)) {
        $qr_image_width = 100;
        $x_position = ($page_width - $qr_image_width) / 2;
        $pdf->Image($qr_file, $x_position, 60, $qr_image_width);
    }

    $pdf->Output('I', 'Invitation_Card.pdf');
} else {
    echo "No data found for the member email: $member_email";
}

$conn->close();
?>
