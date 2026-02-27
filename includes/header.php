<?php
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
    <title><?php echo isset($page_title) ? $page_title . ' - CoGroCart' : 'CoGroCart - Freshness Delivered'; ?></title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">

    <script>
        window.APP_CONFIG = {
            baseUrl: '<?php echo BASE_URL; ?>',
            userId: <?php echo $_SESSION['user_id'] ?? 'null'; ?>,
            isLoggedIn: <?php echo isLoggedIn() ? 'true' : 'false'; ?>
        };
    </script>
    <style>
        :root { 
            --navbar-bg: #131921; 
            --navbar-height: 85px;
            --accent: #fbbf24;
            --nav-text: #94a3b8;
        }

        .navbar { 
            background: var(--navbar-bg); 
            height: var(--navbar-height); 
            display: flex; 
            align-items: center; 
            border-bottom: 1px solid rgba(255,255,255,0.05); 
            position: sticky; 
            top: 0; 
            z-index: 1000;
            backdrop-filter: blur(10px);
        }

        .nav-inner { 
            display: flex; 
            align-items: center; 
            justify-content: space-between; 
            gap: 2.5rem; 
            width: 100%; 
        }

        .logo { 
            font-family: 'Outfit', sans-serif; 
            font-size: 1.8rem; 
            font-weight: 800; 
            color: #fff; 
            text-decoration: none; 
            display: flex; 
            align-items: center; 
            gap: 0.75rem; 
            white-space: nowrap;
        }
        .logo i { color: var(--accent); font-size: 2rem; }
        .logo span { background: linear-gradient(135deg, #fff 0%, var(--nav-text) 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }

        .nav-address { 
            display: flex; 
            align-items: center; 
            gap: 12px; 
            color: white; 
            cursor: pointer; 
            padding: 0.75rem 1rem; 
            border-radius: 1rem; 
            transition: 0.3s;
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.05);
            min-width: 180px;
        }
        .nav-address:hover { background: rgba(255,255,255,0.08); border-color: rgba(255,255,255,0.1); }
        .nav-address i { color: var(--accent); font-size: 1.25rem; }
        .address-info { display: flex; flex-direction: column; }
        .addr-1 { font-size: 0.7rem; color: var(--nav-text); font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
        .addr-2 { font-size: 0.9rem; font-weight: 700; max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

        .search-wrapper { 
            flex: 1; 
            position: relative; 
            max-width: 600px;
        }
        .search-bar { 
            display: flex; 
            background: rgba(255,255,255,0.05); 
            border-radius: 1.25rem; 
            padding: 4px; 
            border: 1px solid rgba(255,255,255,0.1);
            transition: 0.3s;
        }
        .search-bar:focus-within { 
            background: rgba(255,255,255,0.08); 
            border-color: var(--accent); 
            box-shadow: 0 0 0 4px rgba(251, 191, 36, 0.1); 
        }
        .search-input { 
            flex: 1; 
            background: transparent; 
            border: none; 
            color: white; 
            padding: 0.75rem 1.25rem; 
            outline: none; 
            font-family: inherit;
        }
        .search-btn { 
            background: var(--accent); 
            color: #111; 
            border: none; 
            width: 50px; 
            border-radius: 1rem; 
            cursor: pointer; 
            transition: 0.3s;
        }
        .search-btn:hover { background: #f59e0b; transform: scale(1.05); }

        .nav-actions { 
            display: flex; 
            align-items: center; 
            gap: 1.5rem; 
        }

        .nav-item { 
            text-decoration: none; 
            color: white; 
            display: flex; 
            flex-direction: column; 
            transition: 0.3s;
        }
        .item-small { font-size: 0.7rem; color: var(--nav-text); font-weight: 600; text-transform: uppercase; }
        .item-big { font-size: 0.95rem; font-weight: 700; display: flex; align-items: center; gap: 4px; }

        .cart-trigger { 
            background: rgba(251, 191, 36, 0.1); 
            padding: 0.75rem 1.25rem; 
            border-radius: 1.25rem; 
            display: flex; 
            align-items: center; 
            gap: 12px; 
            border: 1px solid rgba(251, 191, 36, 0.2);
            color: var(--accent);
            position: relative;
        }
        .cart-trigger:hover { background: rgba(251, 191, 36, 0.15); }
        .cart-badge { 
            background: var(--accent); 
            color: #111; 
            font-size: 0.75rem; 
            font-weight: 900; 
            width: 20px; 
            height: 20px; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            border-radius: 50%;
        }

        .secondary-nav { 
            background: #232f3e; 
            height: 45px; 
            display: flex; 
            align-items: center; 
        }
        .sec-inner { display: flex; gap: 2rem; color: #94a3b8; font-size: 0.85rem; font-weight: 600; }
        .sec-inner a { color: inherit; text-decoration: none; transition: 0.2s; }
        .sec-inner a:hover { color: white; }
    </style>
</head>
<body>
    <?php
    $user_addr = "Select Location";
    if (isLoggedIn()) {
        $u = getUser($conn, $_SESSION['user_id']);
        $user_addr = ($u['city'] ? $u['city'] : "Set Address");
    }
    ?>
    <nav class="navbar">
        <div class="container">
            <div class="nav-inner">
                <a href="<?php echo BASE_URL; ?>" class="logo">
                    <i class="fas fa-shopping-basket"></i>
                    <span>CoGroCart</span>
                </a>

                <a href="<?php echo isLoggedIn() ? BASE_URL . 'customer/dashboard.php#profile' : BASE_URL . 'login.php'; ?>" class="nav-address" style="text-decoration: none; color: inherit;">
                    <i class="fas fa-location-dot"></i>
                    <div class="address-info">
                        <span class="addr-1">Deliver to</span>
                        <span class="addr-2"><?php echo htmlspecialchars($user_addr); ?></span>
                    </div>
                </a>

                <form action="<?php echo BASE_URL; ?>customer/products.php" method="GET" class="search-wrapper">
                    <div class="search-bar">
                        <input type="text" name="search" class="search-input" placeholder="Search premium essentials..." value="<?php echo $_GET['search'] ?? ''; ?>">
                        <button type="submit" class="search-btn"><i class="fas fa-search"></i></button>
                    </div>
                </form>

                <div class="nav-actions">
                    <a href="<?php echo isLoggedIn() ? BASE_URL . 'customer/dashboard.php?tab=profile' : BASE_URL . 'login.php'; ?>" class="nav-item">
                        <span class="item-small">Hello, <?php echo explode(' ', $_SESSION['user_name'] ?? 'Guest')[0]; ?></span>
                        <span class="item-big">Account <i class="fas fa-chevron-down" style="font-size: 0.7rem;"></i></span>
                    </a>
                    
                    <a href="<?php echo BASE_URL; ?>customer/dashboard.php?tab=orders" class="nav-item">
                        <span class="item-small">Returns</span>
                        <span class="item-big">& Orders</span>
                    </a>

                    <a href="<?php echo BASE_URL; ?>customer/wishlist.php" class="nav-item">
                        <div class="cart-badge" id="wishlistBadge" style="background: #ef4444;">
                            <?php echo isLoggedIn() ? getWishlistCount($conn, $_SESSION['user_id']) : '0'; ?>
                        </div>
                        <i class="far fa-heart" style="font-size: 1.25rem;"></i>
                    </a>

                    <a href="<?php echo BASE_URL; ?>customer/cart.php" class="nav-item cart-trigger">
                        <div class="cart-badge" id="cartBadge">
                            <?php echo isLoggedIn() ? getCartCount($conn, $_SESSION['user_id']) : '0'; ?>
                        </div>
                        <i class="fas fa-shopping-bag" style="font-size: 1.25rem;"></i>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <nav class="secondary-nav">
        <div class="container">
            <div class="sec-inner">
                <a href="<?php echo BASE_URL; ?>customer/products.php"><i class="fas fa-bars"></i> All Departments</a>
                <a href="<?php echo BASE_URL; ?>customer/products.php?category=1">Fresh Groceries</a>
                <a href="<?php echo BASE_URL; ?>customer/products.php?category=2">Beverages</a>
                <a href="<?php echo BASE_URL; ?>customer/products.php?category=3">Snacks & More</a>
                <a href="#">Subscription</a>
                <a href="#">Support</a>
                <?php if(isAdmin()): ?>
                    <a href="<?php echo BASE_URL; ?>admin/index.php" style="color: var(--accent);">Admin Console</a>
                <?php endif; ?>
                <?php if(isDeliveryPartner()): ?>
                    <a href="<?php echo BASE_URL; ?>delivery/index.php" style="color: var(--accent);">Ops Center</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    <div class="toast-container">
        <?php $flash = getFlashMessage(); if ($flash): ?>
            <div class="toast shadow-lg show" id="globalToast" style="background: var(--navbar-bg); border: 1px solid rgba(255,255,255,0.1); border-left: 4px solid var(--accent);">
                <div class="toast-content" style="color: white;">
                    <strong style="color: var(--accent);"><?php echo strtoupper($flash['type']); ?></strong>
                    <p style="margin: 0; font-size: 0.9rem; opacity: 0.8;"><?php echo $flash['message']; ?></p>
                </div>
            </div>
            <script>setTimeout(() => { document.getElementById('globalToast').style.display='none'; }, 5000);</script>
        <?php endif; ?>
    </div>

    <div class="page-wrapper" style="min-height: 80vh;">
