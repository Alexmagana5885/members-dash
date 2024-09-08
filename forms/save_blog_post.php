<?php
// Database connection
require_once 'DBconnection.php';

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $blogTitle = mysqli_real_escape_string($conn, $_POST['blogTitle']);
    $blogContent = mysqli_real_escape_string($conn, $_POST['blogContent']);
    
    // Handle image upload
    $targetDir = "../assets/img/Blogs/"; // specify your target directory
    $uploadDate = date("Ymd_His"); // Get the current date and time for the file name
    
    // Sanitize the blog title for file name
    $sanitizedTitle = preg_replace('/[^a-zA-Z0-9-_]/', '_', $blogTitle);
    
    // Create new file name
    $imageFileType = strtolower(pathinfo($_FILES['blogImage']['name'], PATHINFO_EXTENSION));
    $newFileName = $sanitizedTitle . '_' . $uploadDate . '.' . $imageFileType;
    $targetFilePath = $targetDir . $newFileName;
    
    $uploadOk = 1;

    // Check if image file is an actual image or fake image
    if (!empty($_FILES['blogImage']['tmp_name'])) {
        $check = getimagesize($_FILES['blogImage']['tmp_name']);
        if ($check !== false) {
            $uploadOk = 1;
        } else {
            echo "File is not an image.";
            $uploadOk = 0;
        }
    }

    // Check if file already exists
    if (file_exists($targetFilePath)) {
        echo "Sorry, file already exists.";
        $uploadOk = 0;
    }

    // Check file size
    if ($_FILES['blogImage']['size'] > 500000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    // Allow certain file formats
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array($imageFileType, $allowedExtensions)) {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
    } else {
        // Try to upload the file
        if (move_uploaded_file($_FILES['blogImage']['tmp_name'], $targetFilePath)) {
            // Prepare and bind
            $stmt = $conn->prepare("INSERT INTO blog_posts (title, content, image_path) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $blogTitle, $blogContent, $targetFilePath);

            // Execute the statement
            if ($stmt->execute()) {
                echo "Blog post successfully uploaded.";
            } else {
                echo "Error: " . $stmt->error;
            }

            $stmt->close();
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
}

$conn->close();
?>
