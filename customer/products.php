<?php
$page_title = 'Our Products';
include_once $_SERVER['DOCUMENT_ROOT'] . '/CoGroCart/includes/header.php';

// Pagination and Filters
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = 12;
$offset = ($page - 1) * $limit;

$category_id = isset($_GET['category']) ? intval($_GET['category']) : null;
$search = isset($_GET['search']) ? sanitize($_GET['search']) : null;
$sort = isset($_GET['sort']) ? sanitize($_GET['sort']) : 'newest';

// Build Query
$sql = "SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.is_available = TRUE";
$params = [];
$types = "";

if ($category_id) {
    $sql .= " AND p.category_id = ?";
    $params[] = $category_id;
    $types .= "i";
}

if ($search) {
    $sql .= " AND (p.name LIKE ? OR p.description LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= "ss";
}

switch ($sort) {
    case 'price_low': $sql .= " ORDER BY p.price ASC"; break;
    case 'price_high': $sql .= " ORDER BY p.price DESC"; break;
    case 'name': $sql .= " ORDER BY p.name ASC"; break;
    default: $sql .= " ORDER BY p.created_at DESC"; break;
}

$sql_paged = $sql . " LIMIT ? OFFSET ?";
$p_params = array_merge($params, [$limit, $offset]);
$p_types = $types . "ii";

$stmt = $conn->prepare($sql_paged);
if ($p_types) $stmt->bind_param($p_types, ...$p_params);
$stmt->execute();
$products = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Count total for pagination
$stmt_count = $conn->prepare("SELECT COUNT(*) as total FROM ($sql) as sub");
if ($types) $stmt_count->bind_param($types, ...$params);
$stmt_count->execute();
$total_products = $stmt_count->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total_products / $limit);

$categories = getAllCategories($conn);
?>

<style>
    .shop-layout { display: grid; grid-template-columns: 280px 1fr; gap: 3rem; margin: 4rem 0; }
    .filters-sidebar { position: sticky; top: 100px; height: fit-content; }
    .filter-card { background: white; border-radius: 1.5rem; padding: 2rem; border: 1px solid var(--border); box-shadow: var(--shadow-sm); }
    .filter-title { font-family: 'Outfit', sans-serif; font-size: 1.15rem; font-weight: 700; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem; }
    
    .category-list { list-style: none; }
    .category-item { margin-bottom: 0.5rem; }
    .category-link { display: flex; justify-content: space-between; align-items: center; text-decoration: none; color: var(--text-muted); padding: 0.75rem 1rem; border-radius: 0.75rem; transition: var(--transition); font-weight: 500; }
    .category-link:hover, .category-link.active { background: var(--primary-light); color: var(--primary-dark); }
    .category-link.active { font-weight: 700; }

    .shop-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2.5rem; flex-wrap: wrap; gap: 1rem; }
    .sort-select { padding: 0.6rem 1.5rem; border-radius: 2rem; border: 1px solid var(--border); background: white; outline: none; cursor: pointer; font-family: inherit; font-weight: 600; }

    .pagination { display: flex; justify-content: center; gap: 0.5rem; margin-top: 4rem; }
    .page-btn { width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; text-decoration: none; border: 1px solid var(--border); background: white; color: var(--text-muted); transition: var(--transition); font-weight: 600; }
    .page-btn.active { background: var(--primary); color: white; border-color: var(--primary); }
    .page-btn:hover:not(.active) { border-color: var(--primary); color: var(--primary); }

    @media (max-width: 900px) {
        .shop-layout { grid-template-columns: 1fr; }
        .filters-sidebar { display: none; }
    }
</style>

