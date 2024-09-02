<?php
require_once('../forms/DBconnection.php');

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="assets/img/favicon.png" rel="icon" />
    <link href="assets/img/favicon.png" rel="favicon.png" />
    <title>Members</title>
</head>
<style>
    /* Header Styling */
    .site-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem;
        background-color: #6ec1e4;
        color: #fff;
        position: relative;
    }

    .site-header .logo img {
        max-width: 150px;
        height: auto;
        padding: 0;
    }

    .navigation {
        display: flex;
        gap: 1rem;
    }

    .navigation ul {
        list-style: none;
        display: flex;
        gap: 1rem;
        margin: 0;
        padding: 0;
    }

    .navigation a {
        color: #fff;
        text-decoration: none;
    }

    .navigation a:hover {
        text-decoration: underline;
    }

    .menu-toggle {
        display: none;
        background: none;
        border: none;
        color: #fff;
        font-size: 2rem;
        cursor: pointer;
    }

    /* Footer Styling */
    .site-footer {
        background-color: #f2f2f2;
        color: #333;
        text-align: center;
        padding: 1rem;
        width: 100%;
        font-size: 11px;
        font-style: italic;
        position: relative;
        bottom: 0;
    }

    /* Responsive Styles */
    @media (max-width: 768px) {

        .site-footer{}

        .navigation {
            display: none;
            flex-direction: column;
            position: absolute;
            top: 100%;
            right: 0;
            background-color: #6ed1e5;
            width: 200px;
            padding: 1rem;
        }

        .navigation.active {
            display: flex;
            flex-direction: column;
        }

        .menu-toggle {
            display: block;
        }
    }



    .section {
        display: none;
    }

    .button-container {
        margin-bottom: 20px;
        padding: 20px;
        margin-top: 20px;
        text-align: center;


    }

    .button-container button {
        margin-right: 15px;
        padding: 10px 20px;
        font-size: 16px;
        border-radius: 5px;
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
        width: 100%;
        background-color: #fff;
        padding: 5px;
        margin-bottom: 20px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);

    }

    .popup-table {
        position: relative;

        width: 90%;
        background: #fff;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        z-index: 1000;
        padding: 15px;
        border-radius: 5px;
        overflow: auto;
        scrollbar-width: thin;
        margin: 0 auto;
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

    th,
    td {
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

    @media (max-width: 768px) {
        .button-container {
            display: flex;
            flex-direction: column;

        }

        .button-container button {
            margin-bottom: 10px;
        }
    }

    @media (max-width: 768px) {
        .button-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 10px;
            margin-top: 10px;
            margin-bottom: 10px;
        }

        .button-container button {
            margin-bottom: 10px;
            margin-right: 0;
            width: 100%;
            font-size: 14px;
            padding: 8px 15px;
        }

        .members-container {
            padding: 10px;
        }

        .section {
            padding: 10px;
            margin-bottom: 15px;
            box-shadow: none;
        }

        .popup-table {
            width: 95%;
            max-height: 80vh;
            padding: 10px;
            overflow-x: auto;
        }

        .popup-content-table {
            padding: 5px;
        }

        .close-btn-table {
            font-size: 20px;
        }


        .table-responsive {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        table {
            width: 100%;
            font-size: 12px;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        a {
            font-size: 14px;
        }
    }
</style>

<body>

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
                <li><a href="#http://www.agl.or.ke/">Home</a></li>
                <!-- <li><a href="#about">About</a></li>
                <li><a href="#services">Services</a></li>
                <li><a href="#contact">Contact</a></li> -->
            </ul>
        </nav>
    </header>

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

<script>
      const menuToggle = document.getElementById("menu-toggle");
      const navigation = document.getElementById("navigation");

      menuToggle.addEventListener("click", () => {
        navigation.classList.toggle("active");
      });
    </script>

    <footer class="site-footer">
        <p>
            &copy; 2024
            <a style="text-decoration: none" href="http://www.agl.or.ke/">AGL.or.ke</a> . All
            rights reserved.
        </p>
    </footer>

</body>

</html>