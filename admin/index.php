<?php
$page_title = 'Dashboard';
include_once 'includes/header.php';

// Fetch Statistics
$total_orders = $conn->query("SELECT COUNT(*) as count FROM orders")->fetch_assoc()['count'];
$total_revenue = $conn->query("SELECT SUM(total_price) as sum FROM orders WHERE status = 'delivered'")->fetch_assoc()['sum'] ?? 0;
$total_products = $conn->query("SELECT COUNT(*) as count FROM products")->fetch_assoc()['count'];
$total_customers = $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'customer'")->fetch_assoc()['count'];

$recent_orders = $conn->query("SELECT o.*, u.name as customer FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC LIMIT 5")->fetch_all(MYSQLI_ASSOC);
?>

<div class="animate__animated animate__fadeIn">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 3rem;">
        <div>
            <h1 style="font-size: 2.5rem; margin-bottom: 0.5rem;">Welcome, <span style="color: var(--primary);">Admin</span></h1>
            <p style="color: #64748b;">Here's what's happening in your store today.</p>
        </div>
        <div style="background: white; padding: 1rem 2rem; border-radius: 1.5rem; display: flex; align-items: center; gap: 1rem; box-shadow: var(--shadow-sm); border: 1px solid var(--border);">
            <i class="fas fa-calendar-alt text-primary"></i>
            <span style="font-weight: 700;"><?php echo date('M d, Y'); ?></span>
        </div>
    </div>

    <!-- Stats Grid -->
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 2rem; margin-bottom: 3rem;">
        <div class="admin-card" style="padding: 2rem; position: relative; overflow: hidden;">
            <div style="position: absolute; right: -10px; top: -10px; font-size: 5rem; color: var(--primary-light); opacity: 0.3;"><i class="fas fa-shopping-cart"></i></div>
            <p style="color: #64748b; font-weight: 600; margin-bottom: 1rem;">Total Orders</p>
            <h2 style="font-size: 2.5rem;"><?php echo $total_orders; ?></h2>
        </div>
        <div class="admin-card" style="padding: 2rem; position: relative; overflow: hidden;">
            <div style="position: absolute; right: -10px; top: -10px; font-size: 5rem; color: #dcfce7; opacity: 0.8;"><i class="fas fa-wallet"></i></div>
            <p style="color: #64748b; font-weight: 600; margin-bottom: 1rem;">Revenue</p>
            <h2 style="font-size: 2.5rem;"><?php echo formatCurrency($total_revenue); ?></h2>
        </div>
        <div class="admin-card" style="padding: 2rem; position: relative; overflow: hidden;">
            <div style="position: absolute; right: -10px; top: -10px; font-size: 5rem; color: #e0e7ff; opacity: 0.8;"><i class="fas fa-box"></i></div>
            <p style="color: #64748b; font-weight: 600; margin-bottom: 1rem;">Products</p>
            <h2 style="font-size: 2.5rem;"><?php echo $total_products; ?></h2>
        </div>
        <div class="admin-card" style="padding: 2rem; position: relative; overflow: hidden;">
            <div style="position: absolute; right: -10px; top: -10px; font-size: 5rem; color: #fef3c7; opacity: 0.8;"><i class="fas fa-users"></i></div>
            <p style="color: #64748b; font-weight: 600; margin-bottom: 1rem;">Customers</p>
            <h2 style="font-size: 2.5rem;"><?php echo $total_customers; ?></h2>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2.5rem;">
        <!-- Sales Chart -->
        <div class="admin-card">
            <h3 style="margin-bottom: 2rem;">Revenue Overview (Last 7 Days)</h3>
            <canvas id="salesChart" height="250"></canvas>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const ctx = document.getElementById('salesChart').getContext('2d');
                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                            datasets: [{
                                label: 'Revenue (₹)',
                                data: [1200, 1900, 1500, 2500, 2200, 3000, 2800],
                                borderColor: '#2ecc71',
                                backgroundColor: 'rgba(46, 204, 113, 0.1)',
                                fill: true,
                                tension: 0.4,
                                borderWidth: 3
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: { legend: { display: false } },
                            scales: {
                                y: { beginAtZero: true, grid: { display: false } },
                                x: { grid: { display: false } }
                            }
                        }
                    });
                });
            </script>
        </div>

        <!-- Recent Orders -->
        <div class="admin-card">
            <h3 style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center;">
                Recent Orders
                <a href="orders.php" style="font-size: 0.85rem; color: var(--primary); text-decoration: none;">View All <i class="fas fa-arrow-right"></i></a>
            </h3>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($recent_orders as $order): ?>
                    <tr>
                        <td><strong>#<?php echo str_pad($order['id'], 5, '0', STR_PAD_LEFT); ?></strong></td>
                        <td style="font-weight: 700;"><?php echo formatCurrency($order['total_price']); ?></td>
                        <td>
                            <span class="badge" style="background: var(--bg); color: var(--text-dark); padding: 0.3rem 0.6rem; font-size: 0.7rem;">
                                <?php echo ucfirst($order['status']); ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Quick Actions Row -->
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 2.5rem; margin-top: 2.5rem;">
        <div class="admin-card">
            <h3 style="margin-bottom: 1.5rem;">Inventory</h3>
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <a href="products.php?action=add" class="btn btn-primary btn-block" style="justify-content: center;"><i class="fas fa-plus"></i> New Product</a>
                <a href="categories.php" class="btn btn-secondary btn-block" style="justify-content: center;"><i class="fas fa-tag"></i> Categories</a>
            </div>
        </div>
        <div class="admin-card">
            <h3 style="margin-bottom: 1.5rem;">Marketing</h3>
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <a href="coupons.php" class="btn btn-secondary btn-block" style="justify-content: center;"><i class="fas fa-ticket-alt"></i> Manage Coupons</a>
                <a href="customers.php" class="btn btn-secondary btn-block" style="justify-content: center;"><i class="fas fa-users"></i> Customers</a>
            </div>
        </div>
        <div class="admin-card" style="background: var(--primary); color: white;">
            <h3>Store Settings</h3>
            <p style="margin: 1rem 0 2rem; opacity: 0.8;">Last updated: Today at 09:41 AM</p>
            <a href="settings.php" class="btn btn-block" style="background: white; color: var(--primary); justify-content: center;">Configure Store</a>
        </div>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>
