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

    <style>
        /* ===========================
   ROOT VARIABLES & RESETS
=========================== */
:root {
    --header-height: 83px;
    --primary-color: #1E5BC6;
    --secondary-color: #2E7BFF;
    --accent-color: #28a745;
    --danger-color: #D32F2F;
    --warning-color: #ffc107;
    --text-primary: #2C3E50;
    --text-secondary: #5D7BA3;
    --text-light: #7A8CA5;
    --bg-primary: #FFFFFF;
    --bg-secondary: #F8FAFF;
    --bg-tertiary: #F0F7FF;
    --border-color: #E3EFFF;
    --shadow-light: rgba(30, 91, 198, 0.08);
    --shadow-medium: rgba(30, 91, 198, 0.15);
    --shadow-dark: rgba(0, 0, 0, 0.2);
    --sidebar-width: 240px;
    --border-radius: 8px;
    --transition-speed: 0.3s;
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Segoe UI', 'Helvetica Neue', Arial, sans-serif;
    background-color: var(--bg-secondary);
    color: var(--text-primary);
    line-height: 1.6;
}

/* ===========================
   HEADER STYLES
=========================== */
header {
    background-color: var(--bg-primary);
    box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08);
    padding: 15px 30px;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    border-bottom: 3px solid var(--primary-color);
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 1000;
    gap: 20px;
}

.header-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
    flex: 0 0 auto;
}

.logo {
    display: flex;
    align-items: center;
    flex: 0 0 auto;
}

.logo img {
    height: 45px;
    width: auto;
    max-width: 180px;
    object-fit: contain;
}

.innerlinksNav {
    display: flex;
    justify-content: flex-end;
    gap: 35px;
    align-items: center;
    transition: all var(--transition-speed) ease;
    flex: 1 0 100%;
    order: 2;
}

.innerlinksNav-a {
    color: var(--text-primary);
    text-decoration: none;
    font-weight: 600;
    font-size: 16px;
    padding: 8px 0;
    position: relative;
    transition: color 0.2s ease;
    white-space: nowrap;
}

#toggleMessagesReceivedMessages {
    color: var(--primary-color);
    text-decoration: none;
    font-weight: 700;
    font-size: 16px;
    padding: 8px 16px;
    background-color: var(--bg-tertiary);
    border-radius: 6px;
    border: 1px solid var(--border-color);
    transition: all 0.2s ease;
    white-space: nowrap;
}

.menu-button {
    display: none;
    cursor: pointer;
    font-size: 28px;
    color: var(--primary-color);
    padding: 5px 10px;
    background: var(--bg-secondary);
    border-radius: 4px;
    border: 1px solid var(--border-color);
    transition: all 0.2s ease;
    order: 2;
}

.innerlinksNav-a:not(:last-child):hover {
    color: var(--primary-color);
}

.innerlinksNav-a:not(:last-child):hover::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 3px;
    background-color: var(--primary-color);
    border-radius: 2px;
}

#toggleMessagesReceivedMessages:hover {
    background-color: var(--primary-color);
    color: #FFFFFF;
    border-color: var(--primary-color);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(30, 91, 198, 0.2);
}

/* ===========================
   SIDEBAR STYLES
=========================== */
.sidebar {
    background: linear-gradient(180deg, var(--bg-primary) 0%, var(--bg-secondary) 100%);
    width: var(--sidebar-width);
    height: calc(100vh - var(--header-height));
    position: fixed;
    top: var(--header-height);
    bottom: 0;
    left: 0;
    overflow-y: auto;
    overflow-x: hidden;
    box-shadow: 3px 0 6px rgba(30, 91, 198, 0.25);
    z-index: 999;
    transition: all var(--transition-speed) cubic-bezier(0.4, 0, 0.2, 1);
}

.sidebar::-webkit-scrollbar {
    width: 6px;
}

.sidebar::-webkit-scrollbar-track {
    background: var(--bg-tertiary);
    border-radius: 3px;
}

.sidebar::-webkit-scrollbar-thumb {
    background: var(--primary-color);
    border-radius: 3px;
}

.sidebar::-webkit-scrollbar-thumb:hover {
    background: #174a9e;
}

.sidebar ul {
    list-style: none;
    padding: 20px 0;
    margin: 0;
}

.sidebar > ul > li {
    position: relative;
    margin: 4px 15px;
}

.sidebar a {
    display: flex;
    align-items: center;
    padding: 14px 20px;
    color: var(--text-primary);
    text-decoration: none;
    font-size: 15px;
    font-weight: 500;
    border-radius: var(--border-radius);
    transition: all 0.25s ease;
    gap: 12px;
    position: relative;
    overflow: hidden;
}

.sidebar a i {
    width: 20px;
    text-align: center;
    font-size: 16px;
    color: var(--text-secondary);
    transition: all 0.25s ease;
}

.sidebar a:hover {
    background-color: var(--bg-tertiary);
    color: var(--primary-color);
    transform: translateX(5px);
    box-shadow: 0 4px 12px var(--shadow-light);
}

.sidebar a:hover i {
    color: var(--primary-color);
}

.sidebar a.active {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
    color: white;
    font-weight: 600;
    box-shadow: 0 4px 12px var(--shadow-medium);
}

.sidebar a.active i {
    color: white;
}

