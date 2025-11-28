<?php
// RUN THIS FILE ONCE TO CREATE THE CONTACT MESSAGES TABLE
// Open: http://localhost/school_lost_found/create_messages_table.php
// After success, DELETE this file

require_once 'config.php';

$sql = "CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    subject VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    status ENUM('unread', 'read') DEFAULT 'unread',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

if ($conn->query($sql) === TRUE) {
    echo "<div style='font-family: Arial; max-width: 800px; margin: 50px auto; padding: 20px;'>";
    echo "<h1 style='color: green;'>✓ Success!</h1>";
    echo "<p>Contact messages table created successfully!</p>";
    echo "<hr>";
    echo "<h3>What's Next?</h3>";
    echo "<ol>";
    echo "<li>✓ Table created - You're done!</li>";
    echo "<li>Delete this file: create_messages_table.php (for security)</li>";
    echo "<li>Test the contact form: <a href='contact.php'>contact.php</a></li>";
    echo "<li>View messages as admin: <a href='admin/contact_messages.php'>admin/contact_messages.php</a></li>";
    echo "</ol>";
    echo "<hr>";
    echo "<p><a href='contact.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Go to Contact Form →</a></p>";
    echo "</div>";
} else {
    echo "<h1 style='color: red;'>Error!</h1>";
    echo "<p>Error creating table: " . $conn->error . "</p>";
}

$conn->close();
?>