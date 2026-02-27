<?php
header('Content-Type: application/json');
if (session_status() === PHP_SESSION_NONE) session_start();
include_once '../config/functions.php';

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit;
}

$user_id = $_SESSION['user_id'];
$action = $_GET['action'] ?? '';

if ($action === 'toggle') {
    $product_id = intval($_POST['product_id']);
    
    // Check if exists
    $check = $conn->prepare("SELECT id FROM wishlist WHERE user_id = ? AND product_id = ?");
    $check->bind_param("ii", $user_id, $product_id);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        // Remove
        $stmt = $conn->prepare("DELETE FROM wishlist WHERE user_id = ? AND product_id = ?");
        $stmt->bind_param("ii", $user_id, $product_id);
        $stmt->execute();
        echo json_encode(['success' => true, 'status' => 'removed', 'message' => 'Removed from wishlist', 'count' => getWishlistCount($conn, $user_id)]);
    } else {
        // Add
        $stmt = $conn->prepare("INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $user_id, $product_id);
        $stmt->execute();
        echo json_encode(['success' => true, 'status' => 'added', 'message' => 'Added to wishlist', 'count' => getWishlistCount($conn, $user_id)]);
    }
} elseif ($action === 'count') {
    echo json_encode(['success' => true, 'count' => getWishlistCount($conn, $user_id)]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
}
