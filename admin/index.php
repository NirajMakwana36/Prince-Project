<?php
$page_title = 'Dashboard';
include_once 'includes/header.php';

// Basic Statistics
$total_orders = $conn->query("SELECT COUNT(*) as count FROM orders")->fetch_assoc()['count'];
$total_revenue = $conn->query("SELECT SUM(total_price) as sum FROM orders WHERE status = 'delivered'")->fetch_assoc()['sum'] ?? 0;
$total_products = $conn->query("SELECT COUNT(*) as count FROM products")->fetch_assoc()['count'];
$total_customers = $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'customer'")->fetch_assoc()['count'];
$recent_orders = $conn->query("SELECT o.*, u.name as customer FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC LIMIT 5")->fetch_all(MYSQLI_ASSOC);

// Fetch revenue for last 7 days
$revenue_data = [];
$revenue_labels = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $label = date('D', strtotime($date));
    $rev = $conn->query("SELECT SUM(total_price) as sum FROM orders WHERE status = 'delivered' AND DATE(created_at) = '$date'")->fetch_assoc()['sum'] ?? 0;
    $revenue_data[] = $rev;
    $revenue_labels[] = $label;
}

// Top Selling Products
$top_products = $conn->query("
    SELECT p.name, p.image, SUM(oi.quantity) as sold, SUM(oi.quantity * oi.price) as revenue 
    FROM orders o 
    JOIN order_items oi ON o.id = oi.order_id 
    JOIN products p ON oi.product_id = p.id 
    WHERE o.status = 'delivered' 
    GROUP BY p.id 
    ORDER BY sold DESC 
    LIMIT 5
")->fetch_all(MYSQLI_ASSOC);

// Category Distribution
$cat_dist = $conn->query("
    SELECT c.name, COUNT(p.id) as count 
    FROM categories c 
    JOIN products p ON c.id = p.category_id 
    GROUP BY c.id
")->fetch_all(MYSQLI_ASSOC);
?>

<div class="admin-main-content">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 3rem;">
        <div>
            <h1 style="font-size: 2.5rem; margin-bottom: 0.5rem; font-family: 'Outfit';">Business <span style="color: var(--primary);">Intelligence</span></h1>
            <p style="color: #64748b;">Unified view of your store's performance and operations.</p>
        </div>
        <div style="display: flex; gap: 1rem;">
            <button class="btn btn-secondary"><i class="fas fa-download"></i> Export Reports</button>
            <a href="orders.php" class="btn btn-primary">Process Orders</a>
        </div>
    </div>

    <!-- Modern Stats Grid -->
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.5rem; margin-bottom: 2rem;">
        <div style="background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%); padding: 2rem; border-radius: 1.5rem; color: #111; box-shadow: var(--shadow-lg);">
            <p style="opacity: 0.8; font-weight: 700; font-size: 0.85rem; text-transform: uppercase;">Total Revenue</p>
            <h2 style="font-size: 2rem; color: #111; margin: 0.5rem 0;"><?php echo formatCurrency($total_revenue); ?></h2>
            <p style="font-size: 0.85rem; font-weight: 600;">Overall generated</p>
        </div>
        <div style="background: white; padding: 2rem; border-radius: 1.5rem; border: 1px solid var(--border);">
            <p style="color: var(--text-muted); font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Total Orders</p>
            <h2 style="font-size: 2rem; margin: 0.5rem 0;"><?php echo $total_orders; ?></h2>
            <p style="color: var(--secondary); font-size: 0.85rem; font-weight: 700;">Lifetime volume</p>
        </div>
        <div style="background: white; padding: 2rem; border-radius: 1.5rem; border: 1px solid var(--border);">
            <p style="color: var(--text-muted); font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Active Customers</p>
            <h2 style="font-size: 1.75rem; margin: 0.5rem 0;"><?php echo $total_customers; ?></h2>
            <p style="color: var(--success); font-size: 0.85rem; font-weight: 700;">Registered users</p>
        </div>
        <div style="background: white; padding: 2rem; border-radius: 1.5rem; border: 1px solid var(--border);">
            <p style="color: var(--text-muted); font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">In-Stock Products</p>
            <h2 style="font-size: 1.75rem; margin: 0.5rem 0;"><?php echo $total_products; ?></h2>
            <p style="color: var(--primary-dark); font-size: 0.85rem; font-weight: 700;">Catalog health</p>
        </div>
    </div>

    <!-- Status Specific Grid -->
    <div style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 1rem; margin-bottom: 3rem;">
        <?php 
        $statuses = [
            'pending' => ['bg' => '#fef3c7', 'color' => '#b45309', 'icon' => 'clock'],
            'accepted' => ['bg' => '#e0f2fe', 'color' => '#0369a1', 'icon' => 'check-double'],
            'preparing' => ['bg' => '#ede9fe', 'color' => '#6d28d9', 'icon' => 'box-open'],
            'out_for_delivery' => ['bg' => '#dcfce7', 'color' => '#15803d', 'icon' => 'truck'],
            'delivered' => ['bg' => '#f1f5f9', 'color' => '#334155', 'icon' => 'flag-checkered']
        ];
        
        foreach($statuses as $status => $meta):
            $count = $conn->query("SELECT COUNT(*) as c FROM orders WHERE status = '$status'")->fetch_assoc()['c'];
        ?>
            <div style="background: <?php echo $meta['bg']; ?>; padding: 1.5rem; border-radius: 1.25rem; text-align: center;">
                <div style="width: 40px; height: 40px; background: rgba(0,0,0,0.05); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; color: <?php echo $meta['color']; ?>;">
                    <i class="fas fa-<?php echo $meta['icon']; ?>"></i>
                </div>
                <h3 style="font-size: 1.5rem; color: <?php echo $meta['color']; ?>;"><?php echo $count; ?></h3>
                <p style="font-size: 0.7rem; font-weight: 800; text-transform: uppercase; color: <?php echo $meta['color']; ?>; opacity: 0.8;"><?php echo str_replace('_', ' ', $status); ?></p>
            </div>
        <?php endforeach; ?>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
        <!-- Detailed Analytics Chart -->
        <div style="background: white; padding: 2rem; border-radius: 1.5rem; border: 1px solid var(--border);">
            <h3 style="margin-bottom: 2rem; display: flex; justify-content: space-between;">
                Sales Performance
                <select style="border: none; background: #f1f5f9; padding: 4px 12px; border-radius: 8px; font-size: 0.8rem;">
                    <option>Last 7 Days</option>
                    <option>Last 30 Days</option>
                </select>
            </h3>
            <canvas id="mainDashboardChart" height="280"></canvas>
        </div>

        <!-- Category Distribution -->
        <div style="background: white; padding: 2rem; border-radius: 1.5rem; border: 1px solid var(--border);">
            <h3 style="margin-bottom: 2rem;">Inventory Mix</h3>
            <canvas id="categoryChart" height="280"></canvas>
        </div>
    </div>

    <!-- Low Stock & Top Products -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-top: 2rem;">
        <div style="background: white; padding: 2rem; border-radius: 1.5rem; border: 1px solid var(--border);">
            <h3 style="margin-bottom: 2rem;">Top Selling Products</h3>
            <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                <?php foreach($top_products as $tp): ?>
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <?php 
                        $tp_img = (strpos($tp['image'], 'http') === 0) ? $tp['image'] : '../assets/images/' . ($tp['image'] ?: 'default.png');
                        ?>
                        <img src="<?php echo $tp_img; ?>" style="width: 48px; height: 48px; border-radius: 8px; object-fit: cover;">
                        <div>
                            <div style="font-weight: 700; font-size: 0.95rem;"><?php echo htmlspecialchars($tp['name']); ?></div>
                            <div style="color: #64748b; font-size: 0.8rem;"><?php echo $tp['sold']; ?> units sold</div>
                        </div>
                    </div>
                    <div style="font-weight: 800; color: var(--primary);"><?php echo formatCurrency($tp['revenue']); ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div style="background: white; padding: 2rem; border-radius: 1.5rem; border: 1px solid var(--border);">
            <h3 style="margin-bottom: 2rem;">Recent Activity</h3>
            <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                <?php foreach($recent_orders as $ro): ?>
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="width: 40px; height: 40px; border-radius: 50%; background: #f1f5f9; display: flex; align-items: center; justify-content: center; color: var(--primary);">
                        <i class="fas fa-shopping-bag"></i>
                    </div>
                    <div>
                        <div style="font-size: 0.9rem;">New order <strong>#<?php echo $ro['id']; ?></strong> by <?php echo htmlspecialchars($ro['customer']); ?></div>
                        <div style="font-size: 0.75rem; color: #64748b;"><?php echo time_elapsed_string($ro['created_at']); ?></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Sales Performance Line Chart
    new Chart(document.getElementById('mainDashboardChart'), {
        type: 'line',
        data: {
            labels: <?php echo json_encode($revenue_labels); ?>,
            datasets: [{
                label: 'Revenue',
                data: <?php echo json_encode($revenue_data); ?>,
                borderColor: '#febd69',
                backgroundColor: 'rgba(254, 189, 105, 0.1)',
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                pointBackgroundColor: '#fff',
                pointBorderWidth: 2
            }]
        },
        options: {
            plugins: { legend: { display: false } },
            scales: {
                y: { grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 } } },
                x: { grid: { display: false }, ticks: { font: { size: 10 } } }
            }
        }
    });

    // Category Doughnut Chart
    new Chart(document.getElementById('categoryChart'), {
        type: 'doughnut',
        data: {
            labels: <?php echo json_encode(array_column($cat_dist, 'name')); ?>,
            datasets: [{
                data: <?php echo json_encode(array_column($cat_dist, 'count')); ?>,
                backgroundColor: ['#131921', '#febd69', '#232f3e', '#e47911', '#37475a', '#f0c14b'],
                borderWidth: 0
            }]
        },
        options: {
            cutout: '70%',
            plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 10 } } } }
        }
    });
});
</script>

<?php include_once 'includes/footer.php'; ?>
