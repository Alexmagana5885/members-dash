<?php
session_start();
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member Dashboard</title>
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
                <li><a href="https://www.agl.or.ke/about-us/ ">About</a></li>
                <li><a href="">Messages</a></li>
                <li><a href="https://www.agl.or.ke/contact-us/">Contact</a></li>
            </ul>
            <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
        </nav>



        <section class="dashboard">
            <div class="cards">

                <?php
                require_once('../forms/DBconnection.php');
                

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
                    <p id="memberpayments-current-lastPay">Last payment: <span>12/08/2024</span></p><br>
                    <p id="memberpayments-current-nextP">Next payment: <span>09/05/2024</span></p><br>
                    <p id="memberpayments-current-balance">Current balance: <span>5000sh</span></p><br>
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
                    </p><br>
                    <p>Institution: <span
                            id="institution"><?php echo htmlspecialchars($educationInfo['institution']); ?></span></p><br>
                    <p>Start Date: <span
                            id="start-date"><?php echo htmlspecialchars($educationInfo['start_date']); ?></span></p><br>
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


            <!-- blogs -->
            <!-- delete the stylesheet -->
            <style>
                .blogPoint {
                    width: 100%;
                    background-color: #fff;
                    min-height: 200px;
                    padding: 10px;
                    border-radius: 10px;
                    display: grid;
                    grid-auto-flow: column;
                    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
                    gap: 10px;
                    text-align: center;
                    max-height: 600px;
                    overflow-x: auto;
                    overflow-y: hidden;
                    scrollbar-width: thin;
                }

                .Singleblog {
                    width: 100%;
                    margin-right: 10px;
                    display: flex;
                    flex-direction: column;
                    padding: 10px;
                    margin-bottom: 20px;
                }

                .blogImage {
                    width: 100%;
                    height: 200px;
                    margin-bottom: 5px;
                    border-radius: 20px 0 50px 0;
                    object-fit: cover;
                   
                }


                .blogImage img {
                    width: 100%;
                    border-radius: 20px 0 50px 0;
                    object-fit: cover;
                }

                .blogcontent {
                    width: 100%;
                }

                .blogcontent p {
                    overflow: auto;
                    scrollbar-width: thin;
                    max-height: 200px;
                    padding: 5px;
                    margin: 10px;
                    text-align: start;
                }

                @media screen and (max-width: 600px) {
                    .blogPoint {
                        grid-template-columns: repeat(auto-fill, minmax(100%, 1fr));
                        max-height: 600px;
                        overflow-x: auto;
                    }

                    .Singleblog {
                        width: 100%;
                        margin-bottom: 10px;
                    }

                    .blogImage {
                        width: 100%;
                        margin-bottom: 0;
                    }

                    .blogcontent {
                        width: 100%;
                    }
                }
            </style>

            <h4 style=" padding: 20px; " >Blogs</h4>

            <div class="blogPoint">

                <?php
                // Query to get blog posts
                $sql = "SELECT * FROM blog_posts";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    // Output data of each row
                    while ($row = $result->fetch_assoc()) {
                        echo '<div class="Singleblog">';
                        echo '    <div class="blogImage"><img src="' . $row["image_path"] . '" alt="Blog"></div>';
                        echo '    <div class="blogcontent">';
                        echo '        <h4>' . $row["title"] . '</h4>';
                        echo '        <p>' . $row["content"] . '</p>';
                        echo '        <h6>' . date("d/m/Y", strtotime($row["created_at"])) . '</h6>';
                        echo '    </div>';
                        echo '</div>';
                    }
                } else {
                    echo "0 results";
                }
                // $conn->close();
                ?>
            </div>

            <!-- .............................. -->

            <!-- messages -->

            <?php



            $messages = [];

            require_once('../forms/DBconnection.php');

            // Prepare the query to fetch all messages from the membermessages table
            $messageQuery = "
                SELECT subject, message, date_sent 
                FROM membermessages 
                ORDER BY date_sent DESC";

            // Prepare and execute the statement
            $stmt = $conn->prepare($messageQuery);
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