<div class="container" style="margin-top: 40px;">
    <div style="display: flex; gap: 40px;">
        <!-- Filter Sidebar -->
        <aside style="width: 240px; flex-shrink: 0;">
            <div style="font-weight: 700; font-size: 16px; margin-bottom: 24px; text-transform: uppercase;">Filters</div>
            
            <div style="border: 1px solid var(--border); border-radius: 4px; padding: 20px; background: white;">
                <div style="font-weight: 700; font-size: 14px; margin-bottom: 16px; text-transform: uppercase;">Categories</div>
                <div style="display: flex; flex-direction: column; gap: 12px;">
                    <a href="products.php" style="text-decoration: none; color: <?php echo !$category_id ? 'var(--primary)' : 'var(--text-muted)'; ?>; font-size: 14px; font-weight: <?php echo !$category_id ? '700' : '400'; ?>;">All Items</a>
                    <?php foreach ($categories as $cat): ?>
                    <a href="products.php?category=<?php echo $cat['id']; ?>" style="text-decoration: none; color: <?php echo $category_id == $cat['id'] ? 'var(--primary)' : 'var(--text-muted)'; ?>; font-size: 14px; font-weight: <?php echo $category_id == $cat['id'] ? '700' : '400'; ?>;">
                        <?php echo htmlspecialchars($cat['name']); ?>
                    </a>
                    <?php endforeach; ?>
                </div>
                
                <hr style="margin: 24px 0; border: none; border-top: 1px solid var(--border);">
                
                <div style="font-weight: 700; font-size: 14px; margin-bottom: 16px; text-transform: uppercase;">Price</div>
                <div style="display: flex; flex-direction: column; gap: 12px;">
                    <label style="display: flex; align-items: center; gap: 8px; font-size: 14px; color: var(--text-muted);">
                        <input type="checkbox" style="accent-color: var(--primary);"> Under ₹500
                    </label>
                    <label style="display: flex; align-items: center; gap: 8px; font-size: 14px; color: var(--text-muted);">
                        <input type="checkbox" style="accent-color: var(--primary);"> ₹500 to ₹1000
                    </label>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main style="flex-grow: 1;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; border-bottom: 1px solid var(--border); padding-bottom: 12px;">
                <h1 style="font-size: 18px; font-weight: 700;"><?php echo $category_id ? $categories[array_search($category_id, array_column($categories, 'id'))]['name'] : 'Fresh Catalog'; ?> - <?php echo count($products); ?> items</h1>
                
                <form action="" method="GET" id="sortForm">
                    <?php if($category_id): ?><input type="hidden" name="category" value="<?php echo $category_id; ?>"><?php endif; ?>
                    <select name="sort" onchange="this.form.submit()" style="padding: 8px 12px; border: 1px solid var(--border); border-radius: 4px; font-size: 14px; outline: none;">
                        <option value="newest" <?php echo $sort == 'newest' ? 'selected' : ''; ?>>Sort by: Newest First</option>
                        <option value="price_low" <?php echo $sort == 'price_low' ? 'selected' : ''; ?>>Price: Low to High</option>
                        <option value="price_high" <?php echo $sort == 'price_high' ? 'selected' : ''; ?>>Price: High to Low</option>
                    </select>
                </form>
            </div>

            <?php if (empty($products)): ?>
                <div style="text-align: center; padding: 100px 0;">
                    <i class="fas fa-search" style="font-size: 48px; color: var(--border); margin-bottom: 20px;"></i>
                    <h2 style="color: var(--text-muted);">No products found</h2>
                    <p>Try adjusting your filters or search terms.</p>
                </div>
            <?php else: ?>
                <div class="product-grid">
                    <?php foreach ($products as $product): ?>
                    <div class="product-card" onclick="window.location.href='product-detail.php?id=<?php echo $product['id']; ?>'">
                        <div class="product-image-wrap">
                            <?php 
                            $img_src = (strpos($product['image'], 'http') === 0) ? $product['image'] : '../assets/images/' . ($product['image'] ?: 'default.png');
                            ?>
                            <img src="<?php echo $img_src; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                            <button class="wishlist-btn <?php echo isLoggedIn() && isInWishlist($conn, $_SESSION['user_id'], $product['id']) ? 'active' : ''; ?>" 
                                    onclick="event.stopPropagation(); toggleWishlist(<?php echo $product['id']; ?>, this)">
                                <i class="<?php echo isLoggedIn() && isInWishlist($conn, $_SESSION['user_id'], $product['id']) ? 'fas' : 'far'; ?> fa-heart"></i>
                            </button>
                        </div>
                        <div class="product-info">
                            <div style="color: var(--primary-dark); font-weight: 800; font-size: 0.7rem; text-transform: uppercase; margin-bottom: 0.4rem;"><?php echo htmlspecialchars($product['category_name']); ?></div>
                            <div class="product-title"><?php echo htmlspecialchars($product['name']); ?></div>
                            
                            <?php $card_rating = getProductRating($conn, $product['id']); ?>
                            <div style="display: flex; align-items: center; gap: 0.5rem; margin-top: 0.5rem; font-size: 0.75rem;">
                                <div style="color: #fbbf24;">
                                    <?php 
                                    $stars = floor($card_rating['rating']);
                                    for($i=1; $i<=5; $i++) echo '<i class="' . ($i<=$stars ? 'fas' : 'far') . ' fa-star"></i>';
                                    ?>
                                </div>
                                <span style="color: var(--text-muted); font-weight: 600;">(<?php echo $card_rating['count']; ?>)</span>
                            </div>

                            <div style="display: flex; align-items: center; gap: 0.75rem; margin: 0.75rem 0;">
                                <span class="price-tag"><?php echo formatCurrency(getDiscountedPrice($product['price'], $product['discount'])); ?></span>
                                <?php if ($product['discount'] > 0): ?>
                                    <span style="text-decoration: line-through; color: var(--text-muted); font-size: 0.8rem;"><?php echo formatCurrency($product['price']); ?></span>
                                <?php endif; ?>
                            </div>
                            <button class="btn btn-primary" style="width: 100%; height: 40px; padding: 0; border-radius: 8px;" onclick="event.stopPropagation(); addToCart(<?php echo $product['id']; ?>, 1)">
                                Add to Bag
                            </button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <?php if($total_pages > 1): ?>
                <div class="pagination" style="margin-top: 60px;">
                    <?php for($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="products.php?page=<?php echo $i; ?><?php echo $category_id ? '&category='.$category_id : ''; ?><?php echo $search ? '&search='.urlencode($search) : ''; ?><?php echo $sort ? '&sort='.$sort : ''; ?>" 
                           class="page-btn <?php echo $page == $i ? 'active' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                </div>
                <?php endif; ?>
            <?php endif; ?>
        </main>
    </div>
</div>


<?php include_once $_SERVER['DOCUMENT_ROOT'] . '/CoGroCart/includes/footer.php'; ?>
