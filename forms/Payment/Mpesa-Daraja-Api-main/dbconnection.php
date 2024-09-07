<?php
// Database configuration
$db_host = '192.168.0.106';
$db_username = 'alex';
$db_password = 'Maglex#588599';
$db_name = 'agldatabase';

// Create a database connection
$conn = new mysqli($db_host, $db_username, $db_password, $db_name);

// Check the connection and return HTML accordingly
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Connection Status</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f4f4f4;
        }
        .message {
            padding: 20px;
            border-radius: 5px;
            text-align: center;
            font-size: 18px;
            max-width: 400px;
            width: 100%;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <?php
    if ($conn->connect_error) {
        echo '<div class="message error">Connection failed: ' . $conn->connect_error . '</div>';
    } else {
        echo '<div class="message success">Connection successful</div>';
    }
    ?>
</body>
</html>
