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

<body>
    <header>
        <div class="logo">
            <img src="../assets/img/logo.png" alt="AGL">
        </div>
        <div id="toggleMenu" class="menu-button" onclick="toggleMenu()">☰</div>
        <!-- <button id="toggleMessages">View Messages</button> -->

        <div id="toggleMessagesReceivedMessages" class="notification">
            <img src="../assets/img/bell.png" alt="Notification">
            <h5 style="color: black; cursor: pointer;">Messages</h5>
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
                <li><a id="openMessagePopupSend">Send Message</a></li>
                <li><a href="admin/settings.html">Admit New Members</a></li>
                <li><a id="openBlogPostModal">Post a Blog</a></li>
                <li><a href="#https://www.agl.or.ke/">About</a></li>
                <li><a id="MembersTable-link" href="Members.php">Members</a></li>
                <!-- <li><a href="pages/newfile.html">Donations</a></li> -->
                <li><a href="Payment/index.php">Payments</a></li>
                <li><a href="#https://www.agl.or.ke/Contact">Contact</a></li>
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
                $query = "SELECT highest_degree, institution, start_date, graduation_year FROM personalmembership WHERE email = ?";
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
                // session_start();
                
                if (!isset($_SESSION['user_email'])) {
                    die("User not logged in");
                }

                $userEmail = $_SESSION['user_email'];

                // Debugging session email
                

                // Prepare the SQL query
                $sql = "SELECT event_name, event_location, event_date FROM event_registrations WHERE member_email = ? ORDER BY event_date ASC";
                $stmt = $conn->prepare($sql);

                if ($stmt === false) {
                    die('Prepare failed: ' . htmlspecialchars($conn->error));
                }

                $stmt->bind_param("s", $userEmail);

                if (!$stmt->execute()) {
                    die('Execute failed: ' . htmlspecialchars($stmt->error));
                }

                $resultmessage = $stmt->get_result();

                if ($resultmessage === false) {
                    die('Get result failed: ' . htmlspecialchars($stmt->error));
                }

                ?>
                <div class="card">
                    <h4>Registered Events</h4>

                    <hr>

                    <?php
                    if ($resultmessage->num_rows > 0) {
                        while ($row = $resultmessage->fetch_assoc()) {
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



                    // $stmt->close();
                    // $conn->close();
                    ?>
                </div>



            </div>
            <!-- ................................... -->

            <!-- messages -->

            <?php

                $user_email = $_SESSION['user_email'];
                $messages = [];

                require_once('../forms/DBconnection.php');

                // Check if the user is an official member
                $officialQuery = "SELECT * FROM officialsmembers WHERE personalmembership_email = ?";
                $stmt = $conn->prepare($officialQuery);
                $stmt->bind_param("s", $user_email);
                $stmt->execute();
                $officialResult = $stmt->get_result();

                // Prepare the appropriate message query
                if ($officialResult->num_rows > 0) {
                    // The user is an official member; fetch messages from both tables
                    $messageQuery = "
                    SELECT subject, message, date_sent FROM membermessages WHERE recipient_group = 'all'
                    UNION ALL
                    SELECT subject, message, date_sent FROM officialmessages WHERE recipient_group = 'officials' OR recipient_group = ?
                    ORDER BY date_sent DESC";
                } else {
                    // The user is not an official member; fetch messages only from the membermessages table
                    $messageQuery = "SELECT subject, message, date_sent FROM membermessages WHERE recipient_group = 'all' OR recipient_group = ? ORDER BY date_sent DESC";
                }

                // Prepare the statement for fetching messages
                $stmt = $conn->prepare($messageQuery);
                $stmt->bind_param("s", $user_email);
                $stmt->execute();
                $result = $stmt->get_result();

                // Store the fetched messages in an array
                while ($row = $result->fetch_assoc()) {
                    $messages[] = $row;
                }

                // Close the statement and connection
                // $stmt->close();
                // $conn->close();
            ?>

            <div class="message-popup" id="messagePopupReceivedMessages">
                <div class="message-popup-header">
                    <h4>Messages</h4>
                    <button id="closePopupReceivedMessages">&times;</button>
                </div>
                <div class="message-container">
                    <?php if (empty($messages)): ?>
                        <p>No messages found.</p>
                    <?php else: ?>
                        <?php foreach ($messages as $message): ?>
                            <div class="message"
                                onclick="showFullMessageReceivedMessages('<?php echo htmlspecialchars($message['message'], ENT_QUOTES); ?>')">
                                <p class="message-content"><?php echo htmlspecialchars($message['subject'], ENT_QUOTES); ?></p>
                                <span class="message-time"><?php echo date("h:i A", strtotime($message['date_sent'])); ?></span>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>


            <!-- <script>
                function showFullMessageReceivedMessages(messageContent) {
                    alert(messageContent); 
                }
            </script> -->




            <!-- Full message pop-up -->
            <div class="full-message-popup" id="fullMessagePopupReceivedMessages">
                <div class="full-message-content">
                    <button id="closeFullMessageReceivedMessages">&times;</button>
                    <p id="fullMessageTextReceivedMessages"></p>
                </div>
            </div>
            <!-- Full message pop-up script -->
            <script>
                document.getElementById('toggleMessagesReceivedMessages').addEventListener('click', function () {
                    document.getElementById('messagePopupReceivedMessages').style.display = 'flex';
                });

                document.getElementById('closePopupReceivedMessages').addEventListener('click', function () {
                    document.getElementById('messagePopupReceivedMessages').style.display = 'none';
                });

                function showFullMessageReceivedMessages(message) {
                    document.getElementById('fullMessageTextReceivedMessages').textContent = message;
                    document.getElementById('fullMessagePopupReceivedMessages').style.display = 'flex';
                }

                document.getElementById('closeFullMessageReceivedMessages').addEventListener('click', function () {
                    document.getElementById('fullMessagePopupReceivedMessages').style.display = 'none';
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
            <!-- Post Planned Event Modal  script-->
            <script>

                var modal = document.getElementById("myModal");

                var openModalBtn = document.getElementById("openPostEventModal");

                var closeBtn = document.getElementsByClassName("close")[0];

                openModalBtn.onclick = function (event) {
                    event.preventDefault();
                    modal.style.display = "block";
                };

                closeBtn.onclick = function () {
                    modal.style.display = "none";
                };

                window.onclick = function (event) {
                    if (event.target == modal) {
                        modal.style.display = "none";
                    }
                };
            </script>

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
            <!-- Past Events Modal script-->
            <script>
                var modal = document.getElementById("pastEventModal");
                var btn = document.getElementById("openPastEventModal");
                var span = document.getElementsByClassName("close-past-event")[0];
                btn.onclick = function () {
                    modal.style.display = "block";
                }
                span.onclick = function () {
                    modal.style.display = "none";
                }
                window.onclick = function (event) {
                    if (event.target == modal) {
                        modal.style.display = "none";
                    }
                }
            </script>

            <!-- delete the style alredy delete -->
            <style>
                .message-popup-sendMessage {
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background-color: rgba(0, 0, 0, 0.6);
                    display: none;
                    justify-content: center;
                    align-items: center;
                    z-index: 1000;
                }

                .message-popup-content-sendMessage {
                    background-color: #ffffff;
                    width: 90%;
                    max-width: 500px;
                    padding: 20px;
                    border-radius: 10px;
                    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                    position: relative;
                }

                .message-close-btn-sendMessage {
                    position: absolute;
                    top: 10px;
                    right: 10px;
                    font-size: 24px;
                    background: none;
                    border: none;
                    cursor: pointer;
                }

                .message-form-group-sendMessage {
                    margin-bottom: 15px;
                }

                .message-form-group-sendMessage input,
                .message-form-group-sendMessage select {
                    width: 100%;
                    padding: 8px;
                    border: 1px solid #ddd;
                    border-radius: 4px;
                    margin-top: 5px;
                    font-size: 16px;
                }

                .quill-editor-container-sendMessage {
                    border: 1px solid #ddd;
                    border-radius: 4px;
                    margin-top: 5px;
                    height: 150px;
                }

                .message-submit-btn-sendMessage {
                    width: 100%;
                    padding: 10px;
                    background-color: #4CAF50;
                    color: white;
                    border: none;
                    border-radius: 4px;
                    cursor: pointer;
                    font-size: 16px;
                }

                .message-submit-btn-sendMessage:hover {
                    background-color: #45a049;
                }

                @media screen and (max-width: 600px) {
                    .message-popup-content-sendMessage {
                        width: 90%;
                        padding: 15px;
                    }
                }
            </style>

            <!-- send Message Popup -->
            <div class="message-popup-sendMessage" id="messagePopupsend">
                <div class="message-popup-content-sendMessage">
                    <button class="message-close-btn-sendMessage" id="messageClosePopupBtn">
                        &times;
                    </button>
                    <h2>Send a Message</h2>
                    <form id="messageFormSendMessage" action="../forms/send_message.php" method="post">
                        <div class="message-form-group-sendMessage">
                            <label for="messageSenderName">Your Name:</label>
                            <input type="text" id="messageSenderName" name="sender_name" required />
                        </div>
                        <div class="message-form-group-sendMessage">
                            <label for="messageSenderEmail">Your Email:</label>
                            <input type="email" id="messageSenderEmail" name="sender_email"
                                value="maganaalex634@gmail.com" required readonly />
                        </div>

                        <div class="message-form-group-sendMessage">
                            <label for="messageRecipient">Recipient:</label>
                            <select id="messageRecipient" name="recipient" required>
                                <option value="all_members">All Members</option>
                                <option value="officials_only">Officials Only</option>
                            </select>
                        </div>
                        <div class="message-form-group-sendMessage">
                            <label for="messageSubject">Subject:</label>
                            <input type="text" id="messageSubject" name="subject" required />
                        </div>
                        <div class="message-form-group-sendMessage">
                            <label for="messageContent">Message:</label>
                            <textarea style="width: 100%; min-height: 200px; padding: 5px; " name="message"
                                id="sendmessageContent"></textarea>
                        </div>
                        <button type="submit" class="message-submit-btn-sendMessage">Send Message</button>
                    </form>
                </div>
            </div>

            <!-- Message Popup script-->
            <script>
                function showMessagePopup() {
                    document.getElementById('messagePopupsend').style.display = 'flex';
                }
                function hideMessagePopup() {
                    document.getElementById('messagePopupsend').style.display = 'none';
                }

                document.getElementById('openMessagePopupSend').addEventListener('click', function (event) {
                    event.preventDefault();
                    showMessagePopup();
                });

                document.getElementById('messageClosePopupBtn').addEventListener('click', function (event) {
                    event.preventDefault();
                    hideMessagePopup();
                });

            </script>


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
            <!-- Blog Post Modal script -->
            <script>

                const blogPostModal = document.getElementById("blogPostModal");
                const openBlogPostModal = document.getElementById("openBlogPostModal");
                const closeBlogPost = document.querySelector(".close-blog-post");
                openBlogPostModal.addEventListener("click", function (event) {
                    event.preventDefault();
                    blogPostModal.style.display = "block";
                });
                closeBlogPost.addEventListener("click", function () {
                    blogPostModal.style.display = "none";
                });
                window.addEventListener("click", function (event) {
                    if (event.target == blogPostModal) {
                        blogPostModal.style.display = "none";
                    }
                });
            </script>

            <!-- JavaScript Files -->


            <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>

            <script src="../assets/JS/quill.min.js"></script>
            <script src="../assets/JS/popups.js"></script>
            <!-- // Initialize Quill editor -->
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
        <p>&copy; 2024 <a style="text-decoration: none;" href="AGL.or.ke">http://www.agl.or.ke/</a> . All rights
            reserved.</p>
    </footer>

    <!-- side bar script -->
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