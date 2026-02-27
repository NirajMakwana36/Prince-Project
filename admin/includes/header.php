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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root { --sidebar-w: 280px; }
        body { background: #f8fafc; color: #1e293b; }
        .admin-wrapper { display: flex; min-height: 100vh; }
        
        .admin-sidebar { width: var(--sidebar-w); background: #1e293b; color: white; position: fixed; height: 100vh; padding: 2.5rem; }
        .admin-main { flex: 1; margin-left: var(--sidebar-w); padding: 3rem; }
        
        .sidebar-logo { font-family: 'Outfit'; font-size: 1.75rem; font-weight: 800; color: var(--primary); display: flex; align-items: center; gap: 0.75rem; text-decoration: none; margin-bottom: 3.5rem; }
        
        .side-menu { list-style: none; }
        .side-item { margin-bottom: 0.5rem; }
        .side-link { display: flex; align-items: center; gap: 1rem; padding: 1rem; color: #94a3b8; text-decoration: none; border-radius: 1rem; transition: 0.3s; font-weight: 600; }
        .side-link i { font-size: 1.25rem; width: 24px; text-align: center; }
        .side-link:hover, .side-link.active { background: rgba(255,255,255,0.05); color: white; }
        .side-link.active { background: var(--primary); color: white; }

        .admin-card { background: white; border-radius: 2rem; padding: 2.5rem; border: 1px solid var(--border); box-shadow: var(--shadow-sm); }
        .admin-table { width: 100%; border-collapse: collapse; }
        .admin-table th { text-align: left; padding: 1.25rem; border-bottom: 2px solid #f1f5f9; color: #64748b; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 700; }
        .admin-table td { padding: 1.25rem; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }

        .badge { padding: 0.5rem 1rem; border-radius: 2rem; font-size: 0.75rem; font-weight: 700; }
    </style>
</head>
<body>
    <div class="admin-wrapper">
        <aside class="admin-sidebar">
            <a href="<?php echo BASE_URL; ?>" class="sidebar-logo"><i class="fas fa-shopping-basket"></i> CoGroCart</a>
            <ul class="side-menu">
                <li class="side-item"><a href="index.php" class="side-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>"><i class="fas fa-grid-2"></i> Dashboard</a></li>
                <li class="side-item"><a href="products.php" class="side-link <?php echo basename($_SERVER['PHP_SELF']) == 'products.php' ? 'active' : ''; ?>"><i class="fas fa-box"></i> Products</a></li>
                <li class="side-item"><a href="categories.php" class="side-link <?php echo basename($_SERVER['PHP_SELF']) == 'categories.php' ? 'active' : ''; ?>"><i class="fas fa-tags"></i> Categories</a></li>
                <li class="side-item"><a href="orders.php" class="side-link <?php echo basename($_SERVER['PHP_SELF']) == 'orders.php' ? 'active' : ''; ?>"><i class="fas fa-receipt"></i> Orders</a></li>
                <li class="side-item"><a href="customers.php" class="side-link <?php echo basename($_SERVER['PHP_SELF']) == 'customers.php' ? 'active' : ''; ?>"><i class="fas fa-users"></i> Customers</a></li>
                <li class="side-item"><a href="settings.php" class="side-link <?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : ''; ?>"><i class="fas fa-cog"></i> Settings</a></li>
                <li class="side-item" style="margin-top: 3rem;"><a href="logout.php" class="side-link" style="color: #f87171;"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </aside>
        <main class="admin-main">
