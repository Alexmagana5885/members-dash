<?php
session_start(); // Start the session to store errors

// Include the necessary libraries
include 'DBconnection.php';
require('../assets/fpdf/fpdf.php');
require_once '../assets/phpqrcode/qrlib.php';

$errors = []; // Array to store error messages

$member_email = $_POST['email'];
$event_id = $_POST['eventName'];

$qrDir = '../assets/img/qrcodes/';
if (!is_dir($qrDir)) {
    mkdir($qrDir, 0755, true);
}

$query = "SELECT er.event_name, er.event_date, er.event_location, er.member_name, er.member_email,
       er.invitation_card, er.contact, er.payment_code
FROM event_registrations er
WHERE er.member_email = '$member_email' AND er.event_id = '$event_id'";

$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    $data = $result->fetch_assoc();

    $event_name = $data['event_name'];
    $event_date = $data['event_date'];
    $event_location = $data['event_location'];
    $member_name = $data['member_name'];

    $stmt = $conn->prepare("SELECT name, phone, home_address, highest_degree, institution, graduation_year, profession, experience, current_company, position, work_address FROM personalmembership WHERE email = ?");
    $stmt->bind_param("s", $member_email);
    $stmt->execute();
    $userResult = $stmt->get_result();

    $userData = $userResult->fetch_assoc();

    if ($userData) {
        $content = "Name: " . $userData['name'] . "\n" .
            "Phone: " . $userData['phone'] . "\n" .
            "Address: " . $userData['home_address'] . "\n" .
            "Degree: " . $userData['highest_degree'] . "\n" .
            "Institution: " . $userData['institution'] . "\n" .
            "Graduation Year: " . $userData['graduation_year'] . "\n" .
            "Profession: " . $userData['profession'] . "\n" .
            "Experience: " . $userData['experience'] . "\n" .
            "Current Company: " . $userData['current_company'] . "\n" .
            "Position: " . $userData['position'] . "\n" .
            "Work Address: " . $userData['work_address'];

        $qrCodeFile = $qrDir . 'qr_code_' . md5($member_email) . '.png';
        QRcode::png($content, $qrCodeFile, QR_ECLEVEL_L, 4);
    } else {
        $errors[] = 'No user found with that email address.';
    }

    if (empty($errors)) {
        $pdf = new FPDF('P', 'mm', [100, 150]);
        $pdf->AddPage();

        $header_image = '../assets/img/logo.png';
        $page_width = $pdf->GetPageWidth();

        $pdf->SetFillColor(195, 198, 214);
        $pdf->Rect(0, 0, 100, 150, 'F');

        if (file_exists($header_image)) {
            $header_image_width = 35;
            $header_image_x = ($page_width - $header_image_width) / 2;
            $pdf->Image($header_image, $header_image_x, 5, $header_image_width);
        }

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetXY(0, 25);
        $pdf->Cell(0, 3, 'Association of Government Librarians', 0, 1, 'R');
        $pdf->Ln(5);

        $pdf->SetDrawColor(0, 0, 255);
        $pdf->SetLineWidth(0.5);
        $pdf->Line(5, 40, 95, 40);

        $pdf->Ln(10);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(0, 2, $event_name, 0, 2, 'C');
        $pdf->Ln(1);

        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 8, $member_name, 0, 1, 'C');
        $pdf->Ln(5);

        if (file_exists($qrCodeFile)) {
            $qrCodeWidth = 35;
            $x_position = ($page_width - $qrCodeWidth) / 2;
            $pdf->Image($qrCodeFile, $x_position, 55, $qrCodeWidth);
            $pdf->Ln(10);
        } else {
            $errors[] = 'QR code not found.';
        }
        
        // Final PDF configurations omitted for brevity
        
        if (empty($errors)) {
            $pdf->Output('D', $pdfFileName);
        }
    }
} else {
    $errors[] = "No event registration found for this member.";
}

if (!empty($errors)) {
$_SESSION['response'] = $response;
header("Location: " . $_SERVER['HTTP_REFERER']);
    exit;
}

$conn->close();
?>
