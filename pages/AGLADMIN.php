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
    <link rel="stylesheet" href="../assets/CSS/Dashboard.css">
    <link rel="stylesheet" href="../assets/CSS/popups.css">
    <link href="../assets/img/favicon.png" rel="icon">
    <link href="../assets/img/favicon.png" rel="favicon.png">
    <link rel="stylesheet" href="../assets/CSS/AGLADMIN.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet" />
    <link href="../assets/CSS/quilleditor.css" rel="stylesheet" />

</head>

<style>
    /* General styles for Quill content */
    .quill-content {
        font-family: Arial, sans-serif;
        /* Set your desired font family */
        line-height: 1.6;
        /* Increase line height for readability */
        color: #333;
        /* Set a color for the text */
    }

    /* Styling headers */
    .quill-content h1,
    .quill-content h2,
    .quill-content h3 {
        color: #0056b3;
        /* Example header color */
        margin: 0.5em 0;
        /* Add some margin for spacing */
    }

    /* Styling paragraphs */
    .quill-content p {
        margin-bottom: 1em;
        /* Space between paragraphs */
    }

    /* Styling lists */
    .quill-content ol,
    .quill-content ul {
        margin-left: 20px;
        /* Indent lists */
        margin-bottom: 1em;
        /* Space below lists */
    }

    /* Adding a custom style for blockquotes */
    .quill-content blockquote {
        border-left: 4px solid #ccc;
        /* Left border for blockquotes */
        padding-left: 1em;
        /* Space inside blockquotes */
        color: #666;
        /* Color for blockquotes */
        font-style: italic;
        /* Italic style for blockquotes */
    }

    /* Custom styles for images */
    .quill-content img {
        max-width: 100%;
        /* Ensure images do not overflow their container */
        height: auto;
        /* Maintain aspect ratio */
        display: block;
        /* Block display to add margin */
        margin: 0 auto;
        /* Center images */
    }

    /* Sidebar icons */
    .sidebar ul li a i {
        margin-right: 10px;
        width: 20px;
        text-align: center;
    }
</style>

