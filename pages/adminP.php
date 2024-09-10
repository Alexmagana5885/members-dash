<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin</title>
    <link href="../assets/img/favicon.png" rel="icon" />
    <link rel="stylesheet" href="../assets/CSS/startfile.css" />
</head>



<body>
    <!-- Header -->
    <header class="site-header">
        <div class="logo">
            <img src="../assets/img/logo.png" alt="Logo" />
        </div>
        <button class="menu-toggle" id="menu-toggle">
            &#9776;
        </button>
        <nav class="navigation" id="navigation">
            <ul>
                <li><a href="https://www.agl.or.ke/">Home</a></li>
                <li><a href="#" onclick="window.history.back(); return false;">Back</a></li>

            </ul>
        </nav>
    </header>

    <style>
        /* General table styling */
        table {
            width: 92%;
            border-collapse: collapse;
            margin: 10px auto;
            font-size: 16px;
            overflow-x: auto;
            border-radius: 10px;
        
        }

        /* Table header styling */
        th {
            background-color: #007BFF;
            color: white;
            padding: 12px 15px;
            text-align: left;
        }

        /* Table body styling */
        td {
            padding: 12px 15px;
            border: 1px solid #007BFF;
            text-align: left;
        }

        /* Responsive styling for large screens */
        @media (min-width: 768px) {
            .TablePayDiv {
                width: 90%;
                overflow-x: auto;
                margin: 10px auto;
            }

            th,
            td {
                padding: 14px 20px;
            }

            th {
                font-size: 18px;
            }
        }

        /* Responsive styling for small screens */
        @media (max-width: 767px) {
            .TablePayDiv {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                /* Enables smooth scrolling on iOS */
            }

            table {
                width: 120%;
                /* Set the width greater than 100% to enable scrolling */
                font-size: 14px;
            }

            th,
            td {
                padding: 10px 12px;
            }

            th {
                font-size: 16px;
            }
        }
    </style>


    <div class="TablePayDiv">
        <?php
        // Database connection parameters
        require_once('../forms/DBconnection.php');

        // SQL query
        $sql = "
                        SELECT 
                            mp.id AS payment_id,
                            CASE
                                WHEN pm.email IS NOT NULL THEN pm.name
                                WHEN om.organization_email IS NOT NULL THEN om.organization_name
                                ELSE 'Unknown'
                            END AS member_name,
                            CASE
                                WHEN pm.email IS NOT NULL THEN 'Individual'
                                WHEN om.organization_email IS NOT NULL THEN 'Organization'
                                ELSE 'Unknown'
                            END AS membership_type,
                            mp.member_email,
                            mp.phone_number,
                            mp.payment_code,
                            mp.amount,
                            mp.timestamp
                        FROM 
                            member_payments mp
                        LEFT JOIN 
                            personalmembership pm ON mp.member_email = pm.email
                        LEFT JOIN 
                            organizationmembership om ON mp.member_email = om.organization_email
                    ";

        // Execute the query and get the result
        $result = $conn->query($sql);

        // Check if there are results
        if ($result->num_rows > 0) {
            // Output data in an HTML table
            echo "<table border='1'>";
            echo "<tr>
            <th>Payment ID</th>
            <th>Member Name</th>
            <th>Membership Type</th>
            <th>Member Email</th>
            <th>Phone Number</th>
            <th>Payment Code</th>
            <th>Amount</th>
            <th>Timestamp</th>
          </tr>";

            // Loop through each row in the result set
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                <td>{$row['payment_id']}</td>
                <td>{$row['member_name']}</td>
                <td>{$row['membership_type']}</td>
                <td>{$row['member_email']}</td>
                <td>{$row['phone_number']}</td>
                <td>{$row['payment_code']}</td>
                <td>{$row['amount']}</td>
                <td>{$row['timestamp']}</td>
              </tr>";
            }

            echo "</table>";
        } else {
            echo "No records found.";
        }

        // Close the database connection
        $conn->close();
        ?>

    </div>



    <!-- Footer -->
    <footer class="site-footer">
        <p>
            &copy; 2024
            <a style="text-decoration: none" href="http://www.agl.or.ke/">AGL.or.ke</a>
            . All rights reserved.
        </p>
    </footer>

    <!-- JavaScript for Menu Toggle and Document Handling -->
    <script>
        // Menu toggle functionality
        const menuToggle = document.getElementById("menu-toggle");
        const navigation = document.getElementById("navigation");

        menuToggle.addEventListener("click", () => {
            navigation.classList.toggle("active");
        });

        // Document handling functionality
        const documentsButton = document.getElementById("eventdetailsdocuments");
        documentsButton.addEventListener("click", () => {
            const documentPath = documentsButton.getAttribute("data-document-path");
            if (documentPath) {
                window.location.href = documentPath; // Redirect to the document
            }
        });
    </script>
</body>

</html>