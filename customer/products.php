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

<div class="container animate__animated animate__fadeIn">
    <div class="shop-layout">
        <!-- Sidebar Filters -->
        <aside class="filters-sidebar">
            <div class="filter-card">
                <div class="filter-title"><i class="fas fa-filter"></i> Categories</div>
                <ul class="category-list">
                    <li class="category-item">
                        <a href="products.php" class="category-link <?php echo !$category_id ? 'active' : ''; ?>">
                            <span>All Products</span>
                            <i class="fas fa-chevron-right" style="font-size: 0.7rem;"></i>
                        </a>
                    </li>
                    <?php foreach ($categories as $cat): ?>
                    <li class="category-item">
                        <a href="products.php?category=<?php echo $cat['id']; ?>" class="category-link <?php echo $category_id == $cat['id'] ? 'active' : ''; ?>">
                            <span><?php echo htmlspecialchars($cat['name']); ?></span>
                            <i class="fas fa-chevron-right" style="font-size: 0.7rem;"></i>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>

                <hr style="margin: 2rem 0; border: none; border-top: 1px solid var(--border);">

                <div class="filter-title"><i class="fas fa-gift"></i> Special Deals</div>
                <p style="font-size: 0.85rem; color: var(--text-muted);">Check our limited time offers and save big on your daily groceries.</p>
                <a href="#" class="btn btn-primary" style="margin-top: 1.5rem; width: 100%; font-size: 0.85rem;">View Offers</a>
            </div>
        </aside>

        <!-- Main Content -->
        <main>
            <div class="shop-header">
                <div>
                    <h1 style="font-size: 2rem;"><?php echo $category_id ? $categories[array_search($category_id, array_column($categories, 'id'))]['name'] : 'Fresh Catalog'; ?></h1>
                    <p style="color: var(--text-muted);">Showing <?php echo count($products); ?> of <?php echo $total_products; ?> products</p>
                </div>
                
                <form action="" method="GET" id="sortForm">
                    <?php if($category_id): ?><input type="hidden" name="category" value="<?php echo $category_id; ?>"><?php endif; ?>
                    <?php if($search): ?><input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>"><?php endif; ?>
                    <select name="sort" class="sort-select" onchange="this.form.submit()">
                        <option value="newest" <?php echo $sort == 'newest' ? 'selected' : ''; ?>>Newest First</option>
                        <option value="price_low" <?php echo $sort == 'price_low' ? 'selected' : ''; ?>>Price: Low to High</option>
                        <option value="price_high" <?php echo $sort == 'price_high' ? 'selected' : ''; ?>>Price: High to Low</option>
                        <option value="name" <?php echo $sort == 'name' ? 'selected' : ''; ?>>Name: A-Z</option>
                    </select>
                </form>
            </div>

            <?php if(empty($products)): ?>
                <div style="text-align: center; padding: 5rem 0; border: 2px dashed var(--border); border-radius: 2rem;">
                    <i class="fas fa-search" style="font-size: 4rem; color: var(--border); margin-bottom: 2rem;"></i>
                    <h3 style="color: var(--text-muted);">No products found matching your criteria.</h3>
                    <a href="products.php" class="btn btn-primary" style="margin-top: 1.5rem;">Browse All Products</a>
                </div>
            <?php else: ?>
                <div class="grid-auto">
                    <?php foreach ($products as $product): ?>
                    <div class="product-card">
                        <div class="product-image">
                            <?php if ($product['image']): ?>
                                <img src="../assets/images/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                            <?php else: ?>
                                <div style="height: 100%; display: flex; align-items: center; justify-content: center; background: #f1f5f9;">
                                    <i class="fas fa-image" style="font-size: 3rem; color: #cbd5e1;"></i>
                                </div>
                            <?php endif; ?>
                            <?php if ($product['discount'] > 0): ?>
                                <span class="product-badge">-<?php echo $product['discount']; ?>%</span>
                            <?php endif; ?>
                        </div>
                        <div class="product-info">
                            <div style="font-size: 0.75rem; color: var(--primary); font-weight: 700; text-transform: uppercase; margin-bottom: 0.25rem;"><?php echo htmlspecialchars($product['category_name']); ?></div>
                            <h3 class="product-name" style="font-weight: 700; height: 3rem; overflow: hidden;"><?php echo htmlspecialchars($product['name']); ?></h3>
                            <div class="product-price">
                                <span class="current-price" style="font-size: 1.35rem;"><?php echo formatCurrency(getDiscountedPrice($product['price'], $product['discount'])); ?></span>
                                <?php if ($product['discount'] > 0): ?>
                                    <span class="original-price"><?php echo formatCurrency($product['price']); ?></span>
                                <?php endif; ?>
                            </div>
                            
                            <div style="margin-top: auto; display: flex; gap: 0.5rem;">
                                <?php if (isLoggedIn() && isCustomer()): ?>
                                    <button class="btn btn-primary" style="flex: 1;" onclick="addToCart(<?php echo $product['id']; ?>, 1)">
                                        <i class="fas fa-shopping-basket"></i> Add
                                    </button>
                                <?php else: ?>
                                    <a href="login.php" class="btn btn-secondary btn-block">Login to Shop</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <?php if($total_pages > 1): ?>
                <div class="pagination">
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