.core-link {
    border-left: 3px solid var(--primary-color);
    margin-left: 5px;
}

.secondary-link {
    border-left: 3px solid var(--border-color);
    margin-left: 5px;
    opacity: 0.9;
}

.secondary-link:hover {
    border-left-color: var(--primary-color);
    opacity: 1;
}

.dropdown {
    background: var(--bg-secondary);
    border-radius: 6px;
    margin: 8px 15px 8px 35px;
    padding: 0;
    border: 1px solid var(--border-color);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    overflow: hidden;
    animation: slideDown 0.3s ease-out;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.dropdown li {
    margin: 0;
    border-bottom: 1px solid #f0f4ff;
}

.dropdown li:last-child {
    border-bottom: none;
}

.dropdown a {
    padding: 10px 15px;
    font-size: 13px;
    color: #4A657C;
    border-left: none;
    margin-left: 0;
    border-radius: 0;
}

.dropdown a:hover {
    background-color: #E8F1FF;
    transform: translateX(3px);
}

#togglePayments {
    position: relative;
    padding-right: 40px;
}

#togglePayments::after {
    content: '▼';
    position: absolute;
    right: 20px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 11px;
    color: var(--text-secondary);
    transition: all 0.3s ease;
}

#togglePayments.active::after {
    transform: translateY(-50%) rotate(180deg);
    color: var(--primary-color);
}

.invoice-link {
    font-size: 12px !important;
    color: #666 !important;
    justify-content: flex-end;
    padding-right: 20px !important;
}

.invoice-link:hover {
    color: var(--primary-color) !important;
    background-color: #E8F1FF;
}

.sidebar a[href*="logout.php"] {
    margin-top: 20px;
    background: linear-gradient(135deg, #FFF5F5 0%, #FFE8E8 100%);
    color: var(--danger-color);
    border-left: 3px solid #FF6B6B;
}

.sidebar a[href*="logout.php"]:hover {
    background: linear-gradient(135deg, #FFE8E8 0%, #FFD6D6 100%);
    color: #B71C1C;
    box-shadow: 0 4px 12px rgba(211, 47, 47, 0.15);
}

.sidebar a[href*="logout.php"] i {
    color: #FF6B6B;
}

/* ===========================
   MAIN CONTENT AREA
=========================== */
.main-content {
    margin-left: var(--sidebar-width);
    margin-top: var(--header-height);
    padding: 20px;
    transition: margin-left var(--transition-speed) ease;
}

/* ===========================
   DASHBOARD CARDS
=========================== */
.dashboard {
    width: 100%;
}

.cards {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
    margin-bottom: 30px;
}

.card {
    background: linear-gradient(135deg, var(--bg-primary) 0%, var(--bg-secondary) 100%);
    border-radius: 16px;
    padding: 20px;
    box-shadow: 0 8px 25px var(--shadow-light);
    border: 1px solid var(--border-color);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    display: flex;
    flex-direction: column;
    height: 100%;
    position: relative;
    overflow: hidden;
    animation: cardEntrance 0.5s ease-out;
}

@keyframes cardEntrance {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px var(--shadow-medium);
    border-color: var(--primary-color);
}

.card:nth-child(1) { animation-delay: 0.1s; }
.card:nth-child(2) { animation-delay: 0.2s; }
.card:nth-child(3) { animation-delay: 0.3s; }
.card:nth-child(4) { animation-delay: 0.4s; }

.card h5 {
    color: var(--primary-color);
    font-size: 18px;
    font-weight: 700;
    margin-bottom: 15px;
    position: relative;
    padding-bottom: 8px;
}

.card h5::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 50px;
    height: 3px;
    background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
    border-radius: 2px;
}

.card h4 {
    color: var(--text-primary);
    font-size: 16px;
    font-weight: 600;
    margin: 8px 0;
}

.card p {
    color: var(--text-secondary);
    font-size: 14px;
    line-height: 1.6;
    margin: 8px 0;
}

.card p span {
    color: var(--text-primary);
    font-weight: 600;
    padding: 2px 8px;
    background: var(--bg-tertiary);
    border-radius: 4px;
    border-left: 3px solid var(--primary-color);
}

.card hr {
    border: none;
    height: 1px;
    background: linear-gradient(90deg, var(--border-color), var(--bg-primary));
    margin: 15px 0;
    width: 100%;
}

.cardMemberprofile {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid var(--bg-primary);
    box-shadow: 0 4px 15px var(--shadow-medium);
    margin: 0 auto 20px auto;
    display: block;
    transition: all 0.3s ease;
}

.cardMemberprofile:hover {
    transform: scale(1.05);
    box-shadow: 0 6px 20px var(--shadow-medium);
}

.MemberPaymentBtn {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
    color: white;
    border: none;
    padding: 12px 20px;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-top: 15px;
    width: 100%;
    position: relative;
    overflow: hidden;
}

.MemberPaymentBtn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.5s ease;
}

.MemberPaymentBtn:hover::before {
    left: 100%;
}

