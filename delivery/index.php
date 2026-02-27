<?php
$page_title = 'Partner Ops';
if (session_status() === PHP_SESSION_NONE) session_start();
include_once $_SERVER['DOCUMENT_ROOT'] . '/CoGroCart/config/functions.php';

// Strict Role Check
if (!isDeliveryPartner()) {
    header("Location: " . BASE_URL . "delivery/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Handle Status Update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $order_id = intval($_POST['order_id']);
    $status = sanitize($_POST['status']);
    
    // Validate that order belongs to this partner
    $check = $conn->query("SELECT id, user_id FROM orders WHERE id = $order_id AND delivery_partner_id = $user_id");
    if ($check->num_rows > 0) {
        $order_data = $check->fetch_assoc();
        $conn->query("UPDATE orders SET status = '$status' WHERE id = $order_id");
        
        // Notify Customer
        $cust_id = $order_data['user_id'];
        $clean_status = ucfirst(str_replace('_', ' ', $status));
        $cust_msg = "Your order #" . str_pad($order_id, 5, '0', STR_PAD_LEFT) . " status is now: " . $clean_status;
        $conn->query("INSERT INTO notifications (user_id, role, title, message, link) VALUES ($cust_id, 'customer', 'Order Update', '$cust_msg', 'order-track.php?order_id=$order_id')");
        
        // Notify Admin
        $admin_msg = "Order #" . str_pad($order_id, 5, '0', STR_PAD_LEFT) . " status updated to $clean_status by delivery partner.";
        $conn->query("INSERT INTO notifications (role, title, message, link) VALUES ('admin', 'Delivery Update', '$admin_msg', 'orders.php?view=$order_id')");

        $msg = "Order status updated to " . str_replace('_', ' ', $status);
        redirect("index.php", $msg, "success");
    }
}

// Handle Order Acceptance
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['accept_order'])) {
    $order_id = intval($_POST['order_id']);
    // Ensure order is still available
    $check = $conn->query("SELECT id, user_id FROM orders WHERE id = $order_id AND (delivery_partner_id IS NULL OR delivery_partner_id = 0)");
    if ($check->num_rows > 0) {
        $order_data = $check->fetch_assoc();
        $conn->query("UPDATE orders SET delivery_partner_id = $user_id, status = 'accepted' WHERE id = $order_id");
        
        // Notify Customer
        $cust_id = $order_data['user_id'];
        $cust_msg = "Your order #" . str_pad($order_id, 5, '0', STR_PAD_LEFT) . " has been accepted by a delivery partner and is being processed.";
        $conn->query("INSERT INTO notifications (user_id, role, title, message, link) VALUES ($cust_id, 'customer', 'Order Accepted', '$cust_msg', 'order-track.php?order_id=$order_id')");

        redirect("index.php", "Order accepted successfully!", "success");
    }
}

// Stats & Data
$assigned = $conn->query("SELECT o.*, u.name as customer_name, u.phone as customer_phone FROM orders o JOIN users u ON o.user_id = u.id WHERE o.delivery_partner_id = $user_id AND o.status NOT IN ('delivered', 'cancelled') ORDER BY o.created_at DESC")->fetch_all(MYSQLI_ASSOC);

$available = $conn->query("SELECT o.*, u.name as customer_name, u.city FROM orders o JOIN users u ON o.user_id = u.id WHERE (o.status = 'pending' OR o.status = 'accepted') AND (o.delivery_partner_id IS NULL OR o.delivery_partner_id = 0) ORDER BY o.created_at DESC LIMIT 15")->fetch_all(MYSQLI_ASSOC);

