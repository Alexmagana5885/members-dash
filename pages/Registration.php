<?php
session_start();
// Your PHP code here
?>


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
        <li><a href="https://www.agl.or.ke/">Home</a></li>
        <li><a href="https://www.agl.or.ke/about-us/">About</a></li>
        <li><a href="https://www.agl.or.ke/contact-us/">Contact</a></li>
      </ul>
    </nav>
  </header>

  <!-- Main Content -->
  <main style=" width: 100%; ">

    <body>

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

<div id="response-popup" class="popup">
  <?php
  if (isset($_SESSION['response'])) {
    $response = $_SESSION['response'];

    if (!$response['success']) {
      // Display error messages
      if (!empty($response['errors'])) {
        echo '<div class="alert alert-danger">';
        foreach ($response['errors'] as $error) {
          echo "<p>$error</p>";
        }
        echo '</div>';
      } else {
        echo '<div class="alert alert-danger">' . $response['message'] . '</div>';
      }
    } else {
      // Display success message
      echo '<div class="alert alert-success">' . $response['message'] . '</div>';
    }
    // Clear the session response
    unset($_SESSION['response']);
  } else {
    // Default message
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
            <p>Submit</p>
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
              <span id="email-error" style="color: blue;"></span>

              <label for="phone">Phone Number:</label>
              <input type="number" id="phone" name="phone" required><br><br>
              <span id="phone-error" style="color: blue;"></span>
            </div>

            <div class="stepINdivdiv">
              <label for="dob">Date of Birth:</label>
              <input type="date" id="dob" name="dob" required><br><br>
              <span id="dob-error" style="color: red;"></span>

              <label for="address">Address:</label>
              <textarea id="Homeaddress" name="Homeaddress" rows="4" required></textarea><br><br>

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
              <span id="startDate-error" style="color: red;"></span>
            </div>

            <div class="stepINdivdiv">
              <label for="graduationYear">Year of Graduation:</label>
              <input type="number" id="graduationYear" name="graduationYear" required><br><br>
              <span id="graduationYear-error" style="color: red;"></span>

              <label for="completionLetter">Completion Letter:</label>
              <input style="height: 35px ; padding: 10px" type="file" id="completionLetter" name="completionLetter" accept=".pdf"
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
              <input type="text" id="currentCompany" name="currentCompany" required><br><br>
            </div>

            <div class="stepINdivdiv">
              <label for="position">Position:</label>
              <input type="text" id="position" name="position" required><br><br>

              <label for="workAddress">Work Address:</label>
              <textarea id="workAddress" name="workAddress" rows="4" required></textarea><br><br>
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
              <label>Read the information bellow Before Registration</label><br>
              <p>Please ensure the information you provide is accurate and will be kept confidential.</p><br>
              <p>You must create a strong password following our guidelines and remember it for future logins.
                After logging in, you will have 2 weeks to pay the registration fee of Ksh 2000.00 from your account,
                or your account will be deactivated.</p>


              <p>Please make sure all required sections of the form are completed before submitting it;
                otherwise, it will not be processed.</p>



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

        <style>
          .submit-button:disabled {
            background-color: #cce5ff;
            color: #6c757d;
            cursor: not-allowed;
          }
        </style>

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

      <!-- form validation script -->
      <script>
        document.getElementById('phone').addEventListener('input', function() {
          const phone = this.value;
          const phonePattern = /^(\+254|\+1|\+44|\+91)[0-9]{7,12}$/; // Adjust the country codes as needed
          const errorElement = document.getElementById('phone-error');

          // Check if the phone number matches the pattern
          if (!phonePattern.test(phone)) {
            errorElement.textContent = 'Ensure that you fill a valid phone number with a valid country code e.g., +254, +1, +44, +91';
          } else {
            errorElement.textContent = ''; // Clear any previous error
          }
        });

        document.getElementById('graduationYear').addEventListener('input', function() {
          const graduationYear = parseInt(this.value, 10);
          const currentYear = new Date().getFullYear();
          const errorElement = document.getElementById('graduationYear-error');

          // Check if the year is more than 4 digits
          if (this.value.length > 4) {
            errorElement.textContent = 'Year of Graduation cannot have more than 4 digits.';
            this.value = ''; // Clear the input
          } else if (graduationYear > currentYear) {
            // Check if the year is in the future
            errorElement.textContent = 'Year of Graduation cannot be in the future.';
            this.value = ''; // Clear the input
          } else {
            errorElement.textContent = ''; // Clear any previous error
          }
        });


        document.getElementById('startDate').addEventListener('change', function() {
          const startDate = new Date(this.value);
          const today = new Date();
          const errorElement = document.getElementById('startDate-error');

          // Remove time component from today's date for accurate comparison
          today.setHours(0, 0, 0, 0);

          if (startDate > today) {
            errorElement.textContent = 'The start date cannot be in the future.';
            this.value = ''; // Clear the date input
          } else {
            errorElement.textContent = ''; // Clear any previous error
          }
        });

        document.getElementById('email').addEventListener('input', function() {
          const email = this.value;
          const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

          // Update the domain pattern to include multiple domains
          const domainPattern = /@(example\.com|yahoo\.com|outlook\.com|[^@\s]+\.or\.ke)$/i;
          const errorElement = document.getElementById('email-error');

          if (!emailPattern.test(email)) {
            errorElement.textContent = 'Please enter a valid email address.';
          } else if (!domainPattern.test(email)) {
            errorElement.textContent = 'Make sure that your email is from a valid domain e.g., example.com, yahoo.com, outlook.com, or .or.ke.';
          } else {
            errorElement.textContent = ''; // Clear any previous error
          }
        });


        document.getElementById('dob').addEventListener('change', function() {
          const dob = new Date(this.value);
          const today = new Date();

          // Calculate age
          let age = today.getFullYear() - dob.getFullYear();
          const monthDifference = today.getMonth() - dob.getMonth();
          const dayDifference = today.getDate() - dob.getDate();

          // Adjust age if the birthday hasn't occurred yet this year
          if (monthDifference < 0 || (monthDifference === 0 && dayDifference < 0)) {
            age--;
          }

          const errorElement = document.getElementById('dob-error');

          if (dob > today) {
            errorElement.textContent = 'Date of birth cannot be in the future.';
            this.value = ''; // Clear the date input
          } else if (age < 18) {
            errorElement.textContent = 'You must be at least 18 years old.';
            this.value = ''; // Clear the date input
          } else {
            errorElement.textContent = ''; // Clear any previous error
          }
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
    <p>&copy; 2024 <a style="text-decoration: none;" href="https://www.agl.or.ke/">AGL</a> . All rights reserved.
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