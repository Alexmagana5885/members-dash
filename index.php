<?php

// require_once __DIR__ . '/vendor/autoload.php';

// $dotenv = Dotenv\Dotenv::createImmutable(__DIR__); 
// $dotenv->load(); 

// $passkey = getenv('MPESA_PASSKEY');
// $BusinessShortCode = getenv('MPESA_BUSINESS_SHORTCODE');

?>



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
        <h2>Login</h2>
        <form id="loginForm">
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
          <!-- OTP input,  hidden -->
          <form class="input-group" id="otpForm" style="display: none;">
            <label for="OTP">Enter OTP</label>
            <input class="emailInput" type="text" id="otp" name="otp" placeholder="Enter OTP" required>
          </form>
          <!-- Button will change from 'Get OTP' to 'Verify OTP' dynamically -->
          <button type="submit" id="loginButton">Get OTP</button>
          <p class="register-link">
            Not registered?
            <a href="javascript:void(0)" onclick="showRegistration()">Register as a Member</a>
          </p>
        </form>
        <p id="message"></p>
      </div>

      <script>
        const loginForm = document.getElementById('loginForm');
        const otpForm = document.getElementById('otpForm');
        const loginButton = document.getElementById('loginButton');
        const messageElement = document.getElementById('message');

        loginForm.addEventListener('submit', function(event) {
          event.preventDefault(); // Prevent form from submitting normally

          if (otpForm.style.display === 'none') {
            // Step 1: First submit for credentials verification and OTP generation
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const membershipType = document.getElementById('MembershipType').value;

            fetch('verify_credentials.php', {
                method: 'POST',
                headers: {
                  'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                  email,
                  password,
                  membershipType
                })
              })
              .then(response => response.json())
              .then(data => {
                if (data.status === 'success') {
                  // Show OTP form and change button to "Verify OTP"
                  otpForm.style.display = 'block';
                  loginButton.textContent = 'Verify OTP';
                } else {
                  messageElement.textContent = data.message;
                  messageElement.style.color = 'red';
                }
              })
              .catch(error => {
                console.error('Error:', error);
                messageElement.textContent = 'An error occurred. Please try again.';
              });
          } else {
            // Step 2: Second submit for OTP verification
            const otp = document.getElementById('otp').value;

            fetch('check_otp.php', {
                method: 'POST',
                headers: {
                  'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                  otp
                })
              })
              .then(response => response.json())
              .then(data => {
                if (data.status === 'success') {
                  window.location.href = data.redirect; // Redirect if OTP is correct
                } else {
                  messageElement.textContent = data.message;
                  messageElement.style.color = 'red';
                }
              })
              .catch(error => {
                console.error('Error:', error);
                messageElement.textContent = 'An error occurred. Please try again.';
              });
          }
        });

        function togglePasswordVisibility() {
          const passwordInput = document.getElementById('password');
          if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
          } else {
            passwordInput.type = 'password';
          }
        }
      </script>

    </div>
    </div>

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
        event.preventDefault(); // Prevent default form submission

        const formData = new FormData(this);

        fetch("forms/startT.php", { // Update this path to where your PHP script is located
            method: "POST",
            body: formData,
          })
          .then(response => response.json())
          .then(data => {
            if (data.status === "error") {
              showPopup(data.message);
            } else if (data.status === "success") {
              window.location.href = data.redirect;
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