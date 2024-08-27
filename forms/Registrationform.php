<?php
// Database connection
require_once('DBconnection.php');

// Define file upload directories
$imageUploadDir = '../assets/img/MemberImages/';
if (!is_dir($imageUploadDir)) {
    mkdir($imageUploadDir, 0777, true);
}

$certificateUploadDir = '../assets/img/MemberCertificates/';
if (!is_dir($certificateUploadDir)) {
    mkdir($certificateUploadDir, 0777, true);
}

// Extract form data 
$name = $_POST["name"];
$email = $_POST["email"];
$phone = $_POST["phone"];
$dob = $_POST["dob"];
$address = $_POST["Homeaddress"];
$highestDegree = $_POST["highestDegree"];
$institution = $_POST["institution"];
$startDate = $_POST["startDate"];
$graduationYear = $_POST["graduationYear"];
$profession = $_POST["profession"];
$experience = $_POST["experience"];
$currentCompany = $_POST["currentCompany"];
$position = $_POST["position"];
$workAddress = $_POST["workAddress"];
$password = $_POST["password"]; 

// Function to upload image
function uploadImage($file, $email, $uploadDir) {
    $fileType = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    $targetFile = $uploadDir . $email . '.' . $fileType;

    // Check file size
    if ($file["size"] > 5000000) {
        return "File is too large.";
    }

    // Allow only image file formats
    $allowedTypes = ['jpg', 'jpeg', 'png'];
    if (!in_array($fileType, $allowedTypes)) {
        return "Only JPG, JPEG, PNG files are allowed.";
    }

    // Check if file already exists and remove it if necessary
    if (file_exists($targetFile)) {
        unlink($targetFile); // Delete the existing file
    }

    // Try to upload file
    if (!move_uploaded_file($file["tmp_name"], $targetFile)) {
        return "There was an error uploading your image.";
    }

    return $targetFile;
}

// Function to upload completion letter
function uploadCertificate($file, $email, $uploadDir) {
    $fileType = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    $targetFile = $uploadDir . $email . '.' . $fileType;

    // Check file size
    if ($file["size"] > 5000000) {
        return "File is too large.";
    }

    // Allow only PDF file format
    if ($fileType != 'pdf') {
        return "Only PDF files are allowed.";
    }

    // Check if file already exists and remove it if necessary
    if (file_exists($targetFile)) {
        unlink($targetFile); // Delete the existing file
    }

    // Try to upload file
    if (!move_uploaded_file($file["tmp_name"], $targetFile)) {
        return "There was an error uploading your completion letter.";
    }

    return $targetFile;
}

// Upload files after retrieving form data
$passportPath = uploadImage($_FILES["passport"], $email, $imageUploadDir);
$completionLetterPath = uploadCertificate($_FILES["completionLetter"], $email, $certificateUploadDir);

// Check for errors
if (strpos($passportPath, $imageUploadDir) === false || strpos($completionLetterPath, $certificateUploadDir) === false) {
    echo "File upload error: " . ($passportPath ?: $completionLetterPath);
    exit();
}

// Prepare and bind SQL statement
$stmt = $conn->prepare("INSERT INTO Member_registration (name, email, phone, dob, address, passport, highestDegree, institution, startDate, graduationYear, completionLetter, profession, experience, currentCompany, position, workAddress, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssssssssisssss", $name, $email, $phone, $dob, $address, $passportPath, $highestDegree, $institution, $startDate, $graduationYear, $completionLetterPath, $profession, $experience, $currentCompany, $position, $workAddress, $password);

// Execute SQL statement
if ($stmt->execute()) {
    echo "Registration successful!";
} else {
    echo "Error: " . $stmt->error;
}

// Close statement and connection
$stmt->close();
$conn->close();
?>
