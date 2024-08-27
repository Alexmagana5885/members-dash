CREATE TABLE membershipPayments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    member_id INT NOT NULL,
    time_of_payment DATETIME NOT NULL,
    method_of_payment ENUM('mpesa', 'paypal', 'card') NOT NULL,
    payment_code VARCHAR(255) NOT NULL,
    FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE
);
