<?php
$page_title = 'CoGroCart - Freshness Delivered';
include_once 'includes/header.php';

// Get categories
$categories = getAllCategories($conn);

// Get trending products
$trending_products = getTrendingProducts($conn, 12);

// Check store status
$store_open = isStoreOpen($conn);
?>

<!-- Hero Section -->
<section class="hero animate__animated animate__fadeIn">
    <div class="container hero-grid">
        <div class="hero-text" style="text-align: left;">
            <span style="background: var(--primary-light); color: var(--primary-dark); padding: 0.5rem 1rem; border-radius: 2rem; font-weight: 700; font-size: 0.85rem; margin-bottom: 1.5rem; display: inline-block;">
                🥗 100% Organic & Fresh
            </span>
            <h1 class="hero-title">Freshness Delivered <br>To Your <span style="color: var(--primary);">Doorstep.</span></h1>
            <p class="hero-subtitle">Order high-quality groceries from the comfort of your home and experience the fastest delivery in town.</p>
            <div style="display: flex; gap: 1rem; justify-content: flex-start;">
                <a href="<?php echo BASE_URL; ?>customer/products.php" class="btn btn-primary btn-lg">
                    <i class="fas fa-shopping-basket"></i>
                    Start Shopping
                </a>
                <a href="#categories" class="btn btn-secondary btn-lg">
                    Explore Categories
                </a>
            </div>
        </div>
        <div class="hero-image animate__animated animate__zoomIn">
            <img src="https://img.freepik.com/free-photo/healthy-food-background-concept-with-copy-space_23-2148281395.jpg" alt="Fresh Groceries" style="width: 100%; border-radius: 2rem; box-shadow: var(--shadow-xl);">
        </div>
    </div>
</section>

<!-- Categories Section -->
<section class="container" id="categories" style="margin: 6rem auto;">
    <div style="text-align: center; margin-bottom: 3.5rem;">
        <h2 style="font-size: 2.5rem; margin-bottom: 1rem;">Shop by <span style="color: var(--primary);">Category</span></h2>
        <p style="color: var(--text-muted); max-width: 600px; margin: 0 auto;">Explore our wide range of categories, from farm-fresh vegetables to daily household essentials.</p>
    </div>
    
    <div class="grid-auto">
        <?php 
        $icons = ['fas fa-leaf', 'fas fa-apple-alt', 'fas fa-cheese', 'fas fa-cookie', 'fas fa-home', 'fas fa-spray-can'];
        $i = 0;
        foreach ($categories as $category): 
            $icon = $icons[$i % count($icons)];
            $i++;
        ?>
        <a href="customer/products.php?category=<?php echo $category['id']; ?>" class="category-card">
            <span class="category-icon"><i class="<?php echo $icon; ?>"></i></span>
            <h3 style="margin-bottom: 0.5rem;"><?php echo htmlspecialchars($category['name']); ?></h3>
            <p style="color: var(--text-muted); font-size: 0.85rem;"><?php echo htmlspecialchars($category['description']); ?></p>
        </a>
        <?php endforeach; ?>
    </div>
</section>

<!-- Trending Products Section -->
<section style="background: var(--white); padding: 6rem 0;">
    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 3.5rem;">
            <div>
                <h2 style="font-size: 2.5rem; margin-bottom: 1rem;">Trending <span style="color: var(--primary);">Products</span></h2>
                <p style="color: var(--text-muted);">Our most popular items chosen by thousands of happy customers.</p>
            </div>
            <a href="customer/products.php" class="btn btn-secondary">View All <i class="fas fa-arrow-right"></i></a>
        </div>

        <div class="grid-auto">
            <?php foreach ($trending_products as $product): ?>
            <div class="product-card">
                <div class="product-image">
                    <?php if ($product['image']): ?>
                        <img src="assets/images/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                    <?php else: ?>
                        <div style="height: 100%; display: flex; align-items: center; justify-content: center; background: #f1f5f9;">
                            <i class="fas fa-image" style="font-size: 3rem; color: #cbd5e1;"></i>
                        </div>
                    <?php endif; ?>
                    <?php if ($product['discount'] > 0): ?>
                        <span class="product-badge">-<?php echo $product['discount']; ?>% OFF</span>
                    <?php endif; ?>
                </div>
                <div class="product-info">
                    <h3 class="product-name" style="font-weight: 700;"><?php echo htmlspecialchars($product['name']); ?></h3>
                    <div class="product-price">
                        <span class="current-price"><?php echo formatCurrency(getDiscountedPrice($product['price'], $product['discount'])); ?></span>
                        <?php if ($product['discount'] > 0): ?>
                            <span class="original-price"><?php echo formatCurrency($product['price']); ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <div style="margin-top: auto; display: flex; gap: 0.75rem;">
                        <?php if (isLoggedIn() && isCustomer()): ?>
                            <button class="btn btn-primary btn-block" onclick="addToCart(<?php echo $product['id']; ?>, 1)">
                                <i class="fas fa-shopping-cart"></i> Add to Cart
                            </button>
                        <?php else: ?>
                            <a href="customer/login.php" class="btn btn-secondary btn-block">Login to Buy</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Why Choose Us -->
<section class="container" style="margin: 8rem auto;">
    <div class="grid-auto" style="grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));">
        <div style="text-align: center; padding: 2rem;">
            <div style="width: 70px; height: 70px; background: var(--primary-light); color: var(--primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; font-size: 1.5rem;"><i class="fas fa-shipping-fast"></i></div>
            <h3 style="margin-bottom: 0.75rem;">Fast Delivery</h3>
            <p style="color: var(--text-muted); font-size: 0.9rem;">From our store to your door in less than 60 minutes.</p>
        </div>
        <div style="text-align: center; padding: 2rem;">
            <div style="width: 70px; height: 70px; background: #fee2e2; color: #ef4444; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; font-size: 1.5rem;"><i class="fas fa-shield-alt"></i></div>
            <h3 style="margin-bottom: 0.75rem;">Secure Checkout</h3>
            <p style="color: var(--text-muted); font-size: 0.9rem;">100% secure payment methods and cash on delivery.</p>
        </div>
        <div style="text-align: center; padding: 2rem;">
            <div style="width: 70px; height: 70px; background: #dbeafe; color: #3b82f6; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; font-size: 1.5rem;"><i class="fas fa-sync-alt"></i></div>
            <h3 style="margin-bottom: 0.75rem;">Easy Returns</h3>
            <p style="color: var(--text-muted); font-size: 0.9rem;">Not satisfied with the quality? Return at the doorstep.</p>
        </div>
        <div style="text-align: center; padding: 2rem;">
            <div style="width: 70px; height: 70px; background: #ffedd5; color: #f97316; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; font-size: 1.5rem;"><i class="fas fa-certificate"></i></div>
            <h3 style="margin-bottom: 0.75rem;">Premium Quality</h3>
            <p style="color: var(--text-muted); font-size: 0.9rem;">We source only the finest and freshest products for you.</p>
        </div>
    </div>
</section>

<?php include_once 'includes/footer.php'; ?>
