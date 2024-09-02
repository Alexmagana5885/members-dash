<?php
require_once('../forms/DBconnection.php');

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<style>
    .section {
        display: none;
    }

    .button-container {
        margin-bottom: 20px;
    }

    .button-container button {
        margin-right: 10px;
        padding: 10px 20px;
        font-size: 16px;
    }

    .button-container {
            margin-bottom: 20px;
            text-align: center;
        }

        .button-container button {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .button-container button:hover {
            background-color: #0056b3;
        }

        .members-container {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .section {
            display: none;
            width: 100%;
            max-width: 1200px;
            background-color: #fff;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .popup-table {
            /* display: none; */
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 80%;
            max-width: 1000px;
            background: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
            z-index: 1000;
            padding: 20px;
            border-radius: 8px;
        }

        .popup-content-table {
            position: relative;
        }

        .close-btn-table {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 24px;
            cursor: pointer;
            color: #333;
        }

        .popup-content-table h3 {
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #007bff;
            color: #fff;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        a {
            color: #007bff;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .button-container {
                display: flex;
                flex-direction: column;
            }

            .button-container button {
                margin-bottom: 10px;
            }
        }
</style>

<body>
    <div class="button-container">
        <button id="show-individual">Individual Members</button>
        <button id="show-officials">Official Members</button>
        <button id="show-organisation">Organisation Members</button>
    </div>

    <div class="members-container">
        <div id="individualmembers" class="section">
            <?php

            // Query to select data from the personalmembership table
            $sql = 'SELECT * FROM personalmembership';
            $result = $conn->query($sql);

            if (!$result) {
                die("Query failed: " . $conn->error);
            }
            ?>


            <!-- Popup container for the table members -->
            <div id="MemberDISTablePopup-table" class="popup-table">
                <div class="popup-content-table">
                    <span class="close-btn-table">&times;</span>
                    <div style="margin-top: 20px;" class="MinPrtSecSpace-table">
                        <h3>Members Information</h3><br>
                        <div class="card_table-table">
                            <table>
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Phone Number</th>
                                        <th>Email</th>
                                        <th>User Position</th>
                                        <th>Current Work Place</th>
                                        <th>Full Details</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($row['id']); ?></td>
                                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                                            <td><?php echo htmlspecialchars($row['phone']); ?></td>
                                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                                            <td><?php echo htmlspecialchars($row['position']); ?></td>
                                            <td><?php echo htmlspecialchars($row['current_company']); ?></td>
                                            <td><a href="member_details.php?email=<?php echo urlencode($row['email']); ?>">Show
                                                    More</a></td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="officialsmembers" class="section">
            <?php

            // Query to select data from personalmembership joined with officialsmembers
            $sql = '
    SELECT p.id, p.name, p.phone, p.email, p.position, p.current_company, o.position AS official_position, o.start_date, o.number_of_terms
    FROM personalmembership p
    JOIN officialsmembers o ON p.email = o.personalmembership_email
';
            $result = $conn->query($sql);

            if (!$result) {
                die("Query failed: " . $conn->error);
            }
            ?>

            <!-- Popup container for the table officials -->
            <div id="OfficialMembersTablePopup-table" class="popup-table">
                <div class="popup-content-table">
                    <span class="close-btn-table">&times;</span>
                    <div style="margin-top: 20px;" class="MinPrtSecSpace-table">
                        <h3>Official Members Information</h3><br>
                        <div class="card_table-table">
                            <table>
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Phone Number</th>
                                        <th>Email</th>
                                        <th>User Position</th>
                                        <th>Current Work Place</th>
                                        <th>Official Position</th>
                                        <th>Start Date</th>
                                        <th>Number of Terms</th>
                                        <th>Full Details</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($row['id']); ?></td>
                                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                                            <td><?php echo htmlspecialchars($row['phone']); ?></td>
                                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                                            <td><?php echo htmlspecialchars($row['position']); ?></td>
                                            <td><?php echo htmlspecialchars($row['current_company']); ?></td>
                                            <td><?php echo htmlspecialchars($row['official_position']); ?></td>
                                            <td><?php echo htmlspecialchars($row['start_date']); ?></td>
                                            <td><?php echo htmlspecialchars($row['number_of_terms']); ?></td>
                                            <td><a href="member_details.php?email=<?php echo urlencode($row['email']); ?>">Show
                                                    More</a></td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div id="organisationmembers" class="section">
            <?php

            // Query to select data from the organizationmembership table
            $sql = 'SELECT * FROM organizationmembership';
            $result = $conn->query($sql);

            if (!$result) {
                die("Query failed: " . $conn->error);
            }
            ?>

            <!-- Popup container for the table organizations -->
            <div id="OrganizationMembershipTablePopup-table" class="popup-table">
                <div class="popup-content-table">
                    <span class="close-btn-table">&times;</span>
                    <div style="margin-top: 20px;" class="MinPrtSecSpace-table">
                        <h3>Organization Membership Information</h3><br>
                        <div class="card_table-table">
                            <table>
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Organization Name</th>
                                        <th>Email</th>
                                        <th>Contact Person</th>
                                        <th>Contact Phone Number</th>
                                        <th>Town</th>
                                        <th>Full Details</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($row['id']); ?></td>
                                            <td><?php echo htmlspecialchars($row['organization_name']); ?></td>
                                            <td><?php echo htmlspecialchars($row['organization_email']); ?></td>
                                            <td><?php echo htmlspecialchars($row['contact_person']); ?></td>
                                            <td><?php echo htmlspecialchars($row['contact_phone_number']); ?></td>
                                            <td><?php echo htmlspecialchars($row['location_town']); ?></td>
                                            <td><a
                                                    href="organization_details.php?email=<?php echo urlencode($row['organization_email']); ?>">Show
                                                    More</a></td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>

    <script>
        function showSection(sectionId) {
            // Hide all sections
            const sections = document.querySelectorAll('.section');
            sections.forEach(section => {
                section.style.display = 'none';
            });

            // Show the selected section
            const selectedSection = document.getElementById(sectionId);
            if (selectedSection) {
                selectedSection.style.display = 'block';
            }
        }

        // Add event listeners for the buttons
        document.getElementById('show-individual').addEventListener('click', () => {
            showSection('individualmembers');
        });
        document.getElementById('show-officials').addEventListener('click', () => {
            showSection('officialsmembers');
        });
        document.getElementById('show-organisation').addEventListener('click', () => {
            showSection('organisationmembers');
        });

        // Optionally, display the first section by default
        document.addEventListener('DOMContentLoaded', () => {
            showSection('individualmembers');
        });

    </script>

</body>

</html>