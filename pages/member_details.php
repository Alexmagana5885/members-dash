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
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
        }

        th {
            background-color: #f2f2f2;
        }

        .container {
            margin: 20px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Member Details</h1>
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
                <th>Date of Birth</th>
                <td><?php echo htmlspecialchars($member['dob']); ?></td>
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
                <th>Start Date</th>
                <td><?php echo htmlspecialchars($member['start_date']); ?></td>
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
                <th>Payment Method</th>
                <td><?php echo htmlspecialchars($member['payment_method']); ?></td>
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
    </div>
</body>

</html>

<?php
// Close the database connection
$conn->close();
?>