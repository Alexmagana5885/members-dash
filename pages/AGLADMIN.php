<?php
session_start();

if (!isset($_SESSION['loggedin']) || !$_SESSION['loggedin']) {
    header('Location: login.php'); // Redirect to login page if not logged in
    exit();
}

// Get the user role
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'member';

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <!-- <link rel="stylesheet" href="../assets/CSS/Dashboard.css">
    <link rel="stylesheet" href="../assets/CSS/popups.css"> -->
    <link href="../assets/img/favicon.png" rel="icon">
    <link href="../assets/img/favicon.png" rel="favicon.png">
    <link rel="stylesheet" href="../assets/CSS/Dash_Board.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet" />
    <link href="../assets/CSS/quilleditor.css" rel="stylesheet" />

</head>


<style>


    /* Base mobile styles */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: Arial, sans-serif;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        background-color: #ceddf1;
        overflow-x: hidden;
        font-size: 14px;
        /* Base font size for mobile */
    }



    /* Main content area - mobile first */
    .main-content {
        display: flex;
        flex-direction: column;
        width: 100%;
        flex: 1;
    }

    /* Sidebar - hidden by default on mobile */
    .sidebar {
        width: 280px;
        height: calc(100vh - var(--header-height-mobile, 60px));
        overflow-y: auto;
        background-color: #fff;
        transition: transform 0.3s ease;
        position: fixed;
        top: var(--header-height-mobile, 60px);
        left: -280px;
        z-index: 1000;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .sidebar.show-mobile {
        transform: translateX(280px);
    }

    /* Main content area */
    .mainContent {
        width: 100%;
        padding: 15px;
        background-color: #fff;
        min-height: calc(100vh - var(--header-height-mobile, 60px));
        overflow-y: auto;
    }

    /* Dashboard container */
    .dashboard {
        width: 100%;
        padding: 10px;
    }

    /* Cards grid - mobile */
    .cards {
        display: grid;
        grid-template-columns: 1fr;
        gap: 15px;
        margin-bottom: 20px;
    }

    .card {
        padding: 15px;
        border-radius: 12px;
    }

    .cardMemberprofile {
        width: 80px;
        height: 80px;
    }

    /* Blog section */
    .blogPoint {
        grid-template-columns: 1fr;
        gap: 15px;
        padding: 10px 15px 20px;
    }

    /* Planned events */
    .table-card {
        grid-template-columns: 1fr;
        gap: 15px;
        padding: 10px 15px;
    }

    /* Past events */
    #PastEvents {
        grid-template-columns: 1fr;
        gap: 15px;
        padding: 10px 15px 20px;
    }

    @media (min-width: 600px) {
        body {
            font-size: 15px;
        }


        .cards {
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .blogPoint {
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .table-card {
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        #PastEvents {
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .mainContent {
            padding: 20px;
        }

        .dashboard {
            padding: 15px;
        }

        .card {
            padding: 18px;
        }
    }

    @media (min-width: 769px) {
        body {
            font-size: 16px;
        }

        .main-content {
            flex-direction: row;
        }

        .sidebar {
            width: 280px;
            min-width: 280px;
            height: calc(100vh - var(--header-height-desktop, 83px));
            position: sticky;
            top: var(--header-height-desktop, 83px);
            overflow-y: auto;
            display: block !important;
            /* Force show on desktop */
        }

        .mainContent {
            width: calc(100% - 280px);
            padding: 25px;
            margin-left: 0;
        }

        .cards {
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .blogPoint {
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            padding: 10px 20px 30px;
        }

        .table-card {
            grid-template-columns: repeat(2, 1fr);
            gap: 25px;
            padding: 10px 20px;
        }

        #PastEvents {
            grid-template-columns: repeat(3, 1fr);
            gap: 25px;
            padding: 10px 20px 30px;
        }

        .menu-button {
            display: none !important;
        }

    }

    @media (min-width: 1025px) {
        .sidebar {
            width: 300px;
            min-width: 300px;
        }

        .mainContent {
            width: calc(100% - 300px);
        }

        .cards {
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
        }

        .blogPoint {
            grid-template-columns: repeat(4, 1fr);
        }

        .table-card {
            grid-template-columns: repeat(3, 1fr);
        }

        #PastEvents {
            grid-template-columns: repeat(4, 1fr);
        }
    }

    @media (min-width: 1440px) {
        .sidebar {
            width: 320px;
        }

        .mainContent {
            width: calc(100% - 320px);
            padding: 30px;
        }

        .cards {
            gap: 25px;
        }

        .card {
            padding: 20px;
        }
    }

    h1,
    h2,
    h3,
    h4,
    h5,
    h6 {
        word-wrap: break-word;
        overflow-wrap: break-word;
    }

    @media (max-width: 768px) {
        h1 {
            font-size: 24px;
        }

        h2 {
            font-size: 20px;
        }

        h3 {
            font-size: 18px;
        }

        h4 {
            font-size: 16px;
        }

        h5 {
            font-size: 14px;
        }

        h6 {
            font-size: 12px;
        }
    }
    @media (max-width: 768px) {

        .modal-content,
        .popup-content,
        .form-container {
            width: 95%;
            max-width: 95%;
            padding: 20px;
            margin: 10px;
            max-height: 90vh;
            overflow-y: auto;
        }

        .popup-container,
        .modal,
        .popup-form {
            padding: 10px;
        }

        .message-popup {
            width: 90%;
            right: 5%;
            max-width: 90%;
            max-height: 80vh;
        }

        input,
        select,
        textarea {
            font-size: 16px !important;
            /* Prevents iOS zoom */
        }

        /* Make buttons more tappable on mobile */
        button,
        .btn,
        a.btn,
        .menu-button {
            min-height: 44px;
            min-width: 44px;
            padding: 12px 16px;
        }
    }

    @media (max-width: 768px) {
        .table-container {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        table {
            min-width: 600px;
        }
    }

    @media (hover: none) and (pointer: coarse) {

        /* Disable hover effects on touch devices */
        .card:hover,
        .sidebar a:hover,
        button:hover {
            transform: none !important;
            box-shadow: none !important;
        }

        /* Increase touch targets */
        .sidebar a,
        .invoice-link,
        .dropdown a {
            padding: 16px 20px;
        }
    }

    @media (max-width: 768px) and (orientation: landscape) {
        .cards {
            grid-template-columns: repeat(2, 1fr);
        }

        .cardMemberprofile {
            width: 60px;
            height: 60px;
        }
    }

    @media (prefers-color-scheme: dark) {
        body {
           
            color: #ffffff;
        }

        .card,
        .sidebar,
        .mainContent {
            background-color: #1e1e1e;
            color: #ffffff;
        }
    }

    @media print {

        .sidebar,
        .menu-button,
        .popup-container,
        .modal,
        footer {
            display: none !important;
        }

        .mainContent {
            width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        body {
            background: white !important;
            color: black !important;
        }
    }

    @media (prefers-reduced-motion: reduce) {

        *,
        *::before,
        *::after {
            animation-duration: 0.01ms !important;
            animation-iteration-count: 1 !important;
            transition-duration: 0.01ms !important;
            scroll-behavior: auto !important;
        }
    }

    :root {
        /* Header heights */
        --header-height-mobile: 60px;
        --header-height-tablet: 70px;
        --header-height-desktop: 83px;

        /* Sidebar widths */
        --sidebar-width-tablet: 280px;
        --sidebar-width-desktop: 300px;
        --sidebar-width-large: 320px;

        /* Spacing */
        --spacing-xs: 5px;
        --spacing-sm: 10px;
        --spacing-md: 15px;
        --spacing-lg: 20px;
        --spacing-xl: 30px;

        /* Border radius */
        --radius-sm: 4px;
        --radius-md: 8px;
        --radius-lg: 12px;
        --radius-xl: 16px;
    }

    /* Apply variables */


    @media (min-width: 600px) {

    }

    @media (min-width: 769px) {


        .sidebar {
            top: var(--header-height-desktop);
            height: calc(100vh - var(--header-height-desktop));
        }
    }


    .hide-on-mobile {
        display: none !important;
    }

    @media (min-width: 769px) {
        .hide-on-mobile {
            display: block !important;
        }

        .hide-on-desktop {
            display: none !important;
        }
    }

    .show-on-mobile {
        display: block !important;
    }

    @media (min-width: 769px) {
        .show-on-mobile {
            display: none !important;
        }
    }
</style>


<body>

    <!-- header -->
    <header style="background-color: #a2daf4;">
        <div class="header-top">
            <div class="logo">
                <img src="../assets/img/logo.png" alt="AGL">
            </div>
            <button id="toggleMenu" class="menu-button" type="button">☰</button>
        </div>

        <div class="innerlinksNav">
            <a style="text-decoration: none;" class="innerlinksNav-a" href="#blogPoint">Blogs</a>
            <a style="text-decoration: none;" class="innerlinksNav-a" href="#PlannedEvents">Upcoming Events</a>
            <a style="text-decoration: none;" class="innerlinksNav-a" href="#PastEvents">Past Events</a>
            <a class="innerlinksNav-a" id="toggleMessagesReceivedMessages" href="#">Messages</a>
        </div>

    </header>

    <!-- header -->
    <?php
    require_once('../forms/DBconnection.php');

    $sessionEmail = $_SESSION['user_email'];
    $membershipType = $_SESSION['membership_type'];

    $query = "SELECT * FROM invoices WHERE user_email = ? ORDER BY id DESC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $sessionEmail);
    $stmt->execute();
    $result = $stmt->get_result();
    ?>

    <div class="main-content">

        <nav style="cursor: pointer;" id="sidebar" class="sidebar">
            <ul>

                <li>
                    <a href="https://www.agl.or.ke/" class="active core-link"><i class="fas fa-home"></i> Home</a>
                </li>

                <?php if ($role == 'superadmin'): ?>
                    <li><a id="openPostEventModal" class="core-link"><i class="fas fa-calendar-plus"></i> Planned Event</a></li>
                    <li><a id="openPastEventModal" class="core-link"><i class="fas fa-calendar-check"></i> Past Event</a></li>
                    <li><a id="openBlogPostModal" class="secondary-link"><i class="fas fa-blog"></i> New Blog</a></li>
                    <li><a id="openMessagePopupSend" class="secondary-link"><i class="fas fa-envelope"></i> Message</a></li>
                    <li><a id="MembersTable-link" href="Members.php" class="core-link"><i class="fas fa-users"></i> Members</a></li>
                    <li><a href="adminP.php" class="core-link"><i class="fas fa-credit-card"></i> Member Payments</a></li>
                    <li>
                        <a href="#" id="togglePayments" class="core-link"><i class="fas fa-file-invoice-dollar"></i> Payments Invoices</a>
                        <ul style="display: none;" class="dropdown" id="paymentsDropdown">
                            <?php
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo '<li>
                            <a href="#" class="invoice-link" target="_blank"
                               data-id="' . htmlspecialchars($row['id']) . '" 
                               data-date="' . htmlspecialchars($row['invoice_date']) . '" 
                               style="font-size: 12px; text-align: right;">
                                ' . htmlspecialchars($row['invoice_date']) . '
                            </a>
                          </li>';
                                }
                            } else {
                                echo '<li>No payments found</li>';
                            }
                            ?>
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
                    <li><a id="openPostEventModal" class="core-link"><i class="fas fa-calendar-plus"></i> Planned Event</a></li>
                    <li><a id="openBlogPostModal" class="secondary-link"><i class="fas fa-blog"></i> New Blog</a></li>
                    <li><a id="openMessagePopupSend" class="secondary-link"><i class="fas fa-envelope"></i> Message</a></li>
                    <li><a id="MembersTable-link" href="Members.php" class="core-link"><i class="fas fa-users"></i> Members</a></li>
                    <li><a href="adminP.php" class="core-link"><i class="fas fa-credit-card"></i> Member Payments</a></li>
                    <li>
                        <a href="#" id="togglePayments" class="core-link"><i class="fas fa-file-invoice-dollar"></i> Payments Invoices</a>
                        <ul style="display: none;" class="dropdown" id="paymentsDropdown">
                            <?php
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo '<li>
                            <a href="#" class="invoice-link" target="_blank"
                               data-id="' . htmlspecialchars($row['id']) . '" 
                               data-date="' . htmlspecialchars($row['invoice_date']) . '" 
                               style="font-size: 12px; text-align: right;">
                                ' . htmlspecialchars($row['invoice_date']) . '
                            </a>
                          </li>';
                                }
                            } else {
                                echo '<li>No payments found</li>';
                            }
                            ?>
                        </ul>
                    </li>
                    <li><a href="userinfo.php" target="_blank" class="secondary-link"><i class="fas fa-user"></i> User Information</a></li>
                    <li><a href="mailto:info@agl.or.ke" class="secondary-link"><i class="fas fa-envelope"></i> Email Us</a></li>
                    <li><a href="tel:+254748027123" class="secondary-link"><i class="fas fa-phone"></i> Call Us</a></li>
                    <li><a href="https://wa.me/254722605048" target="_blank" class="secondary-link"><i class="fab fa-whatsapp"></i> Chat on WhatsApp</a></li>
                    <li><a href="../forms/logout.php" class="secondary-link"><i class="fas fa-sign-out-alt"></i> Logout</a></li>

                <?php elseif ($role == 'member'): ?>
                    <li>
                        <a href="https://www.agl.or.ke/" class="active core-link"><i class="fas fa-home"></i> Home</a>
                    </li>
                    <li><a class="core-link"><i class="fas fa-calendar-plus"></i> Planned Event</a></li>
                    <li><a class="core-link"><i class="fas fa-calendar-check"></i> Past Event</a></li>
                    <li><a class="secondary-link"><i class="fas fa-blog"></i> New Blog</a></li>
                    <li><a class="secondary-link"><i class="fas fa-envelope"></i> Message</a></li>
                    <li><a href="Members.php" class="core-link"><i class="fas fa-users"></i> Members</a></li>
                    <li><a href="adminP.php" class="core-link"><i class="fas fa-credit-card"></i> Member Payments</a></li>
                    <li>
                        <a href="#" id="togglePayments" class="core-link"><i class="fas fa-file-invoice-dollar"></i> Payments Invoices</a>
                        <ul style="display: none;" class="dropdown" id="paymentsDropdown">
                            <?php
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo '<li>
                            <a href="#" class="invoice-link" target="_blank"
                               data-id="' . htmlspecialchars($row['id']) . '" 
                               data-date="' . htmlspecialchars($row['invoice_date']) . '" 
                               style="font-size: 12px; text-align: right;">
                                ' . htmlspecialchars($row['invoice_date']) . '
                            </a>
                          </li>';
                                }
                            } else {
                                echo '<li>No payments found</li>';
                            }
                            ?>
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
            <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
        </nav>


        <?php

        if (!isset($_SESSION['user_email'])) {
            header('Location: login.php');
            exit();
        }
        require_once('../forms/DBconnection.php');

        $userEmail = $_SESSION['user_email'];
        $sql = "SELECT id, name, phone, home_address FROM personalmembership WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $userEmail);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $userId = $user['id'];
            $userName = $user['name'];
            $userPhone = $user['phone'];
            $userAddress = $user['home_address'];
        } else {
            echo "User data not found!";
            exit();
        }

        ?>

        <form id="invoiceForm" action="../forms/Invoice.php" method="POST">
            <input type="hidden" name="user_email" id="user_email" value="<?php echo htmlspecialchars($userEmail); ?>">
            <input type="hidden" name="user_type" id="user_type" value="<?php echo htmlspecialchars($_SESSION['membership_type'] ?? ''); ?>">
            <input type="hidden" name="date" id="date">

            <input type="hidden" name="name" id="name" value="<?php echo htmlspecialchars($userName); ?>">
            <input type="hidden" name="user_id" id="user_id" value="<?php echo htmlspecialchars($userId); ?>">
            <input type="hidden" name="address" id="address" value="<?php echo htmlspecialchars($userAddress); ?>">
            <input type="hidden" name="phone" id="phone" value="<?php echo htmlspecialchars($userPhone); ?>">
        </form>


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

                <!-- Memberprofile -->
                <div class="card">
                    <img class="cardMemberprofile" src="<?php echo htmlspecialchars($passportImage); ?>"
                        alt="User Image">
                    <h5><?php echo htmlspecialchars($name); ?></h5>
                    <hr><br>
                    <h4><?php echo htmlspecialchars($userEmail); ?></h4>
                    <p>Registration Date: <?php echo htmlspecialchars($registrationDate); ?></p>
                </div>

                <?php

                // Get the session email
                $sessionEmail = $_SESSION['user_email'];



                // Query to get the latest transaction for the current user
                $query = "SELECT amount, timestamp 
