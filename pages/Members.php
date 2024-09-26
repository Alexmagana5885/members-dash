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
        <!-- <a class="DownloadButton" href="../forms/generate_pdf.php" target="_blank"><button>Print Members PDF</button></a><br><br> -->
                            
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
            <style>
                .headMembersPart {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    flex-wrap: wrap;
                    padding: 20px;
                    background-color: #f0f0f0;
                    border-radius: 10px;
                    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                }

                .headMembersPartbtns {
                    display: flex;
                    flex-direction: column;
                    justify-content: center;
                    align-items: flex-start;
                }

                .headMembersPartbtns h3 {
                    font-size: 24px;
                    margin-bottom: 10px;
                    color: #333;
                }

                .DownloadButton button {
                    padding: 10px 20px;
                    background-color: #007bff;
                    color: white;
                    border: none;
                    border-radius: 5px;
                    cursor: pointer;
                    transition: background-color 0.3s ease;
                }

                .DownloadButton button:hover {
                    background-color: #45a049;
                }

                .headMembersPartSearchp {
                    display: flex;
                    justify-content: flex-end;
                    flex-grow: 1;
                }

                .searchArea {
                    padding: 10px;
                    width: 100%;
                    max-width: 300px;
                    border: 1px solid #ccc;
                    border-radius: 5px;
                    outline: none;
                    font-size: 16px;
                }

                @media (max-width: 768px) {
                    .headMembersPart {
                        flex-direction: column;
                        align-items: flex-start;
                    }

                    .headMembersPartSearchp {
                        width: 100%;
                        margin-top: 10px;
                    }

                    .searchArea {
                        width: 100%;
                    }
                }
            </style>

            <!-- Popup container for the table members -->
            <div id="MemberDISTablePopup-table" class="popup-table">
                <div class="popup-content-table">

                    <div style="margin-top: 20px;" class="MinPrtSecSpace-table">
                        <div class="headMembersPart">
                            <div class="headMembersPartbtns">
                                <h3>Members Information</h3><br>
                                <a id="MembersPartbtn" class="DownloadButton" href="../forms/Membersgenerate_pdf.php" target="_blank"><button>Print Members PDF</button></a><br><br>
                            </div>
                            <div class="headMembersPartSearchp">
                                <input id="MembersSearch" placeholder="Search for Member..." class="searchArea" type="text">
                            </div>
                            <br>
                        </div>

                        <div class="card_table-table">
                            <table id="individualMembersTable">
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
                        <div class="headMembersPart">
                            <div class="headMembersPartbtns">
                                <h3>Members Information</h3><br>
                                <a id="officialsPrintBTN" class="DownloadButton" href="../forms/officialsgenerate_pdf.php" target="_blank"><button>Print Members PDF</button></a><br><br>
                            </div>
                            <div class="headMembersPartSearchp">
                                <input id="officalmembersserach" placeholder="Search for Member..." class="searchArea" type="text">
                            </div>
                            <br>
                        </div>
                        <div class="card_table-table">
                            <table id="officialMembersTable" >
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
                        <div class="headMembersPart">
                            <div class="headMembersPartbtns">
                                <h3>Members Information</h3><br>
                                <a id="organizationPrintBTN" class="DownloadButton" href="../forms/organizationgenerate_pdf.php" target="_blank"><button>Print Members PDF</button></a><br><br>
                            </div>
                            <div class="headMembersPartSearchp">
                                <input id="organizationalMembersSearch" placeholder="Search for Member..." class="searchArea" type="text">
                            </div>
                            <br>
                        </div>
                        <div class="card_table-table">
                            <table id="organizationMembersTable" >
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
        // Function to filter the table rows based on input
        function filterTable(inputId, tableId) {
            const input = document.getElementById(inputId);
            const filter = input.value.toLowerCase();
            const table = document.getElementById(tableId);
            const tr = table.getElementsByTagName('tr');

            for (let i = 1; i < tr.length; i++) {
                const tds = tr[i].getElementsByTagName('td');
                let found = false;
                for (let j = 0; j < tds.length; j++) {
                    if (tds[j]) {
                        const txtValue = tds[j].textContent || tds[j].innerText;
                        if (txtValue.toLowerCase().indexOf(filter) > -1) {
                            found = true;
                            break;
                        }
                    }
                }
                tr[i].style.display = found ? "" : "none";
            }
        }

        // Event listeners for search inputs
        document.getElementById('MembersSearch').addEventListener('input', function() {
            filterTable('MembersSearch', 'individualMembersTable');
        });

        document.getElementById('officalmembersserach').addEventListener('input', function() {
            filterTable('officalmembersserach', 'officialMembersTable');
        });

        document.getElementById('organizationalMembersSearch').addEventListener('input', function() {
            filterTable('organizationalMembersSearch', 'organizationMembersTable');
        });
    </script>

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