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

</head>

<style>
    .userdatadisplaypoint {
        display: flex;
        width: 80%;
        max-height: 600px;
        background: #f2f2f2;
        border-radius: 10px;
        margin: 5% auto;
        padding: 10px;

    }

    .userdatadisplayimage {
        display: flex;
        flex-direction: column;

    }

    .imageenditbtn {
        border: 1px solid blue;
        padding: 5px;
        color: blue;
        text-decoration: none;
        transition: background-color 0.3s, color 0.3s;
        border-radius: 5px;
        font-size: 14px;
        width: 90%;
        text-align: center;
        margin-top: 20px;

    }

    .imageenditbtn:hover {
        background-color: blue;
        color: white;
    }

    .userdatadisplaypoint p {
        font-size: 18px;
        font-weight: bold;
    }

    .userdatadisplayimage {
        width: 50%;
        padding: 5px;
        margin-top: 5px;
    }

    .userdatadisplayimage img {
        max-width: 90%;
        max-height: 90%;
        object-fit: cover;
        border-radius: 8px;
    }

    .userdatadisplay {
        width: 50%;
        padding: 5px;
        max-height: 600px;
        overflow: auto;
        scrollbar-width: thin;

    }

    .userdatadisplay div {
        font-size: 15px;
        margin-top: 10px;

    }

    .userdatadisplay input,
    select {
        width: 100%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 4px;
        font-size: 16px;
        transition: border-color 0.3s;
        margin-bottom: 10px;
    }

    .userdatadisplay input,
    select :focus {
        border-color: #007bff;
        outline: none;
    }

    .userdatadisplay label {
        font-weight: bold;
        margin-bottom: 5px;
        color: #0056b3;
    }


    .userdatadisplaypoint button {
        padding: 10px;
        background-color: #0056b3;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 16px;
        transition: background-color 0.3s;

    }

    .userdatadisplaypoint button :hover {
        background-color: #007bff;
    }

    .userdatadisplay a {
        border: 1px solid blue;
        padding: 5px;
        color: blue;
        text-decoration: none;
        transition: background-color 0.3s, color 0.3s;
        border-radius: 5px;
        font-size: 14px;
        margin-left: 20px;
    }

    .userdatadisplay a:hover {
        background-color: blue;
        color: white;
    }


    .hidden {
        display: none;
    }

    .displayarea {
        width: 100%;
        background-color: #fff;
        border-radius: 8px;
        padding: 10px;
        margin-bottom: 10px;

    }

    @media (max-width: 768px) {
        .userdatadisplaypoint {
            flex-direction: column;
            max-height: unset;
            width: 98%;
        }

        .userdatadisplayimage,
        .userdatadisplay {
            width: 100%;
            padding: 10px 5px;
            max-height: unset;
        }

        .userdatadisplay p {
            font-size: 16px;
        }

        .userdatadisplay div {
            font-size: 14px;
            margin-top: 8px;
        }

        .userdatadisplay input,
        select {
            width: 100%;
            padding: 8px;
            font-size: 14px;
        }

        .imageenditbtn {
            font-size: 13px;
            margin-left: 0;
            width: 100%;
            padding: 8px;
        }

        .userdatadisplaypoint button {
            font-size: 14px;
            width: 100%;
            padding: 8px;
        }

        .displayarea {
            padding: 10px;
        }
    }


    @media (max-width: 480px) {
        .userdatadisplay p {
            font-size: 14px;
        }

        .userdatadisplay div {
            font-size: 12px;
        }

        .userdatadisplay input,
        select {
            font-size: 13px;
            padding: 6px;
        }

        .userdatadisplaypoint button,
        .imageenditbtn {
            font-size: 13px;
            padding: 6px;
        }
    }
