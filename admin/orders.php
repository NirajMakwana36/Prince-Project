<?php
$page_title = 'Orders';
include_once 'includes/header.php';

$action = $_GET['action'] ?? 'list';

// Handle Status Update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $order_id = intval($_POST['order_id']);
    $status = sanitize($_POST['status']);
    $conn->query("UPDATE orders SET status = '$status' WHERE id = $order_id");
    echo "<script>location.href='orders.php?view=$order_id&msg=updated';</script>";
}

// Handle Order Assignment
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['assign_delivery'])) {
    $order_id = intval($_POST['order_id']);
    $partner_id = intval($_POST['partner_id']);
    $conn->query("UPDATE orders SET delivery_partner_id = $partner_id, status='accepted' WHERE id = $order_id");
    echo "<script>location.href='orders.php?view=$order_id&msg=assigned';</script>";
}

$view_id = intval($_GET['view'] ?? 0);
$orders = $conn->query("SELECT o.*, u.name as customer FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC")->fetch_all(MYSQLI_ASSOC);

if ($view_id > 0) {
    $order = getOrder($conn, $view_id);
    $items = getOrderItems($conn, $view_id);
    $partners = $conn->query("SELECT u.id, u.name FROM users u JOIN delivery_partners dp ON u.id = dp.user_id WHERE dp.status = 'available' OR dp.status = 'offline'")->fetch_all(MYSQLI_ASSOC);
}
?>

