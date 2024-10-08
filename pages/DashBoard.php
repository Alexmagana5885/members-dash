<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/CSS/Dashboard.css">
    <link rel="stylesheet" href="../assets/CSS/AGLADMIN.css">
    <link href="../assets/img/favicon.png" rel="icon">
    <link href="../assets/img/favicon.png" rel="favicon.png">

    <style>
        /* Styles for Independent Scrolling */
        body {
            margin: 0;
            padding: 0;
            display: flex;
            height: 120%;
            /* Full viewport height */
            /* overflow: hidden; */
            /* Prevent body from scrolling */
        }

        .sidebar {
            width: 250px;
            background-color: #2c3e50;
            overflow-y: auto;
            /* Enable vertical scrolling */
            /* height: 100vh; */
            /* Full viewport height */
            position: fixed;
            /* Fixed sidebar */
            left: 0;
            top: 0;
            padding-top: 80px;
            /* Adjust padding if necessary */
            scrollbar-width: thin;

        }

        .main-content {
            margin-left: 250px;
            /* Space for sidebar */
            flex-grow: 1;
            overflow-y: auto;
            /* Enable vertical scrolling */
            /* height: 100vh; */
            /* Full viewport height */
            padding: 20px;
            padding-top: 80px;
            /* Adjust padding if necessary */
        }

        header {
            width: 100%;
            height: 70px;
            background-color: #519fff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: fixed;
            /* Fixed header */
            top: 0;
            left: 0;
            z-index: 1;
            padding: 0 20px;
        }

        footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            background-color: #f4f4f4;
            text-align: center;
            padding: 10px 0;
        }
    </style>
</head>

