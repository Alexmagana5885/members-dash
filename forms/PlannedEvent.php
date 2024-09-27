<?php
session_start(); // Start the session

require_once('DBconnection.php');

// Initialize response array
$response = array('success' => false, 'message' => '', 'errors' => array());

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize input data
    $eventName = htmlspecialchars($_POST['eventName']);
    $eventDescription = htmlspecialchars($_POST['eventDescription']);
    $eventLocation = htmlspecialchars($_POST['eventLocation']);
    $eventDate = $_POST['eventDate'];
    $registrationAmount = $_POST['RegistrationAmount']; // Capture Registration Amount

    // Check if the file is uploaded
    if (isset($_FILES['eventImage']) && $_FILES['eventImage']['error'] == 0) {
        $targetDir = "../assets/img/PlannedEvent/"; // Directory to save the uploaded file
        $fileName = basename($_FILES['eventImage']['name']);
        $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
        $newFileName = time() . "_" . str_replace(' ', '_', $eventName) . "." . $fileExtension; // Rename file with timestamp and event name
        $targetFilePath = $targetDir . $newFileName;

        // Move the uploaded file to the target directory
        if (move_uploaded_file($_FILES['eventImage']['tmp_name'], $targetFilePath)) {
            // Prepare SQL query to insert data into the database, including registration amount
            $sql = "INSERT INTO plannedevent (event_name, event_image_path, event_description, event_location, event_date, RegistrationAmount)
                    VALUES (?, ?, ?, ?, ?, ?)";

            // Prepare and bind the statement
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param('sssssd', $eventName, $targetFilePath, $eventDescription, $eventLocation, $eventDate, $registrationAmount);

                // Execute the query
                if ($stmt->execute()) {
                    $response['success'] = true;
                    $response['message'] = 'Event added successfully.';
                    // Redirect to the referring page
                    $_SESSION['response'] = $response;
                    header("Location: " . $_SERVER['HTTP_REFERER']);
                    exit;
                } else {
                    $response['message'] = 'Database insertion error: ' . $stmt->error;
                }
            } else {
                $response['message'] = 'Database query preparation error.';
            }
        } else {
            $response['message'] = 'Error moving uploaded file.';
        }
    } else {
        if ($_FILES['eventImage']['error'] == 4) {
            $response['message'] = 'Image was not set';
        } else {
            $response['message'] = 'Error uploading the file: ' . $_FILES['eventImage']['error'];
        }
    }

    // Set the response in the session
    $_SESSION['response'] = $response;
    // Redirect back to the referring page
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit;
}

// Close the database connection
$conn->close();
?>
