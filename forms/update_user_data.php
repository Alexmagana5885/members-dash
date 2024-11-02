<?php
session_start();
include 'DBconnection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $field = $data['field'];
    $value = $data['value'];

    // Retrieve email from session
    $email = $_SESSION['user_email']; 

    // List of allowed fields to prevent SQL injection
    $allowedFields = [
        'name', 'phone', 'home_address', 'passport_image', 'highest_degree', 
        'institution', 'graduation_year', 'completion_letter', 'profession', 
        'experience', 'current_company', 'position', 'work_address', 
        'payment_Number', 'payment_code', 'password', 'gender'
    ];


    // Check if the field is allowed
    if ($email && in_array($field, $allowedFields)) {
        // Prepare and execute the update query
        $stmt = $conn->prepare("UPDATE personalmembership SET $field = ? WHERE email = ?");
        $stmt->bind_param("ss", $value, $email);

        if ($stmt->execute()) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "error" => $stmt->error]);
        }
        $stmt->close();
    } else {
        echo json_encode(["success" => false, "error" => "Invalid field or input."]);
    }
    $conn->close();
} else {
    echo json_encode(["success" => false, "error" => "Invalid request method."]);
}
?>
