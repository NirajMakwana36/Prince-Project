<?php
include_once 'config/db_config.php';

// Check if categories exist
$result = $conn->query("SELECT COUNT(*) as count FROM categories");
$row = $result->fetch_assoc();

if ($row['count'] == 0) {
    echo "Seeding categories...<br>";
    $categories = [
        ['Fresh Vegetables', 'Organic and fresh from farms', 'veg.jpg'],
        ['Fresh Fruits', 'Sweet and juicy seasonal fruits', 'fruits.jpg'],
        ['Dairy & Bakery', 'Fresh milk, bread and butter', 'dairy.jpg'],
        ['Snacks & Drinks', 'Crunchy snacks and refreshing drinks', 'snacks.jpg'],
        ['Household Items', 'Daily essentials for your home', 'house.jpg'],
        ['Beauty & Hygiene', 'Personal care and beauty products', 'beauty.jpg']
    ];

    $stmt = $conn->prepare("INSERT INTO categories (name, description, image) VALUES (?, ?, ?)");
    foreach ($categories as $cat) {
        $stmt->bind_param("sss", $cat[0], $cat[1], $cat[2]);
        $stmt->execute();
    }
}

// Check if products exist
$result = $conn->query("SELECT COUNT(*) as count FROM products");
$row = $result->fetch_assoc();

if ($row['count'] == 0) {
    echo "Seeding products...<br>";
    // Get category IDs
    $res = $conn->query("SELECT id, name FROM categories");
    $cat_ids = [];
    while($c = $res->fetch_assoc()) {
        $cat_ids[$c['name']] = $c['id'];
    }

    $products = [
        ['Fresh Tomato', $cat_ids['Fresh Vegetables'], 40.00, 10, 100, 'tomato.jpg', 'Local farm fresh tomatoes'],
        ['Broccoli', $cat_ids['Fresh Vegetables'], 80.00, 5, 50, 'broccoli.jpg', 'Highly nutritious fresh broccoli'],
        ['Royal Gala Apple', $cat_ids['Fresh Fruits'], 180.00, 15, 200, 'apple.jpg', 'Sweet and crunchy apples'],
        ['Fresh Banana', $cat_ids['Fresh Fruits'], 60.00, 0, 150, 'banana.jpg', 'Energizing fresh bananas'],
        ['Farm Fresh Milk', $cat_ids['Dairy & Bakery'], 65.00, 0, 80, 'milk.jpg', 'Pure cow milk'],
        ['Whole Wheat Bread', $cat_ids['Dairy & Bakery'], 45.00, 5, 40, 'bread.jpg', 'Freshly baked healthy bread'],
        ['Potato Chips', $cat_ids['Snacks & Drinks'], 20.00, 0, 300, 'chips.jpg', 'Classic salted chips'],
        ['Coca Cola 500ml', $cat_ids['Snacks & Drinks'], 40.00, 2, 100, 'coke.jpg', 'Refreshing cold drink']
    ];

    $stmt = $conn->prepare("INSERT INTO products (name, category_id, price, discount, stock, image, description) VALUES (?, ?, ?, ?, ?, ?, ?)");
    foreach ($products as $prod) {
        $stmt->bind_param("sidiiss", $prod[0], $prod[1], $prod[2], $prod[3], $prod[4], $prod[5], $prod[6]);
        $stmt->execute();
    }
}

echo "Seeding completed successfully!";
?>
