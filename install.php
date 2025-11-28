<?php
// ============================================================================
// SCHOOL LOST & FOUND - DATABASE INSTALLATION SCRIPT
// ============================================================================
// Run this file ONCE to set up the database and tables
// Access: http://localhost/school_lost_found/install.php
// After successful installation, DELETE this file for security
// ============================================================================

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'school_lost_found');

// Create connection without selecting database
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS);

// Check connection
if ($conn->connect_error) {
    die("<h1 style='color: red;'>Connection failed: " . $conn->connect_error . "</h1><p>Please make sure MySQL is running in XAMPP Control Panel.</p>");
}

echo "<div style='font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px;'>";
echo "<h1 style='color: #0d6efd;'>🎓 School Lost & Found - Installation</h1>";
echo "<hr>";

// Create database if not exists
$sql = "CREATE DATABASE IF NOT EXISTS " . DB_NAME;
if ($conn->query($sql) === TRUE) {
    echo "<p style='color: green;'>✓ Database '<strong>" . DB_NAME . "</strong>' created successfully (or already exists)</p>";
} else {
    die("<p style='color: red;'>✗ Error creating database: " . $conn->error . "</p></div>");
}

// Select database
$conn->select_db(DB_NAME);

// ============================================================================
// CREATE USERS TABLE
// ============================================================================
echo "<h3>Creating Tables...</h3>";

$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    student_id VARCHAR(20) UNIQUE,
    role ENUM('student', 'admin') DEFAULT 'student',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_email (email),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

if ($conn->query($sql) === TRUE) {
    echo "<p style='color: green;'>✓ Users table created successfully</p>";
} else {
    echo "<p style='color: orange;'>⚠ Users table: " . $conn->error . "</p>";
}

// ============================================================================
// CREATE ITEMS TABLE
// ============================================================================

