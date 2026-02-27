<?php
$page_title = 'Secure Checkout';
include_once $_SERVER['DOCUMENT_ROOT'] . '/CoGroCart/includes/header.php';

// Check if logged in and is customer
if (!isLoggedIn()) {
    redirect(BASE_URL . 'customer/login.php', 'Please login to checkout', 'info');
}

$user_id = $_SESSION['user_id'];
$user = getUser($conn, $user_id);
$cart_items = getCartItems($conn, $user_id);
$cart_total = getCartTotal($conn, $user_id);
$delivery_charge = getDeliveryCharge($conn);
$grand_total = $cart_total + $delivery_charge;

if (empty($cart_items)) {
    redirect(BASE_URL . 'customer/cart.php', 'Your cart is empty', 'info');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['place_order'])) {
    $address = sanitize($_POST['address'] ?? '');
    $city = sanitize($_POST['city'] ?? '');
    $postal_code = sanitize($_POST['postal_code'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    
    if (empty($address) || empty($city) || empty($postal_code) || empty($phone)) {
        $error = 'All shipping fields are required!';
    } else {
        $conn->begin_transaction();
        try {
            // Create order
            $stmt = $conn->prepare("INSERT INTO orders (user_id, total_price, delivery_charge, address, city, postal_code, phone, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')");
            $stmt->bind_param("iddssss", $user_id, $cart_total, $delivery_charge, $address, $city, $postal_code, $phone);
            $stmt->execute();
            $order_id = $conn->insert_id;

            // Add order items
            $item_stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            foreach ($cart_items as $item) {
                $p_id = $item['product_id'];
                $qty = $item['quantity'];
                $price = getDiscountedPrice($item['price'], $item['discount']);
                $item_stmt->bind_param("iiid", $order_id, $p_id, $qty, $price);
                $item_stmt->execute();

                // Update stock
                $conn->query("UPDATE products SET stock = stock - $qty WHERE id = $p_id");
            }

            // Clear cart
            $conn->query("DELETE FROM cart WHERE user_id = $user_id");

            $conn->commit();
            redirect(BASE_URL . 'customer/order-success.php?order_id=' . $order_id, 'Order placed successfully!', 'success');
        } catch (Exception $e) {
            $conn->rollback();
            $error = 'Something went wrong. Please try again.';
        }
    }
}
?>

<style>
    .checkout-grid { display: grid; grid-template-columns: 1.5fr 1fr; gap: 3rem; margin: 4rem 0; }
    .checkout-card { background: white; border-radius: 2rem; padding: 2.5rem; border: 1px solid var(--border); box-shadow: var(--shadow-sm); }
    
    .form-group { margin-bottom: 1.5rem; }
    .form-group label { display: block; margin-bottom: 0.5rem; font-weight: 600; font-size: 0.9rem; }
    .form-control { width: 100%; padding: 0.85rem 1.25rem; border-radius: 1rem; border: 1px solid var(--border); transition: all 0.3s; font-family: inherit; }
    .form-control:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 4px rgba(46, 204, 113, 0.1); }

    .payment-opt { border: 2px solid var(--border); border-radius: 1.5rem; padding: 1.5rem; display: flex; align-items: center; gap: 1rem; cursor: pointer; transition: var(--transition); margin-bottom: 1rem; }
    .payment-opt.active { border-color: var(--primary); background: var(--primary-light); }
    
    .summary-item { display: flex; justify-content: space-between; align-items: center; padding: 1rem 0; border-bottom: 1px solid var(--border); }
    .summary-item:last-of-type { border-bottom: none; }

    @media (max-width: 900px) {
        .checkout-grid { grid-template-columns: 1fr; }
    }
</style>

<div class="container animate__animated animate__fadeIn">
    <div style="margin-top: 4rem; text-align: center;">
        <h1 style="font-size: 2.5rem;">Secure <span style="color: var(--primary);">Checkout</span></h1>
        <p style="color: var(--text-muted);">Almost there! Just a few more details to confirm your order.</p>
    </div>

    <?php if($error): ?>
        <div class="alert alert-danger" style="margin-top: 2rem;"><?php echo $error; ?></div>
    <?php endif; ?>

    <div class="checkout-grid">
        <div class="checkout-card">
            <form method="POST">
                <h2 style="font-family: 'Outfit', sans-serif; margin-bottom: 2rem;"><i class="fas fa-truck" style="color: var(--primary); margin-right: 0.75rem;"></i> Shipping Details</h2>
                
                <div class="form-group">
                    <label>Full Address</label>
                    <textarea name="address" class="form-control" rows="3" required placeholder="House No, Street, Landmark..."><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                    <div class="form-group">
                        <label>City</label>
                        <input type="text" name="city" class="form-control" value="<?php echo htmlspecialchars($user['city'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Postal Code</label>
                        <input type="text" name="postal_code" class="form-control" value="<?php echo htmlspecialchars($user['postal_code'] ?? ''); ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="tel" name="phone" class="form-control" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" required>
                </div>

                <h2 style="font-family: 'Outfit', sans-serif; margin: 3rem 0 1.5rem;"><i class="fas fa-wallet" style="color: var(--primary); margin-right: 0.75rem;"></i> Payment Method</h2>
                
                <div class="payment-opt active">
                    <input type="radio" name="payment_method" value="COD" checked style="width: 20px; height: 20px; accent-color: var(--primary);">
                    <div>
                        <div style="font-weight: 700;">Cash on Delivery</div>
                        <div style="font-size: 0.85rem; color: var(--text-muted);">Pay when you receive your groceries</div>
                    </div>
                    <i class="fas fa-money-bill-wave" style="margin-left: auto; font-size: 1.5rem; color: #059669;"></i>
                </div>

                <div class="payment-opt" style="opacity: 0.6; cursor: not-allowed;">
                    <input type="radio" disabled style="width: 20px; height: 20px;">
                    <div>
                        <div style="font-weight: 700;">Credit / Debit Card</div>
                        <div style="font-size: 0.85rem; color: var(--text-muted);">Coming Soon</div>
                    </div>
                    <i class="fas fa-credit-card" style="margin-left: auto; font-size: 1.5rem;"></i>
                </div>

                <button type="submit" name="place_order" class="btn btn-primary btn-lg btn-block" style="margin-top: 3rem; justify-content: center; height: 60px; font-size: 1.25rem;">
                    Place My Order <i class="fas fa-check-circle"></i>
                </button>
            </form>
        </div>

        <aside>
            <div class="checkout-card">
                <h2 style="font-family: 'Outfit', sans-serif; margin-bottom: 2rem;">Order Summary</h2>
                
                <div style="max-height: 300px; overflow-y: auto; margin-bottom: 2rem; padding-right: 1rem;">
                    <?php foreach ($cart_items as $item): ?>
                    <div class="summary-item">
                        <div>
                            <div style="font-weight: 700;"><?php echo htmlspecialchars($item['name']); ?></div>
                            <div style="font-size: 0.8rem; color: var(--text-muted);">Qty: <?php echo $item['quantity']; ?></div>
                        </div>
                        <div style="font-weight: 600;">
                            <?php echo formatCurrency(getDiscountedPrice($item['price'], $item['discount']) * $item['quantity']); ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div class="summary-row" style="margin-bottom: 1rem;">
                    <span style="color: var(--text-muted);">Items Total</span>
                    <span><?php echo formatCurrency($cart_total); ?></span>
                </div>
                <div class="summary-row" style="margin-bottom: 2rem;">
                    <span style="color: var(--text-muted);">Delivery Charge</span>
                    <span><?php echo formatCurrency($delivery_charge); ?></span>
                </div>

                <div style="background: var(--bg); padding: 1.5rem; border-radius: 1.5rem; display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-weight: 700;">Grand Total</span>
                    <span style="font-family: 'Outfit', sans-serif; font-size: 1.75rem; font-weight: 800; color: var(--primary);"><?php echo formatCurrency($grand_total); ?></span>
                </div>

                <div style="margin-top: 2rem; padding: 1.5rem; border-radius: 1.5rem; border: 2px dashed var(--border); text-align: center;">
                    <p style="font-size: 0.85rem; color: var(--text-muted);"><i class="fas fa-clock"></i> Est. Delivery: <strong>30-45 Mins</strong></p>
                </div>
            </div>
        </aside>
    </div>
</div>

<?php include_once $_SERVER['DOCUMENT_ROOT'] . '/CoGroCart/includes/footer.php'; ?>
