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

    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
</head>


<style>
    /* Style for the dropdown container */
    .dropdown {
        position: relative;
    }

    /* Style for the dropdown link */
    .dropdown-toggle {
        cursor: pointer;
    }

    /* Hide the dropdown menu initially */
    .dropdown-menu {
        display: none;
        position: absolute;
        top: 100%;
        left: 0;
        background-color: #303538;
        padding: 10px;
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        border-radius: 7px;
    }

    /* Display the dropdown menu on hover */
    .dropdown:hover .dropdown-menu {
        display: block;
    }

    /* Style for dropdown links */
    .dropdown-menu li a {
        color: #fff;
        text-decoration: none;
        display: block;
        padding: 5px 0;
    }
</style>

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

            </div>


            <!-- planned ivents -->


            <div class="MinPrtSecSpace">
                <h3>planned Events</h3>
                <div class="table-card">

                    <div class="eventDiv">
                        <h3>Event</h3>
                        <img src="../assets/img/DemoImage/LoginImage.jpg" alt="Event">
                        <p>Breaf introduction: Connect to the Database: First, establish a connection to your database
                            using PHP. You can use mysqli or PDO. Below is an example using mysqli</p>
                        <div class="eventDivindiv">
                            <p>location</p>
                            <button class="plannedEventsBTN" id="plannedEventsBTN">Edit</button>
                            <p>Date</p>

                        </div>

                    </div>
                </div>


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
            <!-- <div id="pastEventModal" class="past-event-modal">
                <div class="past-event-modal-content">
                    <span class="close-past-event">&times;</span>
                    <form id="pastEventForm" action="../forms/pastEvent.php" method="post" enctype="multipart/form-data">
                        <div class="past-event-form-group">
                            <label for="pastEventName">Event Name:</label>
                            <input type="text" id="pastEventName" name="eventName" required />
                        </div>
                        <div class="past-event-form-group">
                            <label for="pastEventDetails">Event Details:</label>
                            <div id="pastEventDetails" name="eventDetails" style="height: 200px">
                                
                            </div>
                        </div>
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
            </div> -->

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
                            <label for="pastEventDetails">Event Details:</label>
                            <div id="pastEventDetailsEditor" style="height: 200px"></div>
                            <input type="hidden" id="pastEventDetailsHidden" name="eventDetails" />
                        </div>

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


            <!-- <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script> -->

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

            <div style="margin-top: 20px;" class="MinPrtSecSpace">
                <h3>Members information</h3><br>
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
                            <tr>
                                <td>1</td>
                                <td>John Doe</td>
                                <td>+123456789</td>
                                <td>john.doe@example.com</td>
                                <td>Manager</td>
                                <td>Company A</td>
                                <td><a href="#">Show More</a></td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>Jane Smith</td>
                                <td>+987654321</td>
                                <td>jane.smith@example.com</td>
                                <td>Developer</td>
                                <td>Company B</td>
                                <td><a href="#">Show More</a></td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>Jane Smith</td>
                                <td>+987654321</td>
                                <td>jane.smith@example.com</td>
                                <td>Developer</td>
                                <td>Company B</td>
                                <td><a href="#">Show More</a></td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>Jane Smith</td>
                                <td>+987654321</td>
                                <td>jane.smith@example.com</td>
                                <td>Developer</td>
                                <td>Company B</td>
                                <td><a href="#">Show More</a></td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>Jane Smith</td>
                                <td>+987654321</td>
                                <td>jane.smith@example.com</td>
                                <td>Developer</td>
                                <td>Company B</td>
                                <td><a href="#">Show More</a></td>
                            </tr>

                        </tbody>
                    </table>
                </div>
            </div>

            <div class="MinPrtSecSpace">
                <div class="table-card">

                    <div class="eventDiv">
                        <h3>Event</h3>
                        <img src="../assets/img/DemoImage/LoginImage.jpg" alt="Event">
                        <p>Breaf introduction: Connect to the Database: First, establish a connection to your
                            database
                            using PHP. You can use mysqli or PDO. Below is an example using mysqli</p>
                        <div class="eventDivindiv">
                            <p>location</p>
                            <p>Date</p>
                        </div>

                    </div>

                    <div class="eventDiv">
                        <h3>Event</h3>
                        <img src="../assets/img/DemoImage/1-scaled.jpg" alt="Event">
                        <p>Breaf introduction: Connect to the Database: First, establish a connection to your
                            database
                            using PHP. You can use mysqli or PDO. Below is an example using mysqli</p>
                        <div class="eventDivindiv">
                            <p>location</p>
                            <p>Date</p>
                        </div>

                    </div>
                    <div class="eventDiv">
                        <h3>Event</h3>
                        <img src="../assets/img/DemoImage/about.jpg" alt="Event">
                        <p>Breaf introduction: Connect to the Database: First, establish a connection to your
                            database
                            using PHP. You can use mysqli or PDO. Below is an example using mysqli</p>
                        <div class="eventDivindiv">
                            <p>location</p>
                            <p>Date</p>
                        </div>

                    </div>
                    <div class="eventDiv">
                        <h3>Event</h3>
                        <img src="../assets/img/DemoImage/cta-bg.jpg" alt="Event">
                        <p>Breaf introduction: Connect to the Database: First, establish a connection to your
                            database
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