$sql = "CREATE TABLE IF NOT EXISTS items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT NOT NULL,
    category VARCHAR(50) NOT NULL,
    location_found VARCHAR(200) NOT NULL,
    date_found DATE NOT NULL,
    image VARCHAR(255) DEFAULT NULL,
    status ENUM('pending', 'approved', 'claimed') DEFAULT 'pending',
    submitted_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (submitted_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_status (status),
    INDEX idx_category (category),
    INDEX idx_date_found (date_found),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

if ($conn->query($sql) === TRUE) {
    echo "<p style='color: green;'>✓ Items table created successfully</p>";
} else {
    echo "<p style='color: orange;'>⚠ Items table: " . $conn->error . "</p>";
}

// ============================================================================
// CREATE CLAIMS TABLE
// ============================================================================

$sql = "CREATE TABLE IF NOT EXISTS claims (
    id INT AUTO_INCREMENT PRIMARY KEY,
    item_id INT NOT NULL,
    user_id INT NOT NULL,
    description TEXT NOT NULL,
    contact_info VARCHAR(100),
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (item_id) REFERENCES items(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_item_id (item_id),
    INDEX idx_user_id (user_id),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

if ($conn->query($sql) === TRUE) {
    echo "<p style='color: green;'>✓ Claims table created successfully</p>";
} else {
    echo "<p style='color: orange;'>⚠ Claims table: " . $conn->error . "</p>";
}

// ============================================================================
// INSERT DEFAULT ADMIN USER
// ============================================================================

echo "<h3>Creating Default Admin Account...</h3>";

// Check if admin already exists
$check = $conn->query("SELECT id FROM users WHERE username = 'admin'");
if ($check->num_rows > 0) {
    echo "<p style='color: orange;'>⚠ Admin account already exists. Skipping...</p>";
} else {
    $admin_password = password_hash('admin123', PASSWORD_DEFAULT);
    $sql = "INSERT INTO users (username, email, password, full_name, student_id, role) 
            VALUES ('admin', 'admin@school.com', '$admin_password', 'System Administrator', 'ADMIN001', 'admin')";
    
    if ($conn->query($sql) === TRUE) {
        echo "<p style='color: green;'>✓ Default admin account created successfully</p>";
    } else {
        echo "<p style='color: red;'>✗ Error creating admin account: " . $conn->error . "</p>";
    }
}

// ============================================================================
// CREATE SAMPLE DATA (OPTIONAL - FOR TESTING)
// ============================================================================

echo "<h3>Creating Sample Student Account (For Testing)...</h3>";

// Check if sample student exists
$check = $conn->query("SELECT id FROM users WHERE username = 'student'");
if ($check->num_rows > 0) {
    echo "<p style='color: orange;'>⚠ Sample student account already exists. Skipping...</p>";
} else {
    $student_password = password_hash('student123', PASSWORD_DEFAULT);
    $sql = "INSERT INTO users (username, email, password, full_name, student_id, role) 
            VALUES ('student', 'student@school.com', '$student_password', 'John Student', 'STU001', 'student')";
    
    if ($conn->query($sql) === TRUE) {
        echo "<p style='color: green;'>✓ Sample student account created (for testing)</p>";
    } else {
        echo "<p style='color: orange;'>⚠ Could not create sample student: " . $conn->error . "</p>";
    }
}

// ============================================================================
// VERIFY INSTALLATION
// ============================================================================

echo "<h3>Verifying Installation...</h3>";

// Count tables
$result = $conn->query("SHOW TABLES");
$table_count = $result->num_rows;

// Count users
$result = $conn->query("SELECT COUNT(*) as count FROM users");
$user_data = $result->fetch_assoc();
$user_count = $user_data['count'];

echo "<p>✓ Database tables created: <strong>$table_count</strong></p>";
echo "<p>✓ User accounts created: <strong>$user_count</strong></p>";

// ============================================================================
// DISPLAY SUCCESS MESSAGE AND CREDENTIALS
// ============================================================================

echo "<hr>";
echo "<div style='background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 30px; border-radius: 10px; color: white; margin: 20px 0;'>";
echo "<h2 style='margin: 0 0 20px 0;'>🎉 Installation Complete!</h2>";
echo "<p style='font-size: 18px; margin: 10px 0;'>Your School Lost & Found website is ready to use!</p>";
echo "</div>";

echo "<div style='background: #d4edda; padding: 20px; border-radius: 5px; border: 2px solid #28a745; margin: 20px 0;'>";
echo "<h3 style='color: #155724; margin-top: 0;'>🔐 Default Login Credentials</h3>";
echo "<table style='width: 100%; border-collapse: collapse;'>";
echo "<tr style='background: #fff;'><td style='padding: 10px; border: 1px solid #c3e6cb;'><strong>Admin Username:</strong></td><td style='padding: 10px; border: 1px solid #c3e6cb;'><code style='background: #f8f9fa; padding: 5px 10px; border-radius: 3px;'>admin</code></td></tr>";
echo "<tr style='background: #fff;'><td style='padding: 10px; border: 1px solid #c3e6cb;'><strong>Admin Password:</strong></td><td style='padding: 10px; border: 1px solid #c3e6cb;'><code style='background: #f8f9fa; padding: 5px 10px; border-radius: 3px;'>admin123</code></td></tr>";
echo "<tr style='background: #fff;'><td style='padding: 10px; border: 1px solid #c3e6cb;'><strong>Test Student Username:</strong></td><td style='padding: 10px; border: 1px solid #c3e6cb;'><code style='background: #f8f9fa; padding: 5px 10px; border-radius: 3px;'>student</code></td></tr>";
echo "<tr style='background: #fff;'><td style='padding: 10px; border: 1px solid #c3e6cb;'><strong>Test Student Password:</strong></td><td style='padding: 10px; border: 1px solid #c3e6cb;'><code style='background: #f8f9fa; padding: 5px 10px; border-radius: 3px;'>student123</code></td></tr>";
echo "</table>";
echo "</div>";

echo "<div style='background: #fff3cd; padding: 20px; border-radius: 5px; border: 2px solid #ffc107; margin: 20px 0;'>";
echo "<h3 style='color: #856404; margin-top: 0;'>⚠️ IMPORTANT SECURITY STEPS</h3>";
echo "<ol style='color: #856404; margin: 0;'>";
echo "<li><strong>Change the admin password immediately!</strong></li>";
echo "<li><strong>Delete this install.php file after installation</strong> (for security)</li>";
echo "<li>Create backup of database regularly via phpMyAdmin</li>";
echo "<li>Don't use these default passwords in production!</li>";
echo "</ol>";
echo "</div>";

echo "<div style='background: #d1ecf1; padding: 20px; border-radius: 5px; border: 2px solid #17a2b8; margin: 20px 0;'>";
echo "<h3 style='color: #0c5460; margin-top: 0;'>🚀 Next Steps</h3>";
echo "<ol style='color: #0c5460;'>";
echo "<li>Click the button below to access your website</li>";
echo "<li>Login with admin credentials</li>";
echo "<li>Explore the admin dashboard</li>";
echo "<li>Create test items and claims</li>";
echo "<li>Register new student accounts</li>";
echo "</ol>";
echo "</div>";

echo "<div style='text-align: center; margin: 30px 0;'>";
echo "<a href='index.php' style='display: inline-block; background: #0d6efd; color: white; padding: 15px 40px; text-decoration: none; border-radius: 5px; font-size: 18px; font-weight: bold; box-shadow: 0 4px 6px rgba(0,0,0,0.1);'>🏠 Go to Website →</a>";
echo "<a href='login.php' style='display: inline-block; background: #28a745; color: white; padding: 15px 40px; text-decoration: none; border-radius: 5px; font-size: 18px; font-weight: bold; margin-left: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);'>🔐 Login →</a>";
echo "<a href='admin/dashboard.php' style='display: inline-block; background: #dc3545; color: white; padding: 15px 40px; text-decoration: none; border-radius: 5px; font-size: 18px; font-weight: bold; margin-left: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);'>⚙️ Admin Panel →</a>";
echo "</div>";

echo "<hr>";
echo "<div style='background: #e7f3ff; padding: 15px; border-radius: 5px; border-left: 4px solid #0d6efd;'>";
echo "<h4 style='margin-top: 0; color: #004085;'>📊 Database Information</h4>";
echo "<p style='margin: 5px 0;'><strong>Database Name:</strong> school_lost_found</p>";
echo "<p style='margin: 5px 0;'><strong>Tables Created:</strong> users, items, claims</p>";
echo "<p style='margin: 5px 0;'><strong>Admin Panel:</strong> <a href='admin/dashboard.php'>http://localhost/school_lost_found/admin/dashboard.php</a></p>";
echo "<p style='margin: 5px 0;'><strong>phpMyAdmin:</strong> <a href='http://localhost/phpmyadmin' target='_blank'>http://localhost/phpmyadmin</a></p>";
echo "</div>";

echo "<div style='margin-top: 30px; padding: 20px; background: #f8f9fa; border-radius: 5px; text-align: center;'>";
echo "<p style='color: #6c757d; margin: 0;'>School Lost & Found System v1.0</p>";
echo "<p style='color: #6c757d; margin: 5px 0 0 0;'>Developed with PHP, MySQL, Bootstrap, jQuery & AJAX</p>";
echo "</div>";

echo "</div>";

$conn->close();
?>