<?php
session_start();

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Member Registration</title>
    <link href="../assets/img/favicon.png" rel="icon" />
    <link href="../assets/img/favicon.png" rel="favicon.png" />
    <link rel="stylesheet" href="styles.css" />
    <link rel="stylesheet" href="../assets/CSS/registration.cs">
</head>

<style>
    /* Basic Reset */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    /* Body Styling */
    body {
        font-family: Arial, sans-serif;
        line-height: 1.6;
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }

    /* Header Styling */
    .site-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem;
        background-color: #6ec1e4;
        color: #fff;
        position: relative;
    }

    .site-header .logo IMg {
        width: 25%;
        padding: 0;
    }

    .navigation {
        display: flex;
        gap: 1rem;
    }

    .navigation ul {
        list-style: none;
        display: flex;
        gap: 1rem;
    }

    .navigation a {
        color: #fff;
        text-decoration: none;
    }

    .navigation a:hover {
        text-decoration: underline;
    }

    /* Menu Toggle Button */
    .menu-toggle {
        display: none;
        background: none;
        border: none;
        color: #fff;
        font-size: 2rem;
        cursor: pointer;
    }

    /* Main Content Styling */
    main {
        flex: 1;
        padding: 1rem;
    }

    /* Footer Styling */
    .site-footer {
        background-color: #f2f2f2;
        color: #333;
        text-align: center;
        padding: 1rem;
        bottom: 0;
        width: 100%;
        font-size: 11px;
        font-style: italic;
    }

    /* Responsive Styles */
    @media (max-width: 768px) {
        .navigation {
            display: none;
            flex-direction: column;
            position: absolute;
            top: 100%;
            right: 0;
            background-color: #6ed1e5;
            width: 200px;
            padding: 1rem;
            z-index: 1000;
        }

        .site-header .logo IMg {
            width: 150px;
            padding: 0;
        }

        .navigation.active {
            display: flex;
        }

        .menu-toggle {
            display: block;
        }

        .navigation ul {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.5rem;
        }
    }

    .container {
        background: #fff;
        padding: 20px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        /* width: 70%; */
        height: 90%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        overflow-y: auto;
    }

    /* Large screens (desktops) */
    @media (min-width: 1024px) {
        .container {
            width: 80%;
            height: 90%;
        }
    }

    /* Small screens (tablets and phones) */
    @media (max-width: 1024px) {
        .container {
            width: 100%;
            height: auto;
            padding: 10px;
            margin-top: 40px;
        }
    }

    h2 {
        text-align: center;
        margin-bottom: 20px;
    }

    .progress-bar {
        display: flex;
        justify-content: space-between;
        margin-bottom: 30px;
    }

    .progress-step {
        width: 25%;
        text-align: center;
        position: relative;
    }

    .progress-step::before {
        content: "";
        position: absolute;
        top: 10px;
        left: 0;
        right: 0;
        height: 3px;
        background: #e0e0e0;
        z-index: -1;
    }

    .progress-step.active::before {
        background: #3498db;
    }

    .progress-step-circle {
        width: 20px;
        height: 20px;
        background: #e0e0e0;
        border-radius: 50%;
        display: inline-block;
        line-height: 20px;
        text-align: center;
        color: #fff;
    }

    .progress-step.active .progress-step-circle {
        background: #3498db;
    }

    .form-step {
        display: none;
    }

    .form-step.active {
        display: block;
    }

    .form-navigation {
        display: flex;
        justify-content: space-between;
        margin-top: auto;
    }

    .form-navigation button {
        padding: 10px 20px;
        background: #3498db;
        border: none;
        color: #fff;
        cursor: pointer;
        flex-grow: 1;
        margin: 5px;
        border-radius: 5px;
    }

    .form-navigation button:disabled {
        background: #e0e0e0;
        cursor: not-allowed;
    }

    .form-navigation button.previous {
        background: #555;
    }

    /* General styles for labels */
    label {
        display: block;
        margin-bottom: 8px;
        font-weight: bold;
        color: #333;
    }

    /* General styles for input fields */
    input[type="text"],
    input[type="email"],
    input[type="tel"],
    input[type="date"],
    input[type="number"],
    input[type="file"],
    input[type="month"],
    input[type="password"],
    select {
        width: calc(100% - 22px);
        padding: 8px;
        margin-bottom: 5px;
        border: 0;
        border-radius: 4px;
        box-sizing: border-box;
        box-shadow: 2px 2px 2px 2px rgba(0, 0, 0, 0.2);
    }

    textarea {
        width: calc(100% - 22px);
        padding: 10px;
        margin-bottom: 15px;
        border: 0;
        border-radius: 4px;
        box-sizing: border-box;
        resize: vertical;
        box-shadow: 2px 2px 2px 2px rgba(0, 0, 0, 0.2);
    }

    input[type="file"] {
        padding: 0;
        border: none;
    }

    .form-stepINdiv {
        display: flex;
        justify-content: space-between;
    }

    .stepINdivdiv {
        width: 50%;
    }

    /* Responsive adjustments */
    @media (max-width: 1024px) {
        label {
            font-size: 14px;
        }

        input[type="text"],
        input[type="email"],
        input[type="tel"],
        input[type="date"],
        input[type="number"],
        input[type="file"],
        input[type="month"],
        input[type="password"],
        select {
            padding: 8px;
        }

        textarea {
            padding: 8px;
        }

        .form-stepINdiv {
            flex-direction: column;
        }

        .stepINdivdiv {
            width: 100%;
        }
    }

    .password-container {
        position: relative;
        display: flex;
        align-items: center;
        width: 100%;
    }

    .password-container input {
        width: 100%;
        padding: 10px;
        padding-right: 40px;
        box-sizing: border-box;
        border: 0;
        border-radius: 4px;
        box-shadow: 2px 2px 2px 2px rgba(0, 0, 0, 0.2);
    }

    .password-container .toggle-icon {
        position: absolute;
        right: 10px;
        cursor: pointer;
        font-size: 14px;
        color: #333;
    }

    .error-message {
        color: red;
        font-size: 13px;
        display: none;
    }

    .stepINdivdiv {
        margin: 10px;
        display: flex;
        flex-direction: column;
    }

    .payment-button {
        background-color: #007bff;
        color: white;
        border: none;
        border-radius: 5px;
        padding: 10px 20px;
        margin: 5px;
        cursor: pointer;
        font-size: 16px;
        width: 60%;
    }

    .payment-button:hover {
        background-color: #0056b3;
    }

    /* General Popup Container Styling */
    .popup-container {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100vh;
        background-color: rgba(0, 0, 0, 0.5);
    }

    .popup-content {
        background-color: #fff;
        margin: 8% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 80%;
        max-width: 500px;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        position: relative;
    }

    /* Close Button Styling */
    .close {
        color: #aaa;
        float: right;
        font-size: 35px;
        font-weight: bold;
        cursor: pointer;
    }

    .close:hover,
    .close:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }

    .popup-logo {
        display: block;
        margin: 0 auto 15px;
        max-width: 150px;
    }

    form {
        display: flex;
        flex-direction: column;
    }

    label {
        margin: 10px 0 5px;
    }

    input[type="text"] {
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 4px;
        margin-bottom: 15px;
    }

    input[type="text"]:focus {
        border-color: #007bff;
        outline: none;
    }

    .payButtons {
        display: flex;
        justify-content: space-between;
        margin: 0 auto;
    }

    .payButtons button {
        padding: 10px 20px;
        border: none;
        border-radius: 4px;
        color: #fff;
        cursor: pointer;
        font-size: 16px;
    }

    .pay-btn {
        background-color: #28a745;
    }

    .cancel-btn {
        background-color: #dc3545;
    }

    .pay-btn:hover {
        background-color: #218838;
    }

    .cancel-btn:hover {
        background-color: #c82333;
    }

    @media (max-width: 600px) {
        .popup-content {
            width: 90%;
            margin: 10% auto;
        }
    }
