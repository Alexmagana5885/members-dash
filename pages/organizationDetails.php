<?php
// Database connection
include '../forms/DBconnection.php'; // Ensure you include your database connection file

// Get the email parameter from the URL
$email = isset($_GET['email']) ? $_GET['email'] : '';

if (empty($email)) {
    die('Email parameter missing.');
}

// Query to select data from the organizationmembership table based on the email
$sql = 'SELECT * FROM organizationmembership WHERE organization_email = ?';
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die('No organization found with the given email.');
}

$row = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../assets/img/favicon.png" rel="icon" />
    <link href="../assets/img/favicon.png" rel="favicon.png" />
    <title>Organization Details</title>
    <link rel="stylesheet" href="styles.css"> <!-- Include your CSS file here -->
</head>

<style>
    table {
        width: 50%;
        border-collapse: collapse;
        margin: 20px auto;
        border: 1px solid #007bff;
    }

    th {
        text-align: left;
        padding: 10px;
        background-color: #99ccff;
        color: black;
        border-bottom: 2px solid #0056b3;
    }

    td {
        text-align: right;
        padding: 10px;
        border-bottom: 1px solid #007bff;
    }

    tbody tr:nth-child(even) {
        background-color: #e0f0ff;
    }

    .table-container {
        max-width: 1000px;
        margin: auto;
        overflow-x: auto;
    }

    td img {
        max-width: 200px;
        height: auto;
    }

    td a {
        color: #0056b3;
        text-decoration: none;
    }

    td a:hover {
        text-decoration: underline;
        color: #003d7a;
    }

    @media (max-width: 768px) {
        table {
            width: 90%;
        }

        th,
        td {
            padding: 8px;
        }

        th {
            font-size: 14px;
        }

        td {
            font-size: 12px;
        }
    }

    .organization-details {
        text-align: center;
        margin: 20px;
    }

    h2 {
        font-size: 24px;
        color: #0056b3;
        margin-bottom: 20px;
    }

    .DownloadButton {
        text-decoration: none;
    }

    .print-button {
        padding: 10px 20px;
        background-color: #007bff;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
        transition: background-color 0.3s;
    }

    .print-button:hover {
        background-color: #0056b3;
    }
</style>

<body>
    <div class="container">
        <div class="organization-details">
            <h2>Organization Details</h2>
            <a id="organizationPrintBTN" class="DownloadButton" href="../forms/Singleorganizationgenerate_pdf.php?email=<?php echo urlencode($row['organization_email']); ?>" target="_blank">
                <button class="print-button">Print Information PDF</button>
            </a>

        </div>


        <table>
            <tr>
                <th>ID</th>
                <td><?php echo htmlspecialchars($row['id']); ?></td>
            </tr>
            <tr>
                <th>Organization Name</th>
                <td><?php echo htmlspecialchars($row['organization_name']); ?></td>
            </tr>
            <tr>
                <th>Email</th>
                <td><?php echo htmlspecialchars($row['organization_email']); ?></td>
            </tr>
            <tr>
                <th>Contact Person</th>
                <td><?php echo htmlspecialchars($row['contact_person']); ?></td>
            </tr>
            <tr>
                <th>Contact Phone Number</th>
                <td><?php echo htmlspecialchars($row['contact_phone_number']); ?></td>
            </tr>
            <tr>
                <th>Address</th>
                <td><?php echo htmlspecialchars($row['organization_address']); ?></td>
            </tr>
            <tr>
                <th>Country</th>
                <td><?php echo htmlspecialchars($row['location_country']); ?></td>
            </tr>
            <tr>
                <th>County</th>
                <td><?php echo htmlspecialchars($row['location_county']); ?></td>
            </tr>
            <tr>
                <th>Town</th>
                <td><?php echo htmlspecialchars($row['location_town']); ?></td>
            </tr>
            <tr>
                <th>Logo</th>
                <td>
                    <?php if (!empty($row['logo_image'])): ?>
                        <img src="<?php echo htmlspecialchars($row['logo_image']); ?>" alt="Logo" style="max-width: 200px;">
                    <?php else: ?>
                        No logo available
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <th>Registration Certificate</th>
                <td>
                    <?php if (!empty($row['registration_certificate'])): ?>
                        <a href="<?php echo htmlspecialchars($row['registration_certificate']); ?>" target="_blank">View Certificate</a>
                    <?php else: ?>
                        No certificate available
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <th>What You Do</th>
                <td><?php echo htmlspecialchars($row['what_you_do']); ?></td>
            </tr>
            <tr>
                <th>Number of Employees</th>
                <td><?php echo htmlspecialchars($row['number_of_employees']); ?></td>
            </tr>
            <tr>
                <th>Organization Type</th>
                <td><?php echo htmlspecialchars($row['organization_type']); ?></td>
            </tr>
            <tr>
                <th>Date of Registration</th>
                <td><?php echo htmlspecialchars($row['date_of_registration']); ?></td>
            </tr>
            <tr>
                <th>Start Date</th>
                <td><?php echo htmlspecialchars($row['start_date']); ?></td>
            </tr>
        </table>
        <a href="javascript:history.back()">Go Back</a>
    </div>
</body>

</html>

<?php
$stmt->close();
$conn->close();
?>