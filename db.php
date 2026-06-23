<?php
$servername = "localhost";
$username = "root";   // default for XAMPP
$password = "";       // default for XAMPP
$dbname = "zoo_system";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create bookings table if it does not exist
$conn->query("CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_name VARCHAR(100) NULL,
    visitor_name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    phone VARCHAR(30) NOT NULL,
    visit_date DATE NOT NULL,
    visit_time TIME NOT NULL,
    guests INT NOT NULL,
    message TEXT,
    status ENUM('Pending','Approved','Cancelled') NOT NULL DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$conn->query("ALTER TABLE bookings ADD COLUMN IF NOT EXISTS adults INT NOT NULL DEFAULT 0");
$conn->query("ALTER TABLE bookings ADD COLUMN IF NOT EXISTS children INT NOT NULL DEFAULT 0");
$conn->query("ALTER TABLE bookings ADD COLUMN IF NOT EXISTS total_cost DECIMAL(10,2) NOT NULL DEFAULT 0.00");

$zoo_email = "info@zooanimalsystem.com";

function send_email_notification($to, $subject, $message, $from = null) {
    if (!$from) {
        global $zoo_email;
        $from = $zoo_email;
    }
    $headers = "MIME-Version: 1.0\r\n" .
               "Content-Type: text/html; charset=UTF-8\r\n" .
               "From: Zoo Animal System <{$from}>\r\n" .
               "Reply-To: {$from}\r\n";

    return mail($to, $subject, $message, $headers);
}
?>