<?php
include 'config/db_config.php';
$sql = "CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY, 
    user_id INT NULL, 
    role ENUM('admin', 'delivery', 'customer') NULL, 
    title VARCHAR(100), 
    message TEXT, 
    link VARCHAR(255), 
    is_read TINYINT(1) DEFAULT 0, 
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
if ($conn->query($sql)) {
    echo "Table created successfully.";
} else {
    echo "Error: " . $conn->error;
}
?>