// Earnings Calculation (Assume flat 50 per delivery for now or use delivery_charge)
$earnings_res = $conn->query("SELECT SUM(delivery_charge) as total FROM orders WHERE delivery_partner_id = $user_id AND status = 'delivered'")->fetch_assoc();
$total_earnings = $earnings_res['total'] ?? 0;
$delivered_count = $conn->query("SELECT COUNT(*) as count FROM orders WHERE delivery_partner_id = $user_id AND status = 'delivered'")->fetch_assoc()['count'];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Partner Ops - CoGroCart</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --p: #fbbf24; --s: #0f172a; --bg: #f8fafc; --white: #ffffff; }
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--s); }
        
        .ops-nav { background: var(--s); color: white; padding: 1rem 2rem; display: flex; justify-content: space-between; align-items: center; position: sticky; top: 0; z-index: 100; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
        .ops-logo { font-family: 'Outfit'; font-size: 1.5rem; display: flex; align-items: center; gap: 0.5rem; }
        .ops-logo i { color: var(--p); }
        
        .ops-container { max-width: 1200px; margin: 3rem auto; padding: 0 2rem; }
        
        .status-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 2rem; margin-bottom: 4rem; }
        .stat-card { background: var(--white); padding: 2.5rem; border-radius: 2rem; border: 1px solid #e2e8f0; display: flex; flex-direction: column; gap: 0.5rem; }
        .stat-val { font-family: 'Outfit'; font-size: 2.5rem; font-weight: 800; color: var(--s); }
        .stat-lab { color: #64748b; font-weight: 600; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px; }

        .section-header { margin-bottom: 2rem; display: flex; align-items: center; gap: 1rem; }
        .section-header h2 { font-family: 'Outfit'; font-size: 1.75rem; }
        .live-indicator { width: 12px; height: 12px; background: #10b981; border-radius: 50%; position: relative; }
        .live-indicator::after { content:''; position: absolute; width:100%; height:100%; background: inherit; border-radius: inherit; animation: pulse 2s infinite; opacity: 0.5; }
        @keyframes pulse { 0% { transform: scale(1); opacity: 0.5; } 100% { transform: scale(3); opacity: 0; } }

        .order-card { background: var(--white); border-radius: 2rem; border: 1px solid #e2e8f0; padding: 2.5rem; margin-bottom: 2rem; transition: 0.3s; }
        .order-card:hover { border-color: var(--p); transform: translateY(-3px); box-shadow: 0 20px 25px -5px rgba(0,0,0,0.05); }
        
        .card-top { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 2rem; }
        .order-badge { background: #fef3c7; color: #92400e; padding: 0.5rem 1rem; border-radius: 1rem; font-size: 0.75rem; font-weight: 800; }
        
        .info-row { display: grid; grid-template-columns: repeat(3, 1fr); gap: 2rem; margin-bottom: 2rem; background: #f8fafc; padding: 1.5rem; border-radius: 1.5rem; }
        .info-box span:first-child { display: block; font-size: 0.7rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; margin-bottom: 0.5rem; }
        .info-box span:last-child { font-weight: 700; color: var(--s); }

        .btn-group { display: flex; gap: 1rem; }
        .btn-op { flex: 1; padding: 1rem; border-radius: 1rem; border: none; font-weight: 700; cursor: pointer; transition: 0.2s; font-family: inherit; }
        .btn-op:disabled { opacity: 0.5; cursor: not-allowed; }
        
        .btn-p { background: var(--p); color: #111; }
        .btn-p:hover { background: #f59e0b; }
        .btn-outline { background: #f1f5f9; color: var(--s); border: 1px solid #e2e8f0; }
        .btn-outline:hover { background: #e2e8f0; }
        
        .pickup-table { width: 100%; background: white; border-radius: 2rem; overflow: hidden; border: 1px solid #e2e8f0; }
        .pickup-table th { text-align: left; padding: 1.5rem; background: #f8fafc; color: #64748b; font-size: 0.75rem; text-transform: uppercase; }
        .pickup-table td { padding: 1.5rem; border-top: 1px solid #f1f5f9; }

        .toast { position: fixed; bottom: 2rem; right: 2rem; background: var(--s); color: white; padding: 1.5rem 2.5rem; border-radius: 1.5rem; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.2); z-index: 1000; display: flex; align-items: center; gap: 1rem; font-weight: 600; }
    </style>
</head>
<body>
    <nav class="ops-nav">
        <div class="ops-logo"><i class="fas fa-shopping-basket"></i> CoGroCart <span>Partner</span></div>
        <div style="display: flex; align-items: center; gap: 2rem;">
            <?php 
                $uid = $_SESSION['user_id'];
                $unread = $conn->query("SELECT COUNT(*) as c FROM notifications WHERE role = 'delivery' AND user_id = $uid AND is_read = 0")->fetch_assoc()['c'] ?? 0;
            ?>
            <a href="notifications.php" style="color: var(--s); font-size: 1.5rem; position: relative;">
                <i class="fas fa-bell"></i>
                <?php if($unread > 0): ?>
                    <span style="position: absolute; top: -8px; right: -8px; background: #ef4444; color: white; width: 20px; height: 20px; border-radius: 50%; font-size: 0.7rem; display: flex; align-items: center; justify-content: center; font-weight: 800; border: 2px solid white;"><?php echo $unread; ?></span>
                <?php endif; ?>
            </a>
            <div style="text-align: right;">
                <div style="font-weight: 800;"><?php echo htmlspecialchars($_SESSION['user_name']); ?></div>
                <div style="font-size: 0.75rem; color: #94a3b8;">Active Session</div>
            </div>
            <a href="logout.php" style="color: #f87171; font-size: 1.5rem;"><i class="fas fa-power-off"></i></a>
        </div>
    </nav>

    <div class="ops-container">
        <div class="status-grid">
            <div class="stat-card">
                <span class="stat-lab">Total Earnings</span>
                <span class="stat-val"><?php echo formatCurrency($total_earnings); ?></span>
            </div>
            <div class="stat-card">
                <span class="stat-lab">Delivered Orders</span>
                <span class="stat-val"><?php echo $delivered_count; ?></span>
            </div>
            <div class="stat-card">
                <span class="stat-lab">Active Tasks</span>
                <span class="stat-val"><?php echo count($assigned); ?></span>
            </div>
        </div>

        <div class="section-header">
            <div class="live-indicator"></div>
            <h2>Active Assignments</h2>
        </div>

        <?php if(empty($assigned)): ?>
            <div style="padding: 4rem; text-align: center; background: white; border-radius: 2rem; border: 2px dashed #e2e8f0; margin-bottom: 4rem;">
                <p style="color: #94a3b8; font-weight: 600;">No active tasks. Check pickup pool below.</p>
            </div>
        <?php else: ?>
            <?php foreach($assigned as $o): ?>
                <div class="order-card">
                    <div class="card-top">
                        <div>
                            <h3 style="font-family: 'Outfit'; font-size: 1.25rem;">Order #<?php echo str_pad($o['id'], 5, '0', STR_PAD_LEFT); ?></h3>
                            <p style="color: #64748b; font-size: 0.85rem; margin-top: 0.25rem;"><?php echo formatDate($o['created_at']); ?></p>
                        </div>
                        <span class="order-badge"><?php echo strtoupper(str_replace('_', ' ', $o['status'])); ?></span>
                    </div>

                    <div class="info-row">
                        <div class="info-box">
                            <span>Customer</span>
                            <span><?php echo htmlspecialchars($o['customer_name']); ?></span>
                        </div>
                        <div class="info-box">
                            <span>Phone</span>
                            <span><a href="tel:<?php echo $o['customer_phone']; ?>" style="color: var(--s);"><?php echo htmlspecialchars($o['customer_phone']); ?></a></span>
                        </div>
                        <div class="info-box">
                            <span>Address</span>
                            <span><?php echo htmlspecialchars($o['address']); ?></span>
                        </div>
                    </div>

                    <form method="POST" class="btn-group">
                        <input type="hidden" name="order_id" value="<?php echo $o['id']; ?>">
                        <input type="hidden" name="status" id="status_<?php echo $o['id']; ?>">
                        
                        <?php if($o['status'] == 'accepted'): ?>
                            <button type="submit" name="update_status" onclick="document.getElementById('status_<?php echo $o['id']; ?>').value='preparing'" class="btn-op btn-outline">Start Preparing</button>
                        <?php endif; ?>

                        <?php if($o['status'] == 'preparing'): ?>
                            <button type="submit" name="update_status" onclick="document.getElementById('status_<?php echo $o['id']; ?>').value='out_for_delivery'" class="btn-op btn-outline">Out for Delivery</button>
                        <?php endif; ?>

                        <?php if($o['status'] == 'out_for_delivery'): ?>
                            <button type="submit" name="update_status" onclick="document.getElementById('status_<?php echo $o['id']; ?>').value='delivered'" class="btn-op btn-p">Confirm Delivery</button>
                        <?php endif; ?>

                        <button type="submit" name="update_status" onclick="return confirm('Release order?') && (document.getElementById('status_<?php echo $o['id']; ?>').value='pending')" class="btn-op btn-outline" style="flex: 0 0 100px; color: #f87171;">Drop</button>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <div class="section-header" style="margin-top: 5rem;">
            <i class="fas fa-layer-group" style="color: var(--p); font-size: 1.5rem;"></i>
            <h2>Pickup Pool</h2>
        </div>

        <table class="pickup-table">
            <thead>
                <tr>
                    <th>Order</th>
                    <th>Customer</th>
                    <th>City/Locality</th>
                    <th>Earning</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($available)): ?>
                    <tr><td colspan="5" style="text-align: center; padding: 3rem; color: #94a3b8;">No available orders at the moment.</td></tr>
                <?php else: ?>
                    <?php foreach($available as $a): ?>
                        <tr>
                            <td style="font-weight: 700;">#<?php echo str_pad($a['id'], 5, '0', STR_PAD_LEFT); ?></td>
                            <td><?php echo htmlspecialchars($a['customer_name']); ?></td>
                            <td><i class="fas fa-map-marker-alt" style="color: var(--p);"></i> <?php echo htmlspecialchars($a['city']); ?></td>
                            <td style="font-weight: 800; color: #10b981;"><?php echo formatCurrency($a['delivery_charge']); ?></td>
                            <td>
                                <form method="POST">
                                    <input type="hidden" name="order_id" value="<?php echo $a['id']; ?>">
                                    <button type="submit" name="accept_order" class="btn-op btn-p" style="padding: 0.6rem 1.2rem; border-radius: 0.75rem; font-size: 0.85rem;">Accept</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php 
    $flash = getFlashMessage();
    if($flash): ?>
        <div class="toast animate__animated animate__fadeInUp">
            <i class="fas fa-check-circle" style="color: #10b981;"></i>
            <?php echo $flash['message']; ?>
        </div>
        <script>
            setTimeout(() => { document.querySelector('.toast').style.display = 'none'; }, 4000);
        </script>
    <?php endif; ?>

    <script>
        // Real-time Delivery Ops Polling
        let lastOrderTime = '<?php 
            $oq = "SELECT MAX(updated_at) as t FROM orders WHERE (delivery_partner_id = $user_id) OR (delivery_partner_id IS NULL OR delivery_partner_id = 0)";
            $nq = "SELECT MAX(created_at) as t FROM notifications WHERE role = 'delivery' AND user_id = $user_id";
            $ot = $conn->query($oq)->fetch_assoc()["t"] ?? '2000-01-01 00:00:00'; 
            $nt = $conn->query($nq)->fetch_assoc()["t"] ?? '2000-01-01 00:00:00'; 
            echo max($ot, $nt) == '2000-01-01 00:00:00' ? date("Y-m-d H:i:s") : max($ot, $nt);
        ?>';
        
        setInterval(() => {
            fetch('<?php echo BASE_URL; ?>api/check_updates.php?last_time=' + encodeURIComponent(lastOrderTime))
            .then(r => r.json())
            .then(data => {
                if (data.has_updates) {
                    lastOrderTime = data.last_time;
                    // Silently fetch the updated page and replace the content area
                    fetch(location.href)
                    .then(res => res.text())
                    .then(html => {
                        let parser = new DOMParser();
                        let doc = parser.parseFromString(html, 'text/html');
                        
                        let newContent = doc.querySelector('.ops-container').innerHTML;
                        if(newContent) document.querySelector('.ops-container').innerHTML = newContent;

                        let newNav = doc.querySelector('.ops-nav').innerHTML;
                        if(newNav) document.querySelector('.ops-nav').innerHTML = newNav;
                    }).catch(err => console.error(err));
                }
            }).catch(e => console.error("Real-time ops error:", e));
        }, 5000);
    </script>
</body>
</html>
