<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/CSS/Dashboard.css">
    <link rel="stylesheet" href="../assets/CSS/popups.css">
    <link href="../assets/img/favicon.png" rel="icon">
    <link href="../assets/img/favicon.png" rel="favicon.png">
    <link rel="stylesheet" href="../assets/CSS/AGLADMIN.css">

    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
</head>

<!-- <php
session_start();
$userEmail = $_SESSION['user_email'];
$userName = $_SESSION['user_name'];
$userPhone = $_SESSION['user_phone'];
?> -->

<!-- delete the  stylings already transfered -->

<style>
    .sidebar {
        overflow: auto;
        scrollbar-width: thin;
        height: 100vh;
    }

    .dashboard {
        height: 100vh;
        overflow: auto;
        scrollbar-width: thin;
    }

    @media (max-width: 768px) {
        .sidebar {
            position: fixed;
        }

    }


    .popup-form {
        display: none;
        /* Hidden by default */
        position: fixed;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        justify-content: center;
        align-items: center;
        text-align: center;
    }

    .form-container {
        background-color: white;
        padding: 20px;
        border-radius: 8px;
        width: 300px;
        max-width: 80%;
    }

    label {
        display: block;
        margin-top: 10px;
        font-weight: bold;
    }

    input {
        width: 100%;
        padding: 8px;
        margin-top: 5px;
        margin-bottom: 15px;
        border-radius: 5px;
        border: 1px solid #ccc;
    }

    button[type="submit"] {
        padding: 10px;
        background-color: #4CAF50;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    button[type="button"] {
        padding: 10px;
        background-color: #f44336;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        margin-left: 10px;
    }
</style>


