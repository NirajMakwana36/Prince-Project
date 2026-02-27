<?php
$page_title = 'My Wishlist';
include_once $_SERVER['DOCUMENT_ROOT'] . '/CoGroCart/includes/header.php';

if (!isLoggedIn()) {
    redirect(BASE_URL . 'login.php', 'Please login to view your wishlist', 'info');
}

$user_id = $_SESSION['user_id'];

// Get wishlist items
$stmt = $conn->prepare("
    SELECT p.*, c.name as category_name 
    FROM wishlist w 
    JOIN products p ON w.product_id = p.id 
    JOIN categories c ON p.category_id = c.id 
    WHERE w.user_id = ?
    ORDER BY w.created_at DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$wishlist_items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<div class="container animate-fade" style="margin: 4rem auto;">
    <div style="margin-bottom: 4rem; text-align: center;">
        <h1 style="font-family: 'Outfit'; font-size: 3rem;">Your <span style="color: var(--primary);">Wishlist</span></h1>
        <p style="color: var(--text-muted);">Keep track of your favorite premium essentials.</p>
    </div>

    <?php if (empty($wishlist_items)): ?>
        <div style="text-align: center; padding: 6rem 2rem; background: white; border-radius: 2rem; border: 1px solid var(--border);">
            <div style="width: 100px; height: 100px; background: #fef3c7; color: var(--primary-dark); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 3rem; margin: 0 auto 2rem;">
                <i class="far fa-heart"></i>
            </div>
            <h2 style="font-family: 'Outfit';">Wishlist is Empty</h2>
            <p style="color: var(--text-muted); margin-bottom: 2rem;">You haven't saved any items yet. Start exploring our catalog!</p>
            <a href="products.php" class="btn btn-primary" style="padding: 1rem 3rem;">Start Shopping</a>
        </div>
    <?php else: ?>
        <div class="product-grid">
            <?php foreach ($wishlist_items as $product): 
                $img_src = (strpos($product['image'], 'http') === 0) ? $product['image'] : BASE_URL . 'assets/images/' . ($product['image'] ?: 'default.png');
                $discounted_price = getDiscountedPrice($product['price'], $product['discount']);
            ?>
                <div class="product-card" id="wishlist-item-<?php echo $product['id']; ?>">
                    <div class="product-image-wrap">
                        <img src="<?php echo $img_src; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                        <button class="wishlist-btn active" onclick="removeFromWishlist(<?php echo $product['id']; ?>)">
                            <i class="fas fa-heart"></i>
                        </button>
                    </div>
                    <div class="product-info">
                        <div class="category-tag"><?php echo htmlspecialchars($product['category_name']); ?></div>
                        <h3 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h3>
                        <div class="price-tag">
                            <span class="curr-price"><?php echo formatCurrency($discounted_price); ?></span>
                            <?php if($product['discount'] > 0): ?>
                                <span class="old-price"><?php echo formatCurrency($product['price']); ?></span>
                            <?php endif; ?>
                        </div>
                        <div style="margin-top: 1.5rem; display: flex; gap: 0.75rem;">
                            <a href="product-detail.php?id=<?php echo $product['id']; ?>" class="btn btn-secondary" style="flex: 1; padding: 0.75rem;">View</a>
                            <button onclick="addToCart(<?php echo $product['id']; ?>, 1)" class="btn btn-primary" style="flex: 2; padding: 0.75rem;">Add to Bag</button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script>
    function removeFromWishlist(pId) {
        let formData = new FormData();
        formData.append('product_id', pId);
        fetch(APP_CONFIG.baseUrl + 'api/wishlist.php?action=toggle', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                document.getElementById('wishlist-item-' + pId).style.opacity = '0';
                setTimeout(() => {
                    location.reload();
                }, 300);
            }
        });
    }
</script>

<?php include_once $_SERVER['DOCUMENT_ROOT'] . '/CoGroCart/includes/footer.php'; ?>
