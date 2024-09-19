    <?php
        require_once('../forms/DBconnection.php');

        // Get the event ID from the query parameter
        $eventId = isset($_GET['id']) ? intval($_GET['id']) : 0;

        // Fetch the event details from the database
        $sql = "SELECT * FROM pastevents WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $eventId);
        $stmt->execute();
        $result = $stmt->get_result();

        $event = $result->fetch_assoc();
        $stmt->close();
        $conn->close();

        // Decode image paths
        $imagePathsJson = $event["event_image_paths"];
        $imagePathsArray = json_decode($imagePathsJson, true);

        // Saving





        if (is_array($imagePathsArray) && !empty($imagePathsArray)) {
            $imagePath = htmlspecialchars($imagePathsArray[0]);
        } else {
            $imagePath = '';
        }

        // Decode document paths
        $documentPathsJson = $event["event_document_paths"];
        $documentPathsArray = json_decode($documentPathsJson, true);

        if (is_array($documentPathsArray) && !empty($documentPathsArray)) {
            $documentPath = htmlspecialchars($documentPathsArray[0]);
        } else {
            $documentPath = '';
        }





    ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Event Details</title>
    <link href="../assets/img/favicon.png" rel="icon" />
    <link rel="stylesheet" href="../assets/CSS/startfile.css" />
</head>

<style>
    #eventdiv {
        display: flex;
        border: 1px solid #ddd;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        padding: 15px;
        background-color: #fff;
        width: 80%;
        margin: 20px auto;

    }

    /* Style for the image container */
    .EventImage {
        flex: 1;
        max-width: 40%;
        /* Fix the typo by removing 'px' */
        margin-right: 15px;
    }

    .EventImage img {
        width: 100%;
        height: auto;
        border-radius: 8px;
    }

    /* Style for the event details section */
    .eventdetails {
        flex: 2;
    }

    .eventdetailshead {
        font-size: 1.5em;
        font-weight: bold;
        margin-bottom: 10px;
    }

    .eventdetailsdetails {
        font-size: 1em;
        color: #555;
        margin-bottom: 15px;
        max-height: 60vh;
        overflow: auto;
        scrollbar-width: thin;
    }

    .eventdivinner {
        display: flex;
        justify-content: space-between;
        margin-bottom: 15px;
    }

    .eventdetailsdate,
    .eventdetailslocation {
        font-size: 0.9em;
        color: #777;
    }

    .eventdetailsdocuments {
        background-color: #007bff;
        color: #fff;
        border: none;
        border-radius: 4px;
        padding: 10px 15px;
        font-size: 1em;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .eventdetailsdocuments:hover {
        background-color: #0056b3;
    }

    /* Styles for smaller screens */
    @media (max-width: 768px) {
        #eventdiv {
            width: 97%;
            flex-direction: column;
            margin: 0px;
            padding: 10px;
            margin: 10px auto;
        }

        .EventImage {
            max-width: 100%;
            margin-right: 0;
            margin-bottom: 15px;
        }

        .eventdetails {
            flex: none;
        }

        .eventdetailshead {
            font-size: 1.2em;
        }

        .eventdetailsdetails {
            font-size: 0.9em;
        }

        .eventdetailsdate,
        .eventdetailslocation {
            font-size: 0.8em;
        }

        .eventdetailsdocuments {
            padding: 8px 12px;
            font-size: 0.9em;
        }
    }

    @media (max-width: 480px) {
        .eventdetailshead {
            font-size: 1em;
        }

        .eventdetailsdetails {
            font-size: 0.8em;
        }

        .eventdetailsdate,
        .eventdetailslocation {
            font-size: 0.7em;
        }

        .eventdetailsdocuments {
            padding: 6px 10px;
            font-size: 0.8em;
        }
    }
</style>


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
    

    <div id="eventdiv" class="eventdiv">
        <div class="EventImage">
            <img src="<?php echo htmlspecialchars($imagePath); ?>" alt="Event Image">
        </div>
        <div class="eventdetails">
            <div class="eventdetailshead"><?php echo htmlspecialchars($event['event_name']); ?></div>
            <div class="eventdetailsdetails"><?php echo $event['event_details']; ?></div>

            <div class="eventdivinner">
                <div class="eventdetailsdate"><?php echo htmlspecialchars($event['event_date']); ?></div>
                <div class="eventdetailslocation"><?php echo htmlspecialchars($event['event_location']); ?></div>
            </div>
            <!-- Pass the document path as a data attribute -->
            <button class="eventdetailsdocuments" id="eventdetailsdocuments" data-document-path="<?php echo htmlspecialchars($documentPath); ?>">Documents</button>
        </div>
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