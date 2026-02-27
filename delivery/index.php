<?php
$page_title = 'Delivery Dashboard';
if (session_status() === PHP_SESSION_NONE) session_start();
include_once $_SERVER['DOCUMENT_ROOT'] . '/CoGroCart/config/functions.php';

// Check delivery role
if (!isDeliveryPartner()) {
    header("Location: " . BASE_URL . "customer/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Handle Status Update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $order_id = intval($_POST['order_id']);
    $status = sanitize($_POST['status']);
    $conn->query("UPDATE orders SET status = '$status' WHERE id = $order_id AND delivery_partner_id = $user_id");
    header("Location: index.php?msg=updated");
    exit;
}

// Handle Order Acceptance
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['accept_order'])) {
    $order_id = intval($_POST['order_id']);
    $conn->query("UPDATE orders SET delivery_partner_id = $user_id, status = 'accepted' WHERE id = $order_id");
    header("Location: index.php?msg=accepted");
    exit;
}

// Get assigned orders
$assigned = $conn->query("SELECT o.*, u.name as customer, u.phone FROM orders o JOIN users u ON o.user_id = u.id WHERE o.delivery_partner_id = $user_id AND o.status != 'delivered' AND o.status != 'cancelled' ORDER BY o.created_at DESC")->fetch_all(MYSQLI_ASSOC);

// Get available orders
$available = $conn->query("SELECT o.*, u.name as customer, u.city FROM orders o JOIN users u ON o.user_id = u.id WHERE o.status = 'pending' AND o.delivery_partner_id IS NULL ORDER BY o.created_at DESC LIMIT 10")->fetch_all(MYSQLI_ASSOC);

