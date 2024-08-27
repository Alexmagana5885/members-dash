<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/CSS/Dashboard.css">

    <link href="../assets/img/favicon.png" rel="icon">
    <link href="../assets/img/favicon.png" rel="favicon.png">
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

    <div class="main-content">

        <nav style="color: #519fff" id="sidebar" class="sidebar">
            <ul>
                <li>
                    <a href="#hero" class="active">Home<br /></a>
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

                <!-- <div class="card">
                    <?php
                    // Include the PHP script to fetch user data
                    // include 'fetch_user_info.php';
                    
                    // if ($user) {
                    //     echo '<img src="' . htmlspecialchars($user['image']) . '" alt="User Image">';
                    //     echo '<h2>' . htmlspecialchars($user['name']) . '</h2>';
                    //     echo '<p>Membership Type: ' . htmlspecialchars($user['membership_type']) . '</p>';
                    //     echo '<p>Registration Date: ' . htmlspecialchars($user['registration_date']) . '</p>';
                    // } else {
                    //     echo '<p>No user information available.</p>';
                    // }
                    
                    // </div> -->
                    



                    //                 $sql = "SELECT last_payment_date, current_balance FROM payments WHERE member_id = 1"; 
// $result = $conn->query($sql);
                    
                    // if ($result->num_rows > 0) {
                    
                    //     while($row = $result->fetch_assoc()) {
//         $lastPaymentDate = $row["last_payment_date"];
//         $currentBalance = $row["current_balance"];
                    

                    //         $nextPaymentDate = date('d/m/Y', strtotime($lastPaymentDate . ' +1 year'));
                    
                    //         echo '<div class="card">
//                 <h5>Member Payments</h5>
//                 <hr>
//                 <p id="makememberpaymentscurrentlastPay">Last payment: ' . htmlspecialchars($lastPaymentDate) . '</p>
//                 <p id="makememberpaymentscurrentnextP">Next payment: ' . htmlspecialchars($nextPaymentDate) . '</p>
//                 <p id="makememberpaymentscurrent">Current balance: ' . htmlspecialchars($currentBalance) . 'sh</p>
//                 <button id="makememberpaymentsbtn">Make Payments</button>
//             </div>';
// } else {
//     echo "0 results";
// }
                    
                    // ?>
</div> -->


                <!-- <div class="card">
                    <h5>member payments</h5>
                    <hr>
                    <p id="makememberpaymentscurrentlastPay" >last payment: 12/08/2024</p>
                    <p id="makememberpaymentscurrentnextP" >next payment: 09/5/2024</p>
                    <p id="makememberpaymentscurrent" >current balance: 5000sh</p>
                    <button id="makememberpaymentsbtn" >make payments</button>

                </div> -->


                <?php


                // $sql = "SELECT registration_date, last_activity, next_activity FROM member_activities WHERE member_id = ?"; // Adjust query as needed
// $stmt = $conn->prepare($sql);
                
                // $member_id = 1; 
// $stmt->bind_param("i", $member_id);
                
                // $stmt->execute();
                
                // $result = $stmt->get_result();
                
                // $data = $result->fetch_assoc();
                

                ?>

                <!-- <div class="card">
    <h5>Member Activities</h5>
    <hr>
    <p>Registration date: <?php echo htmlspecialchars($data['registration_date']); ?></p>
    <p>Last Activity attended: <?php echo htmlspecialchars($data['last_activity']); ?></p>
    <p>Next Activity: <?php echo htmlspecialchars($data['next_activity']); ?></p>
</div> -->
                <!-- ,...................................................... -->
                <!-- Member Profile Card (Commented Out) -->



                <div class="card">
                    <img class="cardMemberprofile" src="../assets/img/DemoImage/my-profile-img.jpeg" alt="User Image">
                    <h5>[User Name]</h5>
                    <p>Membership Type: [Membership Type]</p>
                    <p>Registration Date: [Registration Date]</p>
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

                <div class="card">
                    <h4>Up coming Events</h4>
                    <hr>

                    <div>
                        <h5>Event Heading</h5>
                        <p>theme of the Event</p>
                        <p>Location</p>
                        <p>date</p>
                    </div>
                    <hr>

                    <div>
                        <h5>Event Heading</h5>
                        <p>theme of the Event</p>
                        <p>Location</p>
                        <p>date</p>
                    </div>
                    <hr>

                    <div>
                        <h5>Event Heading</h5>
                        <p>theme of the Event</p>
                        <p>Location</p>
                        <p>date</p>
                    </div>
                    <hr>
                    <div>
                        <h5>Event Heading</h5>
                        <p>theme of the Event</p>
                        <p>Location</p>
                        <p>date</p>
                    </div>
                    <hr>
                    <div>
                        <h5>Event Heading</h5>
                        <p>theme of the Event</p>
                        <p>Location</p>
                        <p>date</p>
                    </div>
                    <hr>

                </div>


                <!-- ........................................... -->


                <!-- <div class="card">Card 1</div>
                <div class="card">Card 2</div>
                <div class="card">Card 3</div>
                <div class="card">Card 4</div> -->
            </div>

            <div class="MinPrtSecSpace">
                <div class="table-card">

                    <div class="eventDiv">
                        <h3>Event</h3>
                        <img src="../assets/img/DemoImage/LoginImage.jpg" alt="Event">
                        <p>Breaf introduction: Connect to the Database: First, establish a connection to your database
                            using PHP. You can use mysqli or PDO. Below is an example using mysqli</p>
                        <div class="eventDivindiv">
                            <p>location</p>
                            <p>Date</p>
                        </div>

                    </div>

                    <div class="eventDiv">
                        <h3>Event</h3>
                        <img src="../assets/img/DemoImage/1-scaled.jpg" alt="Event">
                        <p>Breaf introduction: Connect to the Database: First, establish a connection to your database
                            using PHP. You can use mysqli or PDO. Below is an example using mysqli</p>
                        <div class="eventDivindiv">
                            <p>location</p>
                            <p>Date</p>
                        </div>

                    </div>
                    <div class="eventDiv">
                        <h3>Event</h3>
                        <img src="../assets/img/DemoImage/about.jpg" alt="Event">
                        <p>Breaf introduction: Connect to the Database: First, establish a connection to your database
                            using PHP. You can use mysqli or PDO. Below is an example using mysqli</p>
                        <div class="eventDivindiv">
                            <p>location</p>
                            <p>Date</p>
                        </div>

                    </div>
                    <div class="eventDiv">
                        <h3>Event</h3>
                        <img src="../assets/img/DemoImage/cta-bg.jpg" alt="Event">
                        <p>Breaf introduction: Connect to the Database: First, establish a connection to your database
                            using PHP. You can use mysqli or PDO. Below is an example using mysqli</p>
                        <div class="eventDivindiv">
                            <p>location</p>
                            <p>Date</p>
                        </div>

                    </div>


                </div>


            </div>


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