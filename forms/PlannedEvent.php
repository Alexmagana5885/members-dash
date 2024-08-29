<?php

require_once('DBconnection.php');

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize input data
    $eventName = htmlspecialchars($_POST['eventName']);
    $eventDescription = htmlspecialchars($_POST['eventDescription']);
    $eventLocation = htmlspecialchars($_POST['eventLocation']);
    $eventDate = $_POST['eventDate'];

    // Handle file upload
    if (isset($_FILES['eventImage']) && $_FILES['eventImage']['error'] == 0) {
        $targetDir = "../assets/img/PlannedEvent/"; // Directory to save the uploaded file
        $fileName = basename($_FILES['eventImage']['name']);
        $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
        $newFileName = time() . "_" . str_replace(' ', '_', $eventName) . "." . $fileExtension; // Rename file with timestamp and event name
        $targetFilePath = $targetDir . $newFileName;

        // Move the uploaded file to the target directory
        if (move_uploaded_file($_FILES['eventImage']['tmp_name'], $targetFilePath)) {
            // Prepare SQL query to insert data into the database
            $sql = "INSERT INTO PlannedEvent (event_name, event_image_path, event_description, event_location, event_date)
                    VALUES ('$eventName', '$targetFilePath', '$eventDescription', '$eventLocation', '$eventDate')";

            // Execute the query
            if ($conn->query($sql) === TRUE) {
                // Redirect to the original page to clear the form and reload the page
                header("Location: {$_SERVER['HTTP_REFERER']}");
                exit;
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    } else {
        echo "No file was uploaded or there was an error uploading the file.";
    }
}

// Close the database connection
$conn->close();
?>
