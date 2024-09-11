<?php
session_start(); // Start the session

require_once('DBconnection.php');

// Initialize response array
$response = array('success' => false, 'message' => '', 'errors' => array());

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize input data
    $eventName = htmlspecialchars(trim($_POST['eventName']));
    $eventDetails = htmlspecialchars(trim($_POST['eventDetails'])); // Make sure to sanitize the Quill editor content appropriately
    $eventLocation = htmlspecialchars(trim($_POST['eventLocation']));
    $eventDate = htmlspecialchars(trim($_POST['eventDate'])); 

    // Initialize arrays to store uploaded files' paths
    $imagePaths = [];
    $documentPaths = [];

    // Handle image uploads
    if (isset($_FILES['eventImages']) && $_FILES['eventImages']['error'][0] == 0) {
        $targetImageDir = "../assets/img/PastEvents/"; // Directory to save the uploaded images

        foreach ($_FILES['eventImages']['tmp_name'] as $key => $tmpName) {
            if ($_FILES['eventImages']['error'][$key] == UPLOAD_ERR_OK) {
                $fileName = basename($_FILES['eventImages']['name'][$key]);
                $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif']; // Allowed image extensions

                if (in_array($fileExtension, $allowedExtensions)) {
                    $newFileName = time() . "_" . str_replace(' ', '_', $eventName) . "_img_" . $key . "." . $fileExtension;
                    $targetFilePath = $targetImageDir . $newFileName;

                    if (move_uploaded_file($tmpName, $targetFilePath)) {
                        $imagePaths[] = $targetFilePath;
                    } else {
                        $response['errors'][] = "Failed to move image file: " . $_FILES['eventImages']['name'][$key];
                    }
                } else {
                    $response['errors'][] = "Invalid image file type: " . $_FILES['eventImages']['name'][$key];
                }
            } else {
                $response['errors'][] = "Error uploading image file: " . $_FILES['eventImages']['name'][$key];
            }
        }
    }

    // Handle document uploads
    if (isset($_FILES['eventDocuments']) && $_FILES['eventDocuments']['error'][0] == 0) {
        $targetDocumentDir = "../assets/Documents/PastEventsDocs/"; // Directory to save the uploaded documents

        foreach ($_FILES['eventDocuments']['tmp_name'] as $key => $tmpName) {
            if ($_FILES['eventDocuments']['error'][$key] == UPLOAD_ERR_OK) {
                $fileName = basename($_FILES['eventDocuments']['name'][$key]);
                $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                $allowedExtensions = ['pdf']; // Only allow PDF documents

                if (in_array($fileExtension, $allowedExtensions)) {
                    $newFileName = time() . "_" . str_replace(' ', '_', $eventName) . "_doc_" . $key . "." . $fileExtension;
                    $targetFilePath = $targetDocumentDir . $newFileName;

                    if (move_uploaded_file($tmpName, $targetFilePath)) {
                        $documentPaths[] = $targetFilePath;
                    } else {
                        $response['errors'][] = "Failed to move document file: " . $_FILES['eventDocuments']['name'][$key];
                    }
                } else {
                    $response['errors'][] = "Invalid document file type: " . $_FILES['eventDocuments']['name'][$key] . ". Only PDF files are allowed.";
                }
            } else {
                $response['errors'][] = "Error uploading document file: " . $_FILES['eventDocuments']['name'][$key];
            }
        }
    }

    // Convert image and document paths to JSON format for storage in the database
    $imagePathsJson = json_encode($imagePaths);
    $documentPathsJson = json_encode($documentPaths);

    // Prepare SQL query to insert data into the database
    $sql = "INSERT INTO pastevents (event_name, event_details, event_location, event_date, event_image_paths, event_document_paths)
            VALUES (?, ?, ?, ?, ?, ?)";

    // Prepare and bind the statement
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param('ssssss', $eventName, $eventDetails, $eventLocation, $eventDate, $imagePathsJson, $documentPathsJson);

        // Execute the query
        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Event added successfully.';
        } else {
            $response['errors'][] = "Database insertion error: " . $stmt->error;
        }
    } else {
        $response['errors'][] = "Database query preparation error.";
    }

    // Store the response in the session
    $_SESSION['response'] = $response;

    // Redirect to the previous page or a specific page
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit;
}

// Close the database connection
$conn->close();
?>