<body>
    <header>
        <div class="logo">
            <img src="../assets/img/logo.png" alt="AGL">
        </div>
        <div id="toggleMenu" class="menu-button" onclick="toggleMenu()">☰</div>
        <div class="notification">
            <img src="../assets/img/bell.png" alt="Notification">
        </div>
    </header>

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
            <li><a id="MembersTable-link" href="#team">Members</a></li>
            <li><a href="#team">Officials</a></li>
            <li><a href="pages/newfile.html">Donations</a></li>
            <li><a href="Payment/index.php">Payments</a></li>
            <li><a href="#contact">Contact</a></li>
            <li><a href="new.html">new</a></li>
            <li><a href="new.html">new</a></li>
            <li><a href="new.html">new</a></li>
        </ul>
        <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
    </nav>

    <div class="main-content">

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
                } else {
                    echo "No user found";
                }

                $stmt->close();
                ?>

                <?php

                ?>
                <div class="card">
                    <img class="cardMemberprofile" src="<?php echo htmlspecialchars($passportImage); ?>"
                        alt="User Image">
                    <h5><?php echo htmlspecialchars($name); ?></h5>
                    <p>Registration Date: <?php echo htmlspecialchars($registrationDate); ?></p>
                </div>




                <!-- Styles to hide the memeber payment popup by default -->

                <!-- this styking is alredy alreday transferd -->
                <style>
                    .memberpayments-popup-container {
                        display: none;
                        position: fixed;
                        z-index: 1000;
                        left: 0;
                        top: 0;
                        width: 100%;
                        height: 100vh;
                        background-color: rgba(0, 0, 0, 0.5);
                    }

                    .memberpayments-popup-content {
                        background-color: #fff;
                        margin: 8% auto;
                        padding: 20px;
                        border: 1px solid #888;
                        width: 80%;
                        max-width: 500px;
                        border-radius: 8px;
                        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
                        position: relative;
                    }

                    .memberpayments-close {
                        cursor: pointer;
                        float: right;
                        font-size: 20px;
                    }

                    .memberpayments-popup-content label {
                        display: block;
                        margin-bottom: 5px;
                        font-weight: bold;
                        color: #333;
                    }

                    .memberpayments-popup-content input[type="number"],
                    .memberpayments-popup-content input[type="text"] {
                        width: calc(100% - 20px);
                        padding: 10px;
                        margin-bottom: 15px;
                        border: 1px solid #ccc;
                        border-radius: 5px;
                        font-size: 16px;
                    }

                    .memberpayments-popup-content p {
                        color: #555;
                        margin-bottom: 20px;
                    }

                    .memberpayments-pay-buttons {
                        text-align: center;

                    }

                    .memberpayments-pay-btn {
                        background-color: #4CAF50;
                        color: white;
                        padding: 10px 20px;
                        border: none;
                        border-radius: 5px;
                        font-size: 16px;
                        cursor: pointer;
                        transition: background-color 0.3s ease;
                    }

                    .memberpayments-pay-btn:hover {
                        background-color: #45a049;
                    }
                </style>

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
                            <!-- <button class="memberpayments-cancel-btn">Cancel</button> -->
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




                <div class="card">
                    <h5>Member Activities</h5>
                    <hr>
                    <p>Registration date: <span id="registration-date">[Registration Date]</span></p>
                    <p>Last Activity attended: <span id="last-activity">[Last Activity]</span></p>
                    <p>Next Activity: <span id="next-activity">[Next Activity]</span></p>
                </div>


                <?php


                //  upcoming events
                $sql = "SELECT event_name, event_image_path, event_location, event_date FROM plannedevent ORDER BY event_date ASC";
                $result = $conn->query($sql);

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
                        echo '<p>No upcoming events.</p>';
                    }
                    // $conn->close();
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





            <!-- ............................ -->
            <!-- the style transferd -->
            <style>
                .popup-table {
                    background-color: #fff;
                    border: 1px solid #fff;
                    border-radius: 8px;
                    max-height: 80vh;
                    display: none;
                    overflow: auto;
                }

                .popup-content-table {
                    background-color: #fefefe;
                    padding: 5px;
                    border: 1px solid #fff;
                    border-radius: 5px;
                    width: 100%;


                }

                .close-btn-table {
                    color: #aaa;
                    float: right;
                    font-size: 28px;
                    font-weight: bold;
                    cursor: pointer;
                }

                .close-btn-table:hover,
                .close-btn-table:focus {
                    color: black;
                    text-decoration: none;
                    cursor: pointer;
                }
            </style>

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

            <script>
                // Get the popup element
                var popup = document.getElementById("MemberDISTablePopup-table");
                var openPopupLink = document.getElementById("MembersTable-link");
                var closeBtn = document.getElementsByClassName("close-btn-table")[0];

                openPopupLink.onclick = function (event) {
                    event.preventDefault();
                    popup.style.display = "flex";
                }

                closeBtn.onclick = function () {
                    popup.style.display = "none";
                }
                window.onclick = function (event) {
                    if (event.target == popup) {
                        popup.style.display = "none";
                    }
                }
            </script>


            <!-- planned ivents -->

            <?php

            // Fetch data from the database
            $sql = "SELECT id, event_name, event_image_path, event_description, event_location, event_date FROM plannedevent";
            $result = $conn->query($sql);

            // Start outputting HTML
            ?>
            <div class="MinPrtSecSpace">
                <h3 style="padding:20px ">Planned Events</h3>
                <div class="table-card">
                    <?php
                    if ($result->num_rows > 0) {
                        // Output data for each row
                        while ($row = $result->fetch_assoc()) {
                            echo '<div class="eventDiv">';
                            echo '<h3>' . htmlspecialchars($row['event_name']) . '</h3>';
                            echo '<img src="' . htmlspecialchars($row['event_image_path']) . '" alt="Event">';
                            echo '<p>' . htmlspecialchars($row['event_description']) . '</p>';
                            echo '<div class="eventDivindiv">';
                            echo '<p>' . htmlspecialchars($row['event_location']) . '</p>';
                            echo '<button class="plannedEventsBTN" id="plannedEventsBTN">Edit</button>';
                            echo '<p>' . htmlspecialchars($row['event_date']) . '</p>';
                            echo '</div>';
                            echo '</div>';
                        }
                    } else {
                        echo '<p>No events found.</p>';
                    }
                    ?>
                </div>
            </div>

            <?php
            ?>


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


    <footer style="color: #2c3e50; font-size: 10px; font-style: italic; " >

        <p>&copy; 2024 Your AGL.or.ke</p>
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