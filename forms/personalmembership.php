<?php
// Include the database connection
include 'DBconnection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize input data
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $dob = mysqli_real_escape_string($conn, $_POST['dob']);
    $homeAddress = mysqli_real_escape_string($conn, $_POST['Homeaddress']);
    $highestDegree = mysqli_real_escape_string($conn, $_POST['highestDegree']);
    $institution = mysqli_real_escape_string($conn, $_POST['institution']);
    $startDate = mysqli_real_escape_string($conn, $_POST['startDate']);
    $graduationYear = mysqli_real_escape_string($conn, $_POST['graduationYear']);
    $profession = mysqli_real_escape_string($conn, $_POST['profession']);
    $experience = mysqli_real_escape_string($conn, $_POST['experience']);
    $currentCompany = mysqli_real_escape_string($conn, $_POST['currentCompany']);
    $position = mysqli_real_escape_string($conn, $_POST['position']);
    $workAddress = mysqli_real_escape_string($conn, $_POST['workAddress']);
    $paymentMethod = mysqli_real_escape_string($conn, $_POST['paymentMethod']);
    $paymentCode = mysqli_real_escape_string($conn, $_POST['paymentCode']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
 

    // Handle file uploads
    $passportFileName = $_FILES['passport']['name'];
    $passportTempName = $_FILES['passport']['tmp_name'];
    $passportFileExt = pathinfo($passportFileName, PATHINFO_EXTENSION);
    $passportFileNewName = $email . '.' . $passportFileExt;
    $passportFilePath = '../assets/img/MembersProfile/' . $passportFileNewName;

    $completionLetterFileName = $_FILES['completionLetter']['name'];
    $completionLetterTempName = $_FILES['completionLetter']['tmp_name'];
    $completionLetterFileExt = pathinfo($completionLetterFileName, PATHINFO_EXTENSION);
    $completionLetterFileNewName = $email . '.' . $completionLetterFileExt;
    $completionLetterFilePath = '../assets/Documents/MembersDocuments/' . $completionLetterFileNewName;

    // Move uploaded files
    if (move_uploaded_file($passportTempName, $passportFilePath) && move_uploaded_file($completionLetterTempName, $completionLetterFilePath)) {
        // Prepare SQL statement to insert data into the database
        $sql = "INSERT INTO members (name, email, phone, dob, homeAddress, passportImage, highestDegree, institution, startDate, graduationYear, completionLetter, profession, experience, currentCompany, position, workAddress, paymentMethod, paymentCode, password, )
                VALUES ('$name', '$email', '$phone', '$dob', '$homeAddress', '$passportFilePath', '$highestDegree', '$institution', '$startDate', '$graduationYear', '$completionLetterFilePath', '$profession', '$experience', '$currentCompany', '$position', '$workAddress', '$paymentMethod', '$paymentCode', '$password', )";

        if ($conn->query($sql) === TRUE) {
            echo "New record created successfully";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        echo "Sorry, there was an error uploading your files.";
    }

    // Close connection
    $conn->close();
}
?>
