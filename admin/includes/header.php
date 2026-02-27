<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include_once $_SERVER['DOCUMENT_ROOT'] . '/CoGroCart/config/functions.php';

if (!isAdmin()) {
    header("Location: " . BASE_URL . "customer/login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'Admin'; ?> - CoGroCart</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root { --sidebar-w: 260px; }
        body { background: #f1f5f9; color: var(--secondary); }
        .admin-wrapper { display: flex; min-height: 100vh; }
        
        .admin-sidebar { width: var(--sidebar-w); background: var(--secondary); color: white; position: fixed; height: 100vh; padding: 2.5rem; z-index: 1000; }
        .admin-main { flex: 1; margin-left: var(--sidebar-w); padding: 3rem; background: #f1f5f9; }
        
        .sidebar-logo { font-family: 'Outfit'; font-size: 1.75rem; font-weight: 800; color: white; display: flex; align-items: center; gap: 0.75rem; text-decoration: none; margin-bottom: 3.5rem; }
        .sidebar-logo i { color: var(--primary); }
        
        .side-menu { list-style: none; }
        .side-item { margin-bottom: 0.5rem; }
        .side-link { display: flex; align-items: center; gap: 1rem; padding: 1rem; color: #94a3b8; text-decoration: none; border-radius: 12px; transition: var(--transition); font-weight: 600; font-size: 0.95rem; }
        .side-link i { font-size: 1.25rem; width: 24px; text-align: center; }
        .side-link:hover { color: white; background: rgba(255,255,255,0.05); }
        .side-link.active { background: var(--primary); color: #111; box-shadow: 0 4px 12px rgba(254, 189, 105, 0.4); }

        .admin-card { background: white; border-radius: 1.5rem; padding: 2.5rem; border: 1px solid var(--border); box-shadow: var(--shadow-sm); }
        .admin-table { width: 100%; border-collapse: collapse; }
        .admin-table th { text-align: left; padding: 1.25rem; border-bottom: 1px solid var(--border); color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.1em; font-weight: 800; }
        .admin-table td { padding: 1.25rem; border-bottom: 1px solid var(--border); vertical-align: middle; }

        .badge { padding: 0.4rem 0.8rem; border-radius: 2rem; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; }
    </style>
</head>
<body>
    <div class="admin-wrapper">
        <aside class="admin-sidebar">
            <a href="<?php echo BASE_URL; ?>" class="sidebar-logo"><i class="fas fa-shopping-basket"></i> CoGroCart</a>
            <ul class="side-menu">
                <li class="side-item"><a href="index.php" class="side-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>"><i class="fas fa-th-large"></i> Dashboard</a></li>
                <li class="side-item"><a href="products.php" class="side-link <?php echo basename($_SERVER['PHP_SELF']) == 'products.php' ? 'active' : ''; ?>"><i class="fas fa-box"></i> Inventory</a></li>
                <li class="side-item"><a href="categories.php" class="side-link <?php echo basename($_SERVER['PHP_SELF']) == 'categories.php' ? 'active' : ''; ?>"><i class="fas fa-tags"></i> Categories</a></li>
                <li class="side-item"><a href="orders.php" class="side-link <?php echo basename($_SERVER['PHP_SELF']) == 'orders.php' ? 'active' : ''; ?>"><i class="fas fa-receipt"></i> Orders</a></li>
                <li class="side-item"><a href="notifications.php" class="side-link <?php echo basename($_SERVER['PHP_SELF']) == 'notifications.php' ? 'active' : ''; ?>"><i class="fas fa-bell"></i> Notifications</a></li>
                <li class="side-item"><a href="customers.php" class="side-link <?php echo basename($_SERVER['PHP_SELF']) == 'customers.php' ? 'active' : ''; ?>"><i class="fas fa-users"></i> Customers</a></li>
                <li class="side-item"><a href="users.php" class="side-link <?php echo basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : ''; ?>"><i class="fas fa-user-shield"></i> User Rights</a></li>
                <li class="side-item"><a href="coupons.php" class="side-link <?php echo basename($_SERVER['PHP_SELF']) == 'coupons.php' ? 'active' : ''; ?>"><i class="fas fa-ticket-alt"></i> Promotions</a></li>
                <li class="side-item"><a href="settings.php" class="side-link <?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : ''; ?>"><i class="fas fa-cog"></i> Settings</a></li>
                <li class="side-item" style="margin-top: 3rem;"><a href="logout.php" class="side-link" style="color: #f87171;"><i class="fas fa-power-off"></i> Logout</a></li>
            </ul>
        </aside>
        <main class="admin-main">
