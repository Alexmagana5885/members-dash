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
                        <p>Payment</p>
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

                            <label for="ContactPerson">Contact Person:</label>
                            <input type="text" id="ContactPerson" name="ContactPerson" required><br><br>

                            <label for="passport">Logo Image:</label>
                            <input style="height: 35px ; padding: 10px" type="file" id="LogoImage" name="LogoImage"
                                accept="image/*" required><br><br>
                        </div>

                        <div class="stepINdivdiv">
                            <label for="ContactPhoneNumber">Contact Phone Number:</label>
                            <input type="tel" id="ContactPhoneNumber" name="ContactPhoneNumber" required><br><br>

                            <label for="OrganizationDateofRegistration">Date of Registration:</label>
                            <input type="date" id="OrganizationDateofRegistration" name="OrganizationDateofRegistration"
                                required><br><br>

                            <label for="OrganizationAddress">Organization Address:</label>
                            <textarea id="OrganizationAddress" name="OrganizationAddress" rows="4"></textarea><br><br>
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
                                name="RegistrationCertificate" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                required><br><br>

                        </div>

                        <div class="stepINdivdiv">
                            <label for="OrganizationType">Organization Type:</label>
                            <input type="text" id="OrganizationType" name="OrganizationType"
                                placeholder="e.g., governmental or non-governmental" required><br><br>

                            <label for="startDate">Start Date:</label>
                            <input type="date" id="startDate" name="startDate" required><br><br>

                            <label for="WhatYouDo">What You Do:</label>
                            <input type="text" id="WhatYouDo" name="WhatYouDo" required><br><br>

                            <label for="NumberOfEmployees">Number of Employees:</label>
                            <input type="number" id="NumberOfEmployees" name="NumberOfEmployees" required><br><br>
                        </div>
                    </div>
                </div>


                <!-- Payment Details -->
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
                            <label>Make payment of Ksh 2000.00 as Registration fee</label><br>
                            <p>Kindly ensure you create your password before making your payment. After completing the
                                payment,
                                you will be redirected to the login page, where you can log in using your credentials.
                            </p>
                            <label>Click to make Your payment</label><br>
                            <button type="button" class="payment-button" id="mpesa"
                                onclick="selectPaymentMethod('mpesa')" disabled>Mpesa</button>

                            <button style="display: none;" type="button" class="payment-button" id="paypal"
                                onclick="selectPaymentMethod('paypal')">PayPal</button>

                            <button style="display: none;" type="button" class="payment-button" id="card"
                                onclick="selectPaymentMethod('card')">Card</button>

                            <input type="hidden" id="selectedPaymentMethod" name="paymentMethod" required>
                            <br><br><br>

                            <p>Kindly ensure that all required sections of the form are completed
                                before submitting it; otherwise, it will not be processed.</p>

                        </div>

                    </div>
                </div>

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


            <!-- M-Pesa Payment Form -->
            <div id="mpesaForm" class="popup-container">
                <form class="popup-content" method="POST" action="../forms/Payment/Mpesa-Daraja-Api-main/stkpush.php">
                    <span class="close">×</span>
                    <img src="../assets/img/mpesa.png" alt="M-Pesa" class="popup-logo">

                    <p style="text-align: center; ">Enter the phone number you are using to make the payment here</p>

                    <label for="phone-number-mpesa">Number</label>
                    <input type="number" id="phone-number-mpesa" name="phone_number"
                        placeholder="Enter your phone number" required>

                    <label for="amount">Amount</label>
                    <input type="text" id="amount" name="amount" value="1" readonly>

                    <p style="text-align:center;">Confirm that you are making a payment of Two Thousand Kenyan
                        Shillings. (2,000 Ksh) as membership fees to the Association of Government
                        Librarians.</p>

                    <div class="payButtons">
                        <button class="pay-btn" id="MakePaymentBTN" type="submit">Make Payment</button>
                    </div>
                </form>
            </div>

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
                }

                nextBtn.addEventListener("click", () => {
                    if (currentStep < steps.length - 1) {
                        currentStep++;
                        updateFormStep();
                    } else {
                        // Check if all required fields are filled out
                        const form = document.getElementById("registrationForm");
                        if (form.checkValidity()) {
                            // Form is valid, submit it
                            form.submit();
                            // alert("Registration submitted successfully. Wait for the reply");
                        } else {
                            // Form is not valid, display errors
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

                updateFormStep();

                // JavaScript for handling the payment popups
                const mpesaForm = document.getElementById('mpesaForm');
                const paypalPopup = document.getElementById('paypalPopup');
                const cardPopup = document.getElementById('cardPopup');

                const mpesaBtn = document.getElementById('mpesa');
                const paypalBtn = document.getElementById('paypal');
                const cardBtn = document.getElementById('card');

                const closeBtns = document.querySelectorAll('.close');

                function openPopup(popup) {
                    popup.style.display = 'block';
                }

                function closeAllPopups() {
                    mpesaForm.style.display = 'none';
                    paypalPopup.style.display = 'none';
                    cardPopup.style.display = 'none';
                }

                mpesaBtn.addEventListener('click', () => openPopup(mpesaForm));
                paypalBtn.addEventListener('click', () => openPopup(paypalPopup));
                cardBtn.addEventListener('click', () => openPopup(cardPopup));

                closeBtns.forEach(button => {
                    button.addEventListener('click', closeAllPopups);
                });

                window.addEventListener('click', (event) => {
                    if (event.target.classList.contains('popup-container')) {
                        closeAllPopups();
                    }
                });

                function togglePassword(id) {
                    const passwordField = document.getElementById(id);
                    passwordField.type = passwordField.type === 'password' ? 'text' : 'password';
                }

                function validatePasswords() {
                    const password = document.getElementById('password').value;
                    const confirmPassword = document.getElementById('confirm-password').value;
                    const passwordPolicyRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&#])[A-Za-z\d@$!%*?&#]{8,}$/;

                    const errorMessage1 = document.getElementById('error-message1');
                    const errorMessage2 = document.getElementById('error-message2');

                    let showError1 = false;
                    let showError2 = false;

                    if (password !== confirmPassword) {
                        showError1 = true;
                    }

                    if (!passwordPolicyRegex.test(password)) {
                        showError2 = true;
                    }

                    if (showError1) {
                        errorMessage1.style.display = 'block';
                    } else {
                        errorMessage1.style.display = 'none';
                    }

                    if (showError2) {
                        errorMessage2.style.display = 'block';
                    } else {
                        errorMessage2.style.display = 'none';
                    }


                    const button = document.getElementById('mpesa');
                    if (password && confirmPassword && !showError1 && !showError2) {
                        button.disabled = false;
                    } else {
                        button.disabled = true;
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