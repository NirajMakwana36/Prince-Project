<?php
include_once 'config/db_config.php';
if ($conn->connect_error) {
    echo "Connection failed: " . $conn->connect_error;
} else {
    echo "Connected successfully to " . DB_NAME;
    
    // Check if table exists
    $result = $conn->query("SHOW TABLES LIKE 'users'");
    if ($result->num_rows > 0) {
        echo "<br>Tables found. Database is ready.";
    } else {
        echo "<br>No tables found. Please import database.sql.";
    }
}
?>