// Stats
$delivered_count = $conn->query("SELECT COUNT(*) as count FROM orders WHERE delivery_partner_id = $user_id AND status = 'delivered'")->fetch_assoc()['count'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery Dashboard - CoGroCart</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
    <style>
        body { background: #f0f9ff; color: #0c4a6e; }
        .delivery-wrapper { max-width: 1000px; margin: 4rem auto; padding: 0 2rem; }
        .delivery-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 3rem; background: white; padding: 2.5rem; border-radius: 2.5rem; box-shadow: var(--shadow-sm); border: 1px solid #bae6fd; }
        .stat-badge { background: #e0f2fe; color: #0369a1; padding: 0.75rem 1.5rem; border-radius: 1.5rem; font-weight: 700; display: flex; align-items: center; gap: 0.5rem; }
        .order-card { background: white; border-radius: 2rem; padding: 2rem; margin-bottom: 2rem; border: 1px solid #e0f2fe; box-shadow: var(--shadow-sm); transition: 0.3s; }
        .order-card:hover { transform: translateY(-5px); border-color: #7dd3fc; }
        .status-btn { padding: 0.75rem 1.25rem; border-radius: 1rem; border: none; font-weight: 700; cursor: pointer; transition: 0.3s; }
        .status-btn.preparing { background: #fae8ff; color: #a21caf; }
        .status-btn.out { background: #dcfce7; color: #15803d; }
        .status-btn.delivered { background: #0ea5e9; color: white; width: 100%; }
    </style>
</head>
<body>
    <div class="delivery-wrapper">
        <div class="delivery-header animate__animated animate__fadeInDown">
            <div>
                <h1 style="font-size: 2rem; font-family: 'Outfit';">Delivery <span style="color: #0ea5e9;">Partner</span></h1>
                <p style="color: #64748b;">Welcome back, <?php echo $_SESSION['user_name']; ?></p>
            </div>
            <div style="display: flex; gap: 1rem;">
                <div class="stat-badge"><i class="fas fa-check-circle"></i> <?php echo $delivered_count; ?> Delivered</div>
                <a href="<?php echo BASE_URL; ?>customer/logout.php" class="btn btn-secondary"><i class="fas fa-sign-out-alt"></i></a>
            </div>
        </div>

        <h3 style="margin-bottom: 2rem; display: flex; align-items: center; gap: 0.75rem;"><i class="fas fa-route text-primary"></i> Current Active Deliveries</h3>
        
        <?php if(empty($assigned)): ?>
            <div style="background: white; padding: 4rem; text-align: center; border-radius: 2.5rem; color: #64748b; border: 2px dashed #bae6fd;">
                <i class="fas fa-box-open" style="font-size: 3rem; margin-bottom: 1.5rem; opacity: 0.5;"></i>
                <p>No active deliveries. Pick up an order below!</p>
            </div>
        <?php else: ?>
            <div class="animate__animated animate__fadeIn">
                <?php foreach($assigned as $o): ?>
                <div class="order-card">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.5rem;">
                        <div>
                            <span style="font-weight: 800; font-size: 1.25rem; display: block; margin-bottom: 0.25rem;">Order #<?php echo str_pad($o['id'], 5, '0', STR_PAD_LEFT); ?></span>
                            <span style="color: #64748b; font-size: 0.85rem;"><i class="fas fa-clock"></i> <?php echo formatDate($o['created_at']); ?></span>
                        </div>
                        <span class="badge" style="background: #e0f2fe; color: #0369a1;"><?php echo ucfirst(str_replace('_', ' ', $o['status'])); ?></span>
                    </div>
                    
                    <div style="background: #f8fafc; padding: 1.5rem; border-radius: 1.5rem; margin-bottom: 1.5rem;">
                        <div style="margin-bottom: 1rem;">
                            <span style="display: block; font-size: 0.75rem; color: #64748b; text-transform: uppercase; font-weight: 700;">CUSTOMER</span>
                            <span style="font-weight: 700;"><?php echo htmlspecialchars($o['customer']); ?></span>
                        </div>
                        <div style="margin-bottom: 1rem;">
                            <span style="display: block; font-size: 0.75rem; color: #64748b; text-transform: uppercase; font-weight: 700;">PHONE</span>
                            <a href="tel:<?php echo $o['phone']; ?>" style="font-weight: 700; color: #0ea5e9; text-decoration: none;"><?php echo htmlspecialchars($o['phone']); ?></a>
                        </div>
                        <div>
                            <span style="display: block; font-size: 0.75rem; color: #64748b; text-transform: uppercase; font-weight: 700;">ADDRESS</span>
                            <span style="font-weight: 700;"><?php echo htmlspecialchars($o['address']); ?>, <?php echo htmlspecialchars($o['city']); ?></span>
                        </div>
                    </div>

                    <form method="POST" style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem;">
                        <input type="hidden" name="order_id" value="<?php echo $o['id']; ?>">
                        <button type="submit" name="update_status" value="preparing" class="status-btn preparing">Preparing</button>
                        <button type="submit" name="update_status" value="out_for_delivery" class="status-btn out">Out for Delivery</button>
                        <button type="submit" name="update_status" value="delivered" class="status-btn delivered" onclick="return confirm('Confirm delivery?')">Delivered</button>
                        <input type="hidden" name="status" id="status_<?php echo $o['id']; ?>">
                    </form>
                    <script>
                        document.querySelectorAll('.status-btn').forEach(btn => {
                            btn.addEventListener('click', function() {
                                this.parentElement.querySelector('input[name="status"]').value = this.value;
                            });
                        });
                    </script>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <h3 style="margin: 4rem 0 2rem; display: flex; align-items: center; gap: 0.75rem;"><i class="fas fa-hand-holding-heart text-primary"></i> Available for Pickup</h3>
        
        <div class="grid-auto">
            <?php foreach($available as $o): ?>
            <div class="order-card" style="margin-bottom: 0;">
                <div style="margin-bottom: 1rem;">
                    <span style="font-weight: 800;">Order #<?php echo str_pad($o['id'], 5, '0', STR_PAD_LEFT); ?></span>
                    <p style="color: #64748b; font-size: 0.85rem;"><?php echo $o['city']; ?></p>
                </div>
                <div style="font-weight: 800; font-size: 1.25rem; color: var(--primary); margin-bottom: 1.5rem;"><?php echo formatCurrency($o['total_price']); ?></div>
                <form method="POST">
                    <input type="hidden" name="order_id" value="<?php echo $o['id']; ?>">
                    <button type="submit" name="accept_order" class="btn btn-primary btn-block">Accept Pickup</button>
                </form>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
