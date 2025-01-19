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

    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet" /> -->
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
</style>

<body>
    <header>
        <div class="logo">
            <img src="../assets/img/logo.png" alt="AGL">
        </div>
        <div id="toggleMenu" class="menu-button" onclick="toggleMenu()">☰</div>

        <!-- <button id="toggleMessages">View Messages</button> -->

        <div style="text-align: center;" id="toggleMessagesReceivedMessages" class="notification">
            <img src="../assets/img/bell.png" alt="Notification">
            <h5 style="color: black; cursor: pointer;">Messages</h5>
        </div>

    </header>

    <style>
        .innerlinksNav {
            width: 100%;
            display: flex;
            justify-content: space-around;
            background-color: #6ec1e4;
            height: 40px;
            text-align: center;
        }

        .innerlinksNav-a {
            margin-top: 10px;
            color: #f2f2f2;
            font-size: 20px;
            text-decoration: none;
            font-weight: bold;
            background-color: #007BFF;
            border-radius: 5px;
            padding: 7px 15px;
            font-family: 'Arial', sans-serif;
            /* Set a clean, modern font */
            transition: all 0.3s ease;
            /* Smooth transition for resizing */
        }

        .innerlinksNav-a:hover {
            background-color: #0056b3;
            /* Darker blue on hover */
        }

        .Aligner {
            height: 5px;
            background-color: #6ec1e4;
        }

        /* Responsive design for small screens */
        @media (max-width: 600px) {
            .innerlinksNav-a {
                font-size: 14px;
                /* Smaller font size */
                padding: 5px 10px;
                /* Reduced padding for smaller buttons */
            }
        }
    </style>

    <div class="innerlinksNav">
        <a class="innerlinksNav-a" href="#blogPoint">Blogs</a>
        <a class="innerlinksNav-a" href="#PlannedEvents">Comming Events</a>
        <a class="innerlinksNav-a" href="#PastEvents">Past Events</a>
    </div>
    <div class="Aligner"></div>



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
                    <a href="https://www.agl.or.ke/" class="active">Home<br /></a>
                </li>

                <?php if ($role == 'superadmin'): ?>
                    <li><a href="https://www.agl.or.ke/about-us/" target="_blank">About</a></li>
                    <li><a id="openPostEventModal">Add a Planned Event</a></li>
                    <li><a id="openPastEventModal">Add a Past Event</a></li>
                    <li><a id="openBlogPostModal">Post a Blog</a></li>
                    <li><a id="openMessagePopupSend">Send Message</a></li>
                    <li><a id="MembersTable-link" href="Members.php">Members</a></li>
                    <li><a href="adminP.php">Payments</a></li>
                    <li><a href="UserDataORG.php" target="_blank"></a>User Information</a></li>
                    <li><a href="https://www.agl.or.ke/contact-us/">Contacts</a></li>
                    <li><a href="mailto:info@agl.or.ke" target="_blank">Email Us</a></li>
                    <li><a href="tel:+254748027123" target="_blank">Call Us</a></li>
                    <li><a href="https://wa.me/254722605048" target="_blank">Chat on WhatsApp</a></li>
                    <li><a href="https://x.com/OfLibraria37902" target="_blank">Tweeter</a></li>
                    <li><a href="https://www.facebook.com/share/zQ8rdvgozvNsZY8J/?mibextid=qi2Omg" target="_blank">FaceBook</a></li>
                    <li><a href="../forms/logout.php">Logout</a></li>
                    <!-- <li><a href="new2.php">invitation card</a></li> -->

                <?php elseif ($role == 'admin'): ?>
                    <li><a href="https://www.agl.or.ke/about-us/" target="_blank">About</a></li>
                    <li><a id="openPostEventModal">Add a Planned Event</a></li>
                    <li><a id="openBlogPostModal">Post a Blog</a></li>
                    <li><a id="openMessagePopupSend">Send Message</a></li>

                    <li><a id="MembersTable-link" href="Members.php">Members</a></li>
                    <li><a href="adminP.php">Payments</a></li>
                    <li><a href="UserDataORG.php" target="_blank"></a>User Information</a></li>

                    <li><a href="mailto:info@agl.or.ke">Email</a></li>
                    <li><a href="tel:+254748027123">Call</a></li>
                    <li><a href="https://wa.me/254722605048" target="_blank">Chat on WhatsApp</a></li>

                    <li><a href="https://x.com/OfLibraria37902" target="_blank">Tweeter</a></li>
                    <li><a href="https://www.facebook.com/share/zQ8rdvgozvNsZY8J/?mibextid=qi2Omg" target="_blank">FaceBook</a></li>
                    <li><a href="../forms/logout.php">Logout</a></li>

                <?php elseif ($role == 'member'): ?>
                    <li><a href="https://www.agl.or.ke/about-us/" target="_blank">About</a></li>
                    <li><a href="https://www.agl.or.ke/contact-us/" target="_blank">Contacts</a></li>
                    <li><a href="UserDataORG.php" target="_blank">User Information</a></li>
                    <li>
                        <a href="#" id="togglePayments">My Payments Invoices</a>
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
                    <!-- <li><a href="UserDataORG.php" ></a>User Information</a></li>
                    <li><a href="UserDataORG.php" target="_blank"></a>User Information</a></li> -->
                    <li><a href="mailto:info@agl.or.ke">Email Us</a></li>
                    <li><a href="tel:+254748027123" target="_blank">Call Us</a></li>
                    <li><a href="https://wa.me/254722605048" target="_blank">Chat on WhatsApp</a></li>
                    <li><a href="https://x.com/OfLibraria37902" target="_blank">Tweeter</a></li>
                    <li><a href="https://www.facebook.com/share/zQ8rdvgozvNsZY8J/?mibextid=qi2Omg" target="_blank">FaceBook</a></li>
                    <li><a href="../forms/logout.php">Logout</a></li>

                <?php endif; ?>
            </ul>
            <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
        </nav>


        <!-- show payment Invoice dropdown -->

        <?php

        if (!isset($_SESSION['user_email'])) {
            header('Location: login.php');
            exit();
        }
        require_once('../forms/DBconnection.php');

        $userEmail = $_SESSION['user_email'];
        $sql = "SELECT id, organization_name, contact_phone_number, organization_address FROM organizationmembership WHERE organization_email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $userEmail);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $userId = $user['id'];
            $userName = $user['organization_name'];
            $userPhone = $user['contact_phone_number'];
            $userAddress = $user['organization_address'];
        } else {
            echo "organization data not found!";
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
                // Fetch organization details based on the session email
                $sql = "SELECT * FROM organizationmembership WHERE organization_email = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $userEmail);
                $stmt->execute();
                $result = $stmt->get_result();

                // Fetch data
                if ($row = $result->fetch_assoc()) {
                    $organizationName = $row['organization_name'];
                    $registrationDate = $row['date_of_registration'];
                    $logoImage = $row['logo_image'];
                    $organizationEmail = $row['organization_email'];
                } else {
                    echo "No organization found";
                }

                $stmt->close();
                ?>

                <div class="card">
                    <img class="cardMemberprofile" src="<?php echo htmlspecialchars($logoImage); ?>"
                        alt="Organization Logo">
                    <h5><?php echo htmlspecialchars($organizationName); ?></h5>
                    <hr><br>
                    <h4><?php echo htmlspecialchars($organizationEmail); ?></h4>
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

                // Get the session email
                // $sessionEmail = $_SESSION['user_email'];

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
                    $disablePaymentButton = true; // personalmembership
                } elseif (!empty($paymentNumberOrganization) || !empty($paymentCodeOrganization)) {
                    $disablePaymentButton = true; // organizationmembership
                }
                ?>

                <!-- HTML Section -->
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
                        <input type="text" id="mpesa-amount" name="amount" class="popup-input" value="1" readonly>

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

                $query = "SELECT location_country, organization_type, what_you_do, start_date FROM organizationmembership WHERE organization_email = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("s", $sessionEmail);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $organizationInfo = $result->fetch_assoc();
                } else {
                    $organizationInfo = [
                        'location_country' => 'N/A',
                        'organization_type' => 'N/A',
                        'what_you_do' => 'N/A',
                        'start_date' => 'N/A'
                    ];
                }
                ?>

                <div class="card">
                    <h5>Organization Information</h5>
                    <hr>
                    <p>Location Country: <span
                            id="location-country"><?php echo htmlspecialchars($organizationInfo['location_country']); ?></span>
                    </p><br>
                    <p>Organization Type: <span
                            id="organization-type"><?php echo htmlspecialchars($organizationInfo['organization_type']); ?></span>
                    </p><br>
                    <p>What You Do: <span
                            id="what-you-do"><?php echo htmlspecialchars($organizationInfo['what_you_do']); ?></span>
                    </p><br>
                    <p>Organization Start Date: <span
                            id="start-date"><?php echo htmlspecialchars($organizationInfo['start_date']); ?></span></p><br>
                </div>

                <?php
                // session_start();

                if (!isset($_SESSION['user_email'])) {
                    die("User not logged in");
                }

                $userEmail = $_SESSION['user_email'];

                // Debugging session email


                // Prepare the SQL query
                $sql = "SELECT event_name, event_location, member_email,event_id, event_id, invitation_card FROM event_registrations WHERE member_email = ? ORDER BY event_date ASC";
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
            </div>
            <!-- ................................... -->

            <!-- ellert -->
            <!-- delete the stylesheet -->
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


            $sql = "SELECT * FROM organizationmembership WHERE organization_email = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $userEmail);
            $stmt->execute();
            $Mresult = $stmt->get_result();

            // Fetch data
            if ($row = $Mresult->fetch_assoc()) {
                $organizationName = $row['organization_name'];
            } else {
                echo "No organization found";
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
                            echo '<input type="text" id="memberName" name="memberName" value="' . $organizationName . '" readonly>';

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

                        <!-- <div class="past-event-form-group">
                            <label for="pastEventLocation">Event Details</label>
                            <textarea style="height: 200px; padding: 10px; " name="eventDetails"
                                id="pastEventDetailsEditor" required></textarea>
                        </div> -->

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
            <h4 style="margin: 20px;">past Events</h4>

            <style>
                .PastEventsmoreButton {
                    display: block;
                    width: 100%;
                    text-align: center;
                    padding: 10px;
                    background-color: #007bff;
                    color: white;
                    text-decoration: none;
                    border-radius: 5px;
                    box-sizing: border-box;
                    margin-top: 10px;
                }

                .PastEventsmoreButton:hover {
                    background-color: #0056b3;
                }

                .past-eventDiv-img {
                    width: 100%;
                    height: 250px;
                    margin-bottom: 5px;
                    object-fit: cover;
                }
            </style>
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