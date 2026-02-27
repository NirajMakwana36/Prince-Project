<?php
include_once 'c:/xampp/htdocs/CoGroCart/config/db_config.php';
include_once 'c:/xampp/htdocs/CoGroCart/config/functions.php';

// Check if any delivery user exists
$res = $conn->query("SELECT id FROM users WHERE role = 'delivery' LIMIT 1");
if ($res->num_rows == 0) {
    // Create a delivery user
    $name = "Rajesh Delivery";
    $email = "delivery@test.com";
    $pass = hashPassword("delivery123");
    $conn->query("INSERT INTO users (name, email, password, role) VALUES ('$name', '$email', '$pass', 'delivery')");
    $uid = $conn->insert_id;
    
    // Create delivery partner record
    $conn->query("INSERT INTO delivery_partners (user_id, status, vehicle_type, vehicle_number) VALUES ($uid, 'available', 'Bike', 'MH-12-AB-1234')");
    echo "Delivery partner created successfully.\n";
} else {
    $uid = $res->fetch_assoc()['id'];
    // Ensure detail record exists
    $conn->query("INSERT IGNORE INTO delivery_partners (user_id, status) VALUES ($uid, 'available')");
    echo "Delivery partner already exists.\n";
}
?>
