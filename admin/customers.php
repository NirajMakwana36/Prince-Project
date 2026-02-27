<?php
$page_title = 'Customers';
include_once 'includes/header.php';

// Pagination
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$per_page = 15;
$offset = ($page - 1) * $per_page;

$customers = $conn->query("SELECT u.*, COUNT(o.id) as total_orders, SUM(o.total_price) as total_spent FROM users u LEFT JOIN orders o ON u.id = o.user_id WHERE u.role = 'customer' GROUP BY u.id ORDER BY u.created_at DESC LIMIT $per_page OFFSET $offset")->fetch_all(MYSQLI_ASSOC);

$total_customers = $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'customer'")->fetch_assoc()['count'];
$total_pages = ceil($total_customers / $per_page);
?>

<div class="animate__animated animate__fadeIn">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 3rem;">
        <div>
            <h1 style="font-size: 2.5rem;">Customers</h1>
            <p style="color: #64748b;">Manage and view your customer base.</p>
        </div>
        <div class="stat-badge"><i class="fas fa-users"></i> <?php echo $total_customers; ?> Total Users</div>
    </div>

    <div class="admin-card" style="padding: 0;">
        <table class="admin-table">
            <thead>
                <tr>
                    <th style="padding-left: 2.5rem;">Customer</th>
                    <th>Contact</th>
                    <th>Orders</th>
                    <th>Total Spent</th>
                    <th>Joined</th>
                    <th style="padding-right: 2.5rem;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($customers as $c): ?>
                <tr>
                    <td style="padding-left: 2.5rem;">
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <div style="width: 45px; height: 45px; border-radius: 50%; background: var(--primary-light); color: var(--primary); display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1.25rem;">
                                <?php echo strtoupper(substr($c['name'], 0, 1)); ?>
                            </div>
                            <div>
                                <div style="font-weight: 700;"><?php echo htmlspecialchars($c['name']); ?></div>
                                <div style="font-size: 0.75rem; color: #64748b;">ID: #<?php echo $c['id']; ?></div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div style="font-size: 0.85rem; font-weight: 500;"><?php echo htmlspecialchars($c['email']); ?></div>
                        <div style="font-size: 0.85rem; color: #64748b;"><?php echo htmlspecialchars($c['phone']); ?></div>
                    </td>
                    <td><span style="font-weight: 700;"><?php echo $c['total_orders']; ?></span></td>
                    <td style="font-weight: 800; color: var(--primary);"><?php echo formatCurrency($c['total_spent'] ?: 0); ?></td>
                    <td style="font-size: 0.85rem; color: #64748b;"><?php echo date('M d, Y', strtotime($c['created_at'])); ?></td>
                    <td style="padding-right: 2.5rem;">
                        <div style="display: flex; gap: 0.5rem;">
                            <button class="btn btn-secondary btn-sm" onclick="alert('Viewing customer <?php echo $c['id']; ?>')"><i class="fas fa-eye"></i></button>
                            <a href="?delete=<?php echo $c['id']; ?>" class="btn btn-secondary btn-sm" style="color: #ef4444;" onclick="return confirm('Delete customer?')"><i class="fas fa-trash"></i></a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php if($total_pages > 1): ?>
        <div style="display: flex; justify-content: center; gap: 0.5rem; padding: 2rem;">
            <?php for($i=1; $i<=$total_pages; $i++): ?>
                <a href="?page=<?php echo $i; ?>" class="btn <?php echo $page == $i ? 'btn-primary' : 'btn-secondary'; ?>" style="width: 40px; height: 40px; padding: 0; justify-content: center;"><?php echo $i; ?></a>
            <?php endfor; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>
