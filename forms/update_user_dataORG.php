<?php
session_start();
include 'DBconnection.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    
    if (isset($data['field'], $data['value']) && isset($_SESSION['user_email'])) {
        $field = $data['field'];
        $value = $data['value'];
        $email = $_SESSION['user_email'];
        
        // Allowed fields to prevent SQL injection
        $allowedFields = [
            'organization_name', 'contact_person', 'logo_image', 'contact_phone_number', 
            'organization_address', 'location_country', 'location_county', 'location_town', 
            'registration_certificate', 'organization_type', 'start_date', 'what_you_do', 
            'number_of_employees', 'payment_Number', 'payment_code', 'password'
        ];

        // Validate the field name
        if (in_array($field, $allowedFields)) {
            // Prepare and execute the update query
            $stmt = $conn->prepare("UPDATE organizationmembership SET $field = ? WHERE organization_email = ?");
            $stmt->bind_param("ss", $value, $email);

            if ($stmt->execute()) {
                echo json_encode(["success" => true]);
            } else {
                echo json_encode(["success" => false, "error" => $stmt->error]);
            }
            $stmt->close();
        } else {
            echo json_encode(["success" => false, "error" => "Invalid field."]);
        }
    } else {
        echo json_encode(["success" => false, "error" => "Missing required data or session."]);
    }
    $conn->close();
} else {
    echo json_encode(["success" => false, "error" => "Invalid request method."]);
}
?>
