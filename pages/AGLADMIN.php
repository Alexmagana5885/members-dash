<?php
session_start();

// Security headers
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("X-XSS-Protection: 1; mode=block");

if (!isset($_SESSION['loggedin']) || !$_SESSION['loggedin']) {
    header('Location: login.php');
    exit();
}

// Get the user role
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'member';

// Database connection
require_once('../forms/DBconnection.php');

// Get user information
$sessionEmail = $_SESSION['user_email'];
$membershipType = $_SESSION['membership_type'] ?? '';

// Fetch user details
$userQuery = "SELECT * FROM personalmembership WHERE email = ?";
$userStmt = $conn->prepare($userQuery);
$userStmt->bind_param("s", $sessionEmail);
$userStmt->execute();
$userResult = $userStmt->get_result();
$userData = $userResult->fetch_assoc();

if (!$userData) {
    die("User data not found!");
}

// Fetch invoices for dropdown
$invoiceQuery = "SELECT * FROM invoices WHERE user_email = ? ORDER BY id DESC";
$invoiceStmt = $conn->prepare($invoiceQuery);
$invoiceStmt->bind_param("s", $sessionEmail);
$invoiceStmt->execute();
$invoiceResult = $invoiceStmt->get_result();

// Fetch education info
$educationQuery = "SELECT highest_degree, institution, graduation_year FROM personalmembership WHERE email = ?";
$educationStmt = $conn->prepare($educationQuery);
$educationStmt->bind_param("s", $sessionEmail);
$educationStmt->execute();
$educationResult = $educationStmt->get_result();
$educationInfo = $educationResult->fetch_assoc() ?? [
    'highest_degree' => 'N/A',
    'institution' => 'N/A',
    'graduation_year' => 'N/A'
];

// Fetch registered events
$eventsQuery = "SELECT event_name, event_location, member_email, event_id, invitation_card, event_date 
                FROM event_registrations 
                WHERE member_email = ? 
                ORDER BY event_date ASC";
$eventsStmt = $conn->prepare($eventsQuery);
$eventsStmt->bind_param("s", $sessionEmail);
$eventsStmt->execute();
$eventsResult = $eventsStmt->get_result();

// Fetch latest payment
$paymentQuery = "SELECT amount, timestamp 
                 FROM member_payments 
                 WHERE member_email = ? 
                 ORDER BY timestamp DESC 
                 LIMIT 1";
$paymentStmt = $conn->prepare($paymentQuery);
$paymentStmt->bind_param("s", $sessionEmail);
$paymentStmt->execute();
$paymentStmt->bind_result($amount, $transaction_date);
$paymentStmt->fetch();
$paymentStmt->close();

// Set payment dates
if ($transaction_date) {
    $lastPaymentDate = date("d/m/Y", strtotime($transaction_date));
    $nextPaymentDate = date("d/m/Y", strtotime("+1 year", strtotime($transaction_date)));
    $currentBalance = $amount;
} else {
    $lastPaymentDate = "N/A";
    $nextPaymentDate = "N/A";
    $currentBalance = "0";
}

// Check payment info to disable button
$disablePaymentButton = false;
$paymentCheckQuery = "SELECT payment_Number, payment_code FROM personalmembership WHERE email = ?";
$paymentCheckStmt = $conn->prepare($paymentCheckQuery);
$paymentCheckStmt->bind_param("s", $sessionEmail);
$paymentCheckStmt->execute();
$paymentCheckStmt->bind_result($paymentNumberPersonal, $paymentCodePersonal);
$paymentCheckStmt->fetch();
$paymentCheckStmt->close();

if (!empty($paymentNumberPersonal) || !empty($paymentCodePersonal)) {
    $disablePaymentButton = true;
}

// Fetch blog posts
$blogQuery = "SELECT * FROM blog_posts ORDER BY created_at DESC";
$blogResult = $conn->query($blogQuery);

// Fetch planned events
$plannedEventsQuery = "SELECT id, event_name, event_image_path, event_description, event_location, event_date, RegistrationAmount 
                       FROM plannedevent 
                       ORDER BY event_date DESC";
$plannedEventsResult = $conn->query($plannedEventsQuery);

// Fetch past events
$pastEventsQuery = "SELECT * FROM pastevents ORDER BY event_date DESC";
$pastEventsResult = $conn->query($pastEventsQuery);