</style>

<style>
    .popup {
        position: fixed;
        top: 20px;
        right: 20px;
        width: 300px;
        padding: 15px;
        background-color: #f8f9fa;
        border-radius: 5px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        z-index: 1000;
        opacity: 0;
        visibility: hidden;
        transform: translateY(-20px);
        transition: opacity 0.5s ease, visibility 0.5s ease, transform 0.5s ease;
    }

    /* Popup visible state */
    .popup.show {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    /* Success and danger alert styles */
    .alert {
        margin: 0;
        padding: 10px;
        border-radius: 3px;
        font-size: 14px;
    }

    .alert-success {
        background-color: #d4edda;
        color: #155724;
    }

    .alert-danger {
        background-color: #f8d7da;
        color: #721c24;
    }
</style>

<body>
    <div id="response-popup" class="popup">
        <?php
        $hasMessage = false; // Flag to check if any message is set

        // Check if session contains response data
        if (isset($_SESSION['response'])) {
            $response = $_SESSION['response'];

            // Check if the response contains error messages
            if (isset($response['error'])) {
                echo '<div class="alert alert-danger">' . htmlspecialchars($response['error']) . '</div>';
                unset($_SESSION['response']);
                $hasMessage = true;
            }

            // Check if the response contains success messages
            if (isset($response['success'])) {
                echo '<div class="alert alert-success">' . htmlspecialchars($response['success']) . '</div>';
                unset($_SESSION['response']);
                $hasMessage = true;
            }
        }

        if (!$hasMessage) {
            // Default message if no specific message is set
            echo '<div style="color: blue;" class="alert alert-info">Welcome!! Kindly fill in your information correctly.</div>';
        }
        ?>
    </div>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var popup = document.getElementById('response-popup');
            if (popup) {
                // Show the popup
                popup.classList.add('show');

                // Hide the popup after 10 seconds
                setTimeout(function() {
                    popup.classList.remove('show');
                }, 10000); // 10000ms = 10 seconds
            }
        });
    </script>


    <body>
        <!-- Header -->
        <header class="site-header">
            <div class="logo">
                <img src="../assets/img/logo.png" alt="" />
            </div>
            <button class="menu-toggle" id="menu-toggle">
                &#9776;
                <!-- Unicode for the three-bar menu icon -->
            </button>
            <nav class="navigation" id="navigation">
                <ul>
                    <li><a href="https://www.agl.or.ke/">Home</a></li>
                    <li><a href="https://www.agl.or.ke/about-us/">About</a></li>
                    <li><a href="https://www.agl.or.ke/contact-us/">Contact</a></li>
                </ul>
            </nav>
        </header>

        <!-- Main Content -->
        <main style=" width: 100%; ">

            <body>


                <form style="margin: 0 auto;" method="post" action="../forms/OrganisationalMembership.php"
                    enctype="multipart/form-data" class="container" id="registrationForm">
                    <h2>Registration Form</h2>

                    <!-- Progress Bar -->
                    <div class="progress-bar">
                        <div class="progress-step active">
                            <div class="progress-step-circle">1</div>
                            <p>Organization Details</p>
                        </div>
                        <div class="progress-step">
                            <div class="progress-step-circle">2</div>
                            <p>Location Details/Profession</p>
                        </div>
                        <div class="progress-step">
                            <div class="progress-step-circle">3</div>
                            <p>Password</p>
                        </div>
                        <div class="progress-step">
                            <div class="progress-step-circle">3</div>
                            <p>Review & Submit</p>
                        </div>

                    </div>

                    <!-- Organization Details -->
                    <div id="organization-details" class="form-step active">
                        <div class="form-stepINdiv">
                            <!-- <h3>Organization Details</h3> -->
                            <div class="stepINdivdiv">
                                <label for="OrganizationName">Organization Name:</label>
                                <input type="text" id="OrganizationName" name="OrganizationName" required><br><br>

                                <label for="OrganizationEmail">Organization Email:</label>
                                <input type="email" id="OrganizationEmail" name="OrganizationEmail" required><br><br>
                                <span id="organization-email-error" style="color: blue;"></span>

                                <label for="ContactPerson">Contact Person:</label>
                                <input type="text" id="ContactPerson" name="ContactPerson" required><br><br>

                                <label for="passport">Logo Image:</label>
                                <input style="height: 35px ; padding: 10px" type="file" id="LogoImage" name="LogoImage"
                                    accept="image/*" required><br><br>
                            </div>

                            <div class="stepINdivdiv">
                                <label for="ContactPhoneNumber">Contact Phone Number:</label>
                                <input type="number" id="ContactPhoneNumber" name="ContactPhoneNumber" required><br><br>
                                <span id="contact-phone-error" style="color: blue;"></span>

                                <label for="OrganizationDateofRegistration">Organization Registration Date:</label>
                                <input type="date" id="OrganizationDateofRegistration" name="OrganizationDateofRegistration" required><br><br>
                                <span id="registration-date-error" style="color: red;"></span>


                                <label for="OrganizationAddress">Organization Address:</label>
                                <textarea id="OrganizationAddress" name="OrganizationAddress" rows="4" required></textarea><br><br>
                            </div>
                        </div>
                    </div>

                    <!-- Location Details -->
                    <div id="location-details" class="form-step">
                        <div class="form-stepINdiv">
                            <!-- <h3>Location Details</h3> -->
                            <div class="stepINdivdiv">
                                <label for="LocationCountry">Location Country:</label>
                                <input type="text" id="LocationCountry" name="LocationCountry" required><br><br>

                                <label for="LocationCounty">Location County:</label>
                                <input type="text" id="LocationCounty" name="LocationCounty" required><br><br>

                                <label for="LocationTown">Location Town:</label>
                                <input type="text" id="LocationTown" name="LocationTown" required><br><br>

                                <label for="completionLetter">Organization Registration Certificate:</label>
                                <input style="height: 35px; padding: 10px; " type="file" id="RegistrationCertificate"
                                    name="RegistrationCertificate" accept=".pdf"
                                    required><br><br>

                            </div>

                            <div class="stepINdivdiv">
                                <label for="OrganizationType">Organization Type:</label>
                                <input type="text" id="OrganizationType" name="OrganizationType"
                                    placeholder="e.g., governmental or non-governmental" required><br><br>

                                <label for="startDate">Date Registered With AGL:</label>
                                <input type="date" id="startDate" name="startDate" required><br><br>
                                <span id="start-date-error" style="color: red;"></span>

                                <label for="WhatYouDo">What You Do:</label>
                                <input type="text" id="WhatYouDo" name="WhatYouDo" required><br><br>

                                <label for="NumberOfEmployees">Number of Employees:</label>
                                <input type="number" id="NumberOfEmployees" name="NumberOfEmployees" required><br><br>
                                <span id="employees-error" style="color: red;"></span>

                            </div>
                        </div>
                    </div>


                    <!-- submit Details -->
                    <div id="payment" class="form-step">
                        <div class="form-stepINdiv">

                            <div class="stepINdivdiv">
                                <p>Create a strong password with a minimum length of 8 characters, including a mix of
                                    upper-case letters, lower-case letters, numbers, and special characters.</p>
                                <label for="password">Enter password</label><br>
                                <div class="password-container">
                                    <input type="password" id="password" name="password" required
                                        oninput="validatePasswords()">
                                    <span class="toggle-icon" onclick="togglePassword('password')">👁️</span>
                                </div><br><br>

                                <label for="confirm-password">Confirm password</label><br>
                                <div class="password-container">
                                    <input type="password" id="confirm-password" name="confirm-password" required
                                        oninput="validatePasswords()">
                                    <span class="toggle-icon" onclick="togglePassword('confirm-password')">👁️</span>
                                </div><br><br>

                                <div class="error-message" id="error-message1">Passwords do not match!</div>

                                <div class="error-message" id="error-message2">Password does not meet policy requirements
                                    Above!</div>

                            </div>

                            <style>
                                .payment-button:disabled {
                                    background-color: #ccc;
                                    cursor: not-allowed;
                                }
                            </style>

                            <div class="stepINdivdiv">
                                <label>Read the information bellow Before Registration</label><br>
                                <p>Please ensure the information you provide is accurate and will be kept confidential.</p>
                                <br>
                                <p>You must create a strong password following our guidelines and remember it for future
                                    logins.
                                    After logging in, you will have 2 weeks to pay the registration fee of Ksh 2000.00 from
                                    your account,
                                    or your account will be deactivated.</p>


                                <p>Please make sure all required sections of the form are completed before submitting it;
                                    otherwise, it will not be processed.</p>

                            </div>

                        </div>
                    </div>

                    <!-- error message -->
                    <div class="error-message" id="error-messageScript">
                        <?php
                        session_start();
                        if (isset($_SESSION['error_message']) && !empty($_SESSION['error_message'])) {
                            echo '<p id="errorMessage">' . $_SESSION['error_message'] . '</p>';
                            unset($_SESSION['error_message']); // Clear the error message after displaying it
                        }
                        ?>
                    </div>

                    <script>
                        document.addEventListener('DOMContentLoaded', (event) => {
                            const errorMessage = document.getElementById('errorMessage');
                            if (errorMessage) {
                                // Hide the error message after 10 seconds
                                setTimeout(() => {
                                    errorMessage.style.display = 'none';
                                }, 10000); // 10000 milliseconds = 10 seconds
                            }
                        });
                    </script>


                    <div class="form-navigation">
                        <button type="button" class="previous" disabled>Previous</button>
                        <button type="button" class="next">Next</button>
                    </div>
                </form>

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const nextButton = document.querySelector('.next');
                        const reviewStep = document.getElementById('review');

                        nextButton.addEventListener('click', function() {
                            if (reviewStep && reviewStep.classList.contains('active')) {
                                // Organization Details
                                document.getElementById('review-organization-name').textContent = document.getElementById('OrganizationName').value;
                                document.getElementById('review-organization-email').textContent = document.getElementById('OrganizationEmail').value;
                                document.getElementById('review-contact-person').textContent = document.getElementById('ContactPerson').value;

                                const logoFile = document.getElementById('LogoImage').files[0];
                                document.getElementById('review-logo').textContent = logoFile ? logoFile.name : 'No file uploaded';

                                document.getElementById('review-contact-phone').textContent = document.getElementById('ContactPhoneNumber').value;
                                document.getElementById('review-organization-registration-date').textContent = document.getElementById('OrganizationDateofRegistration').value;
                                document.getElementById('review-organization-address').textContent = document.getElementById('OrganizationAddress').value;

                                // Location Details
                                document.getElementById('review-location-country').textContent = document.getElementById('LocationCountry').value;
                                document.getElementById('review-location-county').textContent = document.getElementById('LocationCounty').value;
                                document.getElementById('review-location-town').textContent = document.getElementById('LocationTown').value;

                                const certificateFile = document.getElementById('RegistrationCertificate').files[0];
                                document.getElementById('review-registration-certificate').textContent = certificateFile ? certificateFile.name : 'No file uploaded';

                                document.getElementById('review-organization-type').textContent = document.getElementById('OrganizationType').value;
                                document.getElementById('review-start-date').textContent = document.getElementById('startDate').value;
                                document.getElementById('review-what-you-do').textContent = document.getElementById('WhatYouDo').value;
                                document.getElementById('review-number-of-employees').textContent = document.getElementById('NumberOfEmployees').value;

                                // Password (For demonstration purposes, you might not want to display the password in plain text)
                                document.getElementById('review-password').textContent = document.getElementById('password').value ? 'Password entered' : 'No password entered';
                            }
                        });
                    });
                </script>


                <!-- form input validation  -->
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const organizationEmailInput = document.getElementById('OrganizationEmail');
                        const organizationEmailError = document.getElementById('organization-email-error');

                        organizationEmailInput.addEventListener('input', function() {
                            const emailValue = organizationEmailInput.value;
                            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

                            if (emailPattern.test(emailValue)) {
                                organizationEmailError.textContent = ''; // Clear error message if valid
                            } else {
                                organizationEmailError.textContent = 'Ensure that you enter a valid working email address.';
                            }
                        });
                    });


                    document.addEventListener('DOMContentLoaded', function() {
                        const contactPhoneInput = document.getElementById('ContactPhoneNumber');
                        const contactPhoneError = document.getElementById('contact-phone-error');

                        contactPhoneInput.addEventListener('input', function() {
                            const phoneValue = contactPhoneInput.value;
                            const phonePattern = /^(\+|0)\d{9,14}$/;

                            if (phonePattern.test(phoneValue)) {
                                contactPhoneError.textContent = '';
                            } else {
                                contactPhoneError.textContent = 'Please enter a valid phone number starting with valid Country code, + or 0, followed by 9 to 14 digits.';
                            }
                        });
                    });

                    document.addEventListener('DOMContentLoaded', function() {
                        const registrationDateInput = document.getElementById('OrganizationDateofRegistration');
                        const registrationDateError = document.getElementById('registration-date-error');

                        registrationDateInput.addEventListener('change', function() {
                            const selectedDate = new Date(registrationDateInput.value);
                            const currentDate = new Date();

                            // Remove the time component for comparison
                            selectedDate.setHours(0, 0, 0, 0);
                            currentDate.setHours(0, 0, 0, 0);

                            // Check if the selected date is in the future
                            if (selectedDate > currentDate) {
                                registrationDateError.textContent = 'The registration date must be today or in the past.';
                                registrationDateInput.value = ''; // Clear the input if the date is in the future
                            } else {
                                registrationDateError.textContent = ''; // Clear error message if valid
                            }
                        });
                    });

                    document.addEventListener('DOMContentLoaded', function() {
                        const startDateInput = document.getElementById('startDate');
                        const startDateError = document.getElementById('start-date-error');

                        startDateInput.addEventListener('change', function() {
                            const selectedDate = new Date(startDateInput.value);
                            const currentDate = new Date();

                            selectedDate.setHours(0, 0, 0, 0);
                            currentDate.setHours(0, 0, 0, 0);

                            // Check if the selected date is in the future
                            if (selectedDate > currentDate) {
                                startDateError.textContent = 'The registration date cannot be in the future.';
                                startDateInput.value = ''; // Clear the input if the date is in the future
                            } else {
                                startDateError.textContent = ''; // Clear error message if valid
                            }
                        });
                    });

                    document.addEventListener('DOMContentLoaded', function() {
                        const employeesInput = document.getElementById('NumberOfEmployees');
                        const employeesError = document.getElementById('employees-error');

                        employeesInput.addEventListener('input', function() {
                            const numberOfEmployees = parseInt(employeesInput.value, 10);

                            if (numberOfEmployees < 1) {
                                employeesError.textContent = 'The number of employees cannot be less than 1.';
                                employeesInput.value = ''; // Clear the input if the value is invalid
                            } else {
                                employeesError.textContent = ''; // Clear error message if valid
                            }
                        });
                    });
                </script>

                <script>
                    const steps = document.querySelectorAll(".form-step");
                    const nextBtn = document.querySelector(".next");
                    const prevBtn = document.querySelector(".previous");
                    const progressSteps = document.querySelectorAll(".progress-step");

                    let currentStep = 0;

                    function updateFormStep() {
                        steps.forEach((step, index) => {
                            step.classList.toggle("active", index === currentStep);
                        });
                        progressSteps.forEach((step, index) => {
                            step.classList.toggle("active", index <= currentStep);
                        });

                        prevBtn.disabled = currentStep === 0;
                        nextBtn.textContent = currentStep === steps.length - 1 ? "Submit" : "Next";
                        updateButtonState();
                    }

                    nextBtn.addEventListener("click", () => {
                        if (currentStep < steps.length - 1) {
                            currentStep++;
                            updateFormStep();
                        } else {
                            const form = document.getElementById("registrationForm");
                            if (form.checkValidity()) {
                                form.submit();
                            } else {
                                form.reportValidity();
                            }
                        }
                    });

                    prevBtn.addEventListener("click", () => {
                        if (currentStep > 0) {
                            currentStep--;
                            updateFormStep();
                        }
                    });

                    function checkFormCompletion() {
                        const currentInputs = steps[currentStep].querySelectorAll("input[required], textarea[required], select[required]");
                        let allFilled = true;

                        currentInputs.forEach(input => {
                            if (!input.value) {
                                allFilled = false;
                            }
                        });

                        updateButtonState();
                    }

                    function updateButtonState() {
                        const password = document.getElementById('password') ? document.getElementById('password').value : '';
                        const confirmPassword = document.getElementById('confirm-password') ? document.getElementById('confirm-password').value : '';
                        const passwordPolicyRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&#])[A-Za-z\d@$!%*?&#]{8,}$/;

                        const errorMessage1 = document.getElementById('error-message1');
                        const errorMessage2 = document.getElementById('error-message2');

                        let showError1 = false;
                        let showError2 = false;

                        if (password && confirmPassword && password !== confirmPassword) {
                            showError1 = true;
                        }

                        if (password && !passwordPolicyRegex.test(password)) {
                            showError2 = true;
                        }

                        if (errorMessage1) errorMessage1.style.display = showError1 ? 'block' : 'none';
                        if (errorMessage2) errorMessage2.style.display = showError2 ? 'block' : 'none';

                        const allInputsFilled = Array.from(steps[currentStep].querySelectorAll("input[required], textarea[required], select[required]")).every(input => input.value);

                        nextBtn.disabled = !allInputsFilled || showError1 || showError2;
                    }

                    document.querySelectorAll(".form-step input[required], .form-step textarea[required], .form-step select[required]").forEach(input => {
                        input.addEventListener('input', checkFormCompletion);
                    });

                    const passwordField = document.getElementById('password');
                    const confirmPasswordField = document.getElementById('confirm-password');

                    if (passwordField) passwordField.addEventListener('input', updateButtonState);
                    if (confirmPasswordField) confirmPasswordField.addEventListener('input', updateButtonState);

                    document.addEventListener('DOMContentLoaded', checkFormCompletion);


                    function togglePassword(id) {
                        const passwordField = document.getElementById(id);
                        if (passwordField.type === 'password') {
                            passwordField.type = 'text';
                        } else {
                            passwordField.type = 'password';
                        }
                    }
                </script>

                <script src="https://js.stripe.com/v3/"></script>
                <script src="../assets/JS/aglpaycard.js"></script>


            </body>

        </main>

        <!-- Footer -->
        <footer class="site-footer">
            <p>&copy; 2024 <a style="text-decoration: none;" href="https://www.agl.or.ke/">AGL</a> . All rights
                reserved.</p>
        </footer>

        <!-- JavaScript for Menu Toggle -->
        <script>
            const menuToggle = document.getElementById("menu-toggle");
            const navigation = document.getElementById("navigation");

            menuToggle.addEventListener("click", () => {
                navigation.classList.toggle("active");
            });
        </script>
    </body>

</html>