.MemberPaymentBtn:hover {
    background: linear-gradient(135deg, #174a9e 0%, var(--primary-color) 100%);
    transform: translateY(-2px);
    box-shadow: 0 8px 20px var(--shadow-medium);
}

.MemberPaymentBtn:disabled {
    background: linear-gradient(135deg, #A0C1F1 0%, #B5D0FF 100%);
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
}

.card .iventcard {
    background: linear-gradient(135deg, var(--accent-color) 0%, #34ce57 100%);
    color: white;
    border: none;
    padding: 10px 15px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-top: 10px;
    text-align: center;
    width: 100%;
}

.card .iventcard:hover {
    background: linear-gradient(135deg, #218838 0%, var(--accent-color) 100%);
    transform: translateY(-2px);
    box-shadow: 0 6px 15px rgba(40, 167, 69, 0.3);
}

/* ===========================
   BLOGS SECTION
=========================== */
#blogPoint {
    font-size: 22px;
    font-weight: 700;
    color: var(--primary-color);
    margin: 20px 0 10px;
    padding: 20px;
    position: relative;
}

#blogPoint::after {
    content: '';
    display: block;
    width: 50px;
    height: 4px;
    background: var(--primary-color);
    border-radius: 2px;
    margin-top: 8px;
}

.blogPoint {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
    gap: 24px;
    padding: 10px 20px 30px;
    max-height: 600px;
    overflow-y: auto;
    scrollbar-width: thin;
}

.blogSingle {
    background: var(--bg-primary);
    border-radius: 14px;
    overflow: hidden;
    box-shadow: 0 8px 20px var(--shadow-light);
    transition: all 0.3s ease;
    cursor: pointer;
    display: flex;
    flex-direction: column;
}

.blogSingle:hover {
    transform: translateY(-6px);
    box-shadow: 0 14px 28px var(--shadow-medium);
}

.blogImage {
    width: 100%;
    height: 160px;
    overflow: hidden;
    background: var(--bg-tertiary);
}

.blogImage img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.4s ease;
}

.blogSingle:hover .blogImage img {
    transform: scale(1.05);
}

.blogContent {
    padding: 16px 18px 20px;
    display: flex;
    flex-direction: column;
    gap: 8px;
    flex: 1;
}

.blogContent h4 {
    font-size: 16px;
    font-weight: 600;
    color: var(--text-primary);
    margin: 0;
    line-height: 1.3;
}

.blogContent h6 {
    font-size: 12px;
    font-weight: 500;
    color: var(--text-light);
    margin: 0;
}

.moreButton {
    margin-top: auto;
    align-self: flex-start;
    padding: 8px 16px;
    font-size: 13px;
    font-weight: 600;
    color: var(--primary-color);
    background: var(--bg-tertiary);
    border: 1px solid var(--border-color);
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.25s ease;
}

.moreButton:hover {
    background: var(--primary-color);
    color: #FFFFFF;
    border-color: var(--primary-color);
    box-shadow: 0 6px 14px var(--shadow-medium);
}

/* ===========================
   PLANNED EVENTS
=========================== */
#PlannedEvents {
    padding: 10px 0 30px;
}

#PlannedEvents > h3 {
    font-size: 22px;
    font-weight: 700;
    color: var(--primary-color);
    margin: 0 0 10px;
    padding: 20px;
    position: relative;
}

#PlannedEvents > h3::after {
    content: '';
    display: block;
    width: 55px;
    height: 4px;
    background: var(--primary-color);
    border-radius: 2px;
    margin-top: 8px;
}

.table-card {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 26px;
    padding: 10px 20px;
}

.eventDiv {
    background: var(--bg-primary);
    border-radius: 16px;
    padding: 18px;
    box-shadow: 0 10px 24px var(--shadow-light);
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.eventDiv:hover {
    transform: translateY(-6px);
    box-shadow: 0 18px 36px var(--shadow-medium);
}

.eventDiv > h3 {
    font-size: 18px;
    font-weight: 600;
    color: var(--text-primary);
    margin: 0;
}

.plannedEventimg {
    width: 100%;
    height: 180px;
    object-fit: cover;
    border-radius: 12px;
    background: var(--bg-tertiary);
}

.quill-content {
    font-size: 14px;
    color: #4A657C;
    line-height: 1.6;
    max-height: 120px;
    overflow: hidden;
    position: relative;
}

.quill-content::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 40px;
    background: linear-gradient(to top, var(--bg-primary), rgba(255, 255, 255, 0));
}

.eventDivindiv {
    display: flex;
    justify-content: space-between;
    font-size: 13px;
    color: var(--text-light);
    font-weight: 500;
}

.eventDivindiv p {
    margin: 0;
}

.plannedEventsBTN {
    margin-top: auto;
    align-self: flex-start;
    padding: 10px 20px;
    font-size: 14px;
    font-weight: 600;
    color: #FFFFFF;
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.25s ease;
    box-shadow: 0 6px 14px var(--shadow-medium);
}

.plannedEventsBTN:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 22px var(--shadow-medium);
}

/* ===========================
   PAST EVENTS
=========================== */
#PastEvents {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
    gap: 24px;
    padding: 10px 20px 30px;
}

#PastEvents h4 {
    font-size: 22px;
    font-weight: 700;
    color: var(--primary-color);
    margin: 20px;
    position: relative;
}

#PastEvents h4::after {
    content: '';
    display: block;
    width: 45px;
    height: 4px;
    background: var(--primary-color);
    border-radius: 2px;
    margin-top: 8px;
}

