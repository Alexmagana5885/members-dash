<?php
require_once('../forms/DBconnection.php');

session_start();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>User Details</title>
    <link href="../assets/img/favicon.png" rel="icon" />
    <link href="../assets/CSS/quilleditor.css" rel="stylesheet" />
    <link rel="stylesheet" href="../assets/CSS/startfile.css" />
    <link rel="stylesheet" href="../assets/CSS/editfiles.css" />

</head>

<body>
    <!-- Header -->
    <header class="site-header">
        <div class="logo">
            <img src="../assets/img/logo.png" alt="Logo" />
        </div>
        <button class="menu-toggle" id="menu-toggle">
            &#9776;
        </button>
        <nav class="navigation" id="navigation">
            <ul>
                <li><a href="https://www.agl.or.ke/">Home</a></li>
                <li><a href="#" onclick="window.history.back(); return false;">Back</a></li>

            </ul>
        </nav>
    </header>
    <?php

    // Check if user is logged in
    if (isset($_SESSION['user_email'])) {
        $userEmail = $_SESSION['user_email'];

        // Prepare the SQL query to fetch organization data
        $sql = "SELECT * FROM organizationmembership WHERE organization_email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $userEmail);
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if organization data is found
        if ($result->num_rows > 0) {
            // Fetch organization data
            $orgData = $result->fetch_assoc();
        } else {
            echo "No organization data found.";
            exit;
        }
    } else {
        echo "Please log in to view your data.";
        exit;
    }

    // Close the database connection
    $stmt->close();

    ?>

    <!-- HTML Part -->
    <div class="userdatadisplaypoint">

        <div class="userdatadisplayimage">
            <img src="<?php echo htmlspecialchars($orgData['logo_image']); ?>" alt="Organization Logo" id="passportImage" />
            <a class="imageenditbtn" href="#" id="imageenditbtn">Edit</a>
        </div>
        <div class="userdatadisplay">
            <div id="organizationDataForm" method="POST" action="../forms/imageupdateO.php" enctype="multipart/form-data">
                <input type="hidden" name="id" id="orgId" value="<?php echo htmlspecialchars($orgData['id']); ?>">

                <form method="POST" action="../forms/imageupdateO.php" enctype="multipart/form-data" class="displayarea">
                    <div id="logoInputSection" class="hidden">
                        <label for="logo">Organization Logo:</label>
                        <input name="profileImage" type="file" id="logo_imageInput" accept="image/*" required>
                        <button type="submit" class="saveBtn">Save</button>
                    </div>
                </form>

                <div class="displayarea">
                    <label for="organizationName">Organization Name:</label>
                    <p id="organizationNameDisplay">
                        <?php echo htmlspecialchars($orgData['organization_name']); ?>
                        <a href="#" id="editOrganizationNameBtn">Edit</a>
                    </p>
                    <div id="organizationNameEditSection" class="hidden">
                        <input type="text" id="organization_nameInput" value="<?php echo htmlspecialchars($orgData['organization_name']); ?>" required>
                        <button id="saveOrganizationNameBtn" type="button" class="saveBtn" data-section="organization_name">Save</button>

                    </div>
                </div>

                <div class="displayarea">
                    <label for="contactPerson">Contact Person:</label>
                    <p id="contactPersonDisplay">
                        <?php echo htmlspecialchars($orgData['contact_person']); ?>
                        <a href="#" id="editContactPersonBtn">Edit</a>
                    </p>
                    <div id="contactPersonEditSection" class="hidden">
                        <input type="text" id="contact_personInput" value="<?php echo htmlspecialchars($orgData['contact_person']); ?>" required>
                        <button id="contact_personBTN" type="button" class="saveBtn" data-section="contact_person">Save</button>
                    </div>
                </div>

                <div class="displayarea">
                    <label for="contactPhone">Contact Phone Number:</label>
                    <p id="contactPhoneDisplay">
                        <?php echo htmlspecialchars($orgData['contact_phone_number']); ?>
                        <a href="#" id="editContactPhoneBtn">Edit</a>
                    </p>
                    <div id="contactPhoneEditSection" class="hidden">
                        <input type="tel" id="contact_phone_numberInput" value="<?php echo htmlspecialchars($orgData['contact_phone_number']); ?>" required>
                        <button id="saveContactPhoneNumberBtn" type="button" class="saveBtn" data-section="contact_phone_number">Save</button>
                    </div>
                </div>

                <div class="displayarea">
                    <label for="email">Email:</label>
                    <p id="emailDisplay"><?php echo htmlspecialchars($orgData['organization_email']); ?></p>
                </div>



                <form class="displayarea" id="userDataForm" method="POST" action="../forms/fileUpdateO.php" enctype="multipart/form-data">

                    <label for="registrationCertificate">Registration Certificate:</label>
                    <p id="registrationCertificateDisplay">
                        <?php if (!empty($orgData['registration_certificate']) && file_exists($orgData['registration_certificate'])): ?>
                            <a href="<?php echo htmlspecialchars($orgData['registration_certificate']); ?>" target="_blank">View Current Certificate</a>
                        <?php else: ?>
                            No registration certificate uploaded.
                        <?php endif; ?>
                        <a href="#" id="editRegistrationCertificateBtn">Edit</a>
                    </p>
                    <div id="registrationCertificateEditSection" class="hidden">
                        <input type="file" id="registration_certificateInput" name="registration_certificate">
                        <button type="submit" class="saveBtn">Save</button>
                    </div>
                </form>

                <div class="displayarea">
                    <label for="organizationAddress">Organization Address:</label>
                    <p id="organizationAddressDisplay">
                        <?php echo htmlspecialchars($orgData['organization_address']); ?>
                        <a href="#" id="editOrganizationAddressBtn">Edit</a>
                    </p>
                    <div id="organizationAddressEditSection" class="hidden">
                        <input type="text" id="organization_addressInput" value="<?php echo htmlspecialchars($orgData['organization_address']); ?>" required>
                        <button id="saveOrganizationAddressBtn" type="button" class="saveBtn" data-section="organization_address">Save</button>
                    </div>
                </div>

                <!-- ......................................... -->

                <div class="displayarea">
                    <label for="locationCountry">Country:</label>
                    <p id="locationCountryDisplay">
                        <?php echo htmlspecialchars($orgData['location_country']); ?>
                        <a href="#" id="editLocationCountryBtn">Edit</a>
                    </p>
                    <div id="locationCountryEditSection" class="hidden">
                        <input type="text" id="location_countryInput" value="<?php echo htmlspecialchars($orgData['location_country']); ?>" required>
                        <button id="saveLocationCountryBtn" type="button" class="saveBtn" data-section="location_country">Save</button>
                    </div>
                </div>

                <div class="displayarea">
                    <label for="locationCounty">County:</label>
                    <p id="locationCountyDisplay">
                        <?php echo htmlspecialchars($orgData['location_county']); ?>
                        <a href="#" id="editLocationCountyBtn">Edit</a>
                    </p>
                    <div id="locationCountyEditSection" class="hidden">
                        <input type="text" id="location_countyInput" value="<?php echo htmlspecialchars($orgData['location_county']); ?>" required>
                        <button id="saveLocationCountyBtn" type="button" class="saveBtn" data-section="location_county">Save</button>
                    </div>
                </div>

                <div class="displayarea">
                    <label for="locationTown">Town:</label>
                    <p id="locationTownDisplay">
                        <?php echo htmlspecialchars($orgData['location_town']); ?>
                        <a href="#" id="editLocationTownBtn">Edit</a>
                    </p>
                    <div id="locationTownEditSection" class="hidden">
                        <input type="text" id="location_townInput" value="<?php echo htmlspecialchars($orgData['location_town']); ?>" required>
                        <button id="saveLocationTownBtn" type="button" class="saveBtn" data-section="location_town">Save</button>
                    </div>
                </div>

                <div class="displayarea">
                    <label for="organizationType">Organization Type:</label>
                    <p id="organizationTypeDisplay">
                        <?php echo htmlspecialchars($orgData['organization_type']); ?>
                        <a href="#" id="editOrganizationTypeBtn">Edit</a>
                    </p>
                    <div id="organizationTypeEditSection" class="hidden">
                        <input type="text" id="organization_typeInput" value="<?php echo htmlspecialchars($orgData['organization_type']); ?>" required>
                        <button id="saveOrganizationTypeBtn" type="button" class="saveBtn" data-section="organization_type">Save</button>
                    </div>
                </div>



                <div class="displayarea">
                    <label for="whatYouDo">What You Do:</label>
                    <p id="whatYouDoDisplay">
                        <?php echo htmlspecialchars($orgData['what_you_do']); ?>
                        <a href="#" id="editWhatYouDoBtn">Edit</a>
                    </p>
                    <div id="whatYouDoEditSection" class="hidden">
                        <input type="text" id="what_you_doInput" value="<?php echo htmlspecialchars($orgData['what_you_do']); ?>" required>
                        <button id="saveWhatYouDoBtn" type="button" class="saveBtn" data-section="what_you_do">Save</button>
                    </div>
                </div>


                <div class="displayarea">
                    <label for="dateOfRegistration">Date of Registration:</label>
                    <p id="dateOfRegistrationDisplay"><?php echo htmlspecialchars($orgData['date_of_registration']); ?></p>
                </div>
            </div>
        </div>

    </div>

    <!-- user update  -->


    <script>
        document.querySelectorAll('.saveBtn').forEach(button => {
            button.addEventListener('click', function() {
                const section = this.getAttribute('data-section');
                const input = document.getElementById(section + 'Input');
                const value = input.value;

                fetch('../forms/update_user_dataORG.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            field: section,
                            value: value
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert("updated successfully!");

                            document.getElementById(section + 'Display').innerText = value;
                        } else {
                            alert("Failed to update field.");
                        }
                    })
                    .catch(error => console.error("Error:", error));
            });
        });
    </script>

    <script>
        const fields = [{
                display: "organizationNameDisplay",
                edit: "organizationNameEditSection",
                editBtn: "editOrganizationNameBtn",
                saveBtn: "saveOrganizationNameBtn",
                input: "organization_nameInput",
                label: "organizationName"
            },

            {
                edit: "logoInputSection",
                editBtn: "imageenditbtn",
                saveBtn: "logoEditbtn",
                input: "logo_imageInput",
                label: "logo"
            },

            {
                display: "organizationEmailDisplay",
                edit: "organizationEmailEditSection",
                editBtn: "editOrganizationEmailBtn",
                saveBtn: "saveOrganizationEmailBtn",
                input: "organizationEmailInput",
                label: "Email"
            },
            {
                display: "contactPersonDisplay",
                edit: "contactPersonEditSection",
                editBtn: "editContactPersonBtn",
                saveBtn: "contact_personBTN",
                input: "contact_personInput",
                label: "contactPerson"
            },
            {
                display: "contactPhoneDisplay",
                edit: "contactPhoneEditSection",
                editBtn: "editContactPhoneBtn",
                saveBtn: "saveContactPhoneNumberBtn",
                input: "contact_phone_numberInput",
                label: "contactPhone"
            },
            {
                display: "organizationAddressDisplay",
                edit: "organizationAddressEditSection",
                editBtn: "editOrganizationAddressBtn",
                saveBtn: "saveOrganizationAddressBtn",
                input: "organization_addressInput",
                label: "organizationAddress"
            },
            {
                display: "organizationTypeDisplay",
                edit: "organizationTypeEditSection",
                editBtn: "editOrganizationTypeBtn",
                saveBtn: "saveOrganizationTypeBtn",
                input: "organizationTypeInput",
                label: "Organization Type"
            },

            {
                display: "locationCountryDisplay",
                edit: "locationCountryEditSection",
                editBtn: "editLocationCountryBtn",
                saveBtn: "saveLocationCountryBtn",
                input: "location_countryInput",
                label: "locationCountry"
            },
            {
                display: "locationCountyDisplay",
                edit: "locationCountyEditSection",
                editBtn: "editLocationCountyBtn",
                saveBtn: "saveLocationCountyBtn",
                input: "location_countyInput",
                label: "locationCounty"
            },
            {
                display: "locationTownDisplay",
                edit: "locationTownEditSection",
                editBtn: "editLocationTownBtn",
                saveBtn: "saveLocationTownBtn",
                input: "location_townInput",
                label: "locationTown"
            },
            {
                display: "organizationTypeDisplay",
                edit: "organizationTypeEditSection",
                editBtn: "editOrganizationTypeBtn",
                saveBtn: "saveOrganizationTypeBtn",
                input: "organization_typeInput",
                label: "organizationType"
            },

            {
                display: "whatYouDoDisplay",
                edit: "whatYouDoEditSection",
                editBtn: "editWhatYouDoBtn",
                saveBtn: "saveWhatYouDoBtn",
                input: "what_you_doInput",
                label: "whatYouDo"
            },

            {
                edit: "registrationCertificateEditSection",
                editBtn: "editRegistrationCertificateBtn",
                saveBtn: "saveBtn",
                input: "registration_certificate",
                label: "registration_certificate Image"
            }
        ];

        fields.forEach(field => {
            // Event listener for the edit button
            document.getElementById(field.editBtn)?.addEventListener("click", function() {
                document.getElementById(field.edit).classList.toggle("hidden");
                this.style.display = "none";
            });

            // Event listener for the save button
            document.getElementById(field.saveBtn)?.addEventListener("click", function() {
                const newValue = document.getElementById(field.input).value;
                if (field.display) {
                    document.getElementById(field.display).innerText = `${field.label}: ${newValue}`;
                }
                document.getElementById(field.edit).classList.add("hidden");
                document.getElementById(field.editBtn).style.display = "inline";
            });
        });
    </script>

    <!-- Footer -->
    <footer class="site-footer">
        <p>
            &copy; 2024
            <a style="text-decoration: none" href="http://www.agl.or.ke/">AGL.or.ke</a>
            . All rights reserved.
        </p>
    </footer>

    <!-- JavaScript for Menu Toggle and Document Handling -->
    <script>
        // Menu toggle functionality
        const menuToggle = document.getElementById("menu-toggle");
        const navigation = document.getElementById("navigation");

        menuToggle.addEventListener("click", () => {
            navigation.classList.toggle("active");
        });

        // Document handling functionality
        const documentsButton = document.getElementById("eventdetailsdocuments");
        documentsButton.addEventListener("click", () => {
            const documentPath = documentsButton.getAttribute("data-document-path");
            if (documentPath) {
                window.location.href = documentPath;
            }
        });
    </script>
</body>

</html>