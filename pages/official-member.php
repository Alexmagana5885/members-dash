<?php
// Include your database connection file
include '../forms/DBconnection.php';

// Check if 'email' parameter is set in the query string
if (isset($_GET['email'])) {
    // Sanitize the input to prevent SQL injection
    $email = $conn->real_escape_string($_GET['email']);

    // Query to fetch all columns for the specific member from both tables
    $sql = "SELECT p.*, o.position AS official_position, o.start_date AS official_start_date, o.number_of_terms
            FROM personalmembership p
            JOIN officialsmembers o ON p.email = o.personalmembership_email
            WHERE p.email = '$email'";

    $result = $conn->query($sql);

    if (!$result) {
        die("Query failed: " . $conn->error);
    }

    // Check if data is found
    if ($result->num_rows > 0) {
        // Fetch data
        $row = $result->fetch_assoc();
    } else {
        echo "No details found for this member.";
        exit;
    }
} else {
    echo "Invalid request.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Member Details</title>
    <link href="../assets/img/favicon.png" rel="icon" />
    <link href="../assets/img/favicon.png" rel="favicon.png" />
    <link rel="stylesheet" href="styles.css"> <!-- Link your CSS file -->
</head>
<body>

<style>
    /* Styles for the member details table */
.member-details {
    max-width: 800px;
    margin: 20px auto;
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

.member-details h2 {
    text-align: center;
    margin-bottom: 20px;
}

.member-details table {
    width: 100%;
    border-collapse: collapse;
}

.member-details th, .member-details td {
    padding: 10px;
    text-align: left;
}

.member-details th {
    background-color: #f4f4f4;
    font-weight: bold;
    width: 30%;
}

.member-details td {
    background-color: #fff;
    border-bottom: 1px solid #ddd;
    width: 70%;
}

/* Adjustments for the layout */
.member-details table {
    display: table;
    width: 100%;
}

.member-details tbody {
    display: block;
    width: 100%;
}

.member-details thead {
    display: none; /* Hide the thead as we're creating a vertical layout */
}

.member-details tr {
    display: flex;
    flex-direction: column;
    margin-bottom: 10px;
}

.member-details tr:nth-child(even) {
    background-color: #f9f9f9;
}

.member-details td {
    display: flex;
    flex-direction: row;
    justify-content: space-between;
}

.member-details td:before {
    content: attr(data-label);
    font-weight: bold;
    margin-right: 10px;
    flex: 1;
    color: #333;
}

</style>

<div class="member-details">
    <h2>Official Member Full Details</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Date of Birth</th>
                <th>Home Address</th>
                <th>Passport Image</th>
                <th>Highest Degree</th>
                <th>Institution</th>
                <th>Start Date</th>
                <th>Graduation Year</th>
                <th>Completion Letter</th>
                <th>Profession</th>
                <th>Experience</th>
                <th>Current Company</th>
                <th>Position</th>
                <th>Work Address</th>
                <th>Payment Method</th>
                <th>Payment Code</th>
                <th>Password</th>
                <th>Registration Date</th>
                <th>Official Position</th>
                <th>Official Start Date</th>
                <th>Number of Terms</th>
            </tr>
        </thead>
        <tbody>
    <tr>
        <td data-label="ID"><?php echo htmlspecialchars($row['id']); ?></td>
        <td data-label="Name"><?php echo htmlspecialchars($row['name']); ?></td>
        <td data-label="Email"><?php echo htmlspecialchars($row['email']); ?></td>
        <td data-label="Phone"><?php echo htmlspecialchars($row['phone']); ?></td>
        <td data-label="Date of Birth"><?php echo htmlspecialchars($row['dob']); ?></td>
        <td data-label="Home Address"><?php echo htmlspecialchars($row['home_address']); ?></td>
        <td data-label="Passport Image">
            <a href="<?php echo htmlspecialchars($row['passport_image']); ?>" target="_blank">
                <img src="<?php echo htmlspecialchars($row['passport_image']); ?>" alt="Passport Image" style="width: 100px; height: auto;">
            </a>
        </td>
        <td data-label="Highest Degree"><?php echo htmlspecialchars($row['highest_degree']); ?></td>
        <td data-label="Institution"><?php echo htmlspecialchars($row['institution']); ?></td>
        <td data-label="Start Date"><?php echo htmlspecialchars($row['start_date']); ?></td>
        <td data-label="Graduation Year"><?php echo htmlspecialchars($row['graduation_year']); ?></td>
        <td data-label="Completion Letter">
            <a href="<?php echo htmlspecialchars($row['completion_letter']); ?>" target="_blank">
                View Completion Letter
            </a>
        </td>
        <td data-label="Profession"><?php echo htmlspecialchars($row['profession']); ?></td>
        <td data-label="Experience"><?php echo htmlspecialchars($row['experience']); ?></td>
        <td data-label="Current Company"><?php echo htmlspecialchars($row['current_company']); ?></td>
        <td data-label="Position"><?php echo htmlspecialchars($row['position']); ?></td>
        <td data-label="Work Address"><?php echo htmlspecialchars($row['work_address']); ?></td>
        <td data-label="Payment Method"><?php echo htmlspecialchars($row['payment_method']); ?></td>
        <td data-label="Payment Code"><?php echo htmlspecialchars($row['payment_code']); ?></td>
        <td data-label="Password"><?php echo htmlspecialchars($row['password']); ?></td>
        <td data-label="Registration Date"><?php echo htmlspecialchars($row['registration_date']); ?></td>
        <td data-label="Official Position"><?php echo htmlspecialchars($row['official_position']); ?></td>
        <td data-label="Official Start Date"><?php echo htmlspecialchars($row['official_start_date']); ?></td>
        <td data-label="Number of Terms"><?php echo htmlspecialchars($row['number_of_terms']); ?></td>
    </tr>
</tbody>

    </table>
</div>

</body>
</html>
