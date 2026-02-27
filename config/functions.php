<?php
// Common Functions
include_once 'db_config.php';

// Hash password
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT);
}

// Verify password
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

// Sanitize input
function sanitize($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// SQL Injection Prevention - Prepared Statement
function query($conn, $sql, $types = "", $params = []) {
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        return false;
    }
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    return $stmt->execute() ? $stmt : false;
}

// Fetch single row
function fetchOne($stmt) {
    $result = $stmt->get_result();
    return $result->num_rows > 0 ? $result->fetch_assoc() : null;
}

// Fetch all rows
function fetchAll($stmt) {
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Get product by ID
function getProduct($conn, $id) {
    $stmt = $conn->prepare("SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return fetchOne($stmt);
}

// Get category by ID
function getCategory($conn, $id) {
    $stmt = $conn->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return fetchOne($stmt);
}

// Get user by ID
function getUser($conn, $id) {
    $stmt = $conn->prepare("SELECT id, name, email, phone, role, status, address, city, postal_code FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return fetchOne($stmt);
}

// Get all products
function getAllProducts($conn, $limit = 20, $offset = 0) {
    $stmt = $conn->prepare("SELECT * FROM products WHERE is_available = TRUE LIMIT ? OFFSET ?");
    $stmt->bind_param("ii", $limit, $offset);
    $stmt->execute();
    return fetchAll($stmt);
}

// Get products by category
function getProductsByCategory($conn, $category_id, $limit = 20, $offset = 0) {
    $stmt = $conn->prepare("SELECT * FROM products WHERE category_id = ? AND is_available = TRUE LIMIT ? OFFSET ?");
    $stmt->bind_param("iii", $category_id, $limit, $offset);
    $stmt->execute();
    return fetchAll($stmt);
}

// Get all categories
function getAllCategories($conn) {
    $stmt = $conn->prepare("SELECT * FROM categories");
    $stmt->execute();
    return fetchAll($stmt);
}

// Calculate discount price
function getDiscountedPrice($price, $discount) {
    return $price - ($price * $discount / 100);
}

// Get cart count
function getCartCount($conn, $user_id) {
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM cart WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = fetchOne($stmt);
    return $result['count'];
}

// Get cart items
function getCartItems($conn, $user_id) {
    $stmt = $conn->prepare("
        SELECT c.id, c.user_id, c.product_id, c.quantity, 
               p.name, p.price, p.discount, p.image, p.stock
        FROM cart c
        JOIN products p ON c.product_id = p.id
        WHERE c.user_id = ?
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    return fetchAll($stmt);
}

// Calculate cart total
function getCartTotal($conn, $user_id) {
    $items = getCartItems($conn, $user_id);
    $total = 0;
    foreach ($items as $item) {
        $price = getDiscountedPrice($item['price'], $item['discount']);
        $total += $price * $item['quantity'];
    }
    return $total;
}

// Get delivery charge
function getDeliveryCharge($conn) {
    $stmt = $conn->prepare("SELECT setting_value FROM settings WHERE setting_key = 'delivery_charge'");
    $stmt->execute();
    $result = fetchOne($stmt);
    return $result ? intval($result['setting_value']) : 40;
}

// Check if store is open
function isStoreOpen($conn) {
    $stmt = $conn->prepare("SELECT setting_value FROM settings WHERE setting_key = 'store_status'");
    $stmt->execute();
    $result = fetchOne($stmt);
    return $result && $result['setting_value'] === 'open';
}

// Search products
function searchProducts($conn, $search_term, $limit = 20, $offset = 0) {
    $search_term = "%$search_term%";
    $stmt = $conn->prepare("SELECT * FROM products WHERE (name LIKE ? OR description LIKE ?) AND is_available = TRUE LIMIT ? OFFSET ?");
    $stmt->bind_param("ssii", $search_term, $search_term, $limit, $offset);
    $stmt->execute();
    return fetchAll($stmt);
}

// Filter products by price
function filterProductsByPrice($conn, $min_price, $max_price, $limit = 20, $offset = 0) {
    $stmt = $conn->prepare("SELECT * FROM products WHERE price BETWEEN ? AND ? AND is_available = TRUE LIMIT ? OFFSET ?");
    $stmt->bind_param("ddii", $min_price, $max_price, $limit, $offset);
    $stmt->execute();
    return fetchAll($stmt);
}

// Get trending products
function getTrendingProducts($conn, $limit = 8) {
    $stmt = $conn->prepare("SELECT * FROM products WHERE is_available = TRUE ORDER BY created_at DESC LIMIT ?");
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    return fetchAll($stmt);
}

// Get order details
function getOrder($conn, $order_id) {
    $stmt = $conn->prepare("
        SELECT o.*, u.name as customer_name, u.email, u.phone
        FROM orders o
        JOIN users u ON o.user_id = u.id
        WHERE o.id = ?
    ");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    return fetchOne($stmt);
}

// Get order items
function getOrderItems($conn, $order_id) {
    $stmt = $conn->prepare("
        SELECT oi.*, p.name, p.image
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        WHERE oi.order_id = ?
    ");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    return fetchAll($stmt);
}

// Format currency
function formatCurrency($amount) {
    return "₹" . number_format($amount, 2);
}

// Format date
function formatDate($date) {
    return date('d M Y, h:i A', strtotime($date));
}

// Redirect with message
function redirect($url, $message = '', $type = 'info') {
    $_SESSION['message'] = $message;
    $_SESSION['msg_type'] = $type;
    header("Location: $url");
    exit;
}

// Get user by email
function getUserByEmail($conn, $email) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    return fetchOne($stmt);
}

// Check if user is admin
function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

// Check if user is delivery partner
function isDeliveryPartner() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'delivery';
}

// Check if user is customer
function isCustomer() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'customer';
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Get flashmessage
function getFlashMessage() {
    if (isset($_SESSION['message'])) {
        $message = $_SESSION['message'];
        $type = $_SESSION['msg_type'] ?? 'info';
        unset($_SESSION['message']);
        unset($_SESSION['msg_type']);
        return ['message' => $message, 'type' => $type];
    }
    return null;
}

// Time Elapsed String
function time_elapsed_string($datetime, $full = false) {
    if ($datetime == '0000-00-00 00:00:00' || $datetime == null) return "Never";
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = array(
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    );
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' ago' : 'just now';
}
// Get product rating summary
function getProductRating($conn, $product_id) {
    $stmt = $conn->prepare("SELECT AVG(rating) as avg_rating, COUNT(*) as count FROM reviews WHERE product_id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $res = fetchOne($stmt);
    return [
        'rating' => round($res['avg_rating'] ?? 0, 1),
        'count' => $res['count'] ?? 0
    ];
}
function getProductReviews($conn, $product_id) {
    $stmt = $conn->prepare("
        SELECT r.*, u.name as user_name 
        FROM reviews r 
        JOIN users u ON r.user_id = u.id 
        WHERE r.product_id = ? 
        ORDER BY r.created_at DESC
    ");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    return fetchAll($stmt);
}

// Check if user has purchased product (to allow review)
function hasPurchasedProduct($conn, $user_id, $product_id) {
    $stmt = $conn->prepare("
        SELECT oi.id 
        FROM order_items oi 
        JOIN orders o ON oi.order_id = o.id 
        WHERE o.user_id = ? AND oi.product_id = ? AND o.status = 'delivered'
        LIMIT 1
    ");
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0;
}

// Wishlist check
function isInWishlist($conn, $user_id, $product_id) {
    $stmt = $conn->prepare("SELECT id FROM wishlist WHERE user_id = ? AND product_id = ?");
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0;
}

// Get wishlist count
function getWishlistCount($conn, $user_id) {
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM wishlist WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $res = fetchOne($stmt);
    return $res ? $res['count'] : 0;
}
?>
