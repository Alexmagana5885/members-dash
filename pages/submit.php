<?php
// Database configuration
$db_host = 'localhost';
$db_username = 'root'; // Updated username
$db_password = ''; // Updated password
$db_name = 'agldatabase'; // Updated database name

// Create a database connection
$conn = new mysqli($db_host, $db_username, $db_password, $db_name);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the text from the hidden field
    $text = $_POST['text'];

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO test (text) VALUES (?)");
    $stmt->bind_param("s", $text);

    // Execute the statement
    if ($stmt->execute()) {
        echo "New record created successfully.<br><br>";
    } else {
        echo "Error: " . $stmt->error . "<br><br>";
    }

    // Close the statement
    $stmt->close();
}

// Define the SQL query to select id and text from the test table
$sql = "SELECT id, text FROM test";

// Execute the query
$result = $conn->query($sql);

// Check if any rows were returned
if ($result->num_rows > 0) {
    // Output data for each row
    while ($row = $result->fetch_assoc()) {
        echo 'ID: ' . htmlspecialchars($row['id']) . '<br>';
        echo 'Text: ' . $row['text'] . '<br><br>'; // Displaying raw HTML
    }
} else {
    echo "0 results";
}

// Close the connection
$conn->close();
?>
