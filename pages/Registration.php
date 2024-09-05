<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Member Registration</title>
  <link href="../assets/img/favicon.png" rel="icon" />
  <link href="../assets/img/favicon.png" rel="favicon.png" />
  <link rel="stylesheet" href="../assets/CSS/registration.css">
</head>


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
        <li><a href="#home">Home</a></li>
        <li><a href="#about">About</a></li>
        <li><a href="#services">Services</a></li>
        <li><a href="#contact">Contact</a></li>
      </ul>
    </nav>
  </header>

  <!-- Main Content -->
  <main style=" width: 100%; ">

    <body>


      <form style="margin: 0 auto ;" method="post" action="../forms/personalmembership.php"
        enctype="multipart/form-data" class="container" id="registrationForm">
        <h2>Registration Form</h2>



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
        <div id="personal-details" class="form-step active">
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
              <input style="height: 35px ; padding: 10px" type="file" id="passport" name="passport" accept="image/*"
                required><br><br>
            </div>
          </div>
        </div>

        <div id="education" class="form-step">
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
              <input type="file" id="completionLetter" name="completionLetter" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                required><br><br>
            </div>
          </div>
        </div>

        <div id="profession" class="form-step">
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

        <style>
          .payment-button:disabled {
            background-color: #ccc;
            cursor: not-allowed;
          }
        </style>

        <div id="payment" class="form-step">
          <div class="form-stepINdiv">

            <div class="stepINdivdiv">
              <p>Create a strong password with a minimum length of 8 characters, including a mix of
                upper-case letters, lower-case letters, numbers, and special characters.</p>
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

              <!-- <div class="error-message" id="error-message">Passwords do not match or does not Follow the password Policy given above</div>
               -->
              <div class="error-message" id="error-message1">Passwords do not match!</div>

              <div class="error-message" id="error-message2">Password does not meet policy requirements Above!</div>

            </div>

            <div class="stepINdivdiv">
              <label>Make payment of Ksh 2000.00 as Registration fee</label><br>
              <p>Kindly ensure you create your password before making your payment. After completing the payment,
                you will be redirected to the login page, where you can log in using your credentials.</p>
              <label>Click to make Your payment</label><br>
              <button type="button" class="payment-button" id="mpesa"
                onclick="selectPaymentMethod('mpesa')" disabled>Mpesa</button>

              <button style="display: none;" type="button" class="payment-button" id="paypal"
                onclick="selectPaymentMethod('paypal')">PayPal</button>

              <button style="display: none;" type="button" class="payment-button" id="card"
                onclick="selectPaymentMethod('card')">Card</button>

              <input type="hidden" id="selectedPaymentMethod" name="paymentMethod" required>
              <br><br><br>

              <!-- <label for="options">Choose method used to make the payment:</label>
              <select id="options" name="options">
                <option value="Mpesa">Mpesa</option>
                <option value="PayPal">PayPal</option>
                <option value="Card">Card</option>
                <option value="cash">cash</option>
              </select> -->

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

      <script>
    function collectAndRedirect() {
      // Collect form data
      const formData = {
        name: document.getElementById('name').value,
        email: document.getElementById('email').value,
        phone: document.getElementById('phone').value,
        dob: document.getElementById('dob').value,
        homeAddress: document.getElementById('Homeaddress').value,
        passport: document.getElementById('passport').files[0]?.name,
        highestDegree: document.getElementById('highestDegree').value,
        institution: document.getElementById('institution').value,
        startDate: document.getElementById('startDate').value,
        graduationYear: document.getElementById('graduationYear').value,
        completionLetter: document.getElementById('completionLetter').files[0]?.name,
        profession: document.getElementById('profession').value,
        experience: document.getElementById('experience').value,
        currentCompany: document.getElementById('currentCompany').value,
        position: document.getElementById('position').value,
        workAddress: document.getElementById('workAddress').value,
        password: document.getElementById('password').value
      };

      // Convert form data to JSON
      const jsonData = JSON.stringify(formData);

      // Store JSON data in a hidden field or pass it in a URL
      const hiddenField = document.getElementById('hiddenFormData');
      hiddenField.value = jsonData;

      // Optionally, you can redirect to a payment page if needed
      // For example:
      // window.location.href = 'paymentPage.html';

      // Or submit the form if it's part of the same form
      document.getElementById('mpesaFormPay').submit();
    }
  </script>


      <!-- M-Pesa Payment Form -->
      <div id="mpesaForm" class="popup-container">
        <form id="mpesaFormPay"  class="popup-content" method="POST" action="../forms/Payment/Mpesa-Daraja-Api-main/stkpush.php">
          <span class="close">×</span>
          <img src="../assets/img/mpesa.png" alt="M-Pesa" class="popup-logo">

          <p style="text-align: center; " >Enter the phone number you are using to make the payment here</p>

          <label for="phone-number-mpesa">Number</label>
          <input type="number" id="phone-number-mpesa" name="phone_number" placeholder="Enter your phone number"
            required>

          <label for="amount">Amount</label>
          <input type="text" id="amount" name="amount" value="1" readonly>

          <p style="text-align:center;" >Confirm that you are making a payment of Two Thousand Kenyan Shillings. (2,000 Ksh) as membership fees to the Association of Government
            Librarians.</p>

          <div class="payButtons">
          <input type="hidden" id="hiddenFormData" name="formData">

          <button class="pay-btn" id="MakePaymentBTN" type="button" onclick="collectAndRedirect()">Make Payment and Submit</button>
            <!-- <button class="pay-btn" id="MakePaymentBTN" type="submit">Make Payment</button> -->
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

        function togglePassword(id) {
          const passwordField = document.getElementById(id);
          if (passwordField.type === 'password') {
            passwordField.type = 'text';
          } else {
            passwordField.type = 'password';
          }
        }

        // function selectPaymentMethod(method) {
        //   alert(`Selected payment method: ${method}`);
        // }

      </script>

      <script src="https://js.stripe.com/v3/"></script>
      <script src="../assets/JS/aglpaycard.js"></script>


    </body>

  </main>

  <!-- Footer -->
  <footer class="site-footer">
    <p>&copy; 2024 <a style="text-decoration: none;" href="AGL.or.ke">http://www.agl.or.ke/</a> . All rights reserved.
    </p>
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