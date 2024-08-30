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

        <div id="toggleMessages" class="notification">
            <img src="../assets/img/bell.png" alt="Notification">
        </div>

    </header>

    <div class="main-content">

        <nav style="color: black" id="sidebar" class="sidebar">
            <ul>
                <li>
                    <a href="#hero" class="active">Home<br /></a>
                </li>

                <!-- Admin dropdown menu -->
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle">Admin</a>
                    <ul class="dropdown-menu">
                        <li><a id="openPostEventModal">Post Up coming Event</a></li>
                        <li><a id="openPastEventModal">Post past Event</a></li>
                        <li><a id="openMessagePopup">Send Message</a></li>
                        <li><a href="admin/settings.html">Admit New Members</a></li>
                        <li><a id="openBlogPostModal">Post a Blog</a></li>
                        <li><a href="new.html">new</a></li>
                    </ul>
                </li>
                <li><a href="#about">About</a></li>
                <li><a href="#team">Officials</a></li>
                <li><a href="pages/newfile.html">Donations</a></li>
                <li><a href="Payment/index.php">Payments</a></li>
                <li><a href="#contact">Contact</a></li>
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
} else {
    echo "No user found";
}

$stmt->close();
?>

                <?php

                ?>
<div class="card">
    <img class="cardMemberprofile" src="<?php echo htmlspecialchars($passportImage); ?>" alt="User Image">
    <h5><?php echo htmlspecialchars($name); ?></h5>
    <p>Registration Date: <?php echo htmlspecialchars($registrationDate); ?></p>
</div>




                <div class="card">
                    <h5>Member Payments</h5>
                    <hr>
                    <p id="makememberpaymentscurrentlastPay">Last payment: <span>12/08/2024</span></p>
                    <p id="makememberpaymentscurrentnextP">Next payment: <span>09/05/2024</span></p>
                    <p id="makememberpaymentscurrent">Current balance: <span>5000sh</span></p>
                    <button id="makememberpaymentsbtn">Make Payments</button>
                </div>



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
                    <h4>Upcoming Events</h4>
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


            <!-- members information -->


            <?php

            // Query to select data from the personalmembership table
            $sql = 'SELECT * FROM personalmembership';
            $result = $conn->query($sql);

            // Check if query execution was successful
            if (!$result) {
                die("Query failed: " . $conn->error);
            }
            ?>

            <!DOCTYPE html>
            <html lang="en">

            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Members Information</title>
                <style>
                    table {
                        width: 100%;
                        border-collapse: collapse;
                    }

                    th,
                    td {
                        border: 1px solid #ddd;
                        padding: 8px;
                    }

                    th {
                        background-color: #f2f2f2;
                    }

                    .card_table {
                        margin: 20px 0;
                    }
                </style>
            </head>

            <body>
                <div style="margin-top: 20px;" class="MinPrtSecSpace">
                    <h3>Members Information</h3><br>
                    <div class="card_table">
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
            </body>

            </html>

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
// $conn->close();
            ?>


        </section>

    </div>

    <footer>
        <div class="footer-container">
            <div class="footer-section contacts">
                <h4>Contacts</h4>
                <p>Email: contact@yourcompany.com</p>
                <p>Phone: +1234567890</p>
            </div>
            <div class="footer-section quick-links">
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="#">Home</a></li>
                    <li><a href="#">About Us</a></li>
                    <li><a href="#">Services</a></li>
                    <li><a href="#">Contact</a></li>
                </ul>
            </div>
            <div class="footer-section map">
                <h4>Our Location</h4>
                <!-- Replace with your map embed code -->

                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d15955.934720808227!2d36.821946!3d-1.292066!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x182f10d64f24b567%3A0x8c1ecdb58d69d0a5!2sNairobi%2C%20Kenya!5e0!3m2!1sen!2sus!4v1676961268712!5m2!1sen!2sus"
                    width="390" height="150" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
            </div>
        </div>
        <p>&copy; 2024 Your Company</p>
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