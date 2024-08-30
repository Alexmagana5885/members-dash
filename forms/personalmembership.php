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
$name = sanitize_input($_POST['name']);
$email = sanitize_input($_POST['email']);
$phone = sanitize_input($_POST['phone']);
$dob = sanitize_input($_POST['dob']);
$homeAddress = sanitize_input($_POST['Homeaddress']);
$highestDegree = sanitize_input($_POST['highestDegree']);
$institution = sanitize_input($_POST['institution']);
$startDate = sanitize_input($_POST['startDate']);
$graduationYear = sanitize_input($_POST['graduationYear']);
$profession = sanitize_input($_POST['profession']);
$experience = sanitize_input($_POST['experience']);
$currentCompany = sanitize_input($_POST['currentCompany']);
$position = sanitize_input($_POST['position']);
$workAddress = sanitize_input($_POST['workAddress']);
$paymentMethod = sanitize_input($_POST['paymentMethod']);
$paymentCode = sanitize_input($_POST['paymentCode']);
$password = sanitize_input($_POST['password']);
$confirmPassword = sanitize_input($_POST['confirm-password']);

// Verify password match
if ($password !== $confirmPassword) {
    $_SESSION['error_message'] = "Passwords do not match.";
    header('Location: registration.php#payment'); // Redirect to the payment section
    exit();
}

// Hash the password before storing it
$hashedPassword = password_hash($password, PASSWORD_BCRYPT);

// File upload directories
$imageDir = "../assets/img/MembersProfile/";
$documentDir = "../assets/Documents/MembersDocuments/";

// Get the current timestamp
$timestamp = time();

// Handle passport image upload
$passportImagePath = "";
if ($_FILES['passport']['error'] === UPLOAD_ERR_OK) {
    $passportTmpName = $_FILES['passport']['tmp_name'];
    $passportExtension = strtolower(pathinfo($_FILES['passport']['name'], PATHINFO_EXTENSION));
    
    // Check for valid image file
    if (in_array($passportExtension, ['jpg', 'jpeg', 'png'])) {
        $passportImageName = $email . '_' . $timestamp . '.' . $passportExtension;
        $passportImagePath = $imageDir . $passportImageName;
        move_uploaded_file($passportTmpName, $passportImagePath);
    } else {
        $_SESSION['error_message'] = "Only JPG, JPEG, and PNG files are allowed for the passport image.";
        header('Location: ../pages/Registration.php#personal-details'); // Redirect to the personal details section
        exit();
    }
} else {
    $_SESSION['error_message'] = "Error uploading passport image.";
    header('Location: ../pages/Registration.php#personal-details'); // Redirect to the personal details section
    exit();
}

// Handle completion letter upload
$completionLetterPath = "";
if ($_FILES['completionLetter']['error'] === UPLOAD_ERR_OK) {
    $completionTmpName = $_FILES['completionLetter']['tmp_name'];
    $completionExtension = strtolower(pathinfo($_FILES['completionLetter']['name'], PATHINFO_EXTENSION));
    
    if ($completionExtension === 'pdf') {
        $completionLetterName = $email . '_' . $timestamp . '.' . $completionExtension;
        $completionLetterPath = $documentDir . $completionLetterName;
        move_uploaded_file($completionTmpName, $completionLetterPath);
    } else {
        $_SESSION['error_message'] = "Only PDF files are allowed for the completion letter.";
        header('Location: ../pages/Registration.php#education'); // Redirect to the education section
        exit();
    }
} else {
    $_SESSION['error_message'] = "Error uploading completion letter.";
    header('Location: ../pages/Registration.php#education'); // Redirect to the education section
    exit();
}

// Check if the email is already registered
$emailCheckQuery = "SELECT id FROM personalmembership WHERE email = ?";
$emailCheckStmt = $conn->prepare($emailCheckQuery);
$emailCheckStmt->bind_param("s", $email);
$emailCheckStmt->execute();
$emailCheckStmt->store_result();

if ($emailCheckStmt->num_rows > 0) {
    $_SESSION['error_message'] = "The email is already registered. Please use a different email.";
    header('Location: ../pages/Registration.php#personal-details'); // Redirect to the personal details section
    exit();
}

$emailCheckStmt->close();

// SQL query to insert data into the database
$sql = "INSERT INTO personalmembership (name, email, phone, dob, home_address, passport_image, highest_degree, institution, start_date, graduation_year, completion_letter, profession, experience, current_company, position, work_address, payment_method, payment_code, password) 
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssssssssssssssss", $name, $email, $phone, $dob, $homeAddress, $passportImagePath, $highestDegree, $institution, $startDate, $graduationYear, $completionLetterPath, $profession, $experience, $currentCompany, $position, $workAddress, $paymentMethod, $paymentCode, $hashedPassword);

if ($stmt->execute()) {
    echo "<script>
            window.location.href = '../index.html';
          </script>";
} else {
    $_SESSION['error_message'] = "An error occurred during registration. Please try again.";
    header('Location:../pages/Registration.php');
    exit();
}

$stmt->close();
$conn->close();
?>
