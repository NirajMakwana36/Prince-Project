<?php
$page_title = 'Product Details';
include_once $_SERVER['DOCUMENT_ROOT'] . '/CoGroCart/includes/header.php';

$product_id = intval($_GET['id'] ?? 0);
$product = getProduct($conn, $product_id);

if (!$product) {
    echo "<div class='container' style='padding: 100px 0; text-align: center;'>
            <h1 style='color: var(--text-muted);'>Product Not Found</h1>
            <p>The product you are looking for might have been removed or is temporarily unavailable.</p>
            <a href='products.php' class='btn btn-primary' style='margin-top: 2rem;'>Back to Catalog</a>
          </div>";
    include_once $_SERVER['DOCUMENT_ROOT'] . '/CoGroCart/includes/footer.php';
    exit;
}

$page_title = $product['name'];
$img_src = (strpos($product['image'], 'http') === 0) ? $product['image'] : BASE_URL . 'assets/images/' . ($product['image'] ?: 'default.png');
$discounted_price = getDiscountedPrice($product['price'], $product['discount']);
$rating_info = getProductRating($conn, $product_id);
?>

<style>
    .detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 5rem; margin: 4rem 0; align-items: start; }
    
    .image-viewer { position: sticky; top: 120px; border-radius: var(--radius-lg); overflow: hidden; background: white; border: 1px solid var(--border); }
    .main-img { width: 100%; aspect-ratio: 1; object-fit: cover; transition: 0.5s; }
    .main-img:hover { transform: scale(1.05); }

    .product-meta { display: flex; flex-direction: column; gap: 1.5rem; }
    .category-label { color: var(--primary-dark); font-weight: 800; text-transform: uppercase; font-size: 0.8rem; letter-spacing: 2px; }
    .product-name { font-size: 3rem; line-height: 1.1; margin: 0.5rem 0; }
    
    .price-display { display: flex; align-items: center; gap: 1.5rem; background: #f8fafc; padding: 2rem; border-radius: var(--radius-md); border: 1px solid var(--border); }
    .price-main { font-size: 2.5rem; font-weight: 900; color: var(--secondary); font-family: 'Outfit'; }
    .price-old { text-decoration: line-through; color: var(--text-muted); font-size: 1.25rem; }
    .save-badge { background: #fee2e2; color: #ef4444; padding: 0.5rem 1rem; border-radius: 2rem; font-weight: 800; font-size: 0.85rem; }

    .qty-selector { display: flex; align-items: center; gap: 1rem; margin: 2rem 0; }
    .qty-btn { width: 45px; height: 45px; border-radius: 50%; border: 1px solid var(--border); background: white; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; transition: 0.2s; }
    .qty-btn:hover { background: var(--bg); border-color: var(--secondary); }
    #qty { font-size: 1.25rem; font-weight: 800; width: 40px; text-align: center; }

    .perks-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-top: 3rem; }
    .perk-item { display: flex; align-items: center; gap: 0.75rem; color: var(--text-muted); font-size: 0.9rem; font-weight: 600; }
    .perk-item i { color: var(--success); font-size: 1.1rem; }

    @media (max-width: 900px) {
        .detail-grid { grid-template-columns: 1fr; gap: 3rem; }
        .product-name { font-size: 2.25rem; }
    }
</style>

<div class="container animate-fade">
    <div style="margin-top: 2rem;">
        <a href="products.php" style="text-decoration: none; color: var(--text-muted); font-weight: 600;"><i class="fas fa-arrow-left"></i> Back to Catalog</a>
    </div>

    <div class="detail-grid">
        <div class="image-viewer">
            <img src="<?php echo $img_src; ?>" class="main-img" alt="<?php echo htmlspecialchars($product['name']); ?>">
        </div>

        <div class="product-meta">
            <div>
                <span class="category-label"><?php echo htmlspecialchars($product['category_name'] ?? 'Fresh Produce'); ?></span>
                <h1 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h1>
                <div style="display: flex; align-items: center; gap: 1rem; margin-top: 0.5rem;">
                    <div style="color: #fbbf24;">
                        <?php 
                        $stars = floor($rating_info['rating']);
                        for($i=1; $i<=5; $i++) {
                            if($i<=$stars) echo '<i class="fas fa-star"></i>';
                            elseif($i-0.5 == $rating_info['rating']) echo '<i class="fas fa-star-half-alt"></i>';
                            else echo '<i class="far fa-star"></i>';
                        }
                        ?>
                    </div>
                    <span style="font-size: 0.85rem; color: var(--text-muted); font-weight: 600;">
                        <?php echo $rating_info['count'] > 0 ? "({$rating_info['rating']} • {$rating_info['count']} Reviews)" : "(No reviews yet)"; ?>
                    </span>
                </div>
            </div>

            <p style="color: var(--text-muted); font-size: 1.1rem; line-height: 1.8;">
                <?php echo nl2br(htmlspecialchars($product['description'])); ?>
            </p>

            <div class="price-display">
                <div>
                    <div class="price-main"><?php echo formatCurrency($discounted_price); ?></div>
                    <?php if($product['discount'] > 0): ?>
                        <div style="display: flex; align-items: center; gap: 0.75rem; margin-top: 0.25rem;">
                            <span class="price-old"><?php echo formatCurrency($product['price']); ?></span>
                            <span class="save-badge">Save <?php echo $product['discount']; ?>%</span>
                        </div>
                    <?php endif; ?>
                </div>
                <div style="margin-left: auto; text-align: right;">
                    <span style="display: block; font-size: 0.7rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase;">Inclusive of all taxes</span>
                    <?php if ($product['stock'] > 0 && $product['is_available']): ?>
                        <span style="font-weight: 700; color: var(--success);"><i class="fas fa-check-circle"></i> In Stock (<?php echo $product['stock']; ?>)</span>
                    <?php else: ?>
                        <span style="font-weight: 700; color: #ef4444;"><i class="fas fa-times-circle"></i> Out of Stock</span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="qty-selector">
                <span style="font-weight: 700; color: var(--secondary);">Quantity:</span>
                <button class="qty-btn" onclick="updateQty(-1)" <?php echo ($product['stock'] <= 0 || !$product['is_available']) ? 'disabled' : ''; ?>><i class="fas fa-minus"></i></button>
                <span id="qty">1</span>
                <button class="qty-btn" onclick="updateQty(1)" <?php echo ($product['stock'] <= 0 || !$product['is_available']) ? 'disabled' : ''; ?>><i class="fas fa-plus"></i></button>
            </div>

            <div style="display: flex; gap: 1.5rem;">
                <?php if ($product['stock'] > 0 && $product['is_available']): ?>
                    <button onclick="addToCart(<?php echo $product['id']; ?>, parseInt(document.getElementById('qty').innerText))" class="btn btn-primary" style="flex: 2; height: 65px; font-size: 1.1rem;">
                        <i class="fas fa-shopping-cart"></i> Add to Cart
                    </button>
                <?php else: ?>
                    <button class="btn btn-primary" style="flex: 2; height: 65px; font-size: 1.1rem; opacity: 0.5; cursor: not-allowed;" disabled>
                        <i class="fas fa-ban"></i> Out of Stock
                    </button>
                <?php endif; ?>
                <button id="wishlistBtn" onclick="toggleWishlist(<?php echo $product['id']; ?>)" class="btn btn-secondary" style="flex: 1; height: 65px;">
                    <i class="<?php echo isLoggedIn() && isInWishlist($conn, $_SESSION['user_id'], $product['id']) ? 'fas' : 'far'; ?> fa-heart"></i> Wishlist
                </button>
            </div>

            <div class="perks-grid">
                <div class="perk-item"><i class="fas fa-truck-fast"></i> Lightning Fast Delivery</div>
                <div class="perk-item"><i class="fas fa-shield-check"></i> Quality Guaranteed</div>
                <div class="perk-item"><i class="fas fa-rotate"></i> 7-Day Easy Return</div>
                <div class="perk-item"><i class="fas fa-lock"></i> Secure Payment</div>
            </div>
        </div>
    </div>

    <!-- Related Products -->
    <div style="margin-top: 6rem;">
        <h2 style="font-family: 'Outfit'; font-size: 2.25rem; margin-bottom: 3rem;">You Might Also <span style="color: var(--primary);">Like</span></h2>
        <div class="product-grid">
            <?php 
            $related_stmt = $conn->prepare("SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.category_id = ? AND p.id != ? LIMIT 4");
            $related_stmt->bind_param("ii", $product['category_id'], $product['id']);
            $related_stmt->execute();
            $related = $related_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            foreach ($related as $rp): 
                $rp_img = (strpos($rp['image'], 'http') === 0) ? $rp['image'] : BASE_URL . 'assets/images/' . ($rp['image'] ?: 'default.png');
            ?>
                <div class="product-card" onclick="window.location.href='product-detail.php?id=<?php echo $rp['id']; ?>'">
                    <div class="product-image-wrap">
                        <img src="<?php echo $rp_img; ?>" alt="<?php echo htmlspecialchars($rp['name']); ?>">
                    </div>
                    <div class="product-info">
                        <div style="color: var(--primary-dark); font-weight: 800; font-size: 0.7rem; text-transform: uppercase; margin-bottom: 0.4rem;"><?php echo htmlspecialchars($rp['category_name']); ?></div>
                        <div class="product-title"><?php echo htmlspecialchars($rp['name']); ?></div>
                        <div style="display: flex; align-items: center; gap: 0.75rem; margin: 0.75rem 0;">
                            <span class="price-tag"><?php echo formatCurrency(getDiscountedPrice($rp['price'], $rp['discount'])); ?></span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Reviews Section -->
    <div style="margin-top: 6rem; padding-top: 4rem; border-top: 1px solid var(--border);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 3rem;">
            <h2 style="font-family: 'Outfit'; font-size: 2.25rem;">Verified <span style="color: var(--primary);">Reviews</span></h2>
            <?php if (isLoggedIn() && hasPurchasedProduct($conn, $_SESSION['user_id'], $product['id'])): ?>
                <button class="btn btn-primary" onclick="showReviewForm()">Share Your Experience</button>
            <?php endif; ?>
        </div>

        <!-- Review Form Container -->
        <div id="reviewFormWrap" style="display: none; background: white; padding: 3rem; border-radius: 2rem; border: 1px solid var(--border); margin-bottom: 4rem; box-shadow: var(--shadow-md);">
            <h3 style="margin-bottom: 2rem;">How would you rate this product?</h3>
            <form id="reviewForm">
                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                <div class="form-group">
                    <label>Your Rating</label>
                    <div style="display: flex; gap: 0.75rem; font-size: 1.75rem; color: #cbd5e1;">
                        <?php for($i=1; $i<=5; $i++): ?>
                            <i class="far fa-star rating-star" data-val="<?php echo $i; ?>" style="cursor: pointer;"></i>
                        <?php endfor; ?>
                    </div>
                    <input type="hidden" name="rating" id="reviewRating" value="5">
                </div>
                <div class="form-group">
                    <label>Comment</label>
                    <textarea name="comment" class="form-control" rows="4" placeholder="What did you like or dislike?"></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Submit Review <i class="fas fa-paper-plane"></i></button>
            </form>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 2rem;">
            <?php 
            $reviews = getProductReviews($conn, $product['id']);
            if (empty($reviews)): 
            ?>
                <div style="grid-column: 1/-1; padding: 5rem; text-align: center; background: white; border-radius: 2rem; border: 2px dashed var(--border);">
                    <i class="far fa-comments" style="font-size: 4rem; color: #94a3b8; margin-bottom: 1.5rem;"></i>
                    <h3 style="color: var(--text-muted);">No reviews yet</h3>
                    <p>Be the first to review this product!</p>
                </div>
            <?php else: ?>
                <?php foreach($reviews as $rev): ?>
                    <div style="background: white; padding: 2.5rem; border-radius: 2rem; border: 1px solid var(--border); transition: 0.3s; height: fit-content;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 1.5rem;">
                            <div style="display: flex; align-items: center; gap: 1rem;">
                                <div style="width: 45px; height: 45px; background: var(--primary-light); color: var(--primary-dark); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 800;">
                                    <?php echo strtoupper(substr($rev['user_name'], 0, 1)); ?>
                                </div>
                                <div>
                                    <div style="font-weight: 800;"><?php echo htmlspecialchars($rev['user_name']); ?></div>
                                    <div style="font-size: 0.75rem; color: var(--text-muted);"><?php echo date('d M, Y', strtotime($rev['created_at'])); ?></div>
                                </div>
                            </div>
                            <div style="color: var(--primary); font-size: 0.85rem;">
                                <?php for($i=1; $i<=5; $i++): ?>
                                    <i class="<?php echo $i <= $rev['rating'] ? 'fas' : 'far'; ?> fa-star"></i>
                                <?php endfor; ?>
                            </div>
                        </div>
                        <p style="color: var(--secondary); font-size: 0.95rem; line-height: 1.8; opacity: 0.8;"><?php echo nl2br(htmlspecialchars($rev['comment'])); ?></p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    function updateQty(delta) {
        let el = document.getElementById('qty');
        let val = parseInt(el.innerText) + delta;
        if (val >= 1 && val <= <?php echo $product['stock']; ?>) {
            el.innerText = val;
        }
    }


    function showReviewForm() {
        document.getElementById('reviewFormWrap').style.display = 'block';
        window.scrollTo({ top: document.getElementById('reviewFormWrap').offsetTop - 150, behavior: 'smooth' });
    }

    // Interactive Stars
    document.querySelectorAll('.rating-star').forEach(star => {
        star.addEventListener('mouseover', function() {
            let val = this.dataset.val;
            document.querySelectorAll('.rating-star').forEach(s => {
                s.className = s.dataset.val <= val ? 'fas fa-star rating-star' : 'far fa-star rating-star';
                if (s.dataset.val <= val) s.style.color = 'var(--primary)';
                else s.style.color = '#cbd5e1';
            });
        });
        star.addEventListener('click', function() {
            document.getElementById('reviewRating').value = this.dataset.val;
        });
    });

    // Submit Review
    document.getElementById('reviewForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        let formData = new FormData(this);
        fetch(APP_CONFIG.baseUrl + 'api/review.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            alert(data.message);
            if (data.success) location.reload();
        });
    });
</script>

<?php include_once $_SERVER['DOCUMENT_ROOT'] . '/CoGroCart/includes/footer.php'; ?>
