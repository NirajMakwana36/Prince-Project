<?php
$page_title = 'Order Confirmed! 🎉';
include_once $_SERVER['DOCUMENT_ROOT'] . '/CoGroCart/includes/header.php';

if (!isLoggedIn()) redirect(BASE_URL . 'customer/login.php');

$order_id = intval($_GET['order_id'] ?? 0);
$order = getOrder($conn, $order_id);

if (!$order || $order['user_id'] != $_SESSION['user_id']) {
    redirect(BASE_URL . 'customer/dashboard.php');
}

$order_items = getOrderItems($conn, $order_id);
?>

<div class="container animate__animated animate__zoomIn" style="max-width: 800px; margin: 6rem auto; text-align: center;">
    <div style="width: 120px; height: 120px; background: var(--primary-light); color: var(--primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 4rem; margin: 0 auto 2rem;">
        <i class="fas fa-check-circle"></i>
    </div>
    
    <h1 style="font-size: 3rem; margin-bottom: 1rem;">Order <span style="color: var(--primary);">Success!</span></h1>
    <p style="color: var(--text-muted); font-size: 1.15rem; margin-bottom: 3rem;">Thank you for shopping with CoGroCart. Your order <strong>#<?php echo str_pad($order_id, 5, '0', STR_PAD_LEFT); ?></strong> has been received and is being processed.</p>

    <div style="background: white; border-radius: 2rem; padding: 3rem; border: 1px solid var(--border); box-shadow: var(--shadow-sm); text-align: left;">
        <h3 style="font-family: 'Outfit', sans-serif; margin-bottom: 1.5rem; border-bottom: 2px solid var(--bg); padding-bottom: 1rem;">Order Details</h3>
        
        <div style="margin-bottom: 2rem;">
            <?php foreach($order_items as $item): ?>
            <div style="display: flex; justify-content: space-between; margin-bottom: 1rem;">
                <span style="font-weight: 500;"><?php echo htmlspecialchars($item['name']); ?> × <?php echo $item['quantity']; ?></span>
                <span style="font-weight: 700;"><?php echo formatCurrency($item['price'] * $item['quantity']); ?></span>
            </div>
            <?php endforeach; ?>
        </div>

        <div style="background: var(--bg); padding: 1.5rem; border-radius: 1.5rem;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                <span style="color: var(--text-muted);">Amount Paid</span>
                <span style="font-weight: 800; font-size: 1.25rem; color: var(--primary);"><?php echo formatCurrency($order['total_price'] + $order['delivery_charge']); ?></span>
            </div>
            <div style="display: flex; justify-content: space-between;">
                <span style="color: var(--text-muted);">Payment Mode</span>
                <span style="font-weight: 600;">Cash on Delivery</span>
            </div>
        </div>

        <div style="margin-top: 2rem;">
            <h4 style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 0.5rem; text-transform: uppercase; letter-spacing: 0.05em;">DELIVERY ADDRESS</h4>
            <p style="font-weight: 600; line-height: 1.5;"><?php echo htmlspecialchars($order['address']); ?>, <?php echo htmlspecialchars($order['city']); ?> - <?php echo htmlspecialchars($order['postal_code']); ?></p>
        </div>
    </div>

    <div style="margin-top: 4rem; display: flex; gap: 1.5rem; justify-content: center;">
        <a href="dashboard.php?tab=orders" class="btn btn-primary btn-lg">Track My Order</a>
        <a href="products.php" class="btn btn-secondary btn-lg">Continue Shopping</a>
    </div>

    <div style="margin-top: 5rem; color: var(--text-muted); font-size: 0.9rem;">
        <p>Need help? Contact us at <a href="mailto:support@cogrocart.com" style="color: var(--primary); text-decoration: none; font-weight: 700;">support@cogrocart.com</a></p>
    </div>
</div>

<?php include_once $_SERVER['DOCUMENT_ROOT'] . '/CoGroCart/includes/footer.php'; ?>