</style>


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

        // Prepare the SQL query to fetch user data
        $sql = "SELECT * FROM personalmembership WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $userEmail);
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if user data is found
        if ($result->num_rows > 0) {
            // Fetch user data
            $userData = $result->fetch_assoc();
        } else {
            echo "No user data found.";
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
            <img src="<?php echo htmlspecialchars($userData['passport_image']); ?>" alt="Passport Image" id="passportImage" />
            <a class="imageenditbtn" href="#" id="imageenditbtn">Edit</a>
        </div>
        <div class="userdatadisplay">
            <form id="userDataForm" method="POST" action="../forms/imageupdateM.php" enctype="multipart/form-data">
                <input type="hidden" name="id" id="userId" value="<?php echo htmlspecialchars($userData['id']); ?>">

                <form method="POST" action="../forms/update_user_data.php" enctype="multipart/form-data" class="displayarea">
                    <div id="imageEditSection" class="hidden">
                        <label for="image">Edit Image:</label>
                        <input name="passport" type="file" id="passport_imageInput" accept="image/*" required>
                        <button type="submit" class="saveBtn">Save</button>
                    </div>
                </form>


                <div method="POST" action="../forms/update_user_data.php" enctype="multipart/form-data" class="displayarea">
                    <label for="name">Name:</label>
                    <p id="nameDisplay">
                        <?php echo htmlspecialchars($userData['name']); ?>
                        <a href="#" id="editNameBtn">Edit</a>
                    </p>
                    <div id="nameEditSection" class="hidden">
                        <input type="text" id="nameInput" value="<?php echo htmlspecialchars($userData['name']); ?>">
                        <button type="button" class="saveBtn" data-section="name">Save</button>
                    </div>
                </div>

                <div method="POST" action="../forms/update_user_data.php" enctype="multipart/form-data" class="displayarea">
                    <label for="email">Email:</label>
                    <p id="emailDisplay">
                        <?php echo htmlspecialchars($userData['email']); ?>

                    </p>

                </div>

                <div method="POST" action="../forms/update_user_data.php" enctype="multipart/form-data" class="displayarea">
                    <label for="phone">Phone:</label>
                    <p id="phoneDisplay">
                        <?php echo htmlspecialchars($userData['phone']); ?>
                        <a href="#" id="editPhoneBtn">Edit</a>
                    </p>
                    <div id="phoneEditSection" class="hidden">
                        <input type="tel" id="phoneInput" value="<?php echo htmlspecialchars($userData['phone']); ?>" required>
                        <button type="button" class="saveBtn" data-section="phone">Save</button>
                    </div>
                </div>

                <div method="POST" action="../forms/update_user_data.php" enctype="multipart/form-data" class="displayarea">
                    <label for="homeAddress">Home Address:</label>
                    <p id="addressDisplay">
                        <?php echo htmlspecialchars($userData['home_address']); ?>
                        <a href="#" id="editAddressBtn">Edit</a>
                    </p>
                    <div id="addressEditSection" class="hidden">
                        <input type="text" id="home_addressInput" value="<?php echo htmlspecialchars($userData['home_address']); ?>" required>
                        <button type="button" class="saveBtn" data-section="home_address">Save</button>
                    </div>
                </div>


                <div method="POST" action="../forms/update_user_data.php" enctype="multipart/form-data" class="displayarea">
                    <label for="highestDegree">Highest Degree:</label>
                    <p id="degreeDisplay">
                        <?php echo htmlspecialchars($userData['highest_degree']); ?>
                        <a href="#" id="editDegreeBtn">Edit</a>
                    </p>
                    <div id="degreeEditSection" class="hidden">
                        <input type="text" id="highest_degreeInput" value="<?php echo htmlspecialchars($userData['highest_degree']); ?>" required>
                        <button type="button" class="saveBtn" data-section="highest_degree">Save</button>
                    </div>
                </div>

                <div method="POST" action="../forms/update_user_data.php" enctype="multipart/form-data" class="displayarea">
                    <label for="institution">Institution:</label>
                    <p id="institutionDisplay">
                        <?php echo htmlspecialchars($userData['institution']); ?>
                        <a href="#" id="editInstitutionBtn">Edit</a>
                    </p>
                    <div id="institutionEditSection" class="hidden">
                        <input type="text" id="institutionInput" value="<?php echo htmlspecialchars($userData['institution']); ?>" required>
                        <button type="button" class="saveBtn" data-section="institution">Save</button>
                    </div>
                </div>

                <div method="POST" action="../forms/update_user_data.php" enctype="multipart/form-data" class="displayarea">
                    <label for="graduationYear">Graduation Year:</label>
                    <p id="graduationYearDisplay">
                        <?php echo htmlspecialchars($userData['graduation_year']); ?>
                        <a href="#" id="editGraduationYearBtn">Edit</a>
                    </p>
                    <div id="graduationYearEditSection" class="hidden">
                        <input type="number" id="graduation_yearInput" value="<?php echo htmlspecialchars($userData['graduation_year']); ?>" required>
                        <button type="button" class="saveBtn" data-section="graduation_year">Save</button>
                    </div>
                </div>

                <form method="POST" action="../forms/fileUpdate.php" enctype="multipart/form-data" class="displayarea">
                    <label for="completionLetter">Completion Letter:</label>
                    <p id="completionLetterDisplay">
                        <?php if (!empty($userData['completion_letter']) && file_exists($userData['completion_letter'])): ?>
                            <a href="<?php echo htmlspecialchars($userData['completion_letter']); ?>" target="_blank">View Current Letter</a>
                        <?php else: ?>
                            No completion letter uploaded.
                        <?php endif; ?>
                        <a href="#" id="editCompletionLetterBtn">Edit</a>
                    </p>
                    <div id="completionLetterEditSection" class="hidden">
                        <input type="file" id="completion_letterInput" name="completionLetter">
                        <button type="submit" class="saveBtn">Save</button>
                    </div>
                </form>

                <div method="POST" action="../forms/update_user_data.php" enctype="multipart/form-data" class="displayarea">
                    <label for="profession">Profession:</label>
                    <p id="professionDisplay">
                        <?php echo htmlspecialchars($userData['profession']); ?>
                        <a href="#" id="editProfessionBtn">Edit</a>
                    </p>
                    <div id="professionEditSection" class="hidden">
                        <input type="text" id="professionInput" value="<?php echo htmlspecialchars($userData['profession']); ?>" required>
                        <button type="button" class="saveBtn" data-section="profession">Save</button>
                    </div>
                </div>

                <div method="POST" action="../forms/update_user_data.php" enctype="multipart/form-data" class="displayarea">
                    <label for="experience">Experience:</label>
                    <p id="experienceDisplay">
                        <?php echo htmlspecialchars($userData['experience']); ?>
                        <a href="#" id="editExperienceBtn">Edit</a>
                    </p>
                    <div id="experienceEditSection" class="hidden">
                        <input type="text" id="experienceInput" value="<?php echo htmlspecialchars($userData['experience']); ?>" required>
                        <button type="button" class="saveBtn" data-section="experience">Save</button>
                    </div>
                </div>

                <div method="POST" action="../forms/update_user_data.php" enctype="multipart/form-data" class="displayarea">
                    <label for="currentCompany">Current Company:</label>
                    <p id="currentCompanyDisplay">
                        <?php echo htmlspecialchars($userData['current_company']); ?>
                        <a href="#" id="editCurrentCompanyBtn">Edit</a>
                    </p>
                    <div id="currentCompanyEditSection" class="hidden">
                        <input type="text" id="current_companyInput" value="<?php echo htmlspecialchars($userData['current_company']); ?>" required>
                        <button type="button" class="saveBtn" data-section="current_company">Save</button>
                    </div>
                </div>

                <div method="POST" action="../forms/update_user_data.php" enctype="multipart/form-data" class="displayarea">
                    <label for="position">Position:</label>
                    <p id="positionDisplay">
                        <?php echo htmlspecialchars($userData['position']); ?>
                        <a href="#" id="editPositionBtn">Edit</a>
                    </p>
                    <div id="positionEditSection" class="hidden">
                        <input type="text" id="positionInput" value="<?php echo htmlspecialchars($userData['position']); ?>" required>
                        <button type="button" class="saveBtn" data-section="position">Save</button>
                    </div>
                </div>

                <div method="POST" action="../forms/update_user_data.php" enctype="multipart/form-data" class="displayarea">
                    <label for="workAddress">Work Address:</label>
                    <p id="workAddressDisplay">
                        <?php echo htmlspecialchars($userData['work_address']); ?>
                        <a href="#" id="editWorkAddressBtn">Edit</a>
                    </p>
                    <div id="workAddressEditSection" class="hidden">
                        <input type="text" id="work_addressInput" value="<?php echo htmlspecialchars($userData['work_address']); ?>" required>
                        <button type="button" class="saveBtn" data-section="work_address">Save</button>
                    </div>
                </div>

                <div method="POST" action="../forms/update_user_data.php" enctype="multipart/form-data" class="displayarea">
                    <label for="gender">Gender:</label>
                    <p id="genderDisplay">
                        <?php echo htmlspecialchars($userData['gender']); ?>
                        <a href="#" id="editGenderBtn">Edit</a>
                    </p>
                    <div id="genderEditSection" class="hidden">
                        <input type="text" id="genderInput" value="<?php echo htmlspecialchars($userData['gender']); ?>" required>
                        <button type="button" class="saveBtn" data-section="gender">Save</button>
                    </div>
                </div>
            </form>

        </div>
    </div>

    <!-- user update  -->
    <script>
        document.querySelectorAll('.saveBtn').forEach(button => {
            button.addEventListener('click', function() {
                const section = this.getAttribute('data-section');
                const input = document.getElementById(section + 'Input');
                const value = input.value;

                fetch('../forms/update_user_data.php', {
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
                            // Update display value
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
                display: "nameDisplay",
                edit: "nameEditSection",
                editBtn: "editNameBtn",
                saveBtn: "saveNameBtn",
                input: "nameInput",
                label: "Name"
            },
            {
                edit: "imageEditSection",
                editBtn: "imageenditbtn",
                saveBtn: "saveImageBtn",
                input: "imageInput",
                label: "Image"
            },
            {
                display: "emailDisplay",
                edit: "emailEditSection",
                editBtn: "editEmailBtn",
                saveBtn: "saveEmailBtn",
                input: "emailInput",
                label: "Email"
            },
            {
                display: "phoneDisplay",
                edit: "phoneEditSection",
                editBtn: "editPhoneBtn",
                saveBtn: "savePhoneBtn",
                input: "phoneInput",
                label: "Phone"
            },
            {
                display: "addressDisplay",
                edit: "addressEditSection",
                editBtn: "editAddressBtn",
                saveBtn: "saveAddressBtn",
                input: "addressInput",
                label: "Home Address"
            },
            {
                display: "degreeDisplay",
                edit: "degreeEditSection",
                editBtn: "editDegreeBtn",
                saveBtn: "saveDegreeBtn",
                input: "degreeInput",
                label: "Highest Degree"
            },
            {
                display: "institutionDisplay",
                edit: "institutionEditSection",
                editBtn: "editInstitutionBtn",
                saveBtn: "saveInstitutionBtn",
                input: "institutionInput",
                label: "Institution"
            },
            {
                display: "graduationYearDisplay",
                edit: "graduationYearEditSection",
                editBtn: "editGraduationYearBtn",
                saveBtn: "saveGraduationYearBtn",
                input: "graduationYearInput",
                label: "Graduation Year"
            },
            {
                display: "completionLetterDisplay",
                edit: "completionLetterEditSection",
                editBtn: "editCompletionLetterBtn",
                saveBtn: "saveCompletionLetterBtn",
                input: "completionLetterInput",
                label: "Completion Letter"
            },
            {
                display: "professionDisplay",
                edit: "professionEditSection",
                editBtn: "editProfessionBtn",
                saveBtn: "saveProfessionBtn",
                input: "professionInput",
                label: "Profession"
            },
            {
                display: "experienceDisplay",
                edit: "experienceEditSection",
                editBtn: "editExperienceBtn",
                saveBtn: "saveExperienceBtn",
                input: "experienceInput",
                label: "Experience"
            },
            {
                display: "currentCompanyDisplay",
                edit: "currentCompanyEditSection",
                editBtn: "editCurrentCompanyBtn",
                saveBtn: "saveCurrentCompanyBtn",
                input: "currentCompanyInput",
                label: "Current Company"
            },
            {
                display: "positionDisplay",
                edit: "positionEditSection",
                editBtn: "editPositionBtn",
                saveBtn: "savePositionBtn",
                input: "positionInput",
                label: "Position"
            },
            {
                display: "workAddressDisplay",
                edit: "workAddressEditSection",
                editBtn: "editWorkAddressBtn",
                saveBtn: "saveWorkAddressBtn",
                input: "workAddressInput",
                label: "Work Address"
            },
            {
                display: "genderDisplay",
                edit: "genderEditSection",
                editBtn: "editGenderBtn",
                saveBtn: "saveGenderBtn",
                input: "genderInput",
                label: "Gender"
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
                document.getElementById(field.display).innerText = `${field.label}: ${newValue}`;
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