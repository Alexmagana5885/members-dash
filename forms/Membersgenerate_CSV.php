<?php
// Database connection
require_once('DBconnection.php');


// Query to select all relevant columns from personalmembership
$sql = "SELECT id, name, email, CONCAT('\"', phone, '\"') AS phone, gender, home_address, highest_degree, institution, 
        start_date, graduation_year, profession, experience, current_company, position, work_address, 
        payment_Number, payment_code, registration_date 
        FROM personalmembership";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Set the headers to indicate file download
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=members.csv');
    
    // Open the output stream
    $output = fopen('php://output', 'w');
    
    // Output the column headings
    fputcsv($output, array('ID', 'Name', 'Email', 'Phone', 'Gender', 'Home Address', 'Highest Degree', 
                           'Institution', 'Start Date', 'Graduation Year', 'Profession', 'Experience', 
                           'Current Company', 'Position', 'Work Address', 'Payment Number', 'Payment Code', 
                           'Registration Date'));
    
    // Fetch and output the rows as CSV
    while ($row = $result->fetch_assoc()) {
        // Optionally format the date if necessary
        $row['start_date'] = date('Y-m-d', strtotime($row['start_date']));
        $row['graduation_year'] = date('Y', strtotime($row['graduation_year']));
        $row['registration_date'] = date('Y-m-d', strtotime($row['registration_date']));
        
        // Write the row to the CSV
        fputcsv($output, $row);
    }
    
    // Close the output stream
    fclose($output);
} else {
    echo "No records found.";
}

// Close the connection
$conn->close();
?>

