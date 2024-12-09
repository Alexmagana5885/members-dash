<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login</title>
  <link href="assets/img/favicon.png" rel="icon" />
  <link rel="stylesheet" href="assets/CSS/startfile.css" />
</head>

<body>
  <!-- Header -->
  <header class="site-header">
    <div class="logo">
      <img src="assets/img/logo.png" alt="" />
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


    .popup.show {
      opacity: 1;
      visibility: visible;
      transform: translateY(0);
    }

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

    /* otp styles  */

    .otpbox {
      padding: 5px;
      border-radius: 8px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.5);
      min-height: 200px;
      margin-top: 20px;
      display: none;
    }

    .otpbox input {
      width: 100%;
      margin-top: 10px;
      margin-bottom: 10px;
      border-radius: 5px;
      box-shadow: 0 4px 8px rgba(0, 0, 255, 0.4);
      font-size: 16px;
      padding: 10px;
      border: none;
      outline: none;
    }

    .otpbox h3 {
      text-align: center;
      color: #020726;
    }
  </style>

  <div id="response-popup" class="popup"></div>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      fetch('forms/RegResponse.php')
        .then(response => response.json())
        .then(data => {
          var popup = document.getElementById('response-popup');
          if (popup) {
            var alertClass = data.success ? 'alert-success' : 'alert-danger';
            var message = '';

            if (data.success) {
              message = '<div class="alert ' + alertClass + '">' + data.message + '</div>';
            } else {
              if (data.errors && data.errors.length > 0) {
                message += '<div class="alert ' + alertClass + '">';
                data.errors.forEach(function(error) {
                  message += '<p>' + error + '</p>';
                });
                message += '</div>';
              } else {
                message = '<div class="alert ' + alertClass + '">' + data.message + '</div>';
              }
            }

            popup.innerHTML = message;
            popup.classList.add('show');

            // Hide the popup after 10 seconds
            setTimeout(function() {
              popup.classList.remove('show');
            }, 10000); // 10000ms = 10 seconds
          }
        })
        .catch(error => console.error('Error fetching response:', error));
    });
  </script>



  <!-- Main Content -->
  <main>
    <div class="container">
      <div class="image-side">
        <img class="logo" src="assets/img/logo.png" alt="Logo" />
        <img
          class="main-image"
          src="assets/img/DemoImage/LoginImage.jpg"
          alt="AGL" />
      </div>
      <div class="login-side">

        <form id="loginForm">
          <h2>Login</h2>
          <div class="input-group">
            <label for="MembershipType">Membership Type</label>
            <select class="optionLogin" id="MembershipType" name="MembershipType" required>
              <option value="Default">Choose</option>
              <option value="IndividualMember">Individual Member</option>
              <option value="OrganizationMember">Organization Member</option>
            </select>
          </div>
          <div class="input-group">
            <label for="email">Email</label>
            <input class="emailInput" type="email" id="email" name="email" required />
          </div>
          <div class="input-group">
            <label for="password">Password</label>
            <div class="password-container">
              <input type="password" id="password" name="password" required />
              <span class="toggle-password" onclick="togglePasswordVisibility()">👁️</span>
            </div>
          </div>
          <button type="submit">Login</button>
          <!-- <p class="register-link">
            <a href="javascript:void(0)" onclick="ForgotPassword()">Forgot Password</a>
          </p> -->

          <p class="register-link">
            Not registered?
            <a href="javascript:void(0)" onclick="showRegistration()">Register as a Member</a>
          </p>

        </form>

        <!-- .........OTP.................. -->

        <div id="otpbox" class="otpbox">
          <form id="OTPform" action="forms/OTPverf.php" method="POST">
            <h3>Kindly enter the OTP sent on your Mail</h3><br>
            <hr>
            <input name="otp" placeholder="OTP..." type="text" required>
            <button id="submitOTP" type="submit">Submit</button>
          </form>
        </div>

        <!-- .................................. -->

        <!-- ...........................password reset................................ -->

        <style>
          .resetPasswordFormDiv {
            width: 100%;
            margin-top: 20px;
          }

          .resetPasswordFormDiv input {
            width: 100%;
            padding: 10px;
            padding-right: 40px;
            border: 0;
            border-radius: 5px;
            font-size: 16px;
            box-sizing: border-box;
            box-shadow: 2px 2px 2px 2px rgba(0, 0, 0, 0.2);
            margin-bottom: 20px;
          }

          .resetPasswordFormDiv label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
          }

          .resetPasswordFormDiv h2 {
            margin-bottom: 20px;
            font-size: 24px;
            color: #333;
            text-align: center;
          }


          .error-message {
            color: red;
            font-size: 14px;
            margin-top: 5px;
          }

          .password-container {
            position: relative;
          }



          button:disabled {
            background-color: gray;
            cursor: not-allowed;
          }
        </style>

        <form style="display: none; margin-top:20px;" id="resetPasswordForm" action="forms/OTPpassReset.php" method="POST">
          <hr />
          <div class="resetPasswordFormDiv">
            <h2>Reset Password</h2>
            <label for="Emailinsertion">Insert Your Registered Email</label>
            <input
              placeholder="email..."
              type="email"
              id="resetemail"
              name="resetemail"
              required />
            <button type="submit" id="resetPasswordBtn">Send</button>
          </div>
        </form>

        <form style="display: none;" id="resetPasswordFormset" action="forms/PasswordReset.php" method="POST">
          <div class="resetPasswordFormDiv">
            <h2>Set a New Password</h2>
            <p style="margin-bottom: 10px">
              Create a strong password with a minimum length of 8 characters,
              including a mix of upper-case letters, lower-case letters,
              numbers, and special characters.
            </p>

            <!-- Reset Code -->
            <label for="ResetCode">Insert Reset Code Sent In Your Email</label>
            <input
              placeholder="Reset Code..."
              type="text"
              id="ResetCode"
              name="ResetCode"
              required />

            <!-- Membership Type -->
            <div class="input-group">
              <label for="MembershipType">Membership Type</label>
              <select
                class="optionLogin"
                id="MembershipTypereset"
                name="MembershipType"
                required>
                <option value="Default">Choose</option>
                <option value="IndividualMember">Individual Member</option>
                <option value="OrganizationMember">
                  Organization Member
                </option>
              </select>
            </div>

            <!-- User Email -->
            <label for="UserEmail">Insert Your Registered Email</label>
            <input
              placeholder="email..."
              type="email"
              id="UserEmailReset"
              name="UserEmailReset"
              required />

            <!-- New Password -->
            <div class="password-container">
              <label for="NewPassWord">New Password</label>
              <input
                oninput="validatePasswords()"
                placeholder="New Password..."
                type="password"
                id="NewPassWordReset"
                name="NewPassWordReset"
                required />
              <span
                id="NewPassWordResetToggle"
                class="toggle-password"
                onclick="togglePasswordVisibility('NewPassWordReset')">👁️</span>
            </div>

            <!-- Confirm Password -->
            <div class="password-container">
              <label for="NewPassWordConfirm">Confirm the Password</label>
              <input
                oninput="validatePasswords()"
                placeholder="Confirm the Password..."
                type="password"
                id="NewPassWordConfirm"
                name="NewPassWordConfirm"
                required />
              <span
                id="NewPassWordConfirmToggle"
                class="toggle-password"
                onclick="togglePasswordVisibility('NewPassWordConfirm')">👁️</span>
            </div>

            <!-- Error Messages -->
            <div
              class="error-message"
              id="error-message1"
              style="display: none">
              Passwords do not match!
            </div>
            <div
              class="error-message"
              id="error-message2"
              style="display: none">
              Password does not meet policy requirements!
            </div>

            <!-- Submit Button -->
            <button id="resetPasswordBtnset" type="submit" disabled>Set</button>

          </div>
        </form>

        <script>
          document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('resetPasswordFormset');
            const resetCodeInput = document.getElementById('ResetCode');
            const membershipTypeInput = document.getElementById('MembershipTypereset');
            const emailInput = document.getElementById('UserEmailReset');
            const newPasswordInput = document.getElementById('NewPassWordReset');
            const confirmPasswordInput = document.getElementById('NewPassWordConfirm');
            const submitButtonreset = document.getElementById('resetPasswordBtnset');

            const errorMessage1 = document.getElementById('error-message1'); // Passwords do not match
            const errorMessage2 = document.getElementById('error-message2'); // Password policy error

            // Password validation regex (8+ chars, upper, lower, number, special char)
            const passwordPolicyRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+[\]{};':"\\|,.<>/?]).{8,}$/;

            // Function to check if all fields are filled and valid
            function checkFormValidity() {
              const isResetCodeFilled = resetCodeInput.value.trim() !== '';
              const isMembershipTypeSelected = membershipTypeInput.value !== 'Default';
              const isEmailFilled = emailInput.value.trim() !== '';
              const isNewPasswordFilled = newPasswordInput.value.trim() !== '';
              const isConfirmPasswordFilled = confirmPasswordInput.value.trim() !== '';
              const doPasswordsMatch = newPasswordInput.value === confirmPasswordInput.value;
              const isPasswordValid = passwordPolicyRegex.test(newPasswordInput.value);

              // Handle password matching alert
              if (!doPasswordsMatch && isNewPasswordFilled && isConfirmPasswordFilled) {
                errorMessage1.style.display = 'block';
              } else {
                errorMessage1.style.display = 'none';
              }

              // Handle password policy alert
              if (!isPasswordValid && isNewPasswordFilled) {
                errorMessage2.style.display = 'block';
              } else {
                errorMessage2.style.display = 'none';
              }

              // Enable button if all conditions are met
              if (
                isResetCodeFilled &&
                isMembershipTypeSelected &&
                isEmailFilled &&
                isNewPasswordFilled &&
                isConfirmPasswordFilled &&
                doPasswordsMatch &&
                isPasswordValid
              ) {
                submitButtonreset.disabled = false;
              } else {
                submitButtonreset.disabled = true;
              }
            }

            // Function to toggle password visibility
            function togglePasswordVisibility(inputId) {
              const passwordInput = document.getElementById(inputId);
              if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
              } else {
                passwordInput.type = 'password';
              }
            }

            // Attach event listeners to inputs
            resetCodeInput.addEventListener('input', checkFormValidity);
            membershipTypeInput.addEventListener('change', checkFormValidity);
            emailInput.addEventListener('input', checkFormValidity);
            newPasswordInput.addEventListener('input', checkFormValidity);
            confirmPasswordInput.addEventListener('input', checkFormValidity);

            // Attach toggle visibility for password fields
            document.getElementById('NewPassWordResetToggle').addEventListener('click', () => togglePasswordVisibility('NewPassWordReset'));
            document.getElementById('NewPassWordConfirmToggle').addEventListener('click', () => togglePasswordVisibility('NewPassWordConfirm'));
          });

          // Attach onClick event to the submit button
          submitButton.addEventListener('click', handleFormSubmit);

          function ForgotPassword() {
            var resetForm = document.getElementById('resetPasswordForm');
            loginForm

            if (resetForm.style.display === 'none' || resetForm.style.display === '') {
              resetForm.style.display = 'block';
              loginForm.style.display = 'none';
            } else {
              resetForm.style.display = 'none';
            }
          }

          // .........................................
          document.getElementById("resetPasswordForm").addEventListener("submit", function(event) {
            event.preventDefault();

            const emailInput = document.getElementById("resetemail").value.trim();
            if (!emailInput) {
              showPopup("Please enter an email address.");
              return;
            }

            if (!/^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$/.test(emailInput)) {
              showPopup("Please enter a valid email address.");
              return;
            }

            const submitButton = document.getElementById("resetButton");
            submitButton.disabled = true;
            submitButton.textContent = "Sending...";

            const formData = new FormData(this);

            fetch("forms/OTPpassReset.php", {
                method: "POST",
                body: formData,
              })
              .then(response => {
                if (!response.ok) {
                  throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
              })
              .then(data => {
                submitButton.disabled = false;
                submitButton.textContent = "Send OTP";

                if (!data) {
                  showPopup("No response from the server.");
                  return;
                }

                if (data.status === "error") {
                  showPopup(data.message);
                } else if (data.status === "success") {
                  document.getElementById("loginForm").style.display = "none";
                  document.getElementById("resetPasswordForm").style.display = "none";
                  document.getElementById("resetPasswordFormset").style.display = "block";
                }
              })
              .catch(error => {
                submitButton.disabled = false;
                submitButton.textContent = "Send OTP";
                console.error("Error:", error);
                showPopup("An error occurred. Please try again.");
              });
          });
        </script>

        <!-- .......................................................................................................... -->

      </div>
    </div>

    <script>
      document.getElementById("OTPform").addEventListener("submit", function(event) {
        event.preventDefault(); // Prevent default form submission

        const formData = new FormData(this);

        fetch("forms/OTPverf.php", {
            method: "POST",
            body: formData,
          })
          .then(response => response.json())
          .then(data => {
            if (data.status === "success") {

              window.location.href = data.redirect;
            } else if (data.status === "error") {

              showPopup(data.message);
            }
          })
          .catch(error => {
            console.error("Error:", error);
            showPopup("An error occurred. Please try again.");
          });
      });

      function showPopup(message) {
        const popup = document.getElementById("popup");
        popup.textContent = message;
        popup.style.display = "block";

        setTimeout(() => {
          popup.style.display = "none";
        }, 3000); // Hide popup after 3 seconds
      }
    </script>
    <!-- Registration Information Section (Initially Hidden) -->
    <div class="form-step form-stepINdiv">
      <!-- General Information Step -->
      <div class="stepINdivdiv">
        <h3>Guidelines for Membership Registration</h3>
        <p>To be a member of AGL, you need to meet the following criteria:</p>
        <ul>
          <li>
            Have completed a Diploma, Degree, or Masters in Library and
            Information Science from a recognized institution.
          </li>
          <li>Fill in the prescribed form below.</li>
          <li>
            Pay a registration fee of Kshs. 2,000 and an annual fee of Kshs.
            3,600.
          </li>
          <li>There are two types of membership:</li>
          <ul>
            <li>
              <strong>Individual Membership:</strong> Pay Kshs. 2,000 once.
            </li>
            <li>
              <strong>Institutional Membership:</strong> An institution pays a
              fee of Kshs. 15,000 annually.
            </li>
          </ul>
        </ul>
        <p>
          Choose the membership type and follow the instructions to complete
          your registration.
        </p>
      </div>

      <div class="membership-options">
        <button
          type="button"
          class="continue-individual"
          onclick="redirectTo('pages/Registration.php')">
          Individual Membership
        </button>
        <button
          type="button"
          class="continue-institutional"
          onclick="redirectTo('pages/Organizationregistration.php')">
          Institutional Membership
        </button>
      </div>
    </div>

    <script>
      function togglePasswordVisibility() {
        const passwordInput = document.getElementById("password");
        const toggleButton = document.querySelector(".toggle-password");

        if (passwordInput.type === "password") {
          passwordInput.type = "text";
          toggleButton.textContent = "🙈"; // Hide icon
        } else {
          passwordInput.type = "password";
          toggleButton.textContent = "👁️"; // Show icon
        }
      }

      function showRegistration() {
        const registrationSection = document.querySelector(".form-stepINdiv");
        registrationSection.style.display = "flex";
        registrationSection.scrollIntoView({
          behavior: "smooth",
          block: "start",
        });
      }

      function redirectTo(url) {
        window.location.href = url;
      }

      document.getElementById("loginForm").addEventListener("submit", function(event) {
        event.preventDefault();

        const formData = new FormData(this);

        fetch("forms/startT.php", {
            method: "POST",
            body: formData,
          })
          .then(response => response.json())
          .then(data => {
            if (data.status === "error") {
              showPopup(data.message);
            } else if (data.status === "otp_sent") {
              // window.location.href = data.redirect;
              document.getElementById("loginForm").style.display = "none";
              document.getElementById("otpbox").style.display = "block";
            }
          })
          .catch(error => {
            console.error("Error:", error);
            showPopup("An error occurred. Please try again.");
          });
      });

      function showPopup(message) {
        const popup = document.getElementById("popup");
        popup.textContent = message;
        popup.style.display = "block";

        setTimeout(() => {
          popup.style.display = "none";
        }, 3000); // Hide popup after 3 seconds
      }
    </script>

    <!-- HTML for the popup -->
    <div
      id="popup"
      style="
          display: none;
          position: fixed;
          top: 20%;
          left: 50%;
          transform: translate(-50%, -50%);
          padding: 20px;
          background-color: #b5cae4;
          color: #2f4b65;
          border: 1px solid #f5c6cb;
          border-radius: 5px;
          z-index: 1000;
        "></div>
  </main>

  <!-- Footer -->
  <footer class="site-footer">
    <p>
      &copy; 2024
      <a style="text-decoration: none" href="http://www.agl.or.ke/">AGL.or.ke</a>
      . All rights reserved.
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