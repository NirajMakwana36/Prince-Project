<?php
ob_start();
$page_title = 'My Dashboard';
include_once $_SERVER['DOCUMENT_ROOT'] . '/CoGroCart/includes/header.php';

// Check if logged in and is customer
if (!isLoggedIn()) {
    redirect(BASE_URL . 'customer/login.php', 'Please login first', 'info');
}

$user_id = $_SESSION['user_id'];
$user = getUser($conn, $user_id);

// Get user orders
$stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$orders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$tab = sanitize($_GET['tab'] ?? 'overview');

// Handle Profile Update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $tab == 'profile') {
    $name = sanitize($_POST['name']);
    $phone = sanitize($_POST['phone']);
    $address = sanitize($_POST['address']);
    $city = sanitize($_POST['city']);
    
    $stmt = $conn->prepare("UPDATE users SET name = ?, phone = ?, address = ?, city = ? WHERE id = ?");
    $stmt->bind_param("ssssi", $name, $phone, $address, $city, $user_id);
    if ($stmt->execute()) {
        $_SESSION['user_name'] = $name;
        $_SESSION['message'] = "Profile updated successfully!";
        $_SESSION['msg_type'] = "success";
        header("Location: dashboard.php?tab=profile");
        exit;
    }
}
?>

<style>
    .dash-layout { display: grid; grid-template-columns: 280px 1fr; gap: 3rem; margin: 4rem 0; }
    
    .dash-sidebar { background: white; border-radius: 2rem; padding: 2rem; border: 1px solid var(--border); height: fit-content; position: sticky; top: 100px; }
    .dash-menu { list-style: none; }
    .dash-link { display: flex; align-items: center; gap: 1rem; padding: 1rem; border-radius: 1rem; text-decoration: none; color: var(--text-muted); transition: var(--transition); font-weight: 600; margin-bottom: 0.5rem; }
    .dash-link i { font-size: 1.25rem; width: 24px; text-align: center; }
    .dash-link:hover, .dash-link.active { background: var(--primary-light); color: var(--primary-dark); }
    .dash-link.active { background: var(--primary); color: white; }

    .dash-card { background: white; border-radius: 2rem; padding: 2.5rem; border: 1px solid var(--border); box-shadow: var(--shadow-sm); margin-bottom: 2rem; }
    
    .stat-box { background: var(--bg); padding: 2rem; border-radius: 1.5rem; text-align: center; transition: var(--transition); }
    .stat-box:hover { transform: translateY(-5px); background: white; box-shadow: var(--shadow-md); border: 1px solid var(--primary-light); }
    .stat-val { font-family: 'Outfit', sans-serif; font-size: 2.5rem; font-weight: 800; color: var(--secondary); display: block; }
    .stat-lab { color: var(--text-muted); font-weight: 600; font-size: 0.9rem; }

    .order-row { padding: 1.5rem; border-bottom: 1px solid var(--border); display: grid; grid-template-columns: 1fr 1fr 1fr 1fr auto; align-items: center; transition: var(--transition); }
    .order-row:last-child { border-bottom: none; }
    .order-row:hover { background: var(--bg); border-radius: 1rem; }

    .badge { padding: 0.4rem 0.8rem; border-radius: 2rem; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; }
    .badge-pending { background: #fef3c7; color: #92400e; }
    .badge-delivered { background: #dcfce7; color: #166534; }
    .badge-cancelled { background: #fee2e2; color: #991b1b; }

    @media (max-width: 900px) {
        .dash-layout { grid-template-columns: 1fr; }
        .dash-sidebar { position: static; }
    }
</style>

<div class="container">
    <div class="dash-layout">
        <aside class="dash-sidebar">
            <div style="text-align: center; margin-bottom: 2rem; padding-bottom: 2rem; border-bottom: 1px solid var(--border);">
                <div style="width: 80px; height: 80px; background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2rem; font-weight: 800; margin: 0 auto 1rem; box-shadow: var(--shadow-md);">
                    <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                </div>
                <h3 style="font-family: 'Outfit', sans-serif;"><?php echo htmlspecialchars($user['name']); ?></h3>
                <p style="font-size: 0.85rem; color: var(--text-muted);"><?php echo htmlspecialchars($user['email']); ?></p>
                <div style="margin-top: 1rem;"><span class="badge" style="background: var(--primary-light); color: var(--primary-dark);"><?php echo strtoupper($user['role']); ?></span></div>
            </div>
            
            <nav class="dash-menu">
                <a href="?tab=overview" class="dash-link <?php echo $tab == 'overview' ? 'active' : ''; ?>"><i class="fas fa-grid-2"></i> Overview</a>
                <a href="?tab=orders" class="dash-link <?php echo $tab == 'orders' ? 'active' : ''; ?>"><i class="fas fa-shopping-bag"></i> My Orders</a>
                <a href="?tab=profile" class="dash-link <?php echo $tab == 'profile' ? 'active' : ''; ?>"><i class="fas fa-user-circle"></i> Profile Settings</a>
                <?php if(isAdmin()): ?>
                <a href="<?php echo BASE_URL; ?>admin/index.php" class="dash-link" style="background: #f1f5f9; margin-top: 1rem;"><i class="fas fa-chart-pie"></i> Admin Panel</a>
                <?php endif; ?>
                <a href="logout.php" class="dash-link" style="color: #f87171; margin-top: 2rem;"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </nav>
        </aside>

        <main>
            <?php if($tab == 'overview'): ?>
                <div style="margin-bottom: 3rem;">
                    <h1 style="font-size: 2.25rem;">Dashboard <span style="color: var(--primary);">Overview</span></h1>
                    <p style="color: var(--text-muted);">Track your orders and manage your account.</p>
                </div>

                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem; margin-bottom: 3rem;">
                    <div class="stat-box">
                        <span class="stat-val"><?php echo count($orders); ?></span>
                        <span class="stat-lab">Total Orders</span>
                    </div>
                    <div class="stat-box">
                        <span class="stat-val"><?php echo count(array_filter($orders, fn($o) => $o['status'] == 'delivered')); ?></span>
                        <span class="stat-lab">Delivered</span>
                    </div>
                    <div class="stat-box">
                        <span class="stat-val"><?php echo count(array_filter($orders, fn($o) => $o['status'] == 'pending')); ?></span>
                        <span class="stat-lab">In Progress</span>
                    </div>
                </div>

                <div class="dash-card">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                        <h2 style="font-family: 'Outfit';">Recent Orders</h2>
                        <a href="?tab=orders" style="color: var(--primary); font-weight: 700; text-decoration: none;">View All <i class="fas fa-arrow-right"></i></a>
                    </div>
                    
                    <?php if(empty($orders)): ?>
                        <p style="text-align: center; color: var(--text-muted); padding: 2rem;">No orders found.</p>
                    <?php else: ?>
                        <div class="orders-list">
                            <?php foreach(array_slice($orders, 0, 5) as $order): ?>
                            <div class="order-row">
                                <span style="font-weight: 700; color: var(--primary);">#<?php echo str_pad($order['id'], 5, '0', STR_PAD_LEFT); ?></span>
                                <span style="color: var(--text-muted); font-size: 0.9rem;"><?php echo date('M d, Y', strtotime($order['created_at'])); ?></span>
                                <span style="font-weight: 800;"><?php echo formatCurrency($order['total_price'] + $order['delivery_charge']); ?></span>
                                <div><span class="badge badge-<?php echo $order['status']; ?>"><?php echo $order['status']; ?></span></div>
                                <a href="order-track.php?order_id=<?php echo $order['id']; ?>" class="btn btn-secondary" style="padding: 0.5rem 1rem; font-size: 0.8rem;">Details</a>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

            <?php elseif($tab == 'orders'): ?>
                <div style="margin-bottom: 3rem;">
                    <h1 style="font-size: 2.25rem;">My <span style="color: var(--primary);">Orders</span></h1>
                    <p style="color: var(--text-muted);">View and track all your past and current orders.</p>
                </div>

                <div class="dash-card" style="padding: 0;">
                    <?php foreach($orders as $order): ?>
                    <div class="order-row" style="padding: 2rem;">
                        <div>
                            <span style="display: block; font-weight: 800; color: var(--primary); margin-bottom: 0.25rem;">Order #<?php echo str_pad($order['id'], 5, '0', STR_PAD_LEFT); ?></span>
                            <span style="font-size: 0.8rem; color: var(--text-muted);"><i class="fas fa-calendar"></i> <?php echo date('M d, Y • h:i A', strtotime($order['created_at'])); ?></span>
                        </div>
                        <div style="text-align: center;">
                            <span style="display: block; font-size: 0.75rem; color: var(--text-muted); font-weight: 600;">STATUS</span>
                            <span class="badge badge-<?php echo $order['status']; ?>"><?php echo $order['status']; ?></span>
                        </div>
                        <div style="text-align: center;">
                            <span style="display: block; font-size: 0.75rem; color: var(--text-muted); font-weight: 600;">AMOUNT</span>
                            <span style="font-weight: 800;"><?php echo formatCurrency($order['total_price'] + $order['delivery_charge']); ?></span>
                        </div>
                        <div style="text-align: center;">
                            <span style="display: block; font-size: 0.75rem; color: var(--text-muted); font-weight: 600;">METHOD</span>
                            <span style="font-weight: 600; font-size: 0.9rem;">COD</span>
                        </div>
                        <a href="order-track.php?order_id=<?php echo $order['id']; ?>" class="btn btn-primary" style="padding: 0.6rem 1.25rem;">Track Order</a>
                    </div>
                    <?php endforeach; ?>
                </div>

            <?php elseif($tab == 'profile'): ?>
                <div style="margin-bottom: 3rem;">
                    <h1 style="font-size: 2.25rem;">My <span style="color: var(--primary);">Profile</span></h1>
                    <p style="color: var(--text-muted);">Update your personal information and contact details.</p>
                </div>

                <div class="dash-card">
                    <form method="POST">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                            <div class="form-group">
                                <label>Full Name</label>
                                <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Email Address</label>
                                <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required readonly>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Phone Number</label>
                            <input type="tel" name="phone" class="form-control" value="<?php echo htmlspecialchars($user['phone']); ?>" required>
                        </div>
                            <div class="form-group">
                                <label>Direct Address</label>
                                <textarea name="address" class="form-control" rows="3" placeholder="Apartment, Street, Area"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                            </div>
                            <div class="form-group">
                                <label>City</label>
                                <input type="text" name="city" class="form-control" value="<?php echo htmlspecialchars($user['city'] ?? ''); ?>" placeholder="Enter your city">
                            </div>
                        
                        <button type="submit" class="btn btn-primary" style="margin-top: 1rem; width: auto; padding: 1rem 2.5rem;">Save Changes</button>
                    </form>
                </div>
            <?php endif; ?>
        </main>
    </div>
</div>

<?php include_once $_SERVER['DOCUMENT_ROOT'] . '/CoGroCart/includes/footer.php'; ?>
