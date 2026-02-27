if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once $_SERVER['DOCUMENT_ROOT'] . '/CoGroCart/config/functions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - GroCart' : 'GroCart - Smart Online Grocery'; ?></title>
    
    <!-- Modern Typography -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom Style -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
    
    <!-- Animation Library -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

    <script>
        window.APP_CONFIG = {
            baseUrl: '<?php echo BASE_URL; ?>',
            userId: <?php echo $_SESSION['user_id'] ?? 'null'; ?>,
            isLoggedIn: <?php echo isLoggedIn() ? 'true' : 'false'; ?>
        };
    </script>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar glass">
        <div class="container nav-container">
            <a href="<?php echo BASE_URL; ?>" class="logo animate__animated animate__fadeInLeft">
                <i class="fas fa-shopping-basket"></i>
                <span>CoGroCart</span>
            </a>
            
            <form action="<?php echo BASE_URL; ?>customer/products.php" method="GET" class="search-container animate__animated animate__fadeIn">
                <i class="fas fa-search search-icon"></i>
                <input type="text" name="search" class="search-input" placeholder="Search for fresh groceries..." value="<?php echo $_GET['search'] ?? ''; ?>">
            </form>

            <div class="nav-actions animate__animated animate__fadeInRight">
                <?php if (isLoggedIn()): ?>
                    <?php if (isCustomer()): ?>
                        <a href="<?php echo BASE_URL; ?>customer/cart.php" class="nav-link cart-icon-container">
                            <i class="fas fa-shopping-bag"></i>
                            <span class="cart-badge" id="cartBadge"><?php echo getCartCount($conn, $_SESSION['user_id']); ?></span>
                        </a>
                        <a href="<?php echo BASE_URL; ?>customer/dashboard.php" class="nav-link">
                            <i class="fas fa-user-circle"></i>
                            <span>Profile</span>
                        </a>
                    <?php endif; ?>

                    <?php if (isAdmin()): ?>
                        <a href="<?php echo BASE_URL; ?>admin/index.php" class="btn btn-primary">
                            <i class="fas fa-chart-pie"></i>
                            Admin
                        </a>
                    <?php elseif (isDeliveryPartner()): ?>
                        <a href="<?php echo BASE_URL; ?>delivery/index.php" class="btn btn-primary">
                            <i class="fas fa-motorcycle"></i>
                            Deliveries
                        </a>
                    <?php endif; ?>

                    <a href="<?php echo BASE_URL; ?>customer/logout.php" class="btn btn-secondary">
                        <i class="fas fa-sign-out-alt"></i>
                        Logout
                    </a>
                <?php else: ?>
                    <a href="<?php echo BASE_URL; ?>customer/login.php" class="nav-link">Login</a>
                    <a href="<?php echo BASE_URL; ?>customer/register.php" class="btn btn-primary">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Flash Messages (Toast Style) -->
    <div class="toast-container">
        <?php
        $flash = getFlashMessage();
        if ($flash):
        ?>
        <div class="toast shadow-lg show animate__animated animate__slideInRight" id="globalToast">
            <div class="toast-icon">
                <?php if($flash['type'] == 'success'): ?>
                    <i class="fas fa-check-circle text-success" style="font-size: 1.5rem;"></i>
                <?php elseif($flash['type'] == 'error'): ?>
                    <i class="fas fa-times-circle text-danger" style="font-size: 1.5rem;"></i>
                <?php else: ?>
                    <i class="fas fa-info-circle text-info" style="font-size: 1.5rem;"></i>
                <?php endif; ?>
            </div>
            <div class="toast-content">
                <strong><?php echo ucfirst($flash['type']); ?></strong>
                <p style="margin: 0; font-size: 0.9rem;"><?php echo $flash['message']; ?></p>
            </div>
        </div>
        <script>
            setTimeout(() => {
                const toast = document.getElementById('globalToast');
                if (toast) {
                    toast.classList.remove('show');
                    toast.classList.add('animate__slideOutRight');
                }
            }, 5000);
        </script>
        <?php
        endif;
        ?>
    </div>

    <!-- Main Content Wrapper -->
    <div class="page-wrapper" style="min-height: calc(100vh - 5rem - 300px);">