<body>
    <header>
        <div class="logo">
            <img src="../assets/img/logo.png" alt="AGL">
        </div>
        <div id="toggleMenu" class="menu-button" onclick="toggleMenu()">☰</div>
        <!-- <button id="toggleMessages">View Messages</button> -->

        <div id="toggleMessages" class="notification">
            <img src="../assets/img/bell.png" alt="Notification">
        </div>

    </header>


    <div class="main-content">

        <nav id="sidebar" class="sidebar">
            <ul>
                <li>
                    <a href="https://www.agl.or.ke/" class="active">Home<br /></a>
                </li>
                <li><a id="openPostEventModal">Post Up coming Event</a></li>
                <li><a id="openPastEventModal">Post past Event</a></li>
                <li><a id="openMessagePopup">Send Message</a></li>
                <li><a href="admin/settings.html">Admit New Members</a></li>
                <li><a id="openBlogPostModal">Post a Blog</a></li>
                <li><a href="#about">About</a></li>
                <li><a id="MembersTable-link" href="Members.php">Members</a></li>
                <li><a href="#team">Officials</a></li>
                <li><a href="pages/newfile.html">Donations</a></li>
                <li><a href="Payment/index.php">Payments</a></li>
                <li><a href="#contact">Contact</a></li>
                <li><a href="new.php">new</a></li>
            </ul>
            <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
        </nav>

        <section class="dashboard">
            <div class="cards">

                <?php
                require_once('../forms/DBconnection.php');
                session_start();

                // Ensure that the session contains the email
                if (!isset($_SESSION['user_email'])) {
                    die("User not logged in");
                }

                $userEmail = $_SESSION['user_email'];

                // Fetch user details based on the session email
                $sql = "SELECT * FROM personalmembership WHERE email = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $userEmail);
                $stmt->execute();
                $result = $stmt->get_result();

                // Fetch data
                if ($row = $result->fetch_assoc()) {
                    $name = $row['name'];
                    $registrationDate = $row['registration_date'];
                    $passportImage = $row['passport_image'];
                    $userEmail = $row['email'];
                } else {
                    echo "No user found";
                }

                $stmt->close();
                ?>

                <div class="card">
                    <img class="cardMemberprofile" src="<?php echo htmlspecialchars($passportImage); ?>"
                        alt="User Image">
                    <h5><?php echo htmlspecialchars($name); ?></h5>
                    <hr><br>
                    <h4><?php echo htmlspecialchars($userEmail); ?></h4>
                    <p>Registration Date: <?php echo htmlspecialchars($registrationDate); ?></p>
                </div>

                <div class="card">
                    <h5>Member Payments</h5>
                    <hr>
                    <p id="memberpayments-current-lastPay">Last payment: <span>12/08/2024</span></p>
                    <p id="memberpayments-current-nextP">Next payment: <span>09/05/2024</span></p>
                    <p id="memberpayments-current-balance">Current balance: <span>5000sh</span></p>
                    <a href="../forms/Payment/Mpesa-Daraja-Api-main/index.php">Pay</a>
                    <button id="memberpayments-btn">Make Payments</button>
                </div>

                <!-- Member dash payment popup -->
                <div id="memberpayments-popup" class="memberpayments-popup-container">
                    <form class="memberpayments-popup-content">
                        <span class="memberpayments-close" onclick="togglePaymentForm()">×</span>
                        <img src="../assets/img/mpesa.png" alt="M-Pesa" class="memberpayments-popup-logo">
                        <label for="memberpayments-phone-number">Number</label>
                        <input type="number" id="memberpayments-phone-number" name="phone-number"
                            placeholder="Enter your phone number">
                        <label for="memberpayments-amount">Amount</label>
                        <input type="text" id="memberpayments-amount" name="amount" value="300.00" readonly>
                        <p>Confirm that you are making a payment of 300 Ksh as membership fees to the Association of
                            Government Librarians.</p>
                        <div class="memberpayments-pay-buttons">
                            <button class="memberpayments-pay-btn" id="memberpayments-make-payment-btn"
                                type="submit">Make Payment</button>
                        </div>
                    </form>
                </div>

                <!-- JavaScript to handle showing and hiding the popup -->
                <script>
                    document.getElementById("memberpayments-btn").addEventListener("click", function () {
                        document.getElementById("memberpayments-popup").style.display = "flex";
                    });

                    function togglePaymentForm() {
                        document.getElementById("memberpayments-popup").style.display = "none";
                    }
                </script>

                <?php

                $sessionEmail = $_SESSION['user_email'];

                // Fetch education information from the database
                $query = "SELECT highest_degree, institution, start_date, graduation_year 
          FROM personalmembership 
          WHERE email = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("s", $sessionEmail);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $educationInfo = $result->fetch_assoc();
                } else {
                    $educationInfo = [
                        'highest_degree' => 'N/A',
                        'institution' => 'N/A',
                        'start_date' => 'N/A',
                        'graduation_year' => 'N/A'
                    ];
                }
                ?>

                <div class="card">
                    <h5>Education Information</h5>
                    <hr>
                    <p>Highest Degree: <span
                            id="highest-degree"><?php echo htmlspecialchars($educationInfo['highest_degree']); ?></span>
                    </p>
                    <p>Institution: <span
                            id="institution"><?php echo htmlspecialchars($educationInfo['institution']); ?></span></p>
                    <p>Start Date: <span
                            id="start-date"><?php echo htmlspecialchars($educationInfo['start_date']); ?></span></p>
                    <p>Graduation Year: <span
                            id="graduation-year"><?php echo htmlspecialchars($educationInfo['graduation_year']); ?></span>
                    </p>
                </div>


                <?php


                // Ensure that the session contains the email
                if (!isset($_SESSION['user_email'])) {
                    die("User not logged in");
                }

                $userEmail = $_SESSION['user_email'];

                // Prepare the SQL query
                $sql = "SELECT event_name, event_location, event_date FROM event_registrations WHERE member_email = ? ORDER BY event_date ASC";
                $stmt = $conn->prepare($sql);

                // Check if prepare() failed
                if ($stmt === false) {
                    die('Prepare failed: ' . htmlspecialchars($conn->error));
                }

                // Bind parameters
                $stmt->bind_param("s", $userEmail);

                // Execute the statement
                if (!$stmt->execute()) {
                    die('Execute failed: ' . htmlspecialchars($stmt->error));
                }

                // Get the result
                $result = $stmt->get_result();

                // Check if result fetching was successful
                if ($result === false) {
                    die('Get result failed: ' . htmlspecialchars($stmt->error));
                }
                ?>

                <div class="card">
                    <h4>Registered Events</h4>
                    <hr>

                    <?php
                    if ($result->num_rows > 0) {
                        // Output data of each row
                        while ($row = $result->fetch_assoc()) {
                            echo '<div>';
                            echo '<h5>' . htmlspecialchars($row['event_name']) . '</h5>';
                            echo '<p>' . htmlspecialchars($row['event_location']) . '</p>';
                            echo '<p>' . htmlspecialchars($row['event_date']) . '</p>';
                            echo '</div>';
                            echo '<hr>';
                        }
                    } else {
                        echo '<p>No upcoming registered events.</p>';
                    }

                    $stmt->close();
                    ?>
                </div>


            </div>


            <!-- messages -->

            <div class="message-popup" id="messagePopup">
                <div class="message-popup-header">
                    <h4>Messages</h4>
                    <button id="closePopup">&times;</button>
                </div>
                <div class="message-container">
                    <div class="message"
                        onclick="showFullMessage('Hello,  how are  you? This is the full message content. how are  you? This is the full message content. how are  you? This is the full message content. how are  you? This is the full message content. how are  you? This is the full message content.  ')">
                        <p class="message-content">Hello, how are you?</p>
                        <span class="message-time">10:30 AM</span>
                    </div>
                    <div class="message"
                        onclick="showFullMessage('I\'m fine, thank you! Here is more about what I wanted to say...')">
                        <p class="message-content">I'm fine, thank you!</p>
                        <span class="message-time">10:32 AM</span>
                    </div>
                </div>
            </div>

            <!-- Full message pop-up -->
            <div class="full-message-popup" id="fullMessagePopup">
                <div class="full-message-content">
                    <button id="closeFullMessage">&times;</button>
                    <p id="fullMessageText"></p>
                </div>
            </div>


            <script>
                document.getElementById('toggleMessages').addEventListener('click', function () {
                    document.getElementById('messagePopup').style.display = 'flex';
                });

                document.getElementById('closePopup').addEventListener('click', function () {
                    document.getElementById('messagePopup').style.display = 'none';
                });

                function showFullMessage(message) {
                    document.getElementById('fullMessageText').textContent = message;
                    document.getElementById('fullMessagePopup').style.display = 'flex';
                }

                document.getElementById('closeFullMessage').addEventListener('click', function () {
                    document.getElementById('fullMessagePopup').style.display = 'none';
                });
            </script>


            <!-- planned ivents -->

            <?php


            // Check if the email is set in the session
            $userEmail = isset($_SESSION['user_email']) ? htmlspecialchars($_SESSION['user_email']) : '';

            $sql = "SELECT id, event_name, event_image_path, event_description, event_location, event_date FROM plannedevent ORDER BY event_date DESC";

            $result = $conn->query($sql);
            ?>

            <div class="MinPrtSecSpace">
                <h3 style="padding:20px;">Planned Events</h3>
                <div class="table-card">
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $eventId = htmlspecialchars($row['id']);
                            $eventName = htmlspecialchars($row['event_name']);
                            $eventImagePath = htmlspecialchars($row['event_image_path']);
                            $eventDescription = htmlspecialchars($row['event_description']);
                            $eventLocation = htmlspecialchars($row['event_location']);
                            $eventDate = htmlspecialchars($row['event_date']);

                            echo '<div class="eventDiv">';
                            echo '<h3>' . $eventName . '</h3>';
                            echo '<img src="' . $eventImagePath . '" alt="Event">';
                            echo '<p>' . $eventDescription . '</p>';
                            echo '<div class="eventDivindiv">';
                            echo '<p>' . $eventLocation . '</p>';
                            echo '<p>' . $eventDate . '</p>';
                            echo '</div>';
                            echo '<button class="plannedEventsBTN" id="registerBtnEventRegistration_' . $eventId . '">Register for the event</button>';
                            echo '</div>';

                            // Event registration popup form
                            echo '<div id="popupFormEventRegistration_' . $eventId . '" class="popup-form" style="display:none;">';
                            echo '<div class="form-container">';
                            echo '<h4>Event Name: ' . $eventName . '</h4>';
                            echo '<form action="../forms/register_event.php" method="post">'; // Form action points to the PHP script
                            echo '<input type="hidden" name="event_id" value="' . $eventId . '">';
                            echo '<input type="hidden" name="event_name" value="' . $eventName . '">';
                            echo '<input type="hidden" name="event_location" value="' . $eventLocation . '">';
                            echo '<input type="hidden" name="event_date" value="' . $eventDate . '">';

                            echo '<label for="memberEmail">Member Email:</label>';
                            echo '<input type="email" id="memberEmail" name="memberEmail" value="' . $userEmail . '" readonly>';

                            echo '<label for="memberName">Name:</label>';
                            echo '<input type="text" id="memberName" name="memberName" required>';

                            echo '<label for="contact">Contact:</label>';
                            echo '<input type="text" id="contact" name="contact" required>';

                            echo '<button type="submit">Submit</button>';
                            echo '<button type="button" class="closeBtn" id="closeBtn_' . $eventId . '">Close</button>';
                            echo '</form>';
                            echo '</div>';
                            echo '</div>';
                        }
                    } else {
                        echo '<p>No events found.</p>';
                    }
                    ?>
                </div>
            </div>


            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    <?php
                    if ($result->num_rows > 0) {
                        $result->data_seek(0); // Reset the result pointer to the beginning
                        while ($row = $result->fetch_assoc()) {
                            $eventId = htmlspecialchars($row['id']);
                            ?>

                            // JavaScript to handle opening and closing of popup forms
                            var registerBtnEventRegistration_<?php echo $eventId; ?> = document.getElementById('registerBtnEventRegistration_<?php echo $eventId; ?>');
                            var popupFormEventRegistration_<?php echo $eventId; ?> = document.getElementById('popupFormEventRegistration_<?php echo $eventId; ?>');
                            var closeBtn_<?php echo $eventId; ?> = document.getElementById('closeBtn_<?php echo $eventId; ?>');

                            registerBtnEventRegistration_<?php echo $eventId; ?>.addEventListener('click', function () {
                                popupFormEventRegistration_<?php echo $eventId; ?>.style.display = 'flex';
                            });

                            closeBtn_<?php echo $eventId; ?>.addEventListener('click', function () {
                                popupFormEventRegistration_<?php echo $eventId; ?>.style.display = 'none';
                            });

                            window.addEventListener('click', function (event) {
                                if (event.target == popupFormEventRegistration_<?php echo $eventId; ?>) {
                                    popupFormEventRegistration_<?php echo $eventId; ?>.style.display = 'none';
                                }
                            });

                            <?php
                        }
                    }
                    ?>
                });
            </script>

            <!-- ........................................ -->


            <div>
            </div>


            <!-- popups -->


            <!-- Post Planned Event Modal -->
            <div id="myModal" class="modal">
                <div class="modal-content">
                    <span class="close">&times;</span>
                    <form action="../forms/PlannedEvent.php" method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="eventName">Event Name:</label>
                            <input type="text" id="eventName" name="eventName" required />
                        </div>
                        <div class="form-group">
                            <label for="eventImage">Event Image:</label>
                            <input type="file" id="eventImage" name="eventImage" required />
                        </div>
                        <div class="form-group">
                            <label for="eventDescription">Brief Introduction:</label>
                            <textarea id="eventDescription" name="eventDescription" rows="4" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="eventLocation">Location:</label>
                            <input type="text" id="eventLocation" name="eventLocation" required />
                        </div>
                        <div class="form-group">
                            <label for="eventDate">Date:</label>
                            <input type="date" id="eventDate" name="eventDate" required />
                        </div>
                        <div class="form-group">
                            <button type="submit">Save Event</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Past Events Modal -->

            <div id="pastEventModal" class="past-event-modal">
                <div class="past-event-modal-content">
                    <span class="close-past-event">&times;</span>
                    <form id="pastEventForm" action="../forms/pastEvent.php" method="post"
                        enctype="multipart/form-data">
                        <div class="past-event-form-group">
                            <label for="pastEventName">Event Name:</label>
                            <input type="text" id="pastEventName" name="eventName" required />
                        </div>

                        <div class="past-event-form-group">
                            <label for="pastEventLocation">Event Details</label>
                            <textarea style="height: 200px; padding: 10px; " name="eventDetails"
                                id="pastEventDetailsEditor"></textarea>
                        </div>

                        <!-- <div class="past-event-form-group">
                            <label for="pastEventDetails">Event Details:</label>
                            <div id="pastEventDetailsEditor" style="height: 200px"></div>
                            <input type="hidden" id="pastEventDetailsHidden" name="eventDetails" />
                        </div> -->

                        <div class="past-event-form-group">
                            <label for="pastEventLocation">Location:</label>
                            <input type="text" id="pastEventLocation" name="eventLocation" required />
                        </div>
                        <div class="past-event-form-group">
                            <label for="pastEventDate">Date:</label>
                            <input type="date" id="pastEventDate" name="eventDate" required />
                        </div>
                        <div class="past-event-form-group">
                            <label for="pastEventImages">Event Images:</label>
                            <input type="file" id="pastEventImages" name="eventImages[]" multiple />
                        </div>
                        <div class="past-event-form-group">
                            <label for="pastEventDocuments">Event Documents:</label>
                            <input type="file" id="pastEventDocuments" name="eventDocuments[]" multiple />
                        </div>
                        <div class="past-event-form-group">
                            <button type="submit">Save Past Event</button>
                        </div>
                    </form>
                </div>
            </div>



            <!-- Message Popup -->
            <div class="message-popup" id="messagePopup">
                <div class="message-popup-content">
                    <button class="message-close-btn" id="messageClosePopupBtn">
                        &times;
                    </button>
                    <h2>Send a Message</h2>
                    <form id="messageForm" action="send_message.php" method="post">
                        <div class="message-form-group">
                            <label for="messageSenderName">Your Name:</label>
                            <input type="text" id="messageSenderName" name="sender_name" required />
                        </div>
                        <div class="message-form-group">
                            <label for="messageSenderEmail">Your Email:</label>
                            <input type="email" id="messageSenderEmail" name="sender_email" required />
                        </div>
                        <div class="message-form-group">
                            <label for="messageRecipient">Recipient:</label>
                            <select id="messageRecipient" name="recipient" required>
                                <option value="all_members">All Members</option>
                                <option value="officials_only">Officials Only</option>
                            </select>
                        </div>
                        <div class="message-form-group">
                            <label for="messageSubject">Subject:</label>
                            <input type="text" id="messageSubject" name="subject" required />
                        </div>
                        <div class="message-form-group">
                            <label for="messageContent">Message:</label>
                            <div id="messageContent" class="quill-editor-container">
                                <!-- Quill Editor -->
                            </div>
                            <input type="hidden" name="message" id="messageContentHidden" />
                        </div>
                        <button type="submit" class="message-submit-btn">Send Message</button>
                    </form>
                </div>
            </div>

            <!-- Blog Post Modal -->
            <div id="blogPostModal" class="blog-post-modal">
                <div class="blog-post-modal-content">
                    <span class="close-blog-post">&times;</span>
                    <form id="blogPostForm" action="save_blog_post.php" method="post" enctype="multipart/form-data">
                        <div class="blog-post-form-group">
                            <label for="blogTitle">Blog Title:</label>
                            <input type="text" id="blogTitle" name="blogTitle" required />
                        </div>
                        <div class="blog-post-form-group">
                            <label for="blogContent">Content:</label>
                            <div style="height: 200px;" id="blogContentEditor"></div>
                            <input type="hidden" name="blogContent" id="blogContent" required />
                        </div>
                        <div class="blog-post-form-group">
                            <label for="blogImage">Upload Image:</label>
                            <input type="file" id="blogImage" name="blogImage" accept="image/*" />
                        </div>
                        <div class="blog-post-form-group">
                            <button type="submit">Post Blog</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- JavaScript Files -->


            <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>

            <script src="../assets/JS/quill.min.js"></script>
            <script src="../assets/JS/popups.js"></script>

            <script>
                // Initialize Quill editor
                var quill = new Quill('#pastEventDetailsEditor', {
                    theme: 'snow'
                });

                // Handle form submission
                document.getElementById('pastEventForm').onsubmit = function () {
                    // Set hidden input value to the Quill editor content
                    document.getElementById('pastEventDetailsHidden').value = quill.root.innerHTML;
                };
            </script>



            <?php
            // Close the database connection
            // $conn->close();
            ?>
            <h4 style="margin: 20px;">past Events</h4>


            <?php
            // Step 2: Fetch Data from the Database
            $sql = "SELECT * FROM pastevents";
            $result = $conn->query($sql);

            // Step 3: Display Data in HTML
            if ($result->num_rows > 0) {
                echo '<div class="MinPrtSecSpace">
        <div class="table-card">';
                while ($row = $result->fetch_assoc()) {
                    // Decode JSON if needed
                    $imagePathsJson = $row["event_image_paths"];
                    $imagePathsArray = json_decode($imagePathsJson, true);

                    // Ensure the decoding was successful and the array is not empty
                    if (is_array($imagePathsArray) && !empty($imagePathsArray)) {
                        $imagePath = htmlspecialchars($imagePathsArray[0]); // Get the first image path
                    } else {
                        $imagePath = ''; // Default to empty if no valid path found
                    }

                    echo '<div class="eventDiv">
            <h3>' . htmlspecialchars($row["event_name"]) . '</h3>
            <img src="' . $imagePath . '" alt="Event">
            <p>' . htmlspecialchars($row["event_details"]) . '</p>
            <div class="eventDivindiv">
                <p>' . htmlspecialchars($row["event_location"]) . '</p>
                <p>' . htmlspecialchars($row["event_date"]) . '</p>
            </div>
        </div>';
                }
                echo '  </div>
    </div>';
            } else {
                echo "0 results";
            }

            // Close connection
            $conn->close();
            ?>


        </section>

    </div>

    <footer style="font-size: 11px; font-style: italic; text-align: center; background-color: #b4ccdb; color: black; ">
        <p>&copy; 2024 <a style="text-decoration: none;" href="AGL.or.ke">http://www.agl.or.ke/</a> . All rights reserved.</p>
    </footer>


    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggleButton = document.getElementById('toggleMenu');
            const sidebar = document.getElementById('sidebar');

            toggleButton.addEventListener('click', function () {

                if (sidebar.style.display === 'block') {
                    sidebar.style.display = 'none';
                    toggleButton.innerHTML = '☰';

                } else {
                    sidebar.style.display = 'block';
                    toggleButton.innerHTML = 'X';
                    toggleButton.style.fontWeight = 'bold';
                    toggleButton.style.color = 'black';

                }
            });
        });
    </script>



</body>

</html>