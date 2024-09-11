<?php
require_once('../forms/DBconnection.php');

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../assets/img/favicon.png" rel="icon" />
    <link href="../assets/img/favicon.png" rel="favicon.png" />
    <link rel="stylesheet" href="../assets/CSS/Members.css">
    <title>Members</title>
</head>



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
                <li><a href="javascript:history.back()">Back</a></li>
                <!-- <li><a href="#services">Services</a></li>
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
            $sql = 'SELECT p.id, p.name, p.phone, p.email, p.position, p.current_company, o.position AS official_position, o.start_date, o.number_of_terms  FROM personalmembership p JOIN officialsmembers o ON p.email = o.personalmembership_email';
            $result = $conn->query($sql);

            if (!$result) {
                die("Query failed: " . $conn->error);
            }
            ?>

            <!-- Popup container for the table officials -->
            <div id="OfficialMembersTablePopup-table" class="popup-table">
                <div class="popup-content-table">
                    
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
                                            <td><a href="official-member.php?email=<?php echo urlencode($row['email']); ?>">Show
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
                                                    href="organizationDetails.php?email=<?php echo urlencode($row['organization_email']); ?>">Show
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