<?php
include_once 'c:/xampp/htdocs/CoGroCart/config/db_config.php';

$category_images = [
    'Fruits & Vegetables' => 'https://images.unsplash.com/photo-1610832958506-aa56368176cf?auto=format&fit=crop&w=300&q=80',
    'Beverages' => 'https://images.unsplash.com/photo-1544145945-f904253d0c71?auto=format&fit=crop&w=300&q=80',
    'Snacks' => 'https://images.unsplash.com/photo-1599490659213-e2b9527bb087?auto=format&fit=crop&w=300&q=80',
    'Dairy' => 'https://images.unsplash.com/photo-1550583724-1255818c053b?auto=format&fit=crop&w=300&q=80',
    'Household' => 'https://images.unsplash.com/photo-1584622650111-993a426fbf0a?auto=format&fit=crop&w=300&q=80'
];

foreach ($category_images as $name => $url) {
    $conn->query("UPDATE categories SET image = '$url' WHERE name = '$name'");
}

$product_images = [
    'Apple' => 'https://images.unsplash.com/photo-1560806887-1e4cd0b6cbd6?auto=format&fit=crop&w=400&q=80',
    'Milk' => 'https://images.unsplash.com/photo-1563636619-e910019335da?auto=format&fit=crop&w=400&q=80',
    'Bread' => 'https://images.unsplash.com/photo-1509440159596-0249088772ff?auto=format&fit=crop&w=400&q=80',
    'Chips' => 'https://images.unsplash.com/photo-1566478989037-eec170784d0b?auto=format&fit=crop&w=400&q=80',
    'Coca Cola' => 'https://images.unsplash.com/photo-1622483767028-3f66f32aef97?auto=format&fit=crop&w=400&q=80'
];

foreach ($product_images as $name => $url) {
    $conn->query("UPDATE products SET image = '$url' WHERE name LIKE '%$name%'");
}

echo "Images updated successfully!";
?>
