<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="assets/CSS/Main.css">
    <link rel="icon" href="../assets/img/favicon.png" type="image/png">
    <style>
        /* Existing styles... */
        body {
            font-family: Arial, sans-serif;
            justify-content: center;
            align-items: center;
            height: 100%;
            margin: 5px;
            background-color: #f4f4f4;
            display: flex;
            flex-direction: column;
        }

        .container {
            display: flex;
            width: 70%;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        .image-side {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background-color: #f0f0f0;
            padding: 20px;
            box-sizing: border-box;
        }

        .logo {
            max-width: 80%;
            height: auto;
            margin-bottom: 20px;
        }

        .main-image {
            max-width: 100%;
            height: auto;
            object-fit: cover;
            border-radius: 5px;
        }

        .login-side {
            flex: 1;
            padding: 40px;
            box-sizing: border-box;
        }

        .login-side h2 {
            margin-bottom: 20px;
            font-size: 24px;
            color: #333;
        }

        .input-group {
            margin-bottom: 20px;
        }

        .input-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }

        .password-container {
            position: relative;
        }

        .password-container input,
        .emailInput {
            width: 100%;
            padding: 10px;
            padding-right: 40px;
            border: 0;
            border-radius: 5px;
            font-size: 16px;
            box-sizing: border-box;
            box-shadow: 2px 2px 2px 2px rgba(0, 0, 0, 0.2);
        }

        .toggle-password {
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
            cursor: pointer;
            font-size: 18px;
            user-select: none;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #007BFF;
            border: none;
            border-radius: 5px;
            color: white;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #0056b3;
        }

        .register-link {
            text-align: center;
            margin-top: 10px;
            color: #555;
        }

        .register-link a {
            color: #007BFF;
            text-decoration: none;
            cursor: pointer;
        }

        .register-link a:hover {
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .container {
                flex-direction: column;
                width: 90%;
            }

            .image-side {
                order: 1;
                padding: 20px 0;
            }

            .login-side {
                order: 2;
                padding: 20px;
            }

            .logo {
                max-width: 50%;
            }
        }

        .form-stepINdiv {
            display: none;
            justify-content: space-between;
            align-items: flex-start;
            gap: 10px;
            padding: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            flex-wrap: wrap;
            margin: 0 auto;
            width: 70%;
            bottom: 20px;
            border-radius: 5px;
        }

        .stepINdivdiv {
            flex: 2;
            max-width: 60%;
        }

        .membership-options {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 10px;
            align-items: flex-start;
        }

        @media (max-width: 768px) {
            .form-stepINdiv {
                flex-direction: column;
                align-items: center;
                gap: 10px;
                margin-top: 20px;
                width: 90%;
            }

            .stepINdivdiv {
                max-width: 100%;
                flex: none;
                text-align: center;
                overflow: auto;
                scrollbar-width: thin;
            }

            .membership-options {
                width: 100%;
                flex: none;
                align-items: center;
            }

            .membership-options button {
                width: 90%;
                max-width: 300px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="image-side">
            <img class="logo" src="assets/img/logo.png" alt="Logo">
            <img class="main-image" src="assets/img/DemoImage/LoginImage.jpg" alt="AGL">
        </div>
        <div class="login-side">
            <h2>Login</h2>
            <form action="your-login-script.php" method="post">
                <div class="input-group">
                    <label for="email">Email</label>
                    <input class="emailInput" type="email" id="email" name="email" required>
                </div>
                <div class="input-group">
                    <label for="password">Password</label>
                    <div class="password-container">
                        <input type="password" id="password" name="password" required>
                        <span class="toggle-password" onclick="togglePasswordVisibility()">👁️</span>
                    </div>
                </div>
                <button type="submit">Login</button>
                <p class="register-link">Not registered? <a href="javascript:void(0)"
                        onclick="showRegistration()">Register as a Member</a></p>
            </form>
        </div>
    </div>

    <!-- Registration Information Section (Initially Hidden) -->
    <div class="form-step form-stepINdiv">
        <!-- General Information Step -->
        <div class="stepINdivdiv">
            <h3>Guidelines for Membership Registration</h3>
            <p>To be a member of AGL, you need to meet the following criteria:</p>
            <ul>
                <li>Have completed a Diploma, Degree, or Masters in Library and Information Science from a recognized
                    institution.</li>
                <li>Fill in the prescribed form below.</li>
                <li>Pay a registration fee of Kshs. 2,000 and an annual fee of Kshs. 3,600.</li>
                <li>There are two types of membership:</li>
                <ul>
                    <li><strong>Individual Membership:</strong> Pay Kshs. 2,000 once.</li>
                    <li><strong>Institutional Membership:</strong> An institution pays a fee of Kshs. 15,000 annually.
                    </li>
                </ul>
            </ul>
            <p>Choose the membership type and follow the instructions to complete your registration.</p>
        </div>

        <div class="membership-options">
        <button type="button" class="continue-individual" onclick="redirectTo('pages/Registration.php')">Individual Membership</button>
            <button type="button" class="continue-institutional" onclick="redirectTo('institutional-registration.html')">Institutional Membership</button>
        
        </div>
    </div>

    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const toggleButton = document.querySelector('.toggle-password');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleButton.textContent = '🙈'; // Hide icon
            } else {
                passwordInput.type = 'password';
                toggleButton.textContent = '👁️'; // Show icon
            }
        }

        function showRegistration() {
            const registrationSection = document.querySelector('.form-stepINdiv');
            registrationSection.style.display = 'flex'; 
            registrationSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }

        function redirectTo(url) {
            window.location.href = url;
        }

    </script>
</body>

</html>