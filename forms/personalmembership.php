<?php
session_start();
require_once('DBconnection.php');

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
$password = sanitize_input($_POST['password']);
$confirmPassword = sanitize_input($_POST['confirm-password']);

$response = ['success' => false, 'message' => '', 'errors' => []];

// Verify password match
if ($password !== $confirmPassword) {
    $response['errors'][] = "Passwords do not match.";
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

    if (in_array($passportExtension, ['jpg', 'jpeg', 'png'])) {
        $passportImageName = $email . '_' . $timestamp . '.' . $passportExtension;
        $passportImagePath = $imageDir . $passportImageName;
        move_uploaded_file($passportTmpName, $passportImagePath);
    } else {
        $response['errors'][] = "Only JPG, JPEG, and PNG files are allowed for the passport image.";
    }
} else {
    $response['errors'][] = "Error uploading passport image.";
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
        $response['errors'][] = "Only PDF files are allowed for the completion letter.";
    }
} else {
    $response['errors'][] = "Error uploading completion letter.";
}

// Check if the email is already registered
$emailCheckQuery = "SELECT id FROM personalmembership WHERE email = ?";
$emailCheckStmt = $conn->prepare($emailCheckQuery);
$emailCheckStmt->bind_param("s", $email);
$emailCheckStmt->execute();
$emailCheckStmt->store_result();

if ($emailCheckStmt->num_rows > 0) {
    $response['errors'][] = "The email is already registered. Please use a different email.";
    header('Location: ../pages/Registration.php');
}

$emailCheckStmt->close();

if (empty($response['errors'])) {
    // SQL query to insert data into the database
    $sql = "INSERT INTO personalmembership (name, email, phone, dob, home_address, passport_image, highest_degree, institution, start_date, graduation_year, completion_letter, profession, experience, current_company, position, work_address, password) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssssssssssss", $name, $email, $phone, $dob, $homeAddress, $passportImagePath, $highestDegree, $institution, $startDate, $graduationYear, $completionLetterPath, $profession, $experience, $currentCompany, $position, $workAddress, $hashedPassword);

    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = "Registration successful. Login to your account";
        
        // Email sending logic here
        $to = $email; // User's email
        $subject = "Welcome to Association of Government Librarians!";
        $message = "
        Dear $name,

        Congratulations! Your registration with Association of Government Librarians has been successfully completed.
        We are thrilled to have you as part of our community. Here are your registration details:

        Name: $name
        Email: $email

        You can now log in to your account and explore the various features and resources available to you. If you have any questions or need assistance, please feel free to reach out to our support team at admin@or.ke.
        You can log in from here: https://member.log.agl.or.ke/members
        Thank you for joining us, and we look forward to your active participation!

        AGL
        http://agl.or.ke/
        +254748027123
        ";

        // Set content-type header for plain text email
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/plain;charset=UTF-8" . "\r\n";

        // Additional headers
        $headers .= 'From: info@agl.or.ke' . "\r\n";
        header('Location: ../index.html');

        // Send email
        if (!mail($to, $subject, $message, $headers)) {
            $response['errors'][] = "Registration successful, but failed to send confirmation email.";
            header('Location: ../index.html');
        }

    } else {
        $response['errors'][] = "An error occurred during registration. Please try again.";
        header('Location: ../pages/Registration.php');
    }

    $stmt->close();
}

$conn->close();

// Set the response in session
$_SESSION['response'] = $response;

// Redirect to the page with the popup

exit();
