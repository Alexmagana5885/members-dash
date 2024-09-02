<?php
require_once('DBconnection.php');
session_start(); // Start a secure session

// Clear previous error messages
$_SESSION['error_message'] = "";

// Function to sanitize user input
function sanitize_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

// Initialize variables to store sanitized inputs
$organizationName = sanitize_input($_POST['OrganizationName']);
$organizationEmail = sanitize_input($_POST['OrganizationEmail']);
$contactPerson = sanitize_input($_POST['ContactPerson']);
$contactPhoneNumber = sanitize_input($_POST['ContactPhoneNumber']);
$organizationDateOfRegistration = sanitize_input($_POST['OrganizationDateofRegistration']);
$organizationAddress = sanitize_input($_POST['OrganizationAddress']);
$locationCountry = sanitize_input($_POST['LocationCountry']);
$locationCounty = sanitize_input($_POST['LocationCounty']);
$locationTown = sanitize_input($_POST['LocationTown']);
$organizationType = sanitize_input($_POST['OrganizationType']);
$startDate = sanitize_input($_POST['startDate']);
$whatYouDo = sanitize_input($_POST['WhatYouDo']);
$numberOfEmployees = sanitize_input($_POST['NumberOfEmployees']);
$paymentMethod = sanitize_input($_POST['paymentMethod']);
$paymentCode = sanitize_input($_POST['PaymentCode']);
$password = sanitize_input($_POST['Password']);
$confirmPassword = sanitize_input($_POST['ConfirmPassword']);

// Verify password match
if ($password !== $confirmPassword) {
    $_SESSION['error_message'] = "Passwords do not match.";
    header('Location: ../pages/Registration.php#payment'); // Redirect to the payment section
    exit();
}

// Hash the password before storing it
$hashedPassword = password_hash($password, PASSWORD_BCRYPT);

// File upload directories
$logoImageDir = "../assets/img/MembersProfile/orgMembers/";
$registrationCertificateDir = "../assets/Documents/orgMembersDocuments/";

// Get the current timestamp
$timestamp = time();

// Handle logo image upload
$logoImagePath = "";
if ($_FILES['LogoImage']['error'] === UPLOAD_ERR_OK) {
    $logoImageTmpName = $_FILES['LogoImage']['tmp_name'];
    $logoImageExtension = strtolower(pathinfo($_FILES['LogoImage']['name'], PATHINFO_EXTENSION));
    
    // Check for valid image file
    if (in_array($logoImageExtension, ['jpg', 'jpeg', 'png'])) {
        $logoImageName = $organizationEmail . '_' . $timestamp . '.' . $logoImageExtension;
        $logoImagePath = $logoImageDir . $logoImageName;
        if (!move_uploaded_file($logoImageTmpName, $logoImagePath)) {
            $_SESSION['error_message'] = "Failed to move logo image.";
            header('Location: ../pages/Registration.php#organization-details'); // Redirect to the organization details section
            exit();
        }
    } else {
        $_SESSION['error_message'] = "Only JPG, JPEG, and PNG files are allowed for the logo image.";
        header('Location: ../pages/Registration.php#organization-details'); // Redirect to the organization details section
        exit();
    }
} else {
    $_SESSION['error_message'] = "Error uploading logo image.";
    header('Location: ../pages/Registration.php#organization-details'); // Redirect to the organization details section
    exit();
}

// Handle registration certificate upload
$registrationCertificatePath = "";
if ($_FILES['RegistrationCertificate']['error'] === UPLOAD_ERR_OK) {
    $registrationTmpName = $_FILES['RegistrationCertificate']['tmp_name'];
    $registrationExtension = strtolower(pathinfo($_FILES['RegistrationCertificate']['name'], PATHINFO_EXTENSION));
    
    if (in_array($registrationExtension, ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'])) {
        $registrationCertificateName = $organizationEmail . '_' . $timestamp . '.' . $registrationExtension;
        $registrationCertificatePath = $registrationCertificateDir . $registrationCertificateName;
        if (!move_uploaded_file($registrationTmpName, $registrationCertificatePath)) {
            $_SESSION['error_message'] = "Failed to move registration certificate.";
            header('Location: ../pages/Registration.php#location-details'); // Redirect to the location details section
            exit();
        }
    } else {
        $_SESSION['error_message'] = "Only PDF, DOC, DOCX, JPG, JPEG, and PNG files are allowed for the registration certificate.";
        header('Location: ../pages/Registration.php#location-details'); // Redirect to the location details section
        exit();
    }
} else {
    $_SESSION['error_message'] = "Error uploading registration certificate.";
    header('Location: ../pages/Registration.php#location-details'); // Redirect to the location details section
    exit();
}

// Check if the email is already registered
$emailCheckQuery = "SELECT id FROM organizationmembership WHERE organization_email = ?";
$emailCheckStmt = $conn->prepare($emailCheckQuery);
if (!$emailCheckStmt) {
    die("Failed to prepare email check statement: " . $conn->error);
}

$emailCheckStmt->bind_param("s", $organizationEmail);
$emailCheckStmt->execute();
$emailCheckStmt->store_result();

if ($emailCheckStmt->num_rows > 0) {
    $_SESSION['error_message'] = "The email is already registered. Please use a different email.";
    header('Location: ../pages/Registration.php#organization-details'); // Redirect to the organization details section
    exit();
}

$emailCheckStmt->close();

// SQL query to insert data into the database
$sql = "INSERT INTO organizationmembership 
    (organization_name, organization_email, contact_person, contact_phone_number, date_of_registration, organization_address, location_country, location_county, location_town, organization_type, start_date, what_you_do, number_of_employees, logo_image, registration_certificate, payment_method, payment_code, password) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Failed to prepare insert statement: " . $conn->error);
}

$stmt->bind_param(
    "ssssssssssssssssss", 
    $organizationName, 
    $organizationEmail, 
    $contactPerson, 
    $contactPhoneNumber, 
    $organizationDateOfRegistration, 
    $organizationAddress, 
    $locationCountry, 
    $locationCounty, 
    $locationTown, 
    $organizationType, 
    $startDate, 
    $whatYouDo, 
    $numberOfEmployees, 
    $logoImagePath, 
    $registrationCertificatePath, 
    $paymentMethod, 
    $paymentCode, 
    $hashedPassword
);

if ($stmt->execute()) {
    echo "<script>
            window.location.href = '../index.html';
          </script>";
} else {
    $_SESSION['error_message'] = "An error occurred during registration. Please try again.";
    header('Location: ../pages/Registration.php');
    exit();
}

$stmt->close();
$conn->close();
?>
