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

<main>
    <!-- Explore Section -->
    <div class="container" style="margin: 4rem auto;">
        <div class="explore-container" onclick="window.location.href='customer/products.php'">
            <img src="https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=1500&q=80" alt="Fresh Essentials">
            <div class="explore-overlay">
                <div class="explore-content">
                    <h2 style="color: white; font-size: 3rem; margin-bottom: 2rem; font-family: 'Outfit';">Daily Essentials <br> Delivered Fresh</h2>
                    <div class="explore-btn">Explore All <i class="fas fa-chevron-right"></i></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Shop By Category (Circular) -->
    <div class="container category-section">
        <h2 class="section-title">Shop by Category</h2>
        <div class="category-grid">
            <?php foreach ($categories as $cat): ?>
            <a href="customer/products.php?category=<?php echo $cat['id']; ?>" class="category-card">
                <?php 
                $cat_img = (strpos($cat['image'], 'http') === 0) ? $cat['image'] : 'assets/images/' . ($cat['image'] ?: 'default-cat.png');
                ?>
                <img src="<?php echo $cat_img; ?>" alt="<?php echo htmlspecialchars($cat['name']); ?>">
                <div class="category-name"><?php echo htmlspecialchars($cat['name']); ?></div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Deal of the Week (Products) -->
    <div class="container">
        <h2 class="section-title">Deals of the Week</h2>
        <div class="product-grid">
            <?php foreach ($trending_products as $product): ?>
            <div class="product-card" onclick="window.location.href='customer/product-detail.php?id=<?php echo $product['id']; ?>'">
                <div class="product-image-wrap">
                    <?php 
                    $img_src = (strpos($product['image'], 'http') === 0) ? $product['image'] : 'assets/images/' . ($product['image'] ?: 'default.png');
                    ?>
                    <img src="<?php echo $img_src; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                </div>
                <div class="product-info">
                    <div style="color: var(--primary-dark); font-weight: 800; font-size: 0.75rem; text-transform: uppercase; margin-bottom: 0.5rem;"><?php echo htmlspecialchars($product['category_name'] ?? 'Fresh'); ?></div>
                    <div class="product-title"><?php echo htmlspecialchars($product['name']); ?></div>
                    
                    <?php $card_rating = getProductRating($conn, $product['id']); ?>
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-top: 0.5rem; font-size: 0.8rem;">
                        <div style="color: #fbbf24;">
                            <?php 
                            $stars = floor($card_rating['rating']);
                            for($i=1; $i<=5; $i++) echo '<i class="' . ($i<=$stars ? 'fas' : 'far') . ' fa-star"></i>';
                            ?>
                        </div>
                        <span style="color: var(--text-muted); font-weight: 600;">(<?php echo $card_rating['count']; ?>)</span>
                    </div>

                    <div style="display: flex; align-items: center; gap: 1rem; margin: 1rem 0;">
                        <span class="price-tag"><?php echo formatCurrency(getDiscountedPrice($product['price'], $product['discount'])); ?></span>
                        <?php if ($product['discount'] > 0): ?>
                            <span style="text-decoration: line-through; color: var(--text-muted); font-size: 0.9rem;"><?php echo formatCurrency($product['price']); ?></span>
                        <?php endif; ?>
                    </div>
                    <button class="btn btn-primary" style="width: 100%; height: 45px; border-radius: 12px;" onclick="event.stopPropagation(); addToCart(<?php echo $product['id']; ?>, 1)">
                        Add to Bag
                    </button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

   

    <!-- Trust Pillars -->
    <section class="container" style="margin: 6rem auto;">
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 3rem;">
            <div class="trust-card">
                <div class="trust-icon" style="background: #e0f2fe; color: #0ea5e9;"><i class="fas fa-shipping-fast"></i></div>
                <h3 style="margin-bottom: 0.75rem;">Fast Delivery</h3>
                <p style="color: var(--text-muted); font-size: 0.95rem;">Fresh groceries delivered to your doorstep within 60 minutes.</p>
            </div>
            <div class="trust-card">
                <div class="trust-icon" style="background: #dcfce7; color: #16a34a;"><i class="fas fa-shield-alt"></i></div>
                <h3 style="margin-bottom: 0.75rem;">Secure Checkout</h3>
                <p style="color: var(--text-muted); font-size: 0.95rem;">Multiple payment options with 100% secure transaction processing.</p>
            </div>
            <div class="trust-card">
                <div class="trust-icon" style="background: #ffedd5; color: #f97316;"><i class="fas fa-certificate"></i></div>
                <h3 style="margin-bottom: 0.75rem;">Premium Quality</h3>
                <p style="color: var(--text-muted); font-size: 0.95rem;">We source only the finest and freshest products for your family.</p>
            </div>
        </div>
    </section>
</main>

<?php include_once 'includes/footer.php'; ?>
