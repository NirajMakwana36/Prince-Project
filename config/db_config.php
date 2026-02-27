<?php
ob_start();
// Database Configuration
define('DB_HOST', '127.0.0.1');
define('DB_PORT', 3307);
define('DB_USER', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'grocart');

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, DB_PORT);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to utf8
$conn->set_charset("utf8");

// Define base URL
define('BASE_URL', 'http://localhost/CoGroCart/');

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