// Fetch messages
$messages = [];
$memberMessagesQuery = "SELECT * FROM membermessages";
$memberMessagesResult = $conn->query($memberMessagesQuery);
while ($row = $memberMessagesResult->fetch_assoc()) {
    $messages[] = $row;
}

// Check if user is also an official
$officialCheckQuery = "SELECT * FROM officialsmembers WHERE personalmembership_email = ?";
$officialCheckStmt = $conn->prepare($officialCheckQuery);
$officialCheckStmt->bind_param("s", $sessionEmail);
$officialCheckStmt->execute();
$officialCheckResult = $officialCheckStmt->get_result();
$officialMember = $officialCheckResult->fetch_assoc();

if ($officialMember) {
    $officialMessagesQuery = "SELECT * FROM officialmessages";
    $officialMessagesResult = $conn->query($officialMessagesQuery);
    while ($row = $officialMessagesResult->fetch_assoc()) {
        $messages[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <title>Admin Dashboard - Association of Government Librarians</title>
    
    <!-- Favicon -->
    <link href="../assets/img/favicon.png" rel="icon" type="image/png">
    
    <!-- Stylesheets -->
    <link rel="stylesheet" href="../assets/CSS/Dashboard.css">
    <link rel="stylesheet" href="../assets/CSS/popups.css">
    <link rel="stylesheet" href="../assets/CSS/AGLADMIN.css">
    <link rel="stylesheet" href="/assets/CSS/dashboard-styles.css">
    
    <!-- External Libraries -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet" />
    <link href="../assets/CSS/quilleditor.css" rel="stylesheet" />
</head>
<body>

    <!-- Header -->
    <header>
        <div class="header-top">
            <div class="logo">
                <img src="../assets/img/logo.png" alt="AGL Logo" aria-label="Association of Government Librarians">
            </div>
            <div id="toggleMenu" class="menu-button" aria-label="Toggle navigation menu">☰</div>
        </div>

        <div class="innerlinksNav">
            <a href="#blogPoint" class="innerlinksNav-a">Blogs</a>
            <a href="#PlannedEvents" class="innerlinksNav-a">Upcoming Events</a>
            <a href="#PastEvents" class="innerlinksNav-a">Past Events</a>
            <a href="#" class="innerlinksNav-a" id="toggleMessagesReceivedMessages">Messages</a>
        </div>
    </header>

    <!-- Invoice Form (Hidden) -->
    <form id="invoiceForm" action="../forms/Invoice.php" method="POST">
        <input type="hidden" name="user_email" id="user_email" value="<?php echo htmlspecialchars($sessionEmail); ?>">
        <input type="hidden" name="user_type" id="user_type" value="<?php echo htmlspecialchars($membershipType); ?>">
        <input type="hidden" name="date" id="date">
        <input type="hidden" name="name" id="name" value="<?php echo htmlspecialchars($userData['name']); ?>">
        <input type="hidden" name="user_id" id="user_id" value="<?php echo htmlspecialchars($userData['id']); ?>">
        <input type="hidden" name="address" id="address" value="<?php echo htmlspecialchars($userData['home_address']); ?>">
        <input type="hidden" name="phone" id="phone" value="<?php echo htmlspecialchars($userData['phone']); ?>">
    </form>

    <div class="main-content">
        <!-- Sidebar Navigation -->
        <nav id="sidebar" class="sidebar" role="navigation" aria-label="Dashboard navigation">
            <ul>
                <li>
                    <a href="https://www.agl.or.ke/" class="active core-link" aria-label="Go to homepage">
                        <i class="fas fa-home"></i> Home
                    </a>
                </li>

                <?php if ($role == 'superadmin'): ?>
                    <!-- Super Admin Links -->
                    <li><a id="openPostEventModal" class="core-link"><i class="fas fa-calendar-plus"></i> Planned Event</a></li>
                    <li><a id="openPastEventModal" class="core-link"><i class="fas fa-calendar-check"></i> Past Event</a></li>
                    <li><a id="openBlogPostModal" class="secondary-link"><i class="fas fa-blog"></i> New Blog</a></li>
                    <li><a id="openMessagePopupSend" class="secondary-link"><i class="fas fa-envelope"></i> Message</a></li>
                    <li><a id="MembersTable-link" href="Members.php" class="core-link"><i class="fas fa-users"></i> Members</a></li>
                    <li><a href="adminP.php" class="core-link"><i class="fas fa-credit-card"></i> Member Payments</a></li>
                    <li>
                        <a href="#" id="togglePayments" class="core-link">
                            <i class="fas fa-file-invoice-dollar"></i> Payments Invoices
                        </a>
                        <ul class="dropdown" id="paymentsDropdown">
                            <?php if ($invoiceResult->num_rows > 0): ?>
                                <?php while ($row = $invoiceResult->fetch_assoc()): ?>
                                    <li>
                                        <a href="#" class="invoice-link" data-id="<?php echo htmlspecialchars($row['id']); ?>" 
                                           data-date="<?php echo htmlspecialchars($row['invoice_date']); ?>">
                                            <?php echo htmlspecialchars($row['invoice_date']); ?>
                                        </a>
                                    </li>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <li>No payments found</li>
                            <?php endif; ?>
                        </ul>
                    </li>
                    <li><a href="userinfo.php" target="_blank" class="secondary-link"><i class="fas fa-user"></i> User Information</a></li>
                    <li><a href="https://www.agl.or.ke/contact-us/" class="secondary-link"><i class="fas fa-address-book"></i> Contacts</a></li>
                    <li><a href="mailto:info@agl.or.ke" target="_blank" class="secondary-link"><i class="fas fa-envelope"></i> Email Us</a></li>
                    <li><a href="tel:+254748027123" target="_blank" class="secondary-link"><i class="fas fa-phone"></i> Call Us</a></li>
                    <li><a href="https://wa.me/254722605048" target="_blank" class="secondary-link"><i class="fab fa-whatsapp"></i> Chat on WhatsApp</a></li>
                    <li><a href="https://x.com/OfLibraria37902" target="_blank" class="secondary-link"><i class="fab fa-twitter"></i> Twitter</a></li>
                    <li><a href="https://www.facebook.com/share/zQ8rdvgozvNsZY8J/?mibextid=qi2Omg" target="_blank" class="secondary-link"><i class="fab fa-facebook"></i> Facebook</a></li>
                    <li><a href="../forms/logout.php" class="secondary-link"><i class="fas fa-sign-out-alt"></i> Logout</a></li>

                <?php elseif ($role == 'admin'): ?>
                    <!-- Admin Links -->
                    <li><a id="openPostEventModal" class="core-link"><i class="fas fa-calendar-plus"></i> Planned Event</a></li>
                    <li><a id="openBlogPostModal" class="secondary-link"><i class="fas fa-blog"></i> New Blog</a></li>
                    <li><a id="openMessagePopupSend" class="secondary-link"><i class="fas fa-envelope"></i> Message</a></li>
                    <li><a id="MembersTable-link" href="Members.php" class="core-link"><i class="fas fa-users"></i> Members</a></li>
                    <li><a href="adminP.php" class="core-link"><i class="fas fa-credit-card"></i> Member Payments</a></li>
                    <li>
                        <a href="#" id="togglePayments" class="core-link">
                            <i class="fas fa-file-invoice-dollar"></i> Payments Invoices
                        </a>
                        <ul class="dropdown" id="paymentsDropdown">
                            <?php if ($invoiceResult->num_rows > 0): ?>
                                <?php while ($row = $invoiceResult->fetch_assoc()): ?>
                                    <li>
                                        <a href="#" class="invoice-link" data-id="<?php echo htmlspecialchars($row['id']); ?>" 
                                           data-date="<?php echo htmlspecialchars($row['invoice_date']); ?>">
                                            <?php echo htmlspecialchars($row['invoice_date']); ?>
                                        </a>
                                    </li>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <li>No payments found</li>
                            <?php endif; ?>
                        </ul>
                    </li>
                    <li><a href="userinfo.php" target="_blank" class="secondary-link"><i class="fas fa-user"></i> User Information</a></li>
                    <li><a href="mailto:info@agl.or.ke" class="secondary-link"><i class="fas fa-envelope"></i> Email Us</a></li>
                    <li><a href="tel:+254748027123" class="secondary-link"><i class="fas fa-phone"></i> Call Us</a></li>
                    <li><a href="https://wa.me/254722605048" target="_blank" class="secondary-link"><i class="fab fa-whatsapp"></i> Chat on WhatsApp</a></li>
                    <li><a href="../forms/logout.php" class="secondary-link"><i class="fas fa-sign-out-alt"></i> Logout</a></li>

                <?php else: ?>
                    <!-- Member Links -->
                    <li><a href="https://www.agl.or.ke/" class="active core-link"><i class="fas fa-home"></i> Home</a></li>
                    <li><a class="core-link"><i class="fas fa-calendar-plus"></i> Planned Event</a></li>
                    <li><a class="core-link"><i class="fas fa-calendar-check"></i> Past Event</a></li>
                    <li><a class="secondary-link"><i class="fas fa-blog"></i> New Blog</a></li>
                    <li><a class="secondary-link"><i class="fas fa-envelope"></i> Message</a></li>
                    <li><a href="Members.php" class="core-link"><i class="fas fa-users"></i> Members</a></li>
                    <li><a href="adminP.php" class="core-link"><i class="fas fa-credit-card"></i> Member Payments</a></li>
                    <li>
                        <a href="#" id="togglePayments" class="core-link">
                            <i class="fas fa-file-invoice-dollar"></i> Payments Invoices
                        </a>
                        <ul class="dropdown" id="paymentsDropdown">
                            <?php if ($invoiceResult->num_rows > 0): ?>
                                <?php while ($row = $invoiceResult->fetch_assoc()): ?>
                                    <li>
                                        <a href="#" class="invoice-link" data-id="<?php echo htmlspecialchars($row['id']); ?>" 
                                           data-date="<?php echo htmlspecialchars($row['invoice_date']); ?>">
                                            <?php echo htmlspecialchars($row['invoice_date']); ?>
                                        </a>
                                    </li>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <li>No payments found</li>
                            <?php endif; ?>
                        </ul>
                    </li>
                    <li><a href="userinfo.php" target="_blank" class="secondary-link"><i class="fas fa-user"></i> User Information</a></li>
                    <li><a href="https://www.agl.or.ke/contact-us/" target="_blank" class="secondary-link"><i class="fas fa-address-book"></i> Contacts</a></li>
                    <li><a href="mailto:info@agl.or.ke" class="secondary-link"><i class="fas fa-envelope"></i> Email Us</a></li>
                    <li><a href="tel:+254748027123" target="_blank" class="secondary-link"><i class="fas fa-phone"></i> Call Us</a></li>
                    <li><a href="https://wa.me/254722605048" target="_blank" class="secondary-link"><i class="fab fa-whatsapp"></i> Chat on WhatsApp</a></li>
                    <li><a href="https://x.com/OfLibraria37902" target="_blank" class="secondary-link"><i class="fab fa-twitter"></i> Twitter</a></li>
                    <li><a href="https://www.facebook.com/share/zQ8rdvgozvNsZY8J/?mibextid=qi2Omg" target="_blank" class="secondary-link"><i class="fab fa-facebook"></i> Facebook</a></li>
                    <li><a href="../forms/logout.php" class="secondary-link"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                <?php endif; ?>
            </ul>
        </nav>

        <!-- Dashboard Content -->
        <section class="dashboard" role="main">
            <!-- Dashboard Cards -->
            <div class="cards">
                <!-- Profile Card -->
                <div class="card" role="region" aria-label="User Profile">
                    <img class="cardMemberprofile" src="<?php echo htmlspecialchars($userData['passport_image']); ?>" 
                         alt="Profile picture of <?php echo htmlspecialchars($userData['name']); ?>">
                    <h5><?php echo htmlspecialchars($userData['name']); ?></h5>
                    <hr>
                    <h4><?php echo htmlspecialchars($sessionEmail); ?></h4>
                    <p>Registration Date: <?php echo htmlspecialchars($userData['registration_date']); ?></p>
                </div>

                <!-- Payments Card -->
                <div class="card" role="region" aria-label="Payment Information">
                    <h5>Member Payments</h5>
                    <hr>
                    <p id="memberpayments-current-lastPay">Last payment: <span><?php echo $lastPaymentDate; ?></span></p>
                    <p id="memberpayments-current-nextP">Next payment: <span><?php echo $nextPaymentDate; ?></span></p>
                    <p id="memberpayments-current-balance">Last Amount Paid: <span><?php echo $currentBalance; ?>sh</span></p>
                    <hr>
                    <button class="MemberPaymentBtn" id="mpesa-btn" data-popup-target="mpesa-popup"
                        <?php echo $disablePaymentButton ? 'disabled style="background-color: lightblue; cursor: not-allowed;"' : ''; ?>>
                        Pay Membership Fee
                    </button>
                    <button class="MemberPaymentBtn" id="memberpayments-btn" data-popup-target="memberpayments-popup">
                        Pay Membership Premium
                    </button>
                </div>

                <!-- Education Card -->
                <div class="card" role="region" aria-label="Education Information">
                    <h5>Education Information</h5>
                    <hr>
                    <p>Highest Degree: <span id="highest-degree"><?php echo htmlspecialchars($educationInfo['highest_degree']); ?></span></p>
                    <p>Institution: <span id="institution"><?php echo htmlspecialchars($educationInfo['institution']); ?></span></p>
                    <p>Graduation Year: <span id="graduation-year"><?php echo htmlspecialchars($educationInfo['graduation_year']); ?></span></p>
                </div>

                <!-- Events Card -->
                <div class="card" role="region" aria-label="Registered Events">
                    <h4>Registered Events</h4>
                    <hr>
                    <?php if ($eventsResult->num_rows > 0): ?>
                        <?php while ($row = $eventsResult->fetch_assoc()): ?>
                            <div>
                                <h5><?php echo htmlspecialchars($row['event_name']); ?></h5>
                                <p><?php echo htmlspecialchars($row['event_location']); ?></p>
                                <p><?php echo htmlspecialchars($row['event_date']); ?></p>
                                <form method="POST" action="../forms/event_card.php">
                                    <input type="hidden" name="email" value="<?php echo htmlspecialchars($row['member_email']); ?>">
                                    <input type="hidden" name="eventName" value="<?php echo htmlspecialchars($row['event_id']); ?>">
                                    <button class="iventcard" type="submit">Download Invitation Card</button>
                                </form>
                            </div>
                            <hr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p>No upcoming registered events.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Response Popup -->
            <?php if (isset($_SESSION['response'])): ?>
                <div id="response-popup" class="popup">
                    <?php 
                    $response = $_SESSION['response'];
                    if (!$response['success']): 
                        if (!empty($response['errors'])): ?>
                            <div class="alert alert-danger">
                                <?php foreach ($response['errors'] as $error): ?>
                                    <p><?php echo htmlspecialchars($error); ?></p>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-danger"><?php echo htmlspecialchars($response['message']); ?></div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="alert alert-success"><?php echo htmlspecialchars($response['message']); ?></div>
                    <?php endif; ?>
                </div>
                <?php unset($_SESSION['response']); ?>
            <?php endif; ?>

            <!-- Blogs Section -->
            <h4 id="blogPoint">Blogs</h4>
            <div class="blogPoint">
                <?php if ($blogResult->num_rows > 0): ?>
                    <?php while ($row = $blogResult->fetch_assoc()): ?>
                        <div class="blogSingle" data-id="<?php echo htmlspecialchars($row["id"]); ?>">
                            <div class="blogImage">
                                <img src="<?php echo htmlspecialchars($row["image_path"]); ?>" 
                                     alt="Blog image for <?php echo htmlspecialchars($row["title"]); ?>">
                            </div>
                            <div class="blogContent">
                                <h4><?php echo htmlspecialchars($row["title"]); ?></h4>
                                <h6><?php echo date("d/m/Y", strtotime($row["created_at"])); ?></h6>
                                <button class="moreButton">Read More</button>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No blogs available.</p>
                <?php endif; ?>
            </div>

            <!-- Planned Events Section -->
            <div id="PlannedEvents">
                <h3>Planned Events</h3>
                <div class="table-card">
                    <?php if ($plannedEventsResult->num_rows > 0): ?>
                        <?php while ($row = $plannedEventsResult->fetch_assoc()): ?>
                            <div class="eventDiv">
                                <h3><?php echo htmlspecialchars($row['event_name']); ?></h3>
                                <img class="plannedEventimg" src="<?php echo htmlspecialchars($row['event_image_path']); ?>" 
                                     alt="Event image for <?php echo htmlspecialchars($row['event_name']); ?>">
                                <div class="quill-content"><?php echo $row['event_description']; ?></div>
                                <div class="eventDivindiv">
                                    <p><?php echo htmlspecialchars($row['event_location']); ?></p>
                                    <p><?php echo htmlspecialchars($row['event_date']); ?></p>
                                </div>
                                <button class="plannedEventsBTN" id="registerBtnEventRegistration_<?php echo htmlspecialchars($row['id']); ?>">
                                    Register for the event
                                </button>
                            </div>

                            <!-- Event Registration Popup -->
                            <div id="popupFormEventRegistration_<?php echo htmlspecialchars($row['id']); ?>" class="popup-form">
                                <div class="form-container">
                                    <h4>Event Name: <?php echo htmlspecialchars($row['event_name']); ?></h4>
                                    <form action="../forms/Payment/Mpesa-Daraja-Api-main/StkPushEvent.php" method="post">
                                        <input type="hidden" name="event_id" value="<?php echo htmlspecialchars($row['id']); ?>">
                                        <input type="hidden" name="event_name" value="<?php echo htmlspecialchars($row['event_name']); ?>">
                                        <input type="hidden" name="event_location" value="<?php echo htmlspecialchars($row['event_location']); ?>">
                                        <input type="hidden" name="event_date" value="<?php echo htmlspecialchars($row['event_date']); ?>">
                                        
                                        <label for="memberEmail">Member Email:</label>
                                        <input type="email" id="memberEmail" name="User-email" value="<?php echo htmlspecialchars($sessionEmail); ?>" readonly>
                                        
                                        <label for="memberName">Name:</label>
                                        <input type="text" id="memberName" name="memberName" value="<?php echo htmlspecialchars($userData['name']); ?>" readonly>
                                        
                                        <label for="contact">Contact:</label>
                                        <input type="text" id="contact" name="phone_number" required>
                                        
                                        <label for="amount">Registration Amount: Ksh <?php echo number_format($row['RegistrationAmount']); ?></label>
                                        <input value="<?php echo number_format($row['RegistrationAmount']); ?>" readonly type="text" id="amount" name="amount" required>
                                        
                                        <button type="submit">Pay and Submit</button>
                                        <button type="button" class="closeBtn" id="closeBtn_<?php echo htmlspecialchars($row['id']); ?>">Close</button>
                                    </form>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p>No events found.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Past Events Section -->
            <h4 id="PastEvents">Past Events</h4>
            <div class="table-card" id="pastEventsContainer">
                <?php if ($pastEventsResult->num_rows > 0): ?>
                    <?php while ($row = $pastEventsResult->fetch_assoc()): ?>
                        <?php
                        $imagePathsJson = $row["event_image_paths"];
                        $imagePathsArray = json_decode($imagePathsJson, true);
                        $imagePath = (is_array($imagePathsArray) && !empty($imagePathsArray)) 
                            ? htmlspecialchars($imagePathsArray[0]) 
                            : '';
                        ?>
                        <div class="eventDiv">
                            <div class="eventDivindiv">
                                <h3><?php echo htmlspecialchars($row["event_location"]); ?></h3>
                                <h3><?php echo htmlspecialchars($row["event_date"]); ?></h3>
                            </div>
                            <?php if ($imagePath): ?>
                                <img class="past-eventDiv-img" src="<?php echo $imagePath; ?>" 
                                     alt="Event image for <?php echo htmlspecialchars($row['event_name']); ?>">
                            <?php endif; ?>
                            <p><?php echo htmlspecialchars($row["event_name"]); ?></p>
                            <a href="Event.php?id=<?php echo htmlspecialchars($row["id"]); ?>" class="PastEventsmoreButton">More</a>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No past events found.</p>
                <?php endif; ?>
            </div>
        </section>
    </div>

    <!-- Footer -->
    <footer>
        <p>&copy; 2024 <a href="https://www.agl.or.ke/" aria-label="Visit AGL website">http://www.agl.or.ke/</a>. All rights reserved.</p>
    </footer>

    <!-- =========================== -->
    <!-- POPUPS AND MODALS -->
    <!-- =========================== -->

    <!-- M-Pesa Payment Popup -->
    <div id="mpesa-popup" class="popup-container">
        <form id="mpesa-popup-content" class="popup-content" method="POST" 
              action="../forms/Payment/Mpesa-Daraja-Api-main/stkpush.php">
            <span class="popup-close" onclick="togglePopup('mpesa-popup')" aria-label="Close popup">X</span>
            <img src="../assets/img/mpesa.png" alt="M-Pesa Logo" class="popup-logo">
            <p class="popup-confirmation">Confirm that you are making a payment of Two Thousand Kenyan Shillings (2,000 Ksh) as membership fees to the Association of Government Librarians.</p>
            
            <label for="User-email" class="popup-label">User Email</label>
            <input type="email" id="User-email" name="User-email" class="popup-input"
                   value="<?php echo htmlspecialchars($sessionEmail); ?>" required readonly>
            
            <label for="mpesa-phone-number" class="popup-label">Phone Number</label>
            <input type="number" id="mpesa-phone-number" name="phone_number" class="popup-input"
                   placeholder="Enter your phone number" required>
            
            <label for="mpesa-amount" class="popup-label">Amount</label>
            <input type="text" id="mpesa-amount" name="amount" class="popup-input" value="2000" readonly>
            
            <input type="hidden" id="referringPage" name="referringPage"
                   value="<?php echo htmlspecialchars($_SERVER['HTTP_REFERER']); ?>">
            
            <div class="popup-buttons">
                <button class="popup-btn" type="submit">Make Payment</button>
            </div>
        </form>
    </div>

    <!-- Membership Payment Popup -->
    <div id="memberpayments-popup" class="popup-container">
        <form action="../forms/Payment/Mpesa-Daraja-Api-main/STKMembersubscription.php" method="post" 
              id="memberpayments-popup-content" class="popup-content">
            <span class="popup-close" onclick="togglePopup('memberpayments-popup')" aria-label="Close popup">X</span>
            <img src="../assets/img/mpesa.png" alt="M-Pesa Logo" class="popup-logo">
            <p class="popup-description">Confirm that you are making a payment of 3,600 Ksh as annual membership fees to the Association of Government Librarians.</p>
            
            <label for="User-email" class="popup-label">User Email</label>
            <input type="email" id="User-email" name="User-email" class="popup-input"
                   value="<?php echo htmlspecialchars($sessionEmail); ?>" required readonly>
            
            <label for="phone_number" class="popup-label">Phone Number</label>
            <input type="text" id="phone_number" name="phone_number" class="popup-input"
                   placeholder="Enter your phone number" required>
            
            <label for="amount" class="popup-label">Amount</label>
            <input type="text" id="amount" name="amount" class="popup-input" value="3600" readonly>
            
            <div class="popup-buttons">
                <button class="popup-btn" type="submit">Make Payment</button>
            </div>
        </form>
    </div>

    <!-- Planned Event Modal -->
    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close" aria-label="Close modal">&times;</span>
            <form id="eventForm" action="../forms/PlannedEvent.php" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="eventName">Event Name:</label>
                    <input type="text" id="eventName" name="eventName" required />
                </div>
                <div class="form-group">
                    <label for="eventImage">Event Image:</label>
                    <input type="file" id="eventImage" name="eventImage" accept="image/*" required />
                </div>
                <div class="form-group">
                    <label for="eventDescription">Brief Introduction:</label>
                    <div id="quillEditor" style="height: 200px;"></div>
                    <input type="hidden" id="eventDescription" name="eventDescription">
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
                    <label for="RegistrationAmount">Event Registration Amount in Ksh:</label>
                    <input type="number" id="RegistrationAmount" name="RegistrationAmount" required />
                </div>
                <div class="form-group">
                    <button type="submit">Save Event</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Past Event Modal -->
    <div id="pastEventModal" class="past-event-modal">
        <div class="past-event-modal-content">
            <span class="close-past-event" aria-label="Close modal">&times;</span>
            <form id="pastEventForm" action="../forms/pastEvent.php" method="post" enctype="multipart/form-data">
                <div class="past-event-form-group">
                    <label for="pastEventName">Event Name:</label>
                    <input type="text" id="pastEventName" name="eventName" required />
                </div>
                <div class="past-event-form-group">
                    <label for="pastEventDetails">Event Details</label>
                    <div id="pastEventDetailsEditor" style="height: 200px;"></div>
                    <input type="hidden" name="eventDetails" id="pastEventDetails" required />
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
                    <input accept="image/*" type="file" id="pastEventImages" name="eventImages[]" multiple required />
                </div>
                <div class="past-event-form-group">
                    <label for="pastEventDocuments">Event Documents:</label>
                    <input type="file" id="pastEventDocuments" name="eventDocuments[]" accept=".pdf" multiple required />
                </div>
                <div class="past-event-form-group">
                    <button type="submit">Save Past Event</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Blog Post Modal -->
    <div id="blogPostModal" class="blog-post-modal">
        <div class="blog-post-modal-content">
            <span class="close-blog-post" aria-label="Close modal">&times;</span>
            <div id="errorMessages" style="color:red;"></div>
            <div id="successMessage" style="color:green;"></div>
            <form id="blogPostForm" action="../forms/save_blog_post.php" method="post" enctype="multipart/form-data">
                <div class="blog-post-form-group">
                    <label for="blogTitle">Blog Title:</label>
                    <input type="text" id="blogTitle" name="blogTitle" required />
                </div>
                <div class="blog-post-form-group">
                    <label for="blogContent">Content:</label>
                    <div style="min-height: 200px;" id="editor" class="quill-editor"></div>
                    <input type="hidden" id="blogContent" name="blogContent" required />
                </div>
                <div class="blog-post-form-group">
                    <label for="blogImage">Blog Image:</label>
                    <input type="file" id="blogImage" name="blogImage" accept="image/*" required />
                </div>
                <div class="blog-post-form-group">
                    <button type="submit">Post Blog</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Send Message Popup -->
    <div class="message-popup-sendMessage" id="messagePopupsend">
        <div class="message-popup-content-sendMessage">
            <button class="message-close-btn-sendMessage" id="messageClosePopupBtn" aria-label="Close popup">
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
                    <input type="email" id="messageSenderEmail" name="sender_email" value="info@agl.or.ke" required readonly />
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
                    <textarea name="message" id="sendmessageContent" style="width: 100%; min-height: 200px; padding: 5px;"></textarea>
                </div>
                <button type="submit" class="message-submit-btn-sendMessage">Send Message</button>
            </form>
        </div>
    </div>

    <!-- Messages Received Popup -->
    <div class="message-popup" id="messagePopupReceivedMessages">
        <div class="message-popup-header">
            <h4>Messages</h4>
            <button id="closePopupReceivedMessages" aria-label="Close popup">&times;</button>
        </div>
        <div class="message-container">
            <?php if (empty($messages)): ?>
                <p>No messages found.</p>
            <?php else: ?>
                <?php foreach ($messages as $message): ?>
                    <div class="message" onclick="showFullMessageReceivedMessages('<?php echo addslashes(htmlspecialchars($message['message'], ENT_QUOTES)); ?>')">
                        <p class="message-content"><?php echo htmlspecialchars($message['subject'], ENT_QUOTES); ?></p>
                        <span class="message-time"><?php echo date("h:i A", strtotime($message['date_sent'])); ?></span>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Full Message Popup -->
    <div class="full-message-popup" id="fullMessagePopupReceivedMessages">
        <div class="full-message-content">
            <button id="closeFullMessageReceivedMessages" aria-label="Close popup">&times;</button>
            <p id="fullMessageTextReceivedMessages"></p>
        </div>
    </div>

    <!-- =========================== -->
    <!-- JAVASCRIPT FILES -->
    <!-- =========================== -->
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <script src="/assets/JS/dashboard-scripts.js"></script>
    
    <!-- Event Registration JavaScript (Generated by PHP) -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        <?php
        // Generate event registration handlers for each planned event
        if ($plannedEventsResult->num_rows > 0) {
            $plannedEventsResult->data_seek(0); // Reset pointer
            while ($row = $plannedEventsResult->fetch_assoc()) {
                $eventId = htmlspecialchars($row['id']);
                ?>
                // Event Registration Handlers for Event <?php echo $eventId; ?>
                var registerBtnEventRegistration_<?php echo $eventId; ?> = document.getElementById('registerBtnEventRegistration_<?php echo $eventId; ?>');
                var popupFormEventRegistration_<?php echo $eventId; ?> = document.getElementById('popupFormEventRegistration_<?php echo $eventId; ?>');
                var closeBtn_<?php echo $eventId; ?> = document.getElementById('closeBtn_<?php echo $eventId; ?>');

                if (registerBtnEventRegistration_<?php echo $eventId; ?> && popupFormEventRegistration_<?php echo $eventId; ?>) {
                    registerBtnEventRegistration_<?php echo $eventId; ?>.addEventListener('click', function() {
                        popupFormEventRegistration_<?php echo $eventId; ?>.style.display = 'flex';
                    });
                }

                if (closeBtn_<?php echo $eventId; ?> && popupFormEventRegistration_<?php echo $eventId; ?>) {
                    closeBtn_<?php echo $eventId; ?>.addEventListener('click', function() {
                        popupFormEventRegistration_<?php echo $eventId; ?>.style.display = 'none';
                    });
                }

                window.addEventListener('click', function(event) {
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

</body>
</html>
<?php
// Close database connection
if (isset($conn)) {
    $conn->close();
}
?>