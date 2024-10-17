<?php
// Start session
session_start();

// Database connection
require_once 'DBconnection.php';

$response = [
    'success' => false,
    'message' => '',
    'errors' => []
];

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $blogTitle = mysqli_real_escape_string($conn, $_POST['blogTitle']);
    $blogContent = mysqli_real_escape_string($conn, $_POST['blogContent']);
    
    // Check if the file input is set and not empty
    if (isset($_FILES['blogImage']) && !empty($_FILES['blogImage']['tmp_name'])) {
        // Handle image upload
        $targetDir = "../assets/img/Blogs/";
        $uploadDate = date("Ymd_His");
        $sanitizedTitle = preg_replace('/[^a-zA-Z0-9-_]/', '_', $blogTitle);
        $imageFileType = strtolower(pathinfo($_FILES['blogImage']['name'], PATHINFO_EXTENSION));
        $newFileName = $sanitizedTitle . '_' . $uploadDate . '.' . $imageFileType;
        $targetFilePath = $targetDir . $newFileName;

        $uploadOk = 1;

        // Check if image file is an actual image
        $check = getimagesize($_FILES['blogImage']['tmp_name']);
        if ($check === false) {
            $response['errors'][] = "File is not an image.";
            $uploadOk = 0;
        }

        // Check if file already exists
        if (file_exists($targetFilePath)) {
            $response['errors'][] = "Sorry, file already exists.";
            $uploadOk = 0;
        }

        // Check file size (limit to 500KB)
        if ($_FILES['blogImage']['size'] > 5000000) {
            $response['errors'][] = "Sorry, your file is too large.";
            $uploadOk = 0;
        }

        // Allow only certain file formats
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($imageFileType, $allowedExtensions)) {
            $response['errors'][] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }

        // Upload file if validation passes
        if ($uploadOk == 1) {
            if (move_uploaded_file($_FILES['blogImage']['tmp_name'], $targetFilePath)) {
                // Insert blog post into database
                $stmt = $conn->prepare("INSERT INTO blog_posts (title, content, image_path) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $blogTitle, $blogContent, $targetFilePath);

                if ($stmt->execute()) {
                    $response['success'] = true;
                    $response['message'] = "Blog post successfully uploaded.";
                } else {
                    $response['errors'][] = "Error: " . $stmt->error;
                }

                $stmt->close();
            } else {
                $response['errors'][] = "Sorry, there was an error uploading your file.";
            }
        } else {
            $response['message'] = "Sorry, your file was not uploaded.";
        }
    } else {
        $response['errors'][] = "No image file uploaded.";
    }
} else {
    $response['errors'][] = "Invalid request method.";
}

// Store response in session
$_SESSION['response'] = $response;

// Redirect back to the admin page
header("Location: ../pages/AGLADMIN.php");
exit();

$conn->close();
?>
