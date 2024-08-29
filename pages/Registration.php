<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Form</title>
    <link href="assets/img/favicon.png" rel="icon">
    <link href="assets/img/favicon.png" rel="favicon.png">
    <link rel="stylesheet" href="../assets/CSS/registration.css">

</head>


<body>


    <form action="../forms/personalmembership.php" class="container" id="registrationForm">
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
                    <label>Make payment of Ksh 2000.00 as membership fee</label><br>
                    <label>Choose Payment Method:</label><br>
                    <button type="button" class="payment-button" id="mpesa"
                        onclick="selectPaymentMethod('mpesa')">Mpesa</button>
                    <button type="button" class="payment-button" id="paypal"
                        onclick="selectPaymentMethod('paypal')">PayPal</button>
                    <button type="button" class="payment-button" id="card"
                        onclick="selectPaymentMethod('card')">Card</button>
                    <input type="hidden" id="selectedPaymentMethod" name="paymentMethod" required>
                    <br><br><br>

                    <label for="options">Choose method used to make the payment:</label>
                    <select id="options" name="options">
                        <option value="Mpesa">Mpesa</option>
                        <option value="PayPal">PayPal</option>
                        <option value="Card">Card</option>
                        <option value="cash">cash</option>
                    </select>

                </div>

                <div class="stepINdivdiv">
                    <label for="paymentCode">Payment Code/ Receiver</label>
                    <input placeholder="Record the payment code from payment Message" type="text" id="paymentCode"
                        name="paymentCode"><br><br>

                    <label for="password">Enter password</label><br>
                    <div class="password-container">
                        <input type="password" id="password" name="password" required oninput="validatePasswords()">
                        <span class="toggle-icon" onclick="togglePassword('password')">👁️</span>
                    </div><br><br>

                    <label for="confirm-password">Confirm password</label><br>
                    <div class="password-container">
                        <input type="password" id="confirm-password" name="confirm-password" required
                            oninput="validatePasswords()">
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



    <!-- M-Pesa Payment Form -->
    <div id="mpesaForm" class="popup-container ">
        <form class="popup-content">
            <span class="close">×</span>
            <img src="../assets/img/mpesa.png" alt="M-Pesa" class="popup-logo">
            <label for="phone-number">Number</label>
            <input type="number" id="phone-number-mpesa" name="phone-number" placeholder="Enter your phone number">
            <label for="amount">Amount</label>
            <input type="text" id="amount" name="amount" value="300.00" readonly>
            <p>Confirm that you are making a payment of 300 Ksh as membership fees to the Association of Government
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
                // Check if all required fields are filled out
                const form = document.getElementById("registrationForm");
                if (form.checkValidity()) {
                    // Form is valid, submit it
                    form.submit();
                    alert("Registration submitted successfully. Wait for the reply");
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
            const errorMessage = document.getElementById('error-message');

            if (password !== confirmPassword) {
                errorMessage.style.display = 'block';
            } else {
                errorMessage.style.display = 'none';
            }
        }
    </script>



    <!-- <script>

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
                
                document.getElementById("registrationForm").submit();
                alert("Registration submitted successfully. Wait for the reply");
            }
        });

        prevBtn.addEventListener("click", () => {
            if (currentStep > 0) {
                currentStep--;
                updateFormStep();
            }
        });

        updateFormStep();

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
ons
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
            const type = passwordField.type === 'password' ? 'text' : 'password';
            passwordField.type = type;
            toggleIcon.textContent = '🙈';
        }

        function validatePasswords() {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm-password').value;
            const errorMessage = document.getElementById('error-message');

            if (password !== confirmPassword) {
                errorMessage.style.display = 'block'; 
            } else {
                errorMessage.style.display = 'none';
            }
        }

    </script> -->
    <script src="https://js.stripe.com/v3/"></script>
    <script src="../assets/JS/aglpaycard.js"></script>


</body>

</html>