<?php
// Include the PHP QR Code library
require_once '../assets/phpqrcode/qrlib.php';
require_once('../forms/DBconnection.php');

// Prepare and execute the query
$email = 'maganaadmin@agl.or.ke';
$stmt = $conn->prepare("SELECT name, phone, home_address, highest_degree, institution, graduation_year, profession, experience, current_company, position, work_address, passport_image FROM personalmembership WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

// Fetch the user data
$userData = $result->fetch_assoc();

if ($userData) {
    // Prepare the content for the QR code
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
               "Work Address: " . $userData['work_address'] . "\n" .
               "Passport Image: " . $userData['passport_image'];  // Include the passport image path

    // Generate the QR code image and save it to a file
    $qrCodeFile = 'qr_code.png';
    QRcode::png($content, $qrCodeFile, QR_ECLEVEL_L, 4);

    // Display the generated QR code
    echo '<h1>Your QR Code</h1>';
    echo '<img src="' . $qrCodeFile . '" alt="QR Code" />';
} else {
    echo 'No user found with that email address.';
}

// Close the database connection
$conn->close();
?>
