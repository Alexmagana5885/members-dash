<?php
require_once('../forms/DBconnection.php');
session_start();

// Get the member's email from the URL
$email = isset($_GET['email']) ? $conn->real_escape_string($_GET['email']) : '';

// Query to get the member's details
$sql = "SELECT * FROM personalmembership WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();

// Check if member exists
if ($result->num_rows === 0) {
    die("No member found with the provided email.");
}

$member = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member Details</title>
    <link href="../assets/img/favicon.png" rel="icon">
    <link href="../assets/img/favicon.png" rel="favicon.png">


    <style>
        /* Base styles */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #dae6f5;
            padding: 8px;
        }

        th {
            background-color: #7caceb;
        }

        .container {
            margin: 20px;
        }

        .popup {
            display: none;
            position: fixed;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }

        .popup-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 600px;
            border-radius: 5px;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin: 10px 0 5px;
        }

        input[type="text"],
        input[type="date"],
        input[type="number"] {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .button-container {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 15px;
            margin-bottom: 20px;
        }

        button {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button[type="submit"] {
            background-color: #2b98ed;
            color: white;
        }

        button[type="submit"]:hover {
            background-color: #45a049;
        }

        button[type="button"] {
            background-color: #86b0d1;
            color: white;
        }

        button[type="button"]:hover {
            background-color: #e53935;
        }

        /* Styles for small screens */
        @media (max-width: 768px) {
            .popup-content {
                width: 90%;
                margin: 10% auto;
            }

            form {
                flex-direction: column;
            }

            .button-container {
                flex-direction: column;
                align-items: stretch;
            }

            .button-container button {
                width: 80%;
                margin-bottom: 10px;
            }

            table {
                font-size: 14px;
            }
        }

        @media (max-width: 480px) {
            .popup-content {
                width: 95%;
                margin: 5% auto;
            }

            table {
                font-size: 12px;
            }

            label {
                font-size: 14px;
            }

            input[type="text"],
            input[type="date"],
            input[type="number"] {
                font-size: 14px;
            }

            button {
                font-size: 14px;
                padding: 8px 16px;
            }
        }


        /* Styles for small screens */
        @media (max-width: 768px) {
            .popup-content {
                width: 90%;
                margin: 10% auto;
            }

            form {
                flex-direction: column;
            }

            .button-container {
                flex-direction: column;
                align-items: stretch;
            }

            .button-container button {
                width: 80%;
                margin-bottom: 10px;
            }

            table {
                font-size: 14px;
            }
        }

        @media (max-width: 480px) {
            .popup-content {
                width: 85%;
                margin: 0 auto;
            }

            table {
                font-size: 12px;
            }

            label {
                font-size: 14px;
            }

            input[type="text"],
            input[type="date"],
            input[type="number"] {
                font-size: 14px;
            }

            button {
                font-size: 14px;
                padding: 8px 16px;
            }
        }
    </style>


</head>

<body>
    <div class="container">
        <h1>Member Details</h1> <button style="margin: 15px; background-color: #2b98ed; color: white; font-size: 15px; " onclick="openPopup()">Make Official</button>
        <button style="margin: 15px; background-color: #2b98ed; color: white; font-size: 15px;"
            onclick="window.location.href='../forms/generateMemberD_pdf.php?email=<?php echo htmlspecialchars($member['email']); ?>'">
            Print Member Details
        </button>

        <table>
            <tr>
                <th>ID</th>
                <td><?php echo htmlspecialchars($member['id']); ?></td>
            </tr>
            <tr>
                <th>Name</th>
                <td><?php echo htmlspecialchars($member['name']); ?></td>
            </tr>
            <tr>
                <th>Phone Number</th>
                <td><?php echo htmlspecialchars($member['phone']); ?></td>
            </tr>
            <tr>
                <th>Email</th>
                <td><?php echo htmlspecialchars($member['email']); ?></td>
            </tr>
            <tr>
                <th>Gender</th>
                <td><?php echo htmlspecialchars($member['gender']); ?></td>
            </tr>
            <tr>
                <th>Home Address</th>
                <td><?php echo htmlspecialchars($member['home_address']); ?></td>
            </tr>
            <tr>
                <th>Passport Image</th>
                <td><?php echo htmlspecialchars($member['passport_image']); ?></td>
            </tr>
            <tr>
                <th>Highest Degree</th>
                <td><?php echo htmlspecialchars($member['highest_degree']); ?></td>
            </tr>
            <tr>
                <th>Institution</th>
                <td><?php echo htmlspecialchars($member['institution']); ?></td>
            </tr>

            <tr>
                <th>Graduation Year</th>
                <td><?php echo htmlspecialchars($member['graduation_year']); ?></td>
            </tr>
            <tr>
                <th>Completion Letter</th>
                <td><?php echo htmlspecialchars($member['completion_letter']); ?></td>
            </tr>
            <tr>
                <th>Profession</th>
                <td><?php echo htmlspecialchars($member['profession']); ?></td>
            </tr>
            <tr>
                <th>Experience</th>
                <td><?php echo htmlspecialchars($member['experience']); ?></td>
            </tr>
            <tr>
                <th>Current Company</th>
                <td><?php echo htmlspecialchars($member['current_company']); ?></td>
            </tr>
            <tr>
                <th>Position</th>
                <td><?php echo htmlspecialchars($member['position']); ?></td>
            </tr>
            <tr>
                <th>Work Address</th>
                <td><?php echo htmlspecialchars($member['work_address']); ?></td>
            </tr>
            <tr>
                <th>Payment Number</th>
                <td><?php echo htmlspecialchars($member['payment_Number']); ?></td>
            </tr>
            <tr>
                <th>Payment Code</th>
                <td><?php echo htmlspecialchars($member['payment_code']); ?></td>
            </tr>
            <tr>
                <th>Password</th>
                <td><?php echo htmlspecialchars($member['password']); ?></td>
            </tr>
            <tr>
                <th>Registration Date</th>
                <td><?php echo htmlspecialchars($member['registration_date']); ?></td>
            </tr>
        </table>


        <script>
            function openPopup() {
                document.getElementById('popup').style.display = 'flex';
            }

            function closePopup() {
                document.getElementById('popup').style.display = 'none';
            }
        </script>


        <div id="popup" class="popup">
            <div class="popup-content">
                <h2>Make Member Official</h2>
                <form action="../forms/make_official.php" method="post">
                    <input type="hidden" name="email" value="<?php echo htmlspecialchars($member['email']); ?>">
                    <label for="position">Position:</label>
                    <input type="text" id="position" name="position" required>
                    <label for="start_date">Start Date:</label>
                    <input type="date" id="start_date" name="start_date" required>
                    <label for="number_of_terms">Number of Terms:</label>
                    <input type="number" id="number_of_terms" name="number_of_terms" min="0" required>
                    <div class="button-container">
                        <button type="submit">Submit</button>
                        <button type="button" onclick="closePopup()">Cancel</button>
                    </div>
                </form>
            </div>
        </div>


    </div>
</body>

</html>

<?php
// Close the database connection
$conn->close();
?>