#PastEvents .eventDiv {
    background: var(--bg-primary);
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 8px 22px var(--shadow-light);
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
}

#PastEvents .eventDiv:hover {
    transform: translateY(-6px);
    box-shadow: 0 16px 34px var(--shadow-medium);
}

#PastEvents .eventDivindiv {
    display: flex;
    justify-content: space-between;
    padding: 14px 16px;
    background: var(--bg-tertiary);
    border-bottom: 1px solid var(--border-color);
}

#PastEvents .eventDivindiv h3 {
    font-size: 13px;
    font-weight: 600;
    color: var(--primary-color);
    margin: 0;
}

.past-eventDiv-img {
    width: 100%;
    height: 170px;
    object-fit: cover;
    background: var(--bg-tertiary);
}

#PastEvents .eventDiv p {
    padding: 14px 16px 0;
    font-size: 15px;
    font-weight: 600;
    color: var(--text-primary);
    margin: 0;
}

.PastEventsmoreButton {
    margin: 16px;
    align-self: flex-start;
    padding: 8px 18px;
    font-size: 13px;
    font-weight: 600;
    color: var(--primary-color);
    background: var(--bg-tertiary);
    border: 1px solid var(--border-color);
    border-radius: 6px;
    text-decoration: none;
    transition: all 0.25s ease;
}

.PastEventsmoreButton:hover {
    background: var(--primary-color);
    color: #FFFFFF;
    border-color: var(--primary-color);
    box-shadow: 0 6px 14px var(--shadow-medium);
}

/* ===========================
   POPUP STYLES
=========================== */
.popup-container {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    justify-content: center;
    align-items: center;
    z-index: 2000;
}

.popup-content {
    background-color: var(--bg-primary);
    padding: 20px;
    border-radius: 10px;
    width: 400px;
    max-width: 80%;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
    text-align: center;
}

.popup-logo {
    max-width: 150px;
    margin: 0 auto 20px;
}

.popup-label {
    display: block;
    margin: 10px 0 5px;
    font-weight: 600;
    color: var(--text-primary);
}

.popup-input {
    width: 100%;
    padding: 10px;
    margin-bottom: 15px;
    border: 1px solid var(--border-color);
    border-radius: 6px;
    font-size: 14px;
}

.popup-input:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(30, 91, 198, 0.15);
}

.popup-btn {
    background-color: var(--accent-color);
    color: white;
    padding: 12px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    width: 100%;
    font-weight: 600;
    font-size: 14px;
    transition: background-color 0.3s ease;
}

.popup-btn:hover {
    background-color: #218838;
}

.popup-close {
    color: #000;
    float: right;
    font-size: 25px;
    font-weight: bold;
    cursor: pointer;
    line-height: 1;
}

.popup-close:hover {
    color: var(--danger-color);
}

.popup-description,
.popup-confirmation {
    margin-bottom: 15px;
    color: var(--text-secondary);
}

/* ===========================
   MODAL STYLES
=========================== */
.modal, .past-event-modal, .blog-post-modal {
    display: none;
    position: fixed;
    z-index: 2000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    overflow: auto;
}

.modal-content, .past-event-modal-content, .blog-post-modal-content {
    background-color: var(--bg-primary);
    margin: 5% auto;
    padding: 30px;
    border-radius: 12px;
    width: 90%;
    max-width: 600px;
    box-shadow: 0 10px 30px var(--shadow-dark);
    position: relative;
}

.close, .close-past-event, .close-blog-post {
    color: var(--text-light);
    float: right;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
    line-height: 1;
}

.close:hover,
.close:focus,
.close-past-event:hover,
.close-past-event:focus,
.close-blog-post:hover,
.close-blog-post:focus {
    color: var(--danger-color);
    text-decoration: none;
}

.form-group, .past-event-form-group, .blog-post-form-group {
    margin-bottom: 20px;
}

.form-group label, .past-event-form-group label, .blog-post-form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: var(--text-primary);
}

.form-group input, .past-event-form-group input, .blog-post-form-group input,
.form-group select, .past-event-form-group select, .blog-post-form-group select,
.form-group textarea, .past-event-form-group textarea, .blog-post-form-group textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid var(--border-color);
    border-radius: 6px;
    font-size: 14px;
}

.form-group input:focus, .past-event-form-group input:focus, .blog-post-form-group input:focus,
.form-group select:focus, .past-event-form-group select:focus, .blog-post-form-group select:focus,
.form-group textarea:focus, .past-event-form-group textarea:focus, .blog-post-form-group textarea:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(30, 91, 198, 0.15);
}

.form-group button, .past-event-form-group button, .blog-post-form-group button {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
    color: white;
    padding: 12px 24px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 600;
    font-size: 14px;
    transition: all 0.3s ease;
    width: 100%;
}

.form-group button:hover, .past-event-form-group button:hover, .blog-post-form-group button:hover {
    background: linear-gradient(135deg, #174a9e 0%, var(--primary-color) 100%);
    transform: translateY(-2px);
    box-shadow: 0 8px 20px var(--shadow-medium);
}

/* ===========================
   MESSAGES POPUP
=========================== */
.message-popup, .message-popup-sendMessage {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    justify-content: center;
    align-items: center;
    z-index: 2000;
}

.message-popup-content, .message-popup-content-sendMessage {
    background-color: var(--bg-primary);
    padding: 30px;
    border-radius: 12px;
    width: 90%;
    max-width: 500px;
    max-height: 80vh;
    overflow-y: auto;
    box-shadow: 0 10px 30px var(--shadow-dark);
}

.message-popup-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 2px solid var(--border-color);
}

