<?php
require_once('DBconnection.php');

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize input data
    $eventName = htmlspecialchars($_POST['eventName']);
    $eventDetails = htmlspecialchars($_POST['eventDetails']); // Make sure to sanitize the Quill editor content appropriately
    $eventLocation = htmlspecialchars($_POST['eventLocation']);
    $eventDate = $_POST['eventDate']; 

    // Prepare arrays to store uploaded files' paths
    $imagePaths = [];
    $documentPaths = [];

    // Handle image uploads
    if (isset($_FILES['eventImages']) && $_FILES['eventImages']['error'][0] == 0) {
        $targetImageDir = "../assets/img/PastEvents/"; // Directory to save the uploaded images

        foreach ($_FILES['eventImages']['tmp_name'] as $key => $tmpName) {
            $fileName = basename($_FILES['eventImages']['name'][$key]);
            $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
            $newFileName = time() . "_" . str_replace(' ', '_', $eventName) . "_img_" . $key . "." . $fileExtension; // Rename file with timestamp, event name, and key
            $targetFilePath = $targetImageDir . $newFileName;

            // Move the uploaded file to the target directory
            if (move_uploaded_file($tmpName, $targetFilePath)) {
                $imagePaths[] = $targetFilePath;
            }
        }
    }

    // Handle document uploads
    if (isset($_FILES['eventDocuments']) && $_FILES['eventDocuments']['error'][0] == 0) {
        $targetDocumentDir = "../assets/Documents/PastEventsDocs/"; // Directory to save the uploaded documents

        foreach ($_FILES['eventDocuments']['tmp_name'] as $key => $tmpName) {
            $fileName = basename($_FILES['eventDocuments']['name'][$key]);
            $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
            $newFileName = time() . "_" . str_replace(' ', '_', $eventName) . "_doc_" . $key . "." . $fileExtension; // Rename file with timestamp, event name, and key
            $targetFilePath = $targetDocumentDir . $newFileName;

            // Move the uploaded file to the target directory
            if (move_uploaded_file($tmpName, $targetFilePath)) {
                $documentPaths[] = $targetFilePath;
            }
        }
    }

    // Convert image and document paths to JSON format for storage in the database
    $imagePathsJson = json_encode($imagePaths);
    $documentPathsJson = json_encode($documentPaths);

    // Prepare SQL query to insert data into the database
    $sql = "INSERT INTO PastEvents (event_name, event_details, event_location, event_date, event_image_paths, event_document_paths)
            VALUES ('$eventName', '$eventDetails', '$eventLocation', '$eventDate', '$imagePathsJson', '$documentPathsJson')";

    // Execute the query
    if ($conn->query($sql) === TRUE) {
        echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    document.getElementById('successModal').style.display = 'block';
                });
              </script>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Close the database connection
$conn->close();
?>
