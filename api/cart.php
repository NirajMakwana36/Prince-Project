<?php
ob_start();
session_start();
include_once $_SERVER['DOCUMENT_ROOT'] . '/CoGroCart/config/db_config.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/CoGroCart/config/functions.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Please login to continue']);
    exit;
}

$user_id = $_SESSION['user_id'];
$action = $_POST['action'] ?? $_GET['action'] ?? '';

// Support both JSON and Form Data
if (empty($action)) {
    $json = json_decode(file_get_contents('php://input'), true);
    if ($json) {
        $action = $json['action'] ?? '';
    }
}

switch ($action) {
    case 'add':
        $product_id = intval($_POST['product_id'] ?? $json['product_id'] ?? 0);
        $quantity = intval($_POST['quantity'] ?? $json['quantity'] ?? 1);

        if ($product_id <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid product']);
            exit;
        }

        $product = getProduct($conn, $product_id);
        if (!$product) {
            echo json_encode(['status' => 'error', 'message' => 'Product not found']);
            exit;
        }

        if ($product['stock'] < $quantity) {
            echo json_encode(['status' => 'error', 'message' => 'Only ' . $product['stock'] . ' units available']);
            exit;
        }

        // Check existing
        $stmt = $conn->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?");
        $stmt->bind_param("ii", $user_id, $product_id);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows > 0) {
            $row = $res->fetch_assoc();
            $new_qty = $row['quantity'] + $quantity;
            $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
            $stmt->bind_param("ii", $new_qty, $row['id']);
        } else {
            $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
            $stmt->bind_param("iii", $user_id, $product_id, $quantity);
        }

        if ($stmt->execute()) {
            echo json_encode([
                'status' => 'success', 
                'message' => 'Added to cart', 
                'cart_count' => getCartCount($conn, $user_id)
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to add to cart']);
        }
        break;

    case 'remove':
        $cart_id = intval($_POST['cart_id'] ?? $json['cart_id'] ?? 0);
        $stmt = $conn->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $cart_id, $user_id);
        if ($stmt->execute()) {
            echo json_encode([
                'status' => 'success', 
                'message' => 'Removed successfully',
                'cart_count' => getCartCount($conn, $user_id),
                'total' => formatCurrency(getCartTotal($conn, $user_id))
            ]);
        }
        break;

    case 'update':
        $cart_id = intval($_POST['cart_id'] ?? $json['cart_id'] ?? 0);
        $quantity = intval($_POST['quantity'] ?? $json['quantity'] ?? 1);
        
        if ($quantity <= 0) {
            // Delete if zero
            $stmt = $conn->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
            $stmt->bind_param("ii", $cart_id, $user_id);
        } else {
            $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?");
            $stmt->bind_param("iii", $quantity, $cart_id, $user_id);
        }

        if ($stmt->execute()) {
            echo json_encode([
                'status' => 'success', 
                'cart_count' => getCartCount($conn, $user_id),
                'total' => formatCurrency(getCartTotal($conn, $user_id))
            ]);
        }
        break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
        break;
}
?>