<body>

    <!-- header -->
    <header style="background-color: #a2daf4;">
        <div class="header-top">
            <div class="logo">
                <img src="../assets/img/logo.png" alt="AGL">
            </div>
            <div id="toggleMenu" class="menu-button" onclick="toggleMenu()">☰</div>
        </div>

        <div class="innerlinksNav">
            <a style="text-decoration: none;" class="innerlinksNav-a" href="#blogPoint">Blogs</a>
            <a style="text-decoration: none;" class="innerlinksNav-a" href="#PlannedEvents">Upcoming Events</a>
            <a style="text-decoration: none;" class="innerlinksNav-a" href="#PastEvents">Past Events</a>
            <a class="innerlinksNav-a" id="toggleMessagesReceivedMessages" href="#">Messages</a>
        </div>

    </header>

    <!-- <div class="Aligner"></div> -->

    <style>
        /* body {
            padding-top: var(--header-height);
        } */

        :root {
            --header-height: 83px;
        }

        @media (max-width: 768px) {
            :root {
                --header-height: 70px;
            }
        }

        @media (max-width: 480px) {
            :root {
                --header-height: 60px;
            }
        }


        /* Header Container */
        header {
            background-color: #FFFFFF;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08);
            padding: 15px 30px;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
box-shadow: 
  0 6px 15px -3px rgba(30, 91, 198, 0.8);


            font-family: 'Segoe UI', 'Helvetica Neue', Arial, sans-serif;
            gap: 20px;
            /* position: fixed; */
            position: sticky;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1100;
            box-sizing: border-box;

        }



        /* Header top row - logo and menu button */
        .header-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            flex: 0 0 auto;
        }

        /* Logo Container */
        .logo {
            display: flex;
            align-items: center;
            flex: 0 0 auto;
        }

        /* Logo Image */
        .logo img {
            height: 45px;
            width: auto;
            max-width: 180px;
            object-fit: contain;
        }

        /* Navigation Links Container */
        .innerlinksNav {
            display: flex;
            justify-content: flex-end;
            gap: 35px;
            align-items: center;
            transition: all 0.3s ease;
            flex: 1 0 100%;
            order: 2;
        }

        /* Navigation Links (Regular) */
        .innerlinksNav-a {
            color: #2C3E50;
            text-decoration: none;
            font-weight: 600;
            font-size: 16px;
            padding: 8px 0;
            position: relative;
            transition: color 0.2s ease;
            white-space: nowrap;
        }

        /* Messages Button Special Styling */
        #toggleMessagesReceivedMessages {
            color: #1E5BC6;
            text-decoration: none;
            font-weight: 700;
            font-size: 16px;
            padding: 8px 16px;
            background-color: #F0F7FF;
            border-radius: 6px;
            border: 1px solid #D1E3FF;
            transition: all 0.2s ease;
            white-space: nowrap;
        }

        /* Mobile Menu Button */
        .menu-button {
            display: none;
            cursor: pointer;
            font-size: 28px;
            color: #1E5BC6;
            padding: 5px 10px;
            background: #F8FAFF;
            border-radius: 4px;
            border: 1px solid #E3EFFF;
            transition: all 0.2s ease;
            order: 2;
        }

        /* Hover effect for regular navigation links */
        .innerlinksNav-a:not(:last-child):hover {
            color: #1E5BC6;
        }

        /* Underline animation on hover */
        .innerlinksNav-a:not(:last-child):hover::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 3px;
            background-color: #1E5BC6;
            border-radius: 2px;
        }

        /* Messages button hover effect */
        #toggleMessagesReceivedMessages:hover {
            background-color: #1E5BC6;
            color: #FFFFFF;
            border-color: #1E5BC6;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(30, 91, 198, 0.2);
        }

        /* Desktop (≥993px) - Links aligned to the right */
        @media (min-width: 993px) {
            header {
                flex-wrap: nowrap;
                gap: 0;
            }

            .header-top {
                width: auto;
                flex: 0 0 auto;
            }

            .innerlinksNav {
                flex: 1;
                justify-content: flex-end;
                order: 2;
                flex-basis: auto;
            }

            .menu-button {
                display: none !important;
            }
        }

        /* Tablet (769px - 992px) */
        @media (min-width: 769px) and (max-width: 992px) {
            .innerlinksNav {
                gap: 17px;
            }

            .innerlinksNav-a {
                font-size: 13px !important;
            }
        }

        /* Mobile (≤768px) */
        @media (max-width: 768px) {
            header {
                padding: 15px 20px;
                flex-direction: column;
                align-items: stretch;
                gap: 15px;
            }

            .header-top {
                width: 100%;
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
                margin-top: 0;
                padding: 0;
                background-color: transparent;
                box-shadow: none;
                border: none;
                gap: 12px;
            }

            .innerlinksNav-a {
                width: auto;
                padding: 7px 0 !important;
                text-align: left;
                border-bottom: none;
                font-size: 12px !important;
            }

            .innerlinksNav-a:last-child {
                margin-top: 0;
                padding: 10px 15px !important;
            }

            /* Remove hover underline on mobile */
            .innerlinksNav-a:not(:last-child):hover::after {
                display: none;
            }
        }

        /* Small Mobile (≤480px) */
        @media (max-width: 480px) {
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
        }
    </style>

    <!-- header -->

    <style>
        .blogPoint {
            width: 100%;
            background-color: #fff;
            min-height: 200px;
            padding: 10px;
            border-radius: 10px;
            display: flex;
            gap: 10px;
            overflow-x: auto;
            overflow-y: hidden;
            scrollbar-width: thin;
            max-height: 600px;
        }

        .Singleblog {
            flex: 0 0 auto;
            width: 300px;
            /* Fixed width for horizontal scrolling */
            display: flex;
            flex-direction: column;
            padding: 10px;
            background-color: #f9f9f9;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .blogImage {
            width: 100%;
            height: 200px;
            margin-bottom: 10px;
            border-radius: 20px 0 50px 0;
            overflow: hidden;
        }

        .blogImage img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .blogcontent {
            width: 100%;
            text-align: left;
        }

        .blogcontent p {
            overflow: auto;
            scrollbar-width: thin;
            max-height: 200px;
            padding: 5px;
            margin: 10px 0;
        }

        @media screen and (max-width: 600px) {
            .Singleblog {
                width: 90%;
                /* Take most of the screen width on small screens */
                margin-bottom: 10px;
            }
        }
    </style>


    <!-- for the invoice dropdown -->
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

        <!-- navigation sidebar -->

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

        <style>
            /* Sidebar Container */
            .sidebar {
                background: linear-gradient(180deg, #ffffff 0%, #f8faff 100%);
                width: 240px;
                height: auto;
                /* height: calc(100vh - 83px); */
                position: fixed;
                top: var(--header-height);
                bottom: 0;
                /* top: 83px; */
                left: 0;
                overflow-y: auto;
                overflow-x: hidden;
                box-shadow: 2px 0 15px rgba(30, 91, 198, 0.08);
                box-shadow: 3px 0 6px rgba(30, 91, 198, 0.25);
                font-family: 'Segoe UI', 'Helvetica Neue', Arial, sans-serif;
                z-index: 1000;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }


            /* Sidebar Scrollbar Styling */
            .sidebar::-webkit-scrollbar {
                width: 6px;
            }

            .sidebar::-webkit-scrollbar-track {
                background: #f1f5ff;
                border-radius: 3px;
            }

            .sidebar::-webkit-scrollbar-thumb {
                background: #1E5BC6;
                border-radius: 3px;
            }

            .sidebar::-webkit-scrollbar-thumb:hover {
                background: #174a9e;
            }

            /* Main List */
            .sidebar ul {
                list-style: none;
                padding: 20px 0;
                margin: 0;
            }

            .sidebar>ul>li {
                position: relative;
                margin: 4px 15px;
            }

            /* Main Links */
            .sidebar a {
                display: flex;
                align-items: center;
                padding: 14px 20px;
                color: #2C3E50;
                text-decoration: none;
                font-size: 15px;
                font-weight: 500;
                border-radius: 8px;
                transition: all 0.25s ease;
                gap: 12px;
                position: relative;
                overflow: hidden;
            }

            /* Icon Styling */
            .sidebar a i {
                width: 20px;
                text-align: center;
                font-size: 16px;
                color: #5D7BA3;
                transition: all 0.25s ease;
            }

            /* Link Hover Effects */
            .sidebar a:hover {
                background-color: #F0F7FF;
                color: #1E5BC6;
                transform: translateX(5px);
                box-shadow: 0 4px 12px rgba(30, 91, 198, 0.1);
            }

            .sidebar a:hover i {
                color: #1E5BC6;
                /* transform: scale(1.1); */
            }

            /* Active State */
            .sidebar a.active {
                background: linear-gradient(135deg, #1E5BC6 0%, #2E7BFF 100%);
                color: white;
                font-weight: 600;
                box-shadow: 0 4px 12px rgba(30, 91, 198, 0.2);
            }

            .sidebar a.active i {
                color: white;
            }

            /* Core vs Secondary Links */
            .core-link {
                border-left: 3px solid #1E5BC6;
                margin-left: 5px;
            }

            .secondary-link {
                border-left: 3px solid #E3EFFF;
                margin-left: 5px;
                opacity: 0.9;
            }

            .secondary-link:hover {
                border-left-color: #1E5BC6;
                opacity: 1;
            }

            /* Dropdown Styling */
            .dropdown {
                background: #F8FAFF;
                border-radius: 6px;
                margin: 8px 15px 8px 35px;
                padding: 0;
                border: 1px solid #E3EFFF;
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

            /* Toggle Payments Link */
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
                color: #5D7BA3;
                transition: all 0.3s ease;
            }

            #togglePayments.active::after {
                transform: translateY(-50%) rotate(180deg);
                color: #1E5BC6;
            }

            /* Invoice Links Special */
            .invoice-link {
                font-size: 12px !important;
                color: #666 !important;
                justify-content: flex-end;
                padding-right: 20px !important;
            }

            .invoice-link:hover {
                color: #1E5BC6 !important;
                background-color: #E8F1FF;
            }

            /* Section Separator Effect */
            .sidebar>ul>li:nth-child(6) {
                margin-top: 20px;
                position: relative;
            }

            .sidebar>ul>li:nth-child(6)::before {
                content: '';
                position: absolute;
                top: -12px;
                left: 20px;
                right: 20px;
                height: 1px;
                background: linear-gradient(90deg, transparent, #E3EFFF 20%, #E3EFFF 80%, transparent);
            }

            /* Logout Link Special Styling */
            .sidebar a[href*="logout.php"] {
                margin-top: 20px;
                background: linear-gradient(135deg, #FFF5F5 0%, #FFE8E8 100%);
                color: #D32F2F;
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

            /* Mobile Navigation Toggle */
            .mobile-nav-toggle {
                display: none;
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 1001;
                font-size: 24px;
                color: #1E5BC6;
                background: white;
                padding: 10px;
                border-radius: 6px;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
                cursor: pointer;
            }

            /* Mobile Responsiveness */
            @media (max-width: 768px) {
                .sidebar {
                    width: 100%;
                    height: calc(100vh - 70px);
                    top: 70px;
                    left: -100%;
                    transition: left 0.3s ease;
                    border-right: none;
                    box-shadow: none;
                }

                .sidebar.active {
                    left: 0;
                }

                .sidebar>ul>li {
                    margin: 3px 10px;
                }

                .sidebar a {
                    padding: 12px 15px;
                    font-size: 14px;
                }

                .mobile-nav-toggle {
                    display: block;
                }

                /* Adjust dropdown for mobile */
                .dropdown {
                    margin: 6px 10px 6px 30px;
                }

                .dropdown a {
                    padding: 8px 12px;
                }
            }

            /* Small Mobile */
            @media (max-width: 480px) {
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
            }

            /* Animation for link ripple effect */
            .sidebar a::before {
                content: '';
                position: absolute;
                top: 50%;
                left: 0;
                width: 4px;
                height: 0;
                background: #1E5BC6;
                border-radius: 0 2px 2px 0;
                transform: translateY(-50%);
                transition: height 0.3s ease;
            }

            .sidebar a:hover::before {
                height: 70%;
            }

            .sidebar a.active::before {
                height: 70%;
                background: white;
            }
        </style>

        <!-- navigation sidebar -->

        <!-- show payment Invoice dropdown -->

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

        <script>
            document.getElementById('togglePayments').addEventListener('click', function(event) {
                event.preventDefault();
                const dropdown = document.getElementById('paymentsDropdown');
                dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
            });

            const invoiceLinks = document.querySelectorAll('.invoice-link');
            invoiceLinks.forEach(function(link) {
                link.addEventListener('click', function(event) {
                    event.preventDefault();

                    const invoiceId = link.getAttribute('data-id');
                    const invoiceDate = link.getAttribute('data-date');

                    document.getElementById('date').value = invoiceDate;

                    document.getElementById('invoiceForm').submit();
                });
            });
        </script>

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

                <style>
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
                    }

                    .popup-content {
                        background-color: white;
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
                    }

                    .popup-input {
                        width: 100%;
                        padding: 8px;
                        margin-bottom: 15px;
                        border: 1px solid #ccc;
                        border-radius: 5px;
                    }

                    .popup-btn {
                        background-color: #28a745;
                        color: white;
                        padding: 10px;
                        border: none;
                        border-radius: 5px;
                        cursor: pointer;
                        width: 100%;
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
                    }

                    .popup-close :hover {
                        color: red;

                    }

                    .popup-description,
                    .popup-confirmation {
                        margin-bottom: 15px;
                    }

                    .MemberPaymentBtn {
                        width: 90%;
                        padding: 4px;
                        bottom: 5px;
                        margin: 10px auto;
                    }
                </style>

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


                <script>
                    function togglePopup(popupId) {
                        const popup = document.getElementById(popupId);
                        const displayState = popup.style.display === 'flex' ? 'none' : 'flex';
                        popup.style.display = displayState;
                    }

                    document.addEventListener('DOMContentLoaded', function() {
                        const openButtons = document.querySelectorAll('[data-popup-target]');
                        const closeButtons = document.querySelectorAll('.popup-close');

                        // Open popup on button click
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
                                popup.style.display = 'none';
                            });
                        });

                        // Close popup if clicking outside the popup content
                        window.addEventListener('click', function(event) {
                            if (event.target.classList.contains('popup-container')) {
                                event.target.style.display = 'none';
                            }
                        });
                    });
                </script>

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

                <style>
                    .iventcard {
                        background-color: #007bff;
                        color: white;
                        border: none;
                        padding: 10px 20px;
                        border-radius: 5px;
                        cursor: pointer;
                        width: 100%;
                    }

                    .iventcard:hover {
                        background-color: #0056b3;
                    }
                </style>

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
                <style>
                    /* Dashboard Container */
                    .dashboard {
                        width: 100%;
                        padding: 20px;
                        margin-left: 20px;
                        transition: margin-left 0.3s ease;
                    }

                    /* Cards Grid Container */
                    .cards {
                        display: grid;
                        grid-template-columns: repeat(4, 1fr);
                        gap: 10px;
                        margin-bottom: 15px;
                    }

                    /* Individual Card Styling */
                    .card {
                        background: linear-gradient(135deg, #FFFFFF 0%, #F8FAFF 100%);
                        border-radius: 16px;
                        padding: 10px;
                        box-shadow: 0 8px 25px rgba(30, 91, 198, 0.08);
                        border: 1px solid #E3EFFF;
                        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                        display: flex;
                        flex-direction: column;
                        height: 100%;
                        position: relative;
                        overflow: auto;
                    }

                    .card:hover {
                        transform: translateY(-5px);
                        box-shadow: 0 15px 35px rgba(30, 91, 198, 0.15);
                        border-color: #1E5BC6;
                    }

                    /* Card Header */
                    .card h5 {
                        color: #1E5BC6;
                        font-size: 15px;
                        font-weight: 700;
                        margin-bottom: 10px;
                        position: relative;
                        padding-bottom: 2;
                    }

                    .card h5::after {
                        content: '';
                        position: absolute;
                        bottom: 0;
                        left: 0;
                        width: 50px;
                        height: 3px;
                        background: linear-gradient(90deg, #1E5BC6, #2E7BFF);
                        border-radius: 2px;
                    }

                    .card h4 {
                        color: #2C3E50;
                        font-size: 14px;
                        font-weight: 600;
                        margin: 3px 0;
                    }

                    /* Card Content */
                    .card p {
                        color: #5D7BA3;
                        font-size: 13px;
                        line-height: 1.6;
                        margin: 3px 0;
                    }

                    .card p span {
                        color: #2C3E50;
                        font-weight: 600;
                    }

                    /* Horizontal Rule */
                    .card hr {
                        border: none;
                        height: 1px;
                        background: linear-gradient(90deg, #E3EFFF, #FFFFFF);
                        margin: 10px 0;
                        width: 100%;
                    }

                    /* Profile Image */
                    .cardMemberprofile {
                        width: 120px;
                        height: 120px;
                        border-radius: 50%;
                        object-fit: cover;
                        border: 4px solid #FFFFFF;
                        box-shadow: 0 4px 15px rgba(30, 91, 198, 0.2);
                        margin: 0 auto 20px auto;
                        display: block;
                        transition: all 0.3s ease;
                    }

                    .cardMemberprofile:hover {
                        transform: scale(1.05);
                        box-shadow: 0 6px 20px rgba(30, 91, 198, 0.3);
                    }

                    /* Payment Buttons */
                    .MemberPaymentBtn {
                        background: linear-gradient(135deg, #1E5BC6 0%, #2E7BFF 100%);
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
                        background: linear-gradient(135deg, #174a9e 0%, #1E5BC6 100%);
                        transform: translateY(-2px);
                        box-shadow: 0 8px 20px rgba(30, 91, 198, 0.3);
                    }

                    .MemberPaymentBtn:disabled {
                        background: linear-gradient(135deg, #A0C1F1 0%, #B5D0FF 100%);
                        cursor: not-allowed;
                        transform: none;
                        box-shadow: none;
                    }

                    .MemberPaymentBtn:disabled:hover {
                        background: linear-gradient(135deg, #A0C1F1 0%, #B5D0FF 100%);
                        transform: none;
                        box-shadow: none;
                    }

                    /* Event Card Specific Styles */
                    .card .iventcard {
                        background: linear-gradient(135deg, #28a745 0%, #34ce57 100%);
                        color: white;
                        border: none;
                        padding: 10px 15px;
                        border-radius: 8px;
                        font-size: 13px;
                        font-weight: 600;
                        cursor: pointer;
                        transition: all 0.3s ease;
                        margin-top: 10px;
                        text-align: center;
                    }

                    .card .iventcard:hover {
                        background: linear-gradient(135deg, #218838 0%, #28a745 100%);
                        transform: translateY(-2px);
                        box-shadow: 0 6px 15px rgba(40, 167, 69, 0.3);
                    }

                    /* Responsive Breakpoints */

                    /* Large Tablets (769px - 1024px) */
                    @media (max-width: 1024px) {
                        .dashboard {
                            margin-left: 0;
                            padding: 15px;
                        }

                        .cards {
                            grid-template-columns: repeat(2, 1fr);
                            gap: 20px;
                        }

                        .card {
                            padding: 20px;
                        }
                    }

                    /* Tablets (601px - 768px) */
                    @media (max-width: 768px) {
                        .dashboard {
                            padding: 15px;
                        }

                        .cards {
                            grid-template-columns: 1fr;
                            gap: 15px;
                            max-width: 500px;
                            margin: 0 auto 30px auto;
                        }

                        .card {
                            padding: 20px;
                        }

                        .cardMemberprofile {
                            width: 100px;
                            height: 100px;
                        }
                    }

                    /* Mobile (≤600px) */
                    @media (max-width: 600px) {
                        .dashboard {
                            padding: 10px;
                        }

                        .cards {
                            gap: 12px;
                        }

                        .card {
                            padding: 15px;
                            border-radius: 12px;
                        }

                        .card h5 {
                            font-size: 18px;
                        }

                        .card h4 {
                            font-size: 16px;
                        }

                        .card p {
                            font-size: 13px;
                        }

                        .cardMemberprofile {
                            width: 90px;
                            height: 90px;
                        }

                        .MemberPaymentBtn {
                            padding: 10px 15px;
                            font-size: 13px;
                        }
                    }

                    /* Small Mobile (≤480px) */
                    @media (max-width: 480px) {
                        .cards {
                            gap: 10px;
                        }

                        .card {
                            padding: 12px;
                        }

                        .card h5 {
                            font-size: 16px;
                        }

                        .cardMemberprofile {
                            width: 80px;
                            height: 80px;
                        }
                    }

                    /* Animation for card entrance */
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

                    .card {
                        animation: cardEntrance 0.5s ease-out;
                    }

                    /* Stagger the card animations */
                    .card:nth-child(1) {
                        animation-delay: 0.1s;
                    }

                    .card:nth-child(2) {
                        animation-delay: 0.2s;
                    }

                    .card:nth-child(3) {
                        animation-delay: 0.3s;
                    }

                    .card:nth-child(4) {
                        animation-delay: 0.4s;
                    }

                    /* Scrollbar for cards with overflow */
                    .card {
                        scrollbar-width: thin;
                        scrollbar-color: #1E5BC6 #F8FAFF;
                    }

                    .card::-webkit-scrollbar {
                        width: 6px;
                    }

                    .card::-webkit-scrollbar-track {
                        background: #F8FAFF;
                        border-radius: 3px;
                    }

                    .card::-webkit-scrollbar-thumb {
                        background: #1E5BC6;
                        border-radius: 3px;
                    }

                    /* Status indicators for payment information */
                    #memberpayments-current-lastPay span,
                    #memberpayments-current-nextP span,
                    #memberpayments-current-balance span {
                        padding: 2px 8px;
                        background: #F0F7FF;
                        border-radius: 4px;
                        border-left: 3px solid #1E5BC6;
                    }

                    /* Education info specific styling */
                    #highest-degree,
                    #institution,
                    #graduation-year {
                        display: block;
                        margin-top: 5px;
                        padding: 3px 10px;
                        background: #F8FAFF;
                        border-radius: 6px;
                        font-weight: 500;
                    }
                </style>

            </div>



            <style>
                /* Popup container */
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

                /* Popup visible state */
                .popup.show {
                    opacity: 1;
                    visibility: visible;
                    transform: translatex(0);
                }

                /* Success and danger alert styles */
                .alert {
                    margin: 0;
                    padding: 10px;
                    border-radius: 3px;
                    font-size: 14px;
                }

                .alert-success {
                    background-color: #d4edda;
                    color: #155724;
                }

                .alert-danger {
                    background-color: #f8d7da;
                    color: #721c24;
                }
            </style>

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

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    var popup = document.getElementById('response-popup');
                    if (popup) {
                        // Show the popup
                        popup.classList.add('show');

                        // Hide the popup after 30 seconds
                        setTimeout(function() {
                            popup.classList.remove('show');
                        }, 10000); // 30000ms = 30 seconds
                    }
                });
            </script>

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
                    /* Enable horizontal scrolling */
                    overflow-y: hidden;
                    scrollbar-width: thin;
                }

                .Singleblog {
                    min-width: 300px;
                    /* Set a minimum width */
                    display: flex;
                    flex-direction: column;
                    padding: 10px;
                    margin-right: 10px;
                    margin-bottom: 20px;
                    flex: 0 0 auto;
                    /* Prevent shrinking and ensure a consistent width */
                }

                .blogImage {
                    width: 100%;
                    height: 200px;
                    margin-bottom: 5px;
                    border-radius: 10px 10px 0 0;
                    object-fit: cover;
                }

                .blogImage img {
                    width: 100%;
                    border-radius: 10px 10px 0 0;
                    object-fit: cover;
                }

                .blogcontent {
                    width: 100%;
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


                .moreButton {
                    width: 80%;
                    background-color: #007BFF;
                    color: white;
                    border: none;
                    padding: 10px 15px;
                    border-radius: 5px;
                    cursor: pointer;
                    font-size: 16px;
                    transition: background-color 0.3s ease;
                    margin-top: 5px;
                }

                .moreButton:hover {
                    background-color: #0056b3;
                }
            </style>

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

            <style>
                #blogPoint {
                    font-family: 'Segoe UI', 'Helvetica Neue', Arial, sans-serif;
                    font-size: 22px;
                    font-weight: 700;
                    color: #1E5BC6;
                    margin: 20px 0 10px;
                    padding: 20px;
                    position: relative;
                }

                #blogPoint::after {
                    content: '';
                    display: block;
                    width: 50px;
                    height: 4px;
                    background: #1E5BC6;
                    border-radius: 2px;
                    margin-top: 8px;
                }


                .blogPoint {
                    display: grid;
                    grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
                    gap: 24px;
                    padding: 10px 20px 30px;
                }


                .blogSingle {
                    background: #FFFFFF;
                    border-radius: 14px;
                    overflow: hidden;
                    box-shadow: 0 8px 20px rgba(30, 91, 198, 0.08);
                    transition: all 0.3s ease;
                    cursor: pointer;
                    display: flex;
                    flex-direction: column;
                }

                .blogSingle:hover {
                    transform: translateY(-6px);
                    box-shadow: 0 14px 28px rgba(30, 91, 198, 0.15);
                }



                .blogImage {
                    width: 100%;
                    height: 160px;
                    overflow: hidden;
                    background: #f0f4ff;
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
                    color: #2C3E50;
                    margin: 0;
                    line-height: 1.3;
                }

                .blogContent h6 {
                    font-size: 12px;
                    font-weight: 500;
                    color: #7A8CA5;
                    margin: 0;
                }


                .moreButton {
                    margin-top: auto;
                    align-self: flex-start;
                    padding: 8px 16px;
                    font-size: 13px;
                    font-weight: 600;
                    color: #1E5BC6;
                    background: #F0F7FF;
                    border: 1px solid #D1E3FF;
                    border-radius: 6px;
                    cursor: pointer;
                    transition: all 0.25s ease;
                }

                .moreButton:hover {
                    background: #1E5BC6;
                    color: #FFFFFF;
                    border-color: #1E5BC6;
                    box-shadow: 0 6px 14px rgba(30, 91, 198, 0.2);
                }


                @media (max-width: 768px) {
                    .blogPoint {
                        gap: 16px;
                        padding: 10px 15px 25px;
                    }

                    .blogImage {
                        height: 140px;
                    }

                    .blogContent h4 {
                        font-size: 15px;
                    }
                }

                @media (max-width: 480px) {
                    .blogImage {
                        height: 120px;
                    }

                    .moreButton {
                        font-size: 12px;
                        padding: 7px 14px;
                    }
                }
            </style>


            <!-- blogs -->

            <script>
                // Add event listener for the More button
                document.querySelectorAll('.moreButton').forEach(button => {
                    button.addEventListener('click', function() {
                        const blogSingle = this.closest('.blogSingle');
                        const blogId = blogSingle.getAttribute('data-id'); // Get the blog ID

                        // Redirect to another page with the blog ID
                        window.location.href = 'blogs.php?id=' + blogId;
                    });
                });
            </script>

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

            <style>
                @media (max-width: 600px) {
                    .message-popup {
                        width: 70%;
                        right: 0;
                        top: 20px;
                        height: 80%;
                        max-height: 100%;
                        border-radius: 8px;
                        overflow: auto;
                        scrollbar-width: thin;

                    }
                }
            </style>

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
                document.getElementById('toggleMessagesReceivedMessages').addEventListener('click', function() {
                    document.getElementById('messagePopupReceivedMessages').style.display = 'flex';
                });

                document.getElementById('closePopupReceivedMessages').addEventListener('click', function() {
                    document.getElementById('messagePopupReceivedMessages').style.display = 'none';
                });

                function showFullMessageReceivedMessages(message) {
                    document.getElementById('fullMessageTextReceivedMessages').textContent = message;
                    document.getElementById('fullMessagePopupReceivedMessages').style.display = 'flex';
                }

                document.getElementById('closeFullMessageReceivedMessages').addEventListener('click', function() {
                    document.getElementById('fullMessagePopupReceivedMessages').style.display = 'none';
                });
            </script>

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

            <style>
                .plannedEventimg {
                    width: 100%;
                    height: 250px;
                    margin-bottom: 5px;
                    object-fit: cover;
                }

                .quill-content {
                    max-height: 290px;
                    overflow: auto;
                    scrollbar-width: thin;
                    text-align: start;
                }
            </style>

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

            <style>
                #PlannedEvents {
                    padding: 10px 0 30px;
                    font-family: 'Segoe UI', 'Helvetica Neue', Arial, sans-serif;
                }

                #PlannedEvents>h3 {
                    font-size: 22px;
                    font-weight: 700;
                    color: #1E5BC6;
                    margin: 0 0 10px;
                    padding: 20px;
                    position: relative;
                }

                #PlannedEvents>h3::after {
                    content: '';
                    display: block;
                    width: 55px;
                    height: 4px;
                    background: #1E5BC6;
                    border-radius: 2px;
                    margin-top: 8px;
                }

                /* Events Grid Container */
                .table-card {
                    display: grid;
                    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
                    gap: 26px;
                    padding: 10px 20px;
                }

                /* Single Event Card */
                .eventDiv {
                    background: #FFFFFF;
                    border-radius: 16px;
                    padding: 18px;
                    box-shadow: 0 10px 24px rgba(30, 91, 198, 0.08);
                    transition: all 0.3s ease;
                    display: flex;
                    flex-direction: column;
                    gap: 12px;
                }

                .eventDiv:hover {
                    transform: translateY(-6px);
                    box-shadow: 0 18px 36px rgba(30, 91, 198, 0.16);
                }

                /* Event Title */
                .eventDiv>h3 {
                    font-size: 18px;
                    font-weight: 600;
                    color: #2C3E50;
                    margin: 0;
                }

                /* Event Image */
                .plannedEventimg {
                    width: 100%;
                    height: 180px;
                    object-fit: cover;
                    border-radius: 12px;
                    background: #f0f4ff;
                }

                /* Event Description (Quill Content) */
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
                    background: linear-gradient(to top, #FFFFFF, rgba(255, 255, 255, 0));
                }

                /* Location & Date */
                .eventDivindiv {
                    display: flex;
                    justify-content: space-between;
                    font-size: 13px;
                    color: #6B7F99;
                    font-weight: 500;
                }

                .eventDivindiv p {
                    margin: 0;
                }

                /* Register Button */
                .plannedEventsBTN {
                    margin-top: auto;
                    align-self: flex-start;
                    padding: 10px 20px;
                    font-size: 14px;
                    font-weight: 600;
                    color: #FFFFFF;
                    background: linear-gradient(135deg, #1E5BC6 0%, #2E7BFF 100%);
                    border: none;
                    border-radius: 8px;
                    cursor: pointer;
                    transition: all 0.25s ease;
                    box-shadow: 0 6px 14px rgba(30, 91, 198, 0.25);
                }

                .plannedEventsBTN:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 10px 22px rgba(30, 91, 198, 0.35);
                }

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
                    background: #FFFFFF;
                    padding: 24px;
                    width: 100%;
                    max-width: 420px;
                    border-radius: 14px;
                    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
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
                    color: #1E5BC6;
                }

                .form-container label {
                    font-size: 13px;
                    font-weight: 600;
                    color: #2C3E50;
                    display: block;
                    margin-top: 12px;
                }

                .form-container input {
                    width: 100%;
                    padding: 10px 12px;
                    margin-top: 6px;
                    border-radius: 6px;
                    border: 1px solid #D1E3FF;
                    font-size: 14px;
                }

                .form-container input:focus {
                    outline: none;
                    border-color: #1E5BC6;
                    box-shadow: 0 0 0 3px rgba(30, 91, 198, 0.15);
                }

                .form-container button[type="submit"] {
                    margin-top: 18px;
                    width: 100%;
                    padding: 12px;
                    background: linear-gradient(135deg, #1E5BC6 0%, #2E7BFF 100%);
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
                    background: #F5F7FB;
                    color: #2C3E50;
                    border: none;
                    border-radius: 8px;
                    cursor: pointer;
                }



                @media (max-width: 768px) {
                    .table-card {
                        grid-template-columns: 1fr;
                        gap: 20px;
                    }

                    .plannedEventimg {
                        height: 160px;
                    }
                }

                @media (max-width: 480px) {
                    #PlannedEvents>h3 {
                        font-size: 20px;
                    }

                    .eventDiv>h3 {
                        font-size: 16px;
                    }

                    .plannedEventsBTN {
                        width: 100%;
                        text-align: center;
                    }
                }
            </style>


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
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // Initialize Quill editor
                    var quill = new Quill('#quillEditor', {
                        theme: 'snow'
                    });

                    // Handle the form submission
                    document.getElementById('eventForm').onsubmit = function(event) {
                        event.preventDefault();
                        var eventDescriptionInput = document.getElementById('eventDescription');
                        eventDescriptionInput.value = quill.root.innerHTML;

                        // Now submit the form
                        this.submit();
                    };
                });
            </script>

            <script>
                var myModal = document.getElementById("myModal");

                var openPostEventModal = document.getElementById("openPostEventModal");

                var closeBtn = document.getElementsByClassName("close")[0];

                openPostEventModal.onclick = function(event) {
                    event.preventDefault();
                    myModal.style.display = "block";
                };

                closeBtn.onclick = function() {
                    myModal.style.display = "none";
                };

                window.onclick = function(event) {
                    if (event.target == modal) {
                        myModal.style.display = "none";
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
            <script>
                // Initialize the Quill editor for Event Details
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

                // Attach an event listener for form submission
                document.getElementById('pastEventForm').addEventListener('submit', function(event) {
                    // Prevent the form from submitting immediately
                    event.preventDefault();

                    // Set the value of the hidden input to the Quill editor content
                    document.getElementById('pastEventDetails').value = pastEventQuill.root.innerHTML;

                    // Now submit the form
                    this.submit();
                });
            </script>



            <!-- Past Events Modal script-->
            <script>
                var modal = document.getElementById("pastEventModal");
                var btn = document.getElementById("openPastEventModal");
                var span = document.getElementsByClassName("close-past-event")[0];
                btn.onclick = function() {
                    modal.style.display = "block";
                }
                span.onclick = function() {
                    modal.style.display = "none";
                }
                window.onclick = function(event) {
                    if (event.target == modal) {
                        modal.style.display = "none";
                    }
                }
            </script>


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
            <script>
                function showMessagePopup() {
                    document.getElementById('messagePopupsend').style.display = 'flex';
                }

                function hideMessagePopup() {
                    document.getElementById('messagePopupsend').style.display = 'none';
                }

                document.getElementById('openMessagePopupSend').addEventListener('click', function(event) {
                    event.preventDefault();
                    showMessagePopup();
                });

                document.getElementById('messageClosePopupBtn').addEventListener('click', function(event) {
                    event.preventDefault();
                    hideMessagePopup();
                });
            </script>


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
            <script>
                // Initialize the Quill editor
                var quill = new Quill('#editor', {
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

                // Attach an event listener for form submission
                document.getElementById('blogPostForm').addEventListener('submit', function(event) {
                    // Prevent the form from submitting immediately
                    event.preventDefault();

                    // Set the value of the hidden input to the Quill editor content
                    document.getElementById('blogContent').value = quill.root.innerHTML;

                    // Now submit the form
                    this.submit();
                });
            </script>

            <!-- Blog Post Modal script -->
            <script>
                const blogPostModal = document.getElementById("blogPostModal");
                const openBlogPostModal = document.getElementById("openBlogPostModal");
                const closeBlogPost = document.querySelector(".close-blog-post");
                openBlogPostModal.addEventListener("click", function(event) {
                    event.preventDefault();
                    blogPostModal.style.display = "block";
                });
                closeBlogPost.addEventListener("click", function() {
                    blogPostModal.style.display = "none";
                });
                window.addEventListener("click", function(event) {
                    if (event.target == blogPostModal) {
                        blogPostModal.style.display = "none";
                    }
                });
            </script>

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

            <style>
                #PastEvents {
                    display: grid;
                    grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
                    gap: 24px;
                    padding: 10px 20px 30px;
                    font-family: 'Segoe UI', 'Helvetica Neue', Arial, sans-serif;
                }

                /* Section Heading */
                h4 {
                    font-size: 22px;
                    font-weight: 700;
                    color: #1E5BC6;
                    margin: 20px;
                    position: relative;
                }

                h4::after {
                    content: '';
                    display: block;
                    width: 45px;
                    height: 4px;
                    background: #1E5BC6;
                    border-radius: 2px;
                    margin-top: 8px;
                }

                /* Past Event Card */
                #PastEvents .eventDiv {
                    background: #FFFFFF;
                    border-radius: 16px;
                    overflow: hidden;
                    box-shadow: 0 8px 22px rgba(30, 91, 198, 0.08);
                    transition: all 0.3s ease;
                    display: flex;
                    flex-direction: column;
                }

                #PastEvents .eventDiv:hover {
                    transform: translateY(-6px);
                    box-shadow: 0 16px 34px rgba(30, 91, 198, 0.16);
                }

                /* Date & Location Row */
                #PastEvents .eventDivindiv {
                    display: flex;
                    justify-content: space-between;
                    padding: 14px 16px;
                    background: #F0F7FF;
                    border-bottom: 1px solid #E3EFFF;
                }

                #PastEvents .eventDivindiv h3 {
                    font-size: 13px;
                    font-weight: 600;
                    color: #1E5BC6;
                    margin: 0;
                }

                /* Event Image */
                .past-eventDiv-img {
                    width: 100%;
                    height: 170px;
                    object-fit: cover;
                    background: #f0f4ff;
                }

                /* Event Name */
                #PastEvents .eventDiv p {
                    padding: 14px 16px 0;
                    font-size: 15px;
                    font-weight: 600;
                    color: #2C3E50;
                    margin: 0;
                }

                /* More Button */
                .PastEventsmoreButton {
                    margin: 16px;
                    align-self: flex-start;
                    padding: 8px 18px;
                    font-size: 13px;
                    font-weight: 600;
                    color: #1E5BC6;
                    background: #F0F7FF;
                    border: 1px solid #D1E3FF;
                    border-radius: 6px;
                    text-decoration: none;
                    transition: all 0.25s ease;
                }

                .PastEventsmoreButton:hover {
                    background: #1E5BC6;
                    color: #FFFFFF;
                    border-color: #1E5BC6;
                    box-shadow: 0 6px 14px rgba(30, 91, 198, 0.25);
                }

                /* ===========================
   RESPONSIVE
=========================== */

                @media (max-width: 768px) {
                    #PastEvents {
                        gap: 18px;
                        padding: 10px 15px 25px;
                    }

                    .past-eventDiv-img {
                        height: 150px;
                    }
                }

                @media (max-width: 480px) {
                    h4 {
                        font-size: 20px;
                    }

                    #PastEvents .eventDivindiv h3 {
                        font-size: 12px;
                    }

                    .PastEventsmoreButton {
                        width: 100%;
                        text-align: center;
                    }
                }
            </style>

            <!-- past Events -->
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // Attach click event listeners to all buttons with class 'PastEventsmoreButton'
                    document.querySelectorAll('.PastEventsmoreButton').forEach(button => {
                        button.addEventListener('click', function() {
                            // Get the event ID from the button's data-id attribute
                            var eventId = this.getAttribute('data-id');
                            // Redirect to event.php with the event ID as a query parameter
                            window.location.href = 'Event.php?id=' + eventId;
                        });
                    });
                });
            </script>


        </section>

    </div>

    <footer style="font-size: 11px; font-style: italic; text-align: center; background-color: #b4ccdb; color: black; ">
        <p>&copy; 2024 <a style="text-decoration: none;" href="AGL.or.ke">http://www.agl.or.ke/</a> . All rights
            reserved.</p>
    </footer>

    <!-- side bar script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleButton = document.getElementById('toggleMenu');
            const sidebar = document.getElementById('sidebar');

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
        });

        function toggleDropdown(element) {
            const content = element.nextElementSibling;
            content.classList.toggle('show');
        }
    </script>



</body>

</html>