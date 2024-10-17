<?php
require_once('../forms/DBconnection.php');

// Get the blog post ID from the query parameter
$postId = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch the blog post details from the database
$sql = "SELECT * FROM blog_posts WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $postId);
$stmt->execute();
$result = $stmt->get_result();

$blogPost = $result->fetch_assoc();
$stmt->close();
$conn->close();

// Prepare the image path
$imagePath = htmlspecialchars($blogPost["image_path"] ?? '');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Blog Post Details</title>
    <link href="../assets/img/favicon.png" rel="icon" />
    <link href="../assets/CSS/quilleditor.css" rel="stylesheet" />
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

    .eventdetailsdate {
        font-size: 0.9em;
        color: #777;
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

        .eventdetailsdate {
            font-size: 0.8em;
        }
    }

    @media (max-width: 480px) {
        .eventdetailshead {
            font-size: 1em;
        }

        .eventdetailsdetails {
            font-size: 0.8em;
        }

        .eventdetailsdate {
            font-size: 0.7em;
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
            <img src="<?php echo $imagePath; ?>" alt="Blog Image">
        </div>
        <div class="eventdetails">
            <div class="eventdetailshead"><?php echo htmlspecialchars($blogPost['title']); ?></div>
            <div class="eventdetailsdetails">
                <?php echo html_entity_decode($blogPost['content']); ?>
            </div>
            <div class="eventdivinner">
                <div class="eventdetailsdate"><?php echo htmlspecialchars($blogPost['created_at']); ?></div>
            </div>
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

    <!-- JavaScript for Menu Toggle -->
    <script>
        // Menu toggle functionality
        const menuToggle = document.getElementById("menu-toggle");
        const navigation = document.getElementById("navigation");

        menuToggle.addEventListener("click", () => {
            navigation.classList.toggle("active");
        });
    </script>
</body>
</html>
