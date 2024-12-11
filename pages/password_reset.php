<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Password Reset</title>
  <link href="../assets/img/favicon.png" rel="icon" />
  <link rel="stylesheet" href="../assets/CSS/startfile.css" />
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

  <!-- <script>
      document.getElementById("resetPasswordFormset").addEventListener("submit", function(event) {
        event.preventDefault(); 

        const formData = new FormData(this);

        fetch("../forms/PasswordReset.php", {
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
        }, 3000);
      }
    </script> -->

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      document.getElementById('resetPasswordFormset').addEventListener('submit', function(event) {
        event.preventDefault(); 
        const formData = new FormData(this);

        fetch('../forms/PasswordReset.php', {
            method: 'POST',
            body: formData
          })
          .then(response => response.json())
          .then(data => {
            const popup = document.getElementById('response-popup');
            if (popup) {
              const alertClass = data.success ? 'alert-success' : 'alert-danger';
              let message = '';

              if (data.success) {
                message = '<div class="alert ' + alertClass + '">' + data.message + '</div>';
                window.location.href = data.redirect;
               

              
                setTimeout(function() {
                  window.location.href = 'https://member.log.agl.or.ke/members/'; 
                  window.location.href = data.redirect;
                }, 3000); 
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

              setTimeout(function() {
                popup.classList.remove('show');
              }, 10000);
            }
          })
          .catch(error => console.error('Error fetching response:', error));
      });
    });
  </script>

  <!-- Main Content -->
  <main>
    <div class="container">
      <div class="image-side">
        <img class="logo" src="../assets/img/logo.png" alt="Logo" />
        <img
          class="main-image"
          src="../assets/img/DemoImage/LoginImage.jpg"
          alt="AGL" />
      </div>
      <div class="login-side">


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

        <form id="resetPasswordFormset" action="../forms/PasswordReset.php" method="POST">
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
            newPasswordInput.addEventListener('input', checkFormValidity);
            confirmPasswordInput.addEventListener('input', checkFormValidity);

            // Attach toggle visibility for password fields
            document.getElementById('NewPassWordResetToggle').addEventListener('click', () => togglePasswordVisibility('NewPassWordReset'));
            document.getElementById('NewPassWordConfirmToggle').addEventListener('click', () => togglePasswordVisibility('NewPassWordConfirm'));
          });
        </script>

        <!-- <script>
          document.getElementById("resetPasswordFormset").addEventListener("submit", function(event) {
            event.preventDefault();

            const formData = new FormData(this);

            fetch("forms/PasswordReset.php", {
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
            }, 3000);
          }
        </script> -->

        <!-- .......................................................................................................... -->

      </div>
    </div>

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