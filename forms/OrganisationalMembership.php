<?php
session_start();
require_once 'DBconnection.php';

// Function to sanitize user input
function sanitize_input($data)
{
    return htmlspecialchars(stripslashes(trim($data)));
}

// Function to hash the password
function hashPassword($password)
{
    return password_hash($password, PASSWORD_BCRYPT);
}

$response = array(); // Initialize response array

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and collect form data
    $organization_name = sanitize_input($_POST['OrganizationName']);
    $organization_email = sanitize_input($_POST['OrganizationEmail']);
    $contact_person = sanitize_input($_POST['ContactPerson']);
    $contact_phone_number = sanitize_input($_POST['ContactPhoneNumber']);
    $organization_date_of_registration = sanitize_input($_POST['OrganizationDateofRegistration']);
    $organization_address = sanitize_input($_POST['OrganizationAddress']);
    $location_country = sanitize_input($_POST['LocationCountry']);
    $location_county = sanitize_input($_POST['LocationCounty']);
    $location_town = sanitize_input($_POST['LocationTown']);
    $organization_type = sanitize_input($_POST['OrganizationType']);
    $start_date = sanitize_input($_POST['startDate']);
    $what_you_do = sanitize_input($_POST['WhatYouDo']);
    // $number_of_employees = sanitize_input($_POST['NumberOfEmployees']);
    $password = sanitize_input($_POST['password']);
    $confirm_password = sanitize_input($_POST['confirm-password']);

    // Verify password match
    if ($password !== $confirm_password) {
        $response['error'] = "Passwords do not match.";
        $_SESSION['response'] = $response;
        header("Location: ../pages/Organizationregistration.php");
        exit();
    }

    // Hash the password
    $hashed_password = hashPassword($password);

    // File upload directories
    $logo_image_dir = "../assets/img/MembersProfile/orgMembers/";
    $registration_certificate_dir = "../assets/Documents/orgMembersDocuments/";

    // Get the current timestamp for unique file names
    $timestamp = time();

    // Handle logo image upload
    $logo_image_path = "";
    if ($_FILES['LogoImage']['error'] === UPLOAD_ERR_OK) {
        $logo_image_tmp_name = $_FILES['LogoImage']['tmp_name'];
        $logo_image_extension = strtolower(pathinfo($_FILES['LogoImage']['name'], PATHINFO_EXTENSION));

        if (in_array($logo_image_extension, ['jpg', 'jpeg', 'png'])) {
            $logo_image_name = $organization_email . '_' . $timestamp . '.' . $logo_image_extension;
            $logo_image_path = $logo_image_dir . $logo_image_name;
            if (!move_uploaded_file($logo_image_tmp_name, $logo_image_path)) {
                $response['error'] = "Failed to move logo image.";
                $_SESSION['response'] = $response;
                header("Location: ../pages/Organizationregistration.php");
                exit();
            }
        } else {
            $response['error'] = "Only JPG, JPEG, and PNG files are allowed for the logo image.";
            $_SESSION['response'] = $response;
            header("Location: ../pages/Organizationregistration.php");
            exit();
        }
    } else {
        $response['error'] = "Error uploading logo image.";
        $_SESSION['response'] = $response;
        header("Location: ../pages/Organizationregistration.php");
        exit();
    }

    // Handle registration certificate upload
    $registration_certificate_path = "";
    if ($_FILES['RegistrationCertificate']['error'] === UPLOAD_ERR_OK) {
        $registration_tmp_name = $_FILES['RegistrationCertificate']['tmp_name'];
        $registration_extension = strtolower(pathinfo($_FILES['RegistrationCertificate']['name'], PATHINFO_EXTENSION));

        if (in_array($registration_extension, ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'])) {
            $registration_certificate_name = $organization_email . '_' . $timestamp . '.' . $registration_extension;
            $registration_certificate_path = $registration_certificate_dir . $registration_certificate_name;
            if (!move_uploaded_file($registration_tmp_name, $registration_certificate_path)) {
                $response['error'] = "Failed to move registration certificate.";
                $_SESSION['response'] = $response;
                header("Location: ../pages/Organizationregistration.php");
                exit();
            }
        } else {
            $response['error'] = "Only PDF, DOC, DOCX, JPG, JPEG, and PNG files are allowed for the registration certificate.";
            $_SESSION['response'] = $response;
            header("Location: ../pages/Organizationregistration.php");
            exit();
        }
    } else {
        $response['error'] = "Error uploading registration certificate.";
        $_SESSION['response'] = $response;
        header("Location: ../pages/Organizationregistration.php");
        exit();
    }

    // Check if the email is already registered
    $email_check_query = "SELECT id FROM organizationmembership WHERE organization_email = ?";
    $email_check_stmt = $conn->prepare($email_check_query);
    if (!$email_check_stmt) {
        $response['error'] = "Failed to prepare email check statement: " . $conn->error;
        $_SESSION['response'] = $response;
        header("Location: ../pages/Organizationregistration.php");
        exit();
    }

    $email_check_stmt->bind_param("s", $organization_email);
    $email_check_stmt->execute();
    $email_check_stmt->store_result();

    if ($email_check_stmt->num_rows > 0) {
        $response['error'] = "The email is already registered. Please use a different email.";
        $_SESSION['response'] = $response;
        header("Location: ../pages/Organizationregistration.php");
        exit();
    }

    $email_check_stmt->close();

    // Insert data into the database
    $sql = "INSERT INTO organizationmembership (
                organization_name, 
                organization_email, 
                contact_person, 
                logo_image, 
                contact_phone_number, 
                date_of_registration, 
                organization_address, 
                location_country, 
                location_county, 
                location_town, 
                registration_certificate, 
                organization_type, 
                start_date, 
                what_you_do, 
                -- number_of_employees, 
                password
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param(
            "sssssssssssssss",
            $organization_name,
            $organization_email,
            $contact_person,
            $logo_image_path,
            $contact_phone_number,
            $organization_date_of_registration,
            $organization_address,
            $location_country,
            $location_county,
            $location_town,
            $registration_certificate_path,
            $organization_type,
            $start_date,
            $what_you_do,
            // $number_of_employees,
            $hashed_password
        );

        if ($stmt->execute()) {
            // Email sending logic here
            $to = $organization_email;
            $subject = "Welcome to Association of Government Librarians!";
            $message = "
                Dear $contact_person,

                Congratulations! Your registration with the Association of Government Librarians has been successfully completed.
                We are thrilled to have you as part of our community. Here are your registration details:
                Name: $organization_name
                Email: $organization_email
                You can now log in to your account and explore the various features and resources available to you. If you have any questions or need assistance, please feel free to reach out to our support team at admin@or.ke.
                You can log in from here: https://member.log.agl.or.ke/members
                Thank you for joining us, and we look forward to your active participation!

                AGL
                http://agl.or.ke/
                +254748027123
                ";

            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/plain;charset=UTF-8" . "\r\n";
            $headers .= 'From: info@agl.or.ke' . "\r\n";

            // Send email
            mail($to, $subject, $message, $headers);

            $response['success'] = true;
            $response['message'] = "Registration successful. Login to your account";
            $_SESSION['response'] = $response;
            header("Location: ../index.html");
            exit();
        } else {
            $response['error'] = "Database error: Could not register organization.";
            $_SESSION['response'] = $response;
            header("Location: ../pages/Organizationregistration.php");
            exit();
        }

        $stmt->close();
    } else {
        $response['error'] = "Database error: Could not prepare statement.";
        $_SESSION['response'] = $response;
        header("Location: ../pages/Organizationregistration.php");
        exit();
    }

    $conn->close();
} else {
    $response['error'] = "Invalid request.";
    $_SESSION['response'] = $response;
    header("Location: ../pages/Organizationregistration.php");
    exit();
}
?>
