<?php
header('Content-Type: application/json');
if (session_status() === PHP_SESSION_NONE) session_start();
include_once '../config/functions.php';

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = intval($_POST['product_id']);
    $rating = intval($_POST['rating']);
    $comment = sanitize($_POST['comment']);

    // verify purchase
    if (!hasPurchasedProduct($conn, $user_id, $product_id)) {
        echo json_encode(['success' => false, 'message' => 'Only customers who purchased this can leave a review.']);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO reviews (user_id, product_id, rating, comment) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiis", $user_id, $product_id, $rating, $comment);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Review submitted successfully!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to submit review.']);
    }
}