FROM member_payments 
WHERE member_email = ? 
ORDER BY timestamp DESC 
LIMIT 1";

                $stmt = $conn->prepare($query);
                $stmt->bind_param("s", $sessionEmail);
                $stmt->execute();
                $stmt->bind_result($amount, $transaction_date);
                $stmt->fetch();
                $stmt->close();

                if ($transaction_date) {
                    $lastPaymentDate = date("d/m/Y", timestamp: strtotime($transaction_date));

                    $nextPaymentDate = date("d/m/Y", strtotime("+1 year", strtotime($transaction_date)));
                    $currentBalance = $amount;
                } else {
                    // No transactions found, set default values
                    $lastPaymentDate = "N/A";
                    $nextPaymentDate = "N/A";
                    $currentBalance = "0";
                }

                // Check in personalmembership table
                $query_personal = "SELECT payment_Number, payment_code FROM personalmembership WHERE email = ?";
                $stmt = $conn->prepare($query_personal);
                $stmt->bind_param("s", $sessionEmail);
                $stmt->execute();
                $stmt->bind_result($paymentNumberPersonal, $paymentCodePersonal);
                $stmt->fetch();
                $stmt->close();

                // Check in organizationmembership table
                $query_organization = "SELECT payment_Number, payment_code FROM organizationmembership WHERE organization_email = ?";
                $stmt = $conn->prepare($query_organization);
                $stmt->bind_param("s", $sessionEmail);
                $stmt->execute();
                $stmt->bind_result($paymentNumberOrganization, $paymentCodeOrganization);
                $stmt->fetch();
                $stmt->close();

                // Default: Disable button if payment info exists
                $disablePaymentButton = false;

                // Check which table the email belongs to and if fields are filled

                if (!empty($paymentNumberPersonal) || !empty($paymentCodePersonal)) {
                    $disablePaymentButton = true;
                } elseif (!empty($paymentNumberOrganization) || !empty($paymentCodeOrganization)) {
                    $disablePaymentButton = true;
                }

                ?>

                <!-- Member Payments  -->
                <div class="card">
                    <h5>Member Payments</h5>
                    <hr>
                    <p id="memberpayments-current-lastPay">Last payment: <span><?php echo $lastPaymentDate; ?></span></p>
                    <p id="memberpayments-current-nextP">Next payment: <span><?php echo $nextPaymentDate; ?></span></p>
                    <p id="memberpayments-current-balance">Last Amount Paid: <span><?php echo $currentBalance; ?>sh</span></p>
                    <hr>

                    <!-- Adjust the button to be inactive if payment data exists -->
                    <button class="MemberPaymentBtn" id="mpesa-btn" data-popup-target="mpesa-popup"
                        style="<?php echo $disablePaymentButton ? 'background-color: lightblue; cursor: not-allowed;' : ''; ?>"
                        <?php echo $disablePaymentButton ? 'disabled' : ''; ?>>
                        Pay Membership Fee
                    </button>

                    <button class="MemberPaymentBtn" id="memberpayments-btn" data-popup-target="memberpayments-popup">
                        Pay Membership Premium
                    </button>
                </div>


                <!--  Payment Popup Structure registration -->
                <div id="mpesa-popup" class="popup-container">
                    <form id="mpesa-popup-content" class="popup-content" method="POST"
                        action="../forms/Payment/Mpesa-Daraja-Api-main/stkpush.php">
                        <span class="popup-close" onclick="togglePopup('mpesa-popup')">X</span>
                        <img src="../assets/img/mpesa.png" alt="M-Pesa" class="popup-logo">
                        <p class="popup-confirmation">Confirm that you are making a payment of Two Thousand Kenyan
                            Shillings (2,000 Ksh) as membership fees to the Association of Government Librarians.</p>

                        <label for="User-email" class="popup-label">User Email</label>
                        <input type="email" id="User-email" name="User-email" class="popup-input"
                            value="<?php echo htmlspecialchars($userEmail); ?>" required readonly>

                        <label for="mpesa-phone-number" class="popup-label">Number</label>
                        <input type="number" id="mpesa-phone-number" name="phone_number" class="popup-input"
                            placeholder="Enter your phone number" required>

                        <label for="mpesa-amount" class="popup-label">Amount</label>
                        <input type="text" id="mpesa-amount" name="amount" class="popup-input" value="2000" readonly>

                        <!-- Hidden field to store the referring page URL -->
                        <input type="hidden" id="referringPage" name="referringPage"
                            value="<?php echo htmlspecialchars($_SERVER['HTTP_REFERER']); ?>">

                        <div class="popup-buttons">
                            <input type="hidden" id="hiddenFormData" name="formData">
                            <button class="popup-btn" type="submit">Make Payment</button>
                        </div><br><br>

                        <!-- <p>if you made payment and did not respond, kindly fill the MPESA code here</p><br>

                        <h4>Paybill: 34758</h4>

                        <input type="text" id="mpesa-code" name="mpesa_code" class="popup-input" placeholder="Enter your M-Pesa payment code" required oninput="this.value = this.value.toUpperCase()">
                        <a style="text-decoration: none; width: 100%; " class="popup-btn" href="sumbit">send</a> -->


                    </form>
                </div>

                <!-- Payment Popup Structure membership -->

                <div id="memberpayments-popup" class="popup-container">
                    <form action="../forms/Payment/Mpesa-Daraja-Api-main/STKMembersubscription.php" method="post" id="memberpayments-popup-content" class="popup-content">
                        <span class="popup-close" onclick="togglePopup('memberpayments-popup')">X</span>
                        <img src="../assets/img/mpesa.png" alt="M-Pesa" class="popup-logo">
                        <p class="popup-description">Confirm that you are making a payment of 3,600 Ksh as annual
                            membership fees to the Association of Government Librarians.</p>

                        <label for="User-email" class="popup-label">User Email</label>
                        <input type="email" id="User-email" name="User-email" class="popup-input" value="<?php echo htmlspecialchars($userEmail); ?>" required readonly>

                        <label for="phone_number" class="popup-label">Number</label>
                        <input type="text" id="phone_number" name="phone_number" class="popup-input" placeholder="Enter your phone number" required>

                        <label for="amount" class="popup-label">Amount</label>
                        <input type="text" id="amount" name="amount" class="popup-input" value="3600" readonly>

                        <div class="popup-buttons">
                            <button class="popup-btn" type="submit">Make Payment</button>
                        </div>
                    </form>
                </div>




                <?php

                $sessionEmail = $_SESSION['user_email'];

                // Fetch education information from the database
                $query = "SELECT highest_degree, institution, graduation_year FROM personalmembership WHERE email = ?";
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
                        'graduation_year' => 'N/A'
                    ];
                }
                ?>
                <!-- Education Information -->
                <div class="card">
                    <h5>Education Information</h5>
                    <hr>
                    <p>Highest Degree: <span
                            id="highest-degree"><?php echo htmlspecialchars($educationInfo['highest_degree']); ?></span>
                    </p><br>
                    <p>Institution: <span
                            id="institution"><?php echo htmlspecialchars($educationInfo['institution']); ?></span></p>
                    <br>

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
                $sql = "SELECT event_name, event_location, member_email,event_id, invitation_card, event_date FROM event_registrations WHERE member_email = ? ORDER BY event_date ASC";
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

                <!-- Registered Events -->
                <div class="card">
                    <h4>Registered Events</h4>

                    <hr>
                    <?php
                    if ($resultmessage->num_rows > 0) {
                        while ($row = $resultmessage->fetch_assoc()) {
                            $uniqueId = strtolower(str_replace(' ', '_', htmlspecialchars($row['event_name']))) . '_' . htmlspecialchars($row['event_date']);

                            echo '<div>';
                            echo '<h5>' . htmlspecialchars($row['event_name']) . '</h5>';
                            echo '<p>' . htmlspecialchars($row['event_location']) . '</p>';
                            echo '<p>' . htmlspecialchars($row['event_date']) . '</p>';

                            echo '<p hidden>' . htmlspecialchars($row['event_id']) . '</p>';

                            // Create a form that will send data to event_card.php
                            echo '<form method="POST" action="../forms/event_card.php">';
                            echo '<input type="hidden" name="email" value="' . $row['member_email'] . '">';
                            echo '<input type="hidden" name="eventName" value="' . $row['event_id'] . '">';

                            // Use the invitation_card path for the download
                            echo '<button style="background-color: #007bff;" class="iventcard" type="submit">Download Invitation Card</button>';
                            echo '</form>';

                            echo '</div>';
                            echo '<hr>';
                        }
                    } else {
                        echo '<p>No upcoming registered events.</p>';
                    }
                    ?>


                </div>

                <!-- styling for 1st 4 cards -->


            </div>


            <!-- error response-popups-->
            <div>


                <?php
                if (isset($_SESSION['response'])) {
                    $response = $_SESSION['response'];

                    echo '<div id="response-popup" class="popup">';
                    if (!$response['success']) {
                        // Display error messages
                        if (!empty($response['errors'])) {
                            echo '<div class="alert alert-danger">';
                            foreach ($response['errors'] as $error) {
                                echo "<p>$error</p>";
                            }
                            echo '</div>';
                        } else {
                            echo '<div class="alert alert-danger">' . $response['message'] . '</div>';
                        }
                    } else {
                        // Display success message
                        echo '<div class="alert alert-success">' . $response['message'] . '</div>';
                    }
                    echo '</div>';

                    // Clear the session response
                    unset($_SESSION['response']);
                }
                ?>

                <?php

                // Check if there's a response in the session
                if (isset($_SESSION['response'])) {
                    $response = $_SESSION['response'];

                    // Display the response popup
                    echo '<div id="response-popup" class="popup">';

                    // Check if the response indicates an error
                    if (!$response['success']) {
                        // Display error messages
                        if (!empty($response['errors'])) {
                            echo '<div class="alert alert-danger">';
                            foreach ($response['errors'] as $error) {
                                echo "<p>$error</p>";
                            }
                            echo '</div>';
                        } else {
                            echo '<div class="alert alert-danger">' . htmlspecialchars($response['message']) . '</div>';
                        }
                    } else {
                        // Display success message
                        echo '<div class="alert alert-success">' . htmlspecialchars($response['message']) . '</div>';
                    }

                    echo '</div>';

                    // Clear the session response
                    unset($_SESSION['response']);
                }
                ?>

            </div>





            <!-- blogs -->

            <h4 id="blogPoint" style="padding: 20px;">Blogs</h4>
            <div class="blogPoint">
                <?php
                // Query to get blog posts
                $sql = "SELECT * FROM blog_posts";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    // Output data of each row
                    while ($row = $result->fetch_assoc()) {
                        echo '<div class="blogSingle" data-title="' . htmlspecialchars($row["title"], ENT_QUOTES, 'UTF-8') . '" data-content="' . htmlspecialchars($row["content"], ENT_QUOTES, 'UTF-8') . '" data-id="' . htmlspecialchars($row["id"], ENT_QUOTES, 'UTF-8') . '">';
                        echo '    <div class="blogImage"><img src="' . htmlspecialchars($row["image_path"], ENT_QUOTES, 'UTF-8') . '" alt="Blog"></div>';
                        echo '    <div class="blogContent">';
                        echo '        <h4>' . htmlspecialchars($row["title"], ENT_QUOTES, 'UTF-8') . '</h4>';
                        echo '        <h6>' . date("d/m/Y", strtotime($row["created_at"])) . '</h6>';
                        echo '        <button class="moreButton">Read More</button>'; // More button
                        echo '    </div>';
                        echo '</div>';
                    }
                } else {
                    echo "0 results";
                }
                // $conn->close();
                ?>
            </div>

            <!-- blogs -->



            <!-- .............................. -->
            <!-- $user_email = $_SESSION['user_email']; -->
            <!-- messages -->

            <?php

            $session_email = $_SESSION['user_email'];
            // if (isset($_SESSION['user_email'])) {

            //     echo "Logged in as: " . htmlspecialchars($_SESSION['user_email'], ENT_QUOTES);
            // } else {
            //     echo "No email found in session.";
            // }

            $messages = [];

            // Check if the email is in the `personalmembership` table
            $checkPersonalMembershipQuery = "SELECT * FROM personalmembership WHERE email = ?";
            $stmt = $conn->prepare($checkPersonalMembershipQuery);
            $stmt->bind_param("s", $session_email);
            $stmt->execute();
            $result = $stmt->get_result();
            $personalMember = $result->fetch_assoc();

            if ($personalMember) {
                // Fetch all messages from `membermessages` without filtering by recipient group
                $memberMessagesQuery = "SELECT * FROM membermessages";
                $result = $conn->query($memberMessagesQuery);
                while ($row = $result->fetch_assoc()) {
                    $messages[] = $row;
                }

                // Check if the email is also in the `officialsmembers` table
                $checkOfficialMembershipQuery = "SELECT * FROM officialsmembers WHERE personalmembership_email = ?";
                $stmt = $conn->prepare($checkOfficialMembershipQuery);
                $stmt->bind_param("s", $session_email);
                $stmt->execute();
                $result = $stmt->get_result();
                $officialMember = $result->fetch_assoc();

                if ($officialMember) {
                    // Fetch all messages from `officialmessages` without filtering by recipient group
                    $officialMessagesQuery = "SELECT * FROM officialmessages";
                    $result = $conn->query($officialMessagesQuery);
                    while ($row = $result->fetch_assoc()) {
                        $messages[] = $row;
                    }
                }
            }
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


            <!-- planned ivents -->

            <?php


            // Check if the email is set in the session
            $userEmail = isset($_SESSION['user_email']) ? htmlspecialchars($_SESSION['user_email']) : '';

            $sql = "SELECT id, event_name, event_image_path, event_description, event_location, event_date, RegistrationAmount 
            FROM plannedevent 
            ORDER BY event_date DESC";
            $result = $conn->query($sql);

            $sql = "SELECT * FROM personalmembership WHERE email = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $userEmail);
            $stmt->execute();
            $Mresult = $stmt->get_result();

            // Fetch data
            if ($row = $Mresult->fetch_assoc()) {
                $name = $row['name'];
            } else {
                echo "No user found";
            }

            ?>

            <!-- planned event style -->



            <!-- PlannedEvents -->

            <div id="PlannedEvents" class="MinPrtSecSpace">
                <h3 style="padding:20px;">Planned Events</h3>
                <div class="table-card">
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $eventId = htmlspecialchars($row['id']);
                            $eventName = htmlspecialchars($row['event_name']);
                            $eventImagePath = htmlspecialchars($row['event_image_path']);
                            $eventDescription = $row['event_description'];
                            $eventLocation = htmlspecialchars($row['event_location']);
                            $eventDate = htmlspecialchars($row['event_date']);
                            $registrationAmount = htmlspecialchars($row['RegistrationAmount']);

                            // Fetch content from the database
                            // Fetch content from the database
                            echo '<div class="eventDiv">';
                            echo '<h3>' . $eventName . '</h3>';
                            echo '<img class="plannedEventimg" src="' . $eventImagePath . '" alt="Event">';
                            echo '<div class="quill-content">' . $eventDescription . '</div>';

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
                            echo '<form action="../forms/Payment/Mpesa-Daraja-Api-main/StkPushEvent.php" method="post">';
                            echo '<input type="hidden" name="event_id" value="' . $eventId . '">';
                            echo '<input type="hidden" name="event_name" value="' . $eventName . '">';
                            echo '<input type="hidden" name="event_location" value="' . $eventLocation . '">';
                            echo '<input type="hidden" name="event_date" value="' . $eventDate . '">';

                            echo '<label for="memberEmail">Member Email:</label>';
                            echo '<input type="email" id="memberEmail" name="User-email" value="' . $userEmail . '" readonly>';

                            echo '<label for="memberName">Name:</label>';
                            echo '<input type="text" id="memberName" name="memberName" value="' . $name . '" readonly>';

                            echo '<label for="contact">Contact:</label>';
                            echo '<input type="text" id="contact" name="phone_number" required>';

                            echo '<label for="contact">Registration Amount. Ksh ' . number_format($registrationAmount) . '</label>';
                            echo '<input value="' . number_format($registrationAmount,) . '" readonly type="text" id="contact" name="amount" required>';

                            echo '<button type="submit">Pay and Submit</button>';
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

            <!-- PlannedEvents -->

            <script>
                document.addEventListener('DOMContentLoaded', function() {
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

                            registerBtnEventRegistration_<?php echo $eventId; ?>.addEventListener('click', function() {
                                popupFormEventRegistration_<?php echo $eventId; ?>.style.display = 'flex';
                            });

                            closeBtn_<?php echo $eventId; ?>.addEventListener('click', function() {
                                popupFormEventRegistration_<?php echo $eventId; ?>.style.display = 'none';
                            });

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

            <!-- ........................................ -->

            <div>
            </div>


            <!-- popups -->

            <!-- Post Planned Event Modal -->
            <!-- Modal and form -->
            <div id="myModal" class="modal">
                <div class="modal-content">
                    <span class="close">&times;</span>
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
                            <!-- Quill Editor Container -->
                            <div id="quillEditor" style="height: 200px;"></div>
                            <!-- Hidden field to store the formatted content -->
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

            <script src="../assets/JS/quilleditor.js"></script>
            <!-- <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script> -->




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
                            <label for="pastEventDetails">Event Details</label>
                            <div id="pastEventDetailsEditor" style="height: 200px; padding: 10px;"></div>
                            <!-- Hidden input to store the content -->
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
                            <input type="file" id="pastEventDocuments" name="eventDocuments[]" accept=".pdf" multiple
                                required />

                        </div>
                        <div class="past-event-form-group">
                            <button type="submit">Save Past Event</button>
                        </div>
                    </form>
                </div>
            </div>


            <!-- quill -->

            <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>




            <!-- Past Events Modal script-->



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
                            <input type="email" id="messageSenderEmail" name="sender_email" value="info@agl.or.ke"
                                required readonly />
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



            <!-- Blog Post Modal -->
            <div id="blogPostModal" class="blog-post-modal">
                <div class="blog-post-modal-content">
                    <span class="close-blog-post">&times;</span>
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

            <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>


            <!-- Blog Post Modal script -->


            <!-- JavaScript Files -->


            <?php
            // Close the database connection
            // $conn->close();
            ?>

            <!-- past Events -->

            <h4 style="margin: 20px;">past Events</h4>

            <?php
            // Step 2: Fetch Data from the Database
            $sql = "SELECT * FROM pastevents";
            $result = $conn->query($sql);

            // Step 3: Display Data in HTML
            if ($result->num_rows > 0) {
                echo '<div class="MinPrtSecSpace">
                <div id= "PastEvents" class="table-card">';
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

                    $eventId = htmlspecialchars($row["id"]); // Get the event ID

                    echo '<div class="eventDiv">
                    <div class="eventDivindiv">
                    <h3>' . htmlspecialchars($row["event_location"]) . '</h3>
                    <h3>' . htmlspecialchars($row["event_date"]) . '</h3></div>
                    <img class="past-eventDiv-img" src="' . $imagePath . '" alt="Event">
                    <p>' . htmlspecialchars($row["event_name"]) . '</p><br>
                    <a href="Event.php?id=' . $eventId . '" class="PastEventsmoreButton">More</a> 
        
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



            <!-- past Events -->



        </section>



    </div>

    <footer style="font-size: 11px; font-style: italic; text-align: center; background-color: #b4ccdb; color: black; ">
        <p>&copy; 2024 <a style="text-decoration: none;" href="AGL.or.ke">http://www.agl.or.ke/</a> . All rights
            reserved.</p>
    </footer>

    <script src="../assets/JS/Dash_Board.js"></script>



</body>

</html>