.message-popup-header h4 {
    color: var(--primary-color);
    margin: 0;
}

#closePopupReceivedMessages, .message-close-btn-sendMessage {
    background: none;
    border: none;
    font-size: 24px;
    cursor: pointer;
    color: var(--text-light);
    line-height: 1;
}

#closePopupReceivedMessages:hover, .message-close-btn-sendMessage:hover {
    color: var(--danger-color);
}

.message-container {
    max-height: 400px;
    overflow-y: auto;
}

.message {
    padding: 15px;
    margin-bottom: 10px;
    background: var(--bg-tertiary);
    border-radius: 8px;
    border-left: 4px solid var(--primary-color);
    cursor: pointer;
    transition: all 0.3s ease;
}

.message:hover {
    background: var(--border-color);
    transform: translateX(5px);
}

.message-content {
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 5px;
}

.message-time {
    font-size: 12px;
    color: var(--text-light);
    float: right;
}

.full-message-popup {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    justify-content: center;
    align-items: center;
    z-index: 2001;
}

.full-message-content {
    background-color: var(--bg-primary);
    padding: 30px;
    border-radius: 12px;
    width: 90%;
    max-width: 600px;
    max-height: 80vh;
    overflow-y: auto;
    position: relative;
}

#closeFullMessageReceivedMessages {
    position: absolute;
    top: 15px;
    right: 15px;
    background: none;
    border: none;
    font-size: 24px;
    cursor: pointer;
    color: var(--text-light);
    line-height: 1;
}

#closeFullMessageReceivedMessages:hover {
    color: var(--danger-color);
}

#fullMessageTextReceivedMessages {
    margin-top: 20px;
    line-height: 1.6;
    color: var(--text-primary);
}

/* ===========================
   RESPONSE POPUP
=========================== */
.popup {
    position: fixed;
    top: 20px;
    right: 20px;
    width: 300px;
    padding: 15px;
    background-color: #f8f9fa;
    border-radius: 5px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
    z-index: 1000;
    opacity: 0;
    visibility: hidden;
    transform: translateY(-20px);
    transition: opacity 0.5s ease, visibility 0.5s ease, transform 0.5s ease;
}

.popup.show {
    opacity: 1;
    visibility: visible;
    transform: translateX(0);
}

.alert {
    margin: 0;
    padding: 10px;
    border-radius: 3px;
    font-size: 14px;
}

