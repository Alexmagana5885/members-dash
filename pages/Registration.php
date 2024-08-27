<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Form</title>
    <link href="assets/img/favicon.png" rel="icon">
    <link href="assets/img/favicon.png" rel="favicon.png">

</head>

<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 90%;
        margin: 0;
        padding: 0;
    }

    .container {
        background: #fff;
        padding: 20px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        width: 70%;
        height: 90%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        overflow-y: auto;
    }

    /* Large screens (desktops) */
    @media (min-width: 1024px) {
        .container {
            width: 70%;
            height: 90%;
        }
    }

    /* Small screens (tablets and phones) */
    @media (max-width: 1024px) {
        .container {
            width: 90%;
            height: auto;
            padding: 15px;
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
        content: '';
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
    input[type="month"] input[type="password"] {
        width: calc(100% - 22px);
        padding: 8px;
        margin-bottom: 15px;
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
        input[type="password"] {
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
        background-color: #007BFF;
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
</style>

<body>

<form action="../forms/Registrationform.php" class="container" id="registrationForm">
    <h2>Registration Form</h2>

    <a href="DashBoard.php">Dash</a>

    <a href="AGLADMIN.php">Admin Dash</a>

    <!-- Progress Bar -->
    <div class="progress-bar">
        <div class="progress-step active">
            <div class="progress-step-circle">1</div>
            <p>Personal Details</p>
        </div>
        <div class="progress-step">
            <div class="progress-step-circle">2</div>
            <p>Education</p>
        </div>
        <div class="progress-step">
            <div class="progress-step-circle">3</div>
            <p>Profession</p>
        </div>
        <div class="progress-step">
            <div class="progress-step-circle">4</div>
            <p>Payment</p>
        </div>
    </div>

    <!-- Form Steps -->
    <div class="form-step active">
        <div class="form-stepINdiv">
            <div class="stepINdivdiv">
                <h3>Personal Details</h3>
                <label for="name">Full Name:</label>
                <input type="text" id="name" name="name" required><br><br>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required><br><br>

                <label for="phone">Phone Number:</label>
                <input type="tel" id="phone" name="phone" required><br><br>
            </div>

            <div class="stepINdivdiv">
                <label for="dob">Date of Birth:</label>
                <input type="date" id="dob" name="dob" required><br><br>

                <label for="address">Address:</label>
                <textarea id="Homeaddress" name="Homeaddress" rows="4"></textarea><br><br>

                <label for="passport">Passport Image:</label>
                <input type="file" id="passport" name="passport" accept="image/*" required><br><br>
            </div>
        </div>
    </div>

    <div class="form-step">
        <div class="form-stepINdiv">
            <div class="stepINdivdiv">
                <h3>Education</h3>
                <label for="highestDegree">Highest Degree:</label>
                <input type="text" id="highestDegree" name="highestDegree" required><br><br>

                <label for="institution">Institution Name:</label>
                <input type="text" id="institution" name="institution" required><br><br>

                <label for="startDate">Start Date:</label>
                <input type="date" id="startDate" name="startDate" required><br><br>
            </div>

            <div class="stepINdivdiv">
                <label for="graduationYear">Year of Graduation:</label>
                <input type="number" id="graduationYear" name="graduationYear" required><br><br>

                <label for="completionLetter">Completion Letter:</label>
                <input type="file" id="completionLetter" name="completionLetter"
                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required><br><br>
            </div>
        </div>
    </div>

    <div class="form-step">
        <div class="form-stepINdiv">
            <div class="stepINdivdiv">
                <h3>Profession</h3>
                <label for="profession">Profession:</label>
                <input type="text" id="profession" name="profession" required><br><br>

                <label for="experience">Years of Experience:</label>
                <input type="number" id="experience" name="experience" required><br><br>

                <label for="currentCompany">Current Company:</label>
                <input type="text" id="currentCompany" name="currentCompany"><br><br>
            </div>

            <div class="stepINdivdiv">
                <label for="position">Position:</label>
                <input type="text" id="position" name="position"><br><br>

                <label for="workAddress">Work Address:</label>
                <textarea id="workAddress" name="workAddress" rows="4"></textarea><br><br>
            </div>
        </div>
    </div>

    <div class="form-step">
        <div class="form-stepINdiv">
            <div class="stepINdivdiv">
                <label>Make payment of Ksh 300.00 as membership fee</label><br>
                <label>Choose Payment Method:</label><br>
                <button type="button" class="payment-button" id="mpesa"
                    onclick="selectPaymentMethod('mpesa')">Mpesa</button>
                <button type="button" class="payment-button" id="paypal"
                    onclick="selectPaymentMethod('paypal')">PayPal</button>
                <button type="button" class="payment-button" id="card"
                    onclick="selectPaymentMethod('card')">Card</button>
                <input type="hidden" id="selectedPaymentMethod" name="paymentMethod" required>
                <br><br>
            </div>

            <div class="stepINdivdiv">
                <label for="paymentCode">Payment Code</label>
                <input placeholder="Record the payment code from payment Message" type="text" id="paymentCode"
                    name="paymentCode"><br><br>

                <label for="password">Enter password</label><br>
                <div class="password-container">
                    <input type="password" id="password" name="password" required oninput="validatePasswords()">
                    <span class="toggle-icon" onclick="togglePassword('password')">👁️</span>
                </div><br><br>

                <label for="confirm-password">Confirm password</label><br>
                <div class="password-container">
                    <input type="password" id="confirm-password" name="confirm-password" required oninput="validatePasswords()">
                    <span class="toggle-icon" onclick="togglePassword('confirm-password')">👁️</span>
                </div><br><br>

                <div class="error-message" id="error-message">Passwords do not match</div>
            </div>
        </div>
    </div>

    <div class="form-navigation">
        <button type="button" class="previous" disabled>Previous</button>
        <button type="button" class="next">Next</button>
    </div>
</form>

    <style>
        /* General Popup Container Styling */
        .popup-container {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100vh;
            /* overflow: auto; */
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


    <!-- M-Pesa Payment Form -->
    <div id="mpesaForm" class="popup-container ">
        <form class="popup-content">
            <span class="close">×</span>
            <img src="../assets/img/mpesa.png" alt="M-Pesa" class="popup-logo">
            <label for="phone-number">Number</label>
            <input type="number" id="phone-number-mpesa" name="phone-number" placeholder="Enter your phone number">
            <label for="amount">Amount</label>
            <input type="text" id="amount" name="amount" value="300.00" readonly>
            <p>Confirm that you are making a payment of 300 Ksh as membership fees to the Association of Governmental
                Librarians.</p>
            <div class="payButtons">
                <button class="pay-btn" id="MakePaymentBTN" type="submit">Make Payment</button>
                <!-- <button class="cancel-btn">Cancel</button> -->
            </div>
        </form>
    </div>

    <!-- PayPal Payment Popup -->

    <div id="paypalPopup" class="popup-container">
    <div class="popup-content">
        <span class="close">×</span>
        <img src="../assets/img/paypal.png" alt="PayPal" class="popup-logo">
        <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">
            <!-- PayPal required fields -->
            <input type="hidden" name="business" value="maganaalex634@gmail.com">
            <input type="hidden" name="cmd" value="_xclick">
            <input type="hidden" name="currency_code" value="USD">
            <input type="hidden" name="return" value="https://www.yoursite.com/thank_you.html">
            <input type="hidden" name="cancel_return" value="https://www.yoursite.com/cancel.html">

            <!-- Membership form fields -->
            <label for="member_name">Member Name</label>
            <input type="text" id="member_name" name="item_name" placeholder="Enter your name" required>

            <label for="member_email">Email</label>
            <input type="email" id="member_email" name="email" placeholder="Enter your email" required>

            <label for="amount">Amount</label>
            <input type="text" id="amount" name="amount" value="20.00" readonly>

            <!-- Payment button -->
            <div class="payButtons">
                <button id="paypalpayButton" type="submit" class="pay-btn">Pay Now</button>
            </div>
        </form>
    </div>
</div>


    <div id="cardPopup" class="popup-container">
    <div class="popup-content">
        <span class="close">×</span>
        <img src="../assets/img/card.png" alt="Card Payment" class="popup-logo">
        <form id="payment-form">
            <div id="card-element"><!-- Stripe Element will be inserted here --></div>
            <div id="card-errors" role="alert"></div>
            <div class="payButtons">
                <button id="card-button" class="pay-btn" type="button">Pay Now</button>
            </div>
        </form>
    </div>
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
                // Submit form here
                alert("Form submitted successfully!");
                document.getElementById("registrationForm").submit();
            }
        });

        prevBtn.addEventListener("click", () => {
            if (currentStep > 0) {
                currentStep--;
                updateFormStep();
            }
        });

        updateFormStep();

        // Show/Hide card payment details based on selection
        // Get the elements
        const mpesaForm = document.getElementById('mpesaForm');
        const paypalPopup = document.getElementById('paypalPopup');
        const cardPopup = document.getElementById('cardPopup');

        // Get the buttons or triggers to open popups
        const mpesaBtn = document.getElementById('mpesa');
        const paypalBtn = document.getElementById('paypal');
        const cardBtn = document.getElementById('card');

        // Get the close buttons
        const closeBtns = document.querySelectorAll('.close');

        // Function to open a popup
        function openPopup(popup) {
            popup.style.display = 'block';
        }

        // Function to close all popups
        function closeAllPopups() {
            mpesaForm.style.display = 'none';
            paypalPopup.style.display = 'none';
            cardPopup.style.display = 'none';
        }

        // Event listeners for open buttons
        mpesaBtn.addEventListener('click', () => openPopup(mpesaForm));
        paypalBtn.addEventListener('click', () => openPopup(paypalPopup));
        cardBtn.addEventListener('click', () => openPopup(cardPopup));

        // Event listeners for close buttons
        closeBtns.forEach(button => {
            button.addEventListener('click', closeAllPopups);
        });

        // Close popup when clicking outside of popup content
        window.addEventListener('click', (event) => {
            if (event.target.classList.contains('popup-container')) {
                closeAllPopups();
            }
        });





        // Function to toggle the visibility of the password
        function togglePassword(id) {
            const passwordField = document.getElementById(id);
            const type = passwordField.type === 'password' ? 'text' : 'password';
            passwordField.type = type;
            toggleIcon.textContent = '🙈';
        }

        // Function to validate if passwords match
        function validatePasswords() {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm-password').value;
            const errorMessage = document.getElementById('error-message');

            if (password !== confirmPassword) {
                errorMessage.style.display = 'block'; // Show error message
            } else {
                errorMessage.style.display = 'none'; // Hide error message
            }
        }

    </script>
    <script src="https://js.stripe.com/v3/"></script>
    <script src="../assets/JS/aglpaycard.js" ></script>


</body>

</html>