<div class="animate__animated animate__fadeIn">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 3rem;">
        <div>
            <h1 style="font-size: 2.5rem;"><?php echo $view_id ? 'Order Details' : 'Orders'; ?></h1>
            <p style="color: #64748b;">Manage customer orders and delivery assignments.</p>
        </div>
        <?php if($view_id): ?>
        <a href="orders.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back to Orders</a>
        <?php endif; ?>
    </div>

    <?php if($view_id && $order): ?>
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2.5rem;">
        <div>
            <div class="admin-card">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 2rem; border-bottom: 2px solid #f1f5f9; padding-bottom: 1.5rem;">
                    <div>
                        <h2 style="font-size: 1.5rem; margin-bottom: 0.5rem;">Order #<?php echo str_pad($order['id'], 5, '0', STR_PAD_LEFT); ?></h2>
                        <p style="color: #64748b;"><?php echo formatDate($order['created_at']); ?></p>
                    </div>
                    <?php 
                    $status_colors = [
                        'pending' => ['#fef3c7', '#d97706'],
                        'accepted' => ['#e0e7ff', '#4f46e5'],
                        'preparing' => ['#fae8ff', '#d946ef'],
                        'out_for_delivery' => ['#dcfce7', '#16a34a'],
                        'delivered' => ['#d1fae5', '#059669'],
                        'cancelled' => ['#fee2e2', '#dc2626']
                    ];
                    $colors = $status_colors[$order['status']] ?? ['#f1f5f9', '#64748b'];
                    ?>
                    <span class="badge" style="background: <?php echo $colors[0]; ?>; color: <?php echo $colors[1]; ?>; font-size: 1rem; padding: 0.75rem 1.5rem;">
                        <?php echo ucfirst(str_replace('_', ' ', $order['status'])); ?>
                    </span>
                </div>

                <h3 style="margin-bottom: 1.5rem;">Items</h3>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Qty</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($items as $item): ?>
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 1rem;">
                                    <img src="../assets/images/<?php echo $item['image'] ?: 'default.png'; ?>" style="width: 40px; height: 40px; border-radius: 0.75rem; object-fit: cover;">
                                    <span style="font-weight: 600;"><?php echo htmlspecialchars($item['name']); ?></span>
                                </div>
                            </td>
                            <td><?php echo $item['quantity']; ?></td>
                            <td style="font-weight: 700;"><?php echo formatCurrency($item['price'] * $item['quantity']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div style="margin-top: 2rem; padding: 2rem; background: #f8fafc; border-radius: 1.5rem;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                        <span style="color: #64748b;">Subtotal</span>
                        <span style="font-weight: 600;"><?php echo formatCurrency($order['total_price']); ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                        <span style="color: #64748b;">Delivery Fee</span>
                        <span style="font-weight: 600;"><?php echo formatCurrency($order['delivery_charge']); ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; border-top: 2px solid #e2e8f0; margin-top: 1rem; padding-top: 1rem;">
                        <span style="font-weight: 800; font-size: 1.25rem;">Total</span>
                        <span style="font-weight: 800; font-size: 1.25rem; color: var(--primary);"><?php echo formatCurrency($order['total_price'] + $order['delivery_charge']); ?></span>
                    </div>
                </div>
            </div>
        </div>

        <div>
            <div class="admin-card" style="margin-bottom: 2rem;">
                <h3 style="margin-bottom: 1.5rem;">Actions</h3>
                <form method="POST">
                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                    <div class="form-group">
                        <label>Update Status</label>
                        <select name="status" class="form-control" style="margin-bottom: 1rem;">
                            <?php foreach($status_colors as $s => $c): ?>
                            <option value="<?php echo $s; ?>" <?php echo $order['status'] == $s ? 'selected' : ''; ?>><?php echo ucfirst(str_replace('_', ' ', $s)); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" name="update_status" class="btn btn-primary btn-block">Update Status</button>
                    </div>
                </form>

                <hr style="margin: 2rem 0; border: none; border-top: 2px solid #f1f5f9;">

                <form method="POST">
                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                    <div class="form-group">
                        <label>Assign Delivery</label>
                        <select name="partner_id" class="form-control" style="margin-bottom: 1rem;" required>
                            <option value="">Select Partner</option>
                            <?php foreach($partners as $p): ?>
                            <option value="<?php echo $p['id']; ?>" <?php echo $order['delivery_partner_id'] == $p['id'] ? 'selected' : ''; ?>><?php echo $p['name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" name="assign_delivery" class="btn btn-secondary btn-block">Assign Order</button>
                    </div>
                </form>
            </div>

            <div class="admin-card">
                <h3 style="margin-bottom: 1.5rem;">Customer Details</h3>
                <div style="margin-bottom: 1rem;">
                    <span style="display: block; font-size: 0.75rem; color: #64748b; text-transform: uppercase;">Name</span>
                    <span style="font-weight: 600;"><?php echo htmlspecialchars($order['customer_name']); ?></span>
                </div>
                <div style="margin-bottom: 1rem;">
                    <span style="display: block; font-size: 0.75rem; color: #64748b; text-transform: uppercase;">Phone</span>
                    <span style="font-weight: 600;"><?php echo htmlspecialchars($order['phone']); ?></span>
                </div>
                <div style="margin-bottom: 1rem;">
                    <span style="display: block; font-size: 0.75rem; color: #64748b; text-transform: uppercase;">Address</span>
                    <span style="font-weight: 600;"><?php echo htmlspecialchars($order['address']); ?></span>
                </div>
            </div>
        </div>
    </div>
    <?php else: ?>
    <div class="admin-card" style="padding: 0;">
        <table class="admin-table">
            <thead>
                <tr>
                    <th style="padding-left: 2.5rem;">Order</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th style="padding-right: 2.5rem;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($orders as $o): ?>
                <tr>
                    <td style="padding-left: 2.5rem;"><strong>#<?php echo str_pad($o['id'], 5, '0', STR_PAD_LEFT); ?></strong></td>
                    <td><?php echo htmlspecialchars($o['customer']); ?></td>
                    <td><?php echo date('M d, Y', strtotime($o['created_at'])); ?></td>
                    <td style="font-weight: 700;"><?php echo formatCurrency($o['total_price'] + $o['delivery_charge']); ?></td>
                    <td>
                        <?php 
                        $colors = $status_colors[$o['status']] ?? ['#f1f5f9', '#64748b'];
                        ?>
                        <span class="badge" style="background: <?php echo $colors[0]; ?>; color: <?php echo $colors[1]; ?>;">
                            <?php echo ucfirst(str_replace('_', ' ', $o['status'])); ?>
                        </span>
                    </td>
                    <td style="padding-right: 2.5rem;">
                        <a href="?view=<?php echo $o['id']; ?>" class="btn btn-secondary btn-sm">View Details</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<?php include_once 'includes/footer.php'; ?>
