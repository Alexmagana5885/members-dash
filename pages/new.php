<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Registration Form</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file -->
</head>
<style>
    .popup-form {
        display: none;
        /* Hidden by default */
        position: fixed;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        justify-content: center;
        align-items: center;
        text-align: center;
    }

    .form-container {
        background-color: white;
        padding: 20px;
        border-radius: 8px;
        width: 300px;
        max-width: 80%;
    }

    label {
        display: block;
        margin-top: 10px;
        font-weight: bold;
    }

    input {
        width: 100%;
        padding: 8px;
        margin-top: 5px;
        margin-bottom: 15px;
        border-radius: 5px;
        border: 1px solid #ccc;
    }

    button[type="submit"] {
        padding: 10px;
        background-color: #4CAF50;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    button[type="button"] {
        padding: 10px;
        background-color: #f44336;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        margin-left: 10px;
    }
</style>

<body>
    <!-- Button to open the popup form -->
    <button id="registerBtnEventRegistration">Register</button>

    <!-- Popup form -->
    <div id="popupFormEventRegistration" class="popup-form">
        <div class="form-container">
            <h4>Event Name</h4>
            <form>
                <label for="memberEmail">Member Email:</label>
                <input type="email" id="memberEmail" name="memberEmail" required>

                <label for="memberName">Name:</label>
                <input type="text" id="memberName" name="memberName" required>

                <label for="contact">Contact:</label>
                <input type="text" id="contact" name="contact" required>

                <button type="submit">Submit</button>
                <button type="button" id="closeBtn">Close</button>
            </form>
        </div>
    </div>

    <script>document.addEventListener('DOMContentLoaded', function () {
            var registerBtnEventRegistration = document.getElementById('registerBtnEventRegistration');
            var popupFormEventRegistration = document.getElementById('popupFormEventRegistration');
            var closeBtn = document.getElementById('closeBtn');

            registerBtnEventRegistration.addEventListener('click', function () {
                popupFormEventRegistration.style.display = 'flex';
            });

            closeBtn.addEventListener('click', function () {
                popupFormEventRegistration.style.display = 'none';
            });

            window.addEventListener('click', function (event) {
                if (event.target == popupFormEventRegistration) {
                    popupFormEventRegistration.style.display = 'none';
                }
            });
        });
    </script>

    <script src="script.js"></script> <!-- Link to your JS file -->
</body>

</html>