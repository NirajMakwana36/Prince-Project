<?php
include_once 'c:/xampp/htdocs/CoGroCart/config/db_config.php';

$queries = [
    "ALTER TABLE coupons ADD COLUMN IF NOT EXISTS discount_type ENUM('percentage', 'fixed') DEFAULT 'percentage' AFTER code",
    "ALTER TABLE coupons ADD COLUMN IF NOT EXISTS discount_value DECIMAL(10, 2) NOT NULL DEFAULT 0 AFTER discount_type",
    "ALTER TABLE coupons ADD COLUMN IF NOT EXISTS min_purchase DECIMAL(10, 2) DEFAULT 0 AFTER discount_value",
    "ALTER TABLE coupons ADD COLUMN IF NOT EXISTS is_active BOOLEAN DEFAULT TRUE AFTER expiry_date",
    "ALTER TABLE coupons DROP COLUMN IF EXISTS discount_percentage"
];

foreach ($queries as $q) {
    if ($conn->query($q)) {
        echo "Success: $q\n";
    } else {
        echo "Error: (" . $conn->errno . ") " . $conn->error . " for query: $q\n";
    }
}
?>