.alert-success {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.alert-danger {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

/* ===========================
   EVENT REGISTRATION POPUP
=========================== */
.popup-form {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.45);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 2000;
}

.form-container {
    background: var(--bg-primary);
    padding: 24px;
    width: 100%;
    max-width: 420px;
    border-radius: 14px;
    box-shadow: 0 20px 40px var(--shadow-dark);
    animation: scaleIn 0.3s ease;
}

@keyframes scaleIn {
    from {
        opacity: 0;
        transform: scale(0.9);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

.form-container h4 {
    margin-bottom: 16px;
    font-size: 18px;
    color: var(--primary-color);
}

.form-container label {
    font-size: 13px;
    font-weight: 600;
    color: var(--text-primary);
    display: block;
    margin-top: 12px;
}

.form-container input {
    width: 100%;
    padding: 10px 12px;
    margin-top: 6px;
    border-radius: 6px;
    border: 1px solid var(--border-color);
    font-size: 14px;
}

.form-container input:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(30, 91, 198, 0.15);
}

.form-container button[type="submit"] {
    margin-top: 18px;
    width: 100%;
    padding: 12px;
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
    color: white;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
}

.form-container .closeBtn {
    margin-top: 10px;
    width: 100%;
    padding: 10px;
    background: var(--bg-secondary);
    color: var(--text-primary);
    border: 1px solid var(--border-color);
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.form-container .closeBtn:hover {
    background: var(--bg-tertiary);
}

/* ===========================
   QUILL EDITOR STYLES
=========================== */
.ql-toolbar.ql-snow {
    border: 1px solid var(--border-color) !important;
    border-radius: 6px 6px 0 0;
    background: var(--bg-secondary);
}

.ql-container.ql-snow {
    border: 1px solid var(--border-color) !important;
    border-top: none !important;
    border-radius: 0 0 6px 6px;
    font-family: inherit;
}

.ql-editor {
    min-height: 150px;
    max-height: 300px;
    overflow-y: auto;
}

/* ===========================
   FOOTER
=========================== */
footer {
    font-size: 11px;
    font-style: italic;
    text-align: center;
    background-color: #b4ccdb;
    color: black;
    padding: 10px;
    margin-top: 40px;
}

footer a {
    text-decoration: none;
    color: var(--primary-color);
}

footer a:hover {
    text-decoration: underline;
}

/* ===========================
   RESPONSIVE DESIGN
=========================== */

/* Tablet (769px - 992px) */
@media (min-width: 769px) and (max-width: 992px) {
    .innerlinksNav {
        gap: 17px;
    }
    
    .innerlinksNav-a {
        font-size: 13px !important;
    }
    
    .cards {
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
    }
}

/* Mobile (≤768px) */
@media (max-width: 768px) {
    :root {
        --header-height: 70px;
    }
    
    header {
        padding: 15px 20px;
        flex-direction: column;
        gap: 15px;
    }
    
    .header-top {
        flex-wrap: nowrap;
    }
    
    .logo {
        flex: 1;
        margin-right: 15px;
    }
    
    .menu-button {
        display: block !important;
        order: 2;
        flex: 0 0 auto;
    }
    
    .innerlinksNav {
        flex-basis: auto;
        order: 2;
        display: flex !important;
        flex-direction: row;
        flex-wrap: wrap;
        justify-content: flex-start;
        width: 100%;
        gap: 12px;
    }
    
    .innerlinksNav-a {
        padding: 7px 0 !important;
        font-size: 12px !important;
        border-bottom: none;
    }
    
    #toggleMessagesReceivedMessages {
        padding: 10px 15px !important;
    }
    
    .sidebar {
        width: 100%;
        height: calc(100vh - 70px);
        top: 70px;
        left: -100%;
        border-right: none;
        box-shadow: none;
    }
    
    .sidebar.active {
        left: 0;
    }
    
    .sidebar > ul > li {
        margin: 3px 10px;
    }
    
    .sidebar a {
        padding: 12px 15px;
        font-size: 14px;
    }
    
    .main-content {
        margin-left: 0;
        padding: 15px;
    }
    
    .cards {
        grid-template-columns: 1fr;
        gap: 15px;
        max-width: 500px;
        margin: 0 auto 30px auto;
    }
    
    .cardMemberprofile {
        width: 100px;
        height: 100px;
    }
    
    .blogPoint {
        grid-template-columns: 1fr;
        gap: 16px;
        padding: 10px 15px 25px;
    }
    
    .blogImage {
        height: 140px;
    }
    
    .blogContent h4 {
        font-size: 15px;
    }
    
    .table-card {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .plannedEventimg {
        height: 160px;
    }
    
    #PastEvents {
        gap: 18px;
        padding: 10px 15px 25px;
    }
    
    .past-eventDiv-img {
        height: 150px;
    }
    
    .message-popup-content, .message-popup-content-sendMessage {
        width: 95%;
        padding: 20px;
    }
}

/* Small Mobile (≤480px) */
@media (max-width: 480px) {
    :root {
        --header-height: 60px;
    }
    
    .sidebar {
        height: calc(100vh - 60px);
        top: 60px;
    }
    
    .sidebar a {
        padding: 10px 15px;
        font-size: 13px;
        gap: 10px;
    }
    
    .sidebar a i {
        font-size: 15px;
    }
    
    .innerlinksNav {
        gap: 12px;
    }
    
    .innerlinksNav-a {
        font-size: 12px !important;
    }
    
    #toggleMessagesReceivedMessages {
        padding: 6px 10px;
        font-size: 12px;
    }
    
    .cards {
        gap: 12px;
    }
    
    .card {
        padding: 15px;
        border-radius: 12px;
    }
    
    .card h5 {
        font-size: 16px;
    }
    
    .card h4 {
        font-size: 14px;
    }
    
    .card p {
        font-size: 13px;
    }
    
    .cardMemberprofile {
        width: 80px;
        height: 80px;
    }
    
    .MemberPaymentBtn {
        padding: 10px 15px;
        font-size: 13px;
    }
    
    #blogPoint, #PlannedEvents > h3, #PastEvents h4 {
        font-size: 20px;
        padding: 15px;
    }
    
    .blogImage {
        height: 120px;
    }
    
    .moreButton, .PastEventsmoreButton {
        font-size: 12px;
        padding: 7px 14px;
    }
    
    .eventDiv > h3 {
        font-size: 16px;
    }
    
    .plannedEventsBTN, .PastEventsmoreButton {
        width: 100%;
        text-align: center;
    }
    
    #PastEvents .eventDivindiv h3 {
        font-size: 12px;
    }
    
    .modal-content, .past-event-modal-content, .blog-post-modal-content {
        padding: 20px;
        margin: 10% auto;
    }
    
    .message-popup-content, .message-popup-content-sendMessage {
        padding: 15px;
    }
}
    </style>

    <script>
        // ===========================
        // DOM Ready Function
        // ===========================
        document.addEventListener('DOMContentLoaded', function() {
            initializeSidebar();
            initializePopups();
            initializeModals();
            initializePaymentDropdown();
            initializeInvoiceLinks();
            initializeBlogButtons();
            initializeEventRegistration();
            initializeResponsePopup();
            initializeMessageSystem();
        });

        // ===========================
        // SIDEBAR FUNCTIONALITY
        // ===========================
        function initializeSidebar() {
            const toggleButton = document.getElementById('toggleMenu');
            const sidebar = document.getElementById('sidebar');

            if (toggleButton && sidebar) {
                toggleButton.addEventListener('click', function() {
                    if (window.innerWidth <= 768) {
                        // Mobile: toggle active class
                        sidebar.classList.toggle('active');
                        if (sidebar.classList.contains('active')) {
                            toggleButton.innerHTML = 'X';
                            toggleButton.style.fontWeight = 'bold';
                            toggleButton.style.color = 'black';
                        } else {
                            toggleButton.innerHTML = '☰';
                        }
                    } else {
                        // Desktop: toggle display
                        if (sidebar.style.display === 'block') {
                            sidebar.style.display = 'none';
                            toggleButton.innerHTML = '☰';
                        } else {
                            sidebar.style.display = 'block';
                            toggleButton.innerHTML = 'X';
                            toggleButton.style.fontWeight = 'bold';
                            toggleButton.style.color = 'black';
                        }
                    }
                });
            }
        }

        // ===========================
        // PAYMENT POPUPS
        // ===========================
        function initializePopups() {
            // Toggle popup function
            window.togglePopup = function(popupId) {
                const popup = document.getElementById(popupId);
                const displayState = popup.style.display === 'flex' ? 'none' : 'flex';
                popup.style.display = displayState;
            };

            // Open popup on button click
            const openButtons = document.querySelectorAll('[data-popup-target]');
            const closeButtons = document.querySelectorAll('.popup-close');

            openButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const popupId = this.getAttribute('data-popup-target');
                    togglePopup(popupId);
                });
            });

            // Close popup on close button click
            closeButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const popup = this.closest('.popup-container');
                    if (popup) {
                        popup.style.display = 'none';
                    }
                });
            });

            // Close popup if clicking outside the popup content
            window.addEventListener('click', function(event) {
                if (event.target.classList.contains('popup-container')) {
                    event.target.style.display = 'none';
                }
            });
        }

        // ===========================
        // MODALS FUNCTIONALITY
        // ===========================
        function initializeModals() {
            // Planned Event Modal
            const plannedEventModal = document.getElementById("myModal");
            const openPostEventModal = document.getElementById("openPostEventModal");
            const closePlannedEventBtn = document.querySelector(".close");

            if (openPostEventModal && plannedEventModal) {
                openPostEventModal.onclick = function(event) {
                    event.preventDefault();
                    plannedEventModal.style.display = "block";
                };
            }

            if (closePlannedEventBtn && plannedEventModal) {
                closePlannedEventBtn.onclick = function() {
                    plannedEventModal.style.display = "none";
                };
            }

            // Past Event Modal
            const pastEventModal = document.getElementById("pastEventModal");
            const openPastEventModal = document.getElementById("openPastEventModal");
            const closePastEventBtn = document.querySelector(".close-past-event");

            if (openPastEventModal && pastEventModal) {
                openPastEventModal.onclick = function(event) {
                    event.preventDefault();
                    pastEventModal.style.display = "block";
                };
            }

            if (closePastEventBtn && pastEventModal) {
                closePastEventBtn.onclick = function() {
                    pastEventModal.style.display = "none";
                };
            }

            // Blog Post Modal
            const blogPostModal = document.getElementById("blogPostModal");
            const openBlogPostModal = document.getElementById("openBlogPostModal");
            const closeBlogPostBtn = document.querySelector(".close-blog-post");

            if (openBlogPostModal && blogPostModal) {
                openBlogPostModal.addEventListener("click", function(event) {
                    event.preventDefault();
                    blogPostModal.style.display = "block";
                });
            }

            if (closeBlogPostBtn && blogPostModal) {
                closeBlogPostBtn.addEventListener("click", function() {
                    blogPostModal.style.display = "none";
                });
            }

            // Message Send Modal
            const messagePopupSend = document.getElementById("messagePopupsend");
            const openMessagePopupSend = document.getElementById("openMessagePopupSend");
            const closeMessagePopupBtn = document.getElementById("messageClosePopupBtn");

            if (openMessagePopupSend && messagePopupSend) {
                openMessagePopupSend.addEventListener('click', function(event) {
                    event.preventDefault();
                    messagePopupSend.style.display = 'flex';
                });
            }

            if (closeMessagePopupBtn && messagePopupSend) {
                closeMessagePopupBtn.addEventListener('click', function(event) {
                    event.preventDefault();
                    messagePopupSend.style.display = 'none';
                });
            }

            // Close modals when clicking outside
            window.onclick = function(event) {
                if (event.target == plannedEventModal) {
                    plannedEventModal.style.display = "none";
                }
                if (event.target == pastEventModal) {
                    pastEventModal.style.display = "none";
                }
                if (event.target == blogPostModal) {
                    blogPostModal.style.display = "none";
                }
                if (event.target == messagePopupSend) {
                    messagePopupSend.style.display = 'none';
                }
            };
        }

        // ===========================
        // PAYMENT DROPDOWN
        // ===========================
        function initializePaymentDropdown() {
            const togglePayments = document.getElementById('togglePayments');
            if (togglePayments) {
                togglePayments.addEventListener('click', function(event) {
                    event.preventDefault();
                    const dropdown = document.getElementById('paymentsDropdown');
                    if (dropdown) {
                        dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
                        this.classList.toggle('active');
                    }
                });
            }
        }

        // ===========================
        // INVOICE LINKS
        // ===========================
        function initializeInvoiceLinks() {
            const invoiceLinks = document.querySelectorAll('.invoice-link');
            invoiceLinks.forEach(function(link) {
                link.addEventListener('click', function(event) {
                    event.preventDefault();

                    const invoiceId = link.getAttribute('data-id');
                    const invoiceDate = link.getAttribute('data-date');

                    document.getElementById('date').value = invoiceDate;

                    const invoiceForm = document.getElementById('invoiceForm');
                    if (invoiceForm) {
                        invoiceForm.submit();
                    }
                });
            });
        }

        // ===========================
        // BLOG BUTTONS
        // ===========================
        function initializeBlogButtons() {
            // Add event listener for the More button
            document.querySelectorAll('.moreButton').forEach(button => {
                button.addEventListener('click', function() {
                    const blogSingle = this.closest('.blogSingle');
                    const blogId = blogSingle.getAttribute('data-id');
                    window.location.href = 'blogs.php?id=' + blogId;
                });
            });
        }

        // ===========================
        // EVENT REGISTRATION
        // ===========================
        function initializeEventRegistration() {
            // This will be populated by PHP-generated JavaScript for each event
            // The PHP code in the main file will generate specific event handlers
        }

        // ===========================
        // RESPONSE POPUP
        // ===========================
        function initializeResponsePopup() {
            var popup = document.getElementById('response-popup');
            if (popup) {
                // Show the popup
                popup.classList.add('show');

                // Hide the popup after 10 seconds
                setTimeout(function() {
                    popup.classList.remove('show');
                }, 10000);
            }
        }

        // ===========================
        // MESSAGE SYSTEM
        // ===========================
        function initializeMessageSystem() {
            // Received Messages
            const toggleMessagesBtn = document.getElementById('toggleMessagesReceivedMessages');
            const messagePopup = document.getElementById('messagePopupReceivedMessages');
            const closeMessagePopup = document.getElementById('closePopupReceivedMessages');
            const closeFullMessage = document.getElementById('closeFullMessageReceivedMessages');
            const fullMessagePopup = document.getElementById('fullMessagePopupReceivedMessages');

            if (toggleMessagesBtn && messagePopup) {
                toggleMessagesBtn.addEventListener('click', function(event) {
                    event.preventDefault();
                    messagePopup.style.display = 'flex';
                });
            }

            if (closeMessagePopup && messagePopup) {
                closeMessagePopup.addEventListener('click', function() {
                    messagePopup.style.display = 'none';
                });
            }

            // Function to show full message
            window.showFullMessageReceivedMessages = function(message) {
                const fullMessageText = document.getElementById('fullMessageTextReceivedMessages');
                if (fullMessageText && fullMessagePopup) {
                    fullMessageText.textContent = message;
                    fullMessagePopup.style.display = 'flex';
                }
            };

            if (closeFullMessage && fullMessagePopup) {
                closeFullMessage.addEventListener('click', function() {
                    fullMessagePopup.style.display = 'none';
                });
            }

            // Close popups when clicking outside
            window.addEventListener('click', function(event) {
                if (event.target == messagePopup) {
                    messagePopup.style.display = 'none';
                }
                if (event.target == fullMessagePopup) {
                    fullMessagePopup.style.display = 'none';
                }
            });
        }

        // ===========================
        // QUILL EDITOR INITIALIZATION
        // ===========================
        function initializeQuillEditors() {
            // Planned Event Editor
            if (document.getElementById('quillEditor')) {
                var quill = new Quill('#quillEditor', {
                    theme: 'snow'
                });

                // Handle the form submission
                document.getElementById('eventForm').onsubmit = function(event) {
                    event.preventDefault();
                    var eventDescriptionInput = document.getElementById('eventDescription');
                    eventDescriptionInput.value = quill.root.innerHTML;
                    this.submit();
                };
            }

            // Past Event Editor
            if (document.getElementById('pastEventDetailsEditor')) {
                var pastEventQuill = new Quill('#pastEventDetailsEditor', {
                    theme: 'snow',
                    placeholder: 'Enter event details...',
                    modules: {
                        toolbar: [
                            [{
                                'header': '1'
                            }, {
                                'header': '2'
                            }],
                            [{
                                'list': 'ordered'
                            }, {
                                'list': 'bullet'
                            }],
                            ['bold', 'italic', 'underline'],
                            ['link', 'image']
                        ]
                    }
                });

                document.getElementById('pastEventForm').addEventListener('submit', function(event) {
                    event.preventDefault();
                    document.getElementById('pastEventDetails').value = pastEventQuill.root.innerHTML;
                    this.submit();
                });
            }

            // Blog Editor
            if (document.getElementById('editor')) {
                var blogQuill = new Quill('#editor', {
                    theme: 'snow',
                    placeholder: 'Write your blog content...',
                    modules: {
                        toolbar: [
                            [{
                                'header': '1'
                            }, {
                                'header': '2'
                            }],
                            [{
                                'list': 'ordered'
                            }, {
                                'list': 'bullet'
                            }],
                            ['bold', 'italic', 'underline'],
                        ]
                    }
                });

                document.getElementById('blogPostForm').addEventListener('submit', function(event) {
                    event.preventDefault();
                    document.getElementById('blogContent').value = blogQuill.root.innerHTML;
                    this.submit();
                });
            }
        }

        // Initialize Quill editors when Quill is loaded
        if (typeof Quill !== 'undefined') {
            initializeQuillEditors();
        } else {
            // Wait for Quill to load
            document.addEventListener('quill-loaded', initializeQuillEditors);
        }
    </script>

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