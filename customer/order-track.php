<?php
$page_title = 'Track Order';
include_once $_SERVER['DOCUMENT_ROOT'] . '/CoGroCart/includes/header.php';

if (!isLoggedIn()) {
    redirect(BASE_URL . 'customer/login.php');
}

$order_id = intval($_GET['order_id'] ?? 0);
$order = getOrder($conn, $order_id);

if (!$order || $order['user_id'] != $_SESSION['user_id']) {
    redirect(BASE_URL . 'customer/dashboard.php', 'Order not found', 'error');
}

$items = getOrderItems($conn, $order_id);
$status_steps = ['pending', 'accepted', 'preparing', 'out_for_delivery', 'delivered'];
$current_step = array_search($order['status'], $status_steps);
if($current_step === false && $order['status'] == 'cancelled') $current_step = -1;
?>

<style>
    .track-wrapper { max-width: 900px; margin: 4rem auto; }
    .status-timeline { display: flex; justify-content: space-between; position: relative; margin-bottom: 4rem; padding: 0 40px; }
    .status-timeline::before { content: ''; position: absolute; top: 25px; left: 60px; right: 60px; height: 4px; background: #e2e8f0; z-index: 1; }
    .timeline-progress { position: absolute; top: 25px; left: 60px; height: 4px; background: var(--primary); z-index: 2; transition: width 1s ease; }
    
    .status-step { position: relative; z-index: 3; text-align: center; width: 50px; }
    .step-icon { width: 50px; height: 50px; background: white; border: 4px solid #e2e8f0; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; color: #94a3b8; transition: all 0.4s; }
    .status-step.active .step-icon { border-color: var(--primary); color: var(--primary); box-shadow: 0 0 0 8px rgba(254, 189, 105, 0.2); }
    .status-step.completed .step-icon { background: var(--primary); border-color: var(--primary); color: #111; }
    
    .step-label { position: absolute; top: 60px; left: 50%; transform: translateX(-50%); white-space: nowrap; font-size: 0.8rem; font-weight: 700; color: #64748b; }
    .status-step.active .step-label { color: var(--secondary); }

    .order-details-card { background: white; border-radius: 12px; border: 1px solid var(--border); overflow: hidden; }
    .card-header { background: #f8fafc; padding: 20px 30px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; }
    .card-body { padding: 30px; }
    
    .item-row { display: flex; gap: 20px; align-items: center; padding: 15px 0; border-bottom: 1px solid #f1f5f9; }
    .item-row:last-child { border-bottom: none; }
    .item-img { width: 60px; height: 60px; border-radius: 8px; object-fit: cover; background: #f1f1f1; }
</style>

<div class="container">
    <div class="track-wrapper">
        <div style="margin-bottom: 3rem;">
            <a href="dashboard.php?tab=orders" style="text-decoration: none; color: var(--primary); font-weight: 700;"><i class="fas fa-arrow-left"></i> Back to Orders</a>
            <h1 style="margin-top: 1rem; font-size: 2.5rem;">Track Order <span style="color: var(--primary);">#<?php echo str_pad($order['id'], 5, '0', STR_PAD_LEFT); ?></span></h1>
        </div>

        <div class="status-timeline">
            <?php 
            $progress_width = ($current_step / (count($status_steps) - 1)) * 100;
            if($order['status'] == 'cancelled') $progress_width = 0;
            ?>
            <div class="timeline-progress" style="width: <?php echo $progress_width; ?>%"></div>
            
            <?php 
            $icons = ['clock', 'check-double', 'box-open', 'shipping-fast', 'box-check'];
            $labels = ['Placed', 'Accepted', 'Preparing', 'On the Way', 'Delivered'];
            
            foreach($status_steps as $i => $s): 
                $class = '';
                if($i < $current_step) $class = 'completed';
                elseif($i == $current_step) $class = 'active';
            ?>
            <div class="status-step <?php echo $class; ?>">
                <div class="step-icon"><i class="fas fa-<?php echo $icons[$i]; ?>"></i></div>
                <span class="step-label"><?php echo $labels[$i]; ?></span>
            </div>
            <?php endforeach; ?>
        </div>

        <?php if($order['status'] == 'cancelled'): ?>
            <div style="background: #fee2e2; color: #991b1b; padding: 20px; border-radius: 12px; margin-bottom: 2rem; text-align: center; border: 1px solid #fecaca;">
                <i class="fas fa-exclamation-circle"></i> This order has been cancelled.
            </div>
        <?php endif; ?>

        <div class="order-details-card">
            <div class="card-header">
                <div>
                    <span style="font-size: 0.8rem; color: #64748b; text-transform: uppercase; font-weight: 700; letter-spacing: 1px;">Summary</span>
                </div>
                <div style="text-align: right;">
                    <span style="font-weight: 800; font-size: 1.25rem; color: var(--secondary);"><?php echo formatCurrency($order['total_price'] + $order['delivery_charge']); ?></span>
                </div>
            </div>
            <div class="card-body">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px; margin-bottom: 30px;">
                    <div>
                        <h4 style="margin-bottom: 10px;">Delivery Address</h4>
                        <p style="color: #64748b; font-size: 0.95rem; line-height: 1.6;"><?php echo nl2br(htmlspecialchars($order['address'])); ?></p>
                    </div>
                    <div>
                        <h4 style="margin-bottom: 10px;">Payment Information</h4>
                        <p style="color: #64748b; font-size: 0.95rem;">Method: <strong>Cash on Delivery</strong></p>
                        <p style="color: #64748b; font-size: 0.95rem;">Order Date: <?php echo date('d M Y, h:i A', strtotime($order['created_at'])); ?></p>
                    </div>
                </div>

                <h4 style="margin-bottom: 15px; border-top: 1px solid #f1f5f9; padding-top: 30px;">Order Items</h4>
                <div class="items-list">
                    <?php foreach($items as $item): ?>
                    <div class="item-row">
                        <?php 
                        $img = (strpos($item['image'], 'http') === 0) ? $item['image'] : BASE_URL . 'assets/images/' . ($item['image'] ?: 'default.png');
                        ?>
                        <img src="<?php echo $img; ?>" class="item-img" alt="">
                        <div style="flex: 1;">
                            <h5 style="margin: 0;"><?php echo htmlspecialchars($item['name']); ?></h5>
                            <span style="font-size: 0.85rem; color: #64748b;">Qty: <?php echo $item['quantity']; ?> x <?php echo formatCurrency($item['price']); ?></span>
                        </div>
                        <span style="font-weight: 700;"><?php echo formatCurrency($item['quantity'] * $item['price']); ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Real-time Order Tracking Polling
    let lastOrderTime = '<?php echo $order["updated_at"] ?? date("Y-m-d H:i:s"); ?>';
    const orderId = <?php echo $order_id; ?>;
    
    setInterval(() => {
        fetch(`<?php echo BASE_URL; ?>api/check_updates.php?last_time=${encodeURIComponent(lastOrderTime)}&order_id=${orderId}`)
        .then(r => r.json())
        .then(data => {
            if (data.has_updates) {
                lastOrderTime = data.last_time;
                
                // Silently fetch and update the DOM
                fetch(location.href)
                .then(res => res.text())
                .then(html => {
                    let parser = new DOMParser();
                    let doc = parser.parseFromString(html, 'text/html');
                    let newContent = doc.querySelector('.track-wrapper').innerHTML;
                    if(newContent) {
                        document.querySelector('.track-wrapper').innerHTML = newContent;
                    }
                }).catch(err => console.error("DOM Refresh Error:", err));
            }
        }).catch(e => console.error("Tracking Polling error:", e));
    }, 5000); // Check every 5 seconds
</script>

<?php include_once $_SERVER['DOCUMENT_ROOT'] . '/CoGroCart/includes/footer.php'; ?>
