<?php
session_start(); // Start the session

// Include the database connection file
include 'AGLdbconnection.php';

// Log the callback response for debugging
file_put_contents('mpesa_callback_response.log', file_get_contents('php://input'), FILE_APPEND);

// Read the incoming callback data
$data = json_decode(file_get_contents('php://input'), true);

// Extract relevant data from the response
$MerchantRequestID = $data['Body']['stkCallback']['MerchantRequestID'] ?? null;
$CheckoutRequestID = $data['Body']['stkCallback']['CheckoutRequestID'] ?? null;
$ResultCode = $data['Body']['stkCallback']['ResultCode'] ?? null;
$ResultDesc = $data['Body']['stkCallback']['ResultDesc'] ?? null;
$Amount = $data['Body']['stkCallback']['CallbackMetadata']['Item'][0]['Value'] ?? null;
$TransactionId = $data['Body']['stkCallback']['CallbackMetadata']['Item'][1]['Value'] ?? null;
$UserPhoneNumber = $data['Body']['stkCallback']['CallbackMetadata']['Item'][4]['Value'] ?? null;

// Check if the transaction was successful
if ($ResultCode == 0) {

    // Retrieve the email associated with the CheckoutRequestID from the eventregcheckout table
    $checkoutQuery = $conn->prepare("SELECT email, member_name, event_id, event_name, event_location, event_date FROM eventregcheckout WHERE CheckoutRequestID = ?");
    $checkoutQuery->bind_param("s", $CheckoutRequestID);
    $checkoutQuery->execute();
    $checkoutResult = $checkoutQuery->get_result();

    if (!$checkoutResult) {
        $response['errors'][] = "Database query failed: " . $conn->error;
        $_SESSION['response'] = $response;
        exit;
    }

    if ($checkoutResult->num_rows > 0) {
        $checkoutData = $checkoutResult->fetch_assoc();

        // Extract necessary information
        $email = $checkoutData['email'];
        $memberName = $checkoutData['member_name'];
        $eventId = $checkoutData['event_id'];
        $eventName = $checkoutData['event_name'];
        $eventLocation = $checkoutData['event_location'];
        $eventDate = $checkoutData['event_date'];
        $registrationDate = date('Y-m-d H:i:s');

        // Insert the data into event_registrations table
        $insertQuery = $conn->prepare("INSERT INTO event_registrations (event_id, event_name, event_location, event_date, member_email, member_name, contact, registration_date, payment_code, invitation_card)  
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $invitationCardPath = ''; // Initialize invitation card path
        $insertQuery->bind_param("ssssssssss", $eventId, $eventName, $eventLocation, $eventDate, $email, $memberName, $UserPhoneNumber, $registrationDate, $TransactionId, $invitationCardPath);
        
        if (!$insertQuery->execute()) {
            $response['errors'][] = "Failed to insert event registration: " . $conn->error;
            $_SESSION['response'] = $response;
            exit;
        }

        // Send email with registration confirmation
        $to = $email;
        $subject = "Registration Successful!";
        $message = "
            Dear $memberName,

            Thank you for registering for $eventName! We're excited to have you join us on $eventDate.

            Event Details:
            
            Location: $eventLocation
            Time: 10:00 AM

            Please check your email for more details and any future updates.

            We look forward to seeing you there!

            Warm regards,
            The AGL Team
        ";
        $headers = "From: events@agl.or.ke\r\n";
        $headers .= "Reply-To: events@agl.or.ke\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

        if (!mail($to, $subject, $message, $headers)) {
            $response['errors'][] = "Failed to send registration confirmation email to $to";
            $_SESSION['response'] = $response;
            exit;
        }

        // Send POST request to event_card.php
        $url = 'event_card.php';
        $postData = ['email' => $email];

        // Initialize cURL session
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        
        // Execute the request
        $result = curl_exec($ch);
        curl_close($ch);
        
        // Optionally log the result of the cURL request
        file_put_contents('event_card_response.log', $result, FILE_APPEND);

        $response['success'] = true;
        $response['message'] = "Event registration successful, confirmation email sent, and event card processing initiated.";
    } else {
        $response['errors'][] = "No records found for CheckoutRequestID: $CheckoutRequestID";
    }
} else {
    $response['errors'][] = "Transaction failed: $ResultDesc";
}

// Save the response to the session
$_SESSION['response'] = $response;

// Return the JSON response
echo json_encode($response);
