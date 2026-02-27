<?php
$page_title = 'Products';
include_once 'includes/header.php';

$action = $_GET['action'] ?? 'list';
$categories = getAllCategories($conn);

// Handle POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = intval($_POST['id'] ?? 0);
    $name = sanitize($_POST['name']);
    $cat_id = intval($_POST['category_id']);
    $price = floatval($_POST['price']);
    $discount = intval($_POST['discount']);
    $stock = intval($_POST['stock']);
    $desc = sanitize($_POST['description']);
    $is_available = isset($_POST['is_available']) ? 1 : 0;
    
    // Simple file upload logic (simulated for now, actual upload would need $_FILES)
    $image = sanitize($_POST['image_filename'] ?? '');

    if ($id > 0) {
        $stmt = $conn->prepare("UPDATE products SET name=?, category_id=?, price=?, discount=?, stock=?, description=?, image=?, is_available=? WHERE id=?");
        $stmt->bind_param("sididssii", $name, $cat_id, $price, $discount, $stock, $desc, $image, $is_available, $id);
    } else {
        $stmt = $conn->prepare("INSERT INTO products (name, category_id, price, discount, stock, description, image, is_available) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sididssi", $name, $cat_id, $price, $discount, $stock, $desc, $image, $is_available);
    }
    
    if ($stmt->execute()) {
        echo "<script>location.href='products.php?msg=success';</script>";
    }
}

// Handle Delete
if ($action == 'delete' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $conn->query("DELETE FROM products WHERE id = $id");
    echo "<script>location.href='products.php?msg=deleted';</script>";
}

$products = $conn->query("SELECT p.*, c.name as category FROM products p JOIN categories c ON p.category_id = c.id ORDER BY p.created_at DESC")->fetch_all(MYSQLI_ASSOC);
$edit_product = null;
if ($action == 'edit' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $edit_product = $conn->query("SELECT * FROM products WHERE id = $id")->fetch_assoc();
}
?>

<div class="animate__animated animate__fadeIn">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 3rem;">
        <div>
            <h1 style="font-size: 2.5rem;"><?php echo $action == 'list' ? 'Inventory' : ($action == 'add' ? 'Add Product' : 'Edit Product'); ?></h1>
            <p style="color: #64748b;">Manage your product catalog and stock levels.</p>
        </div>
        <?php if($action == 'list'): ?>
        <a href="?action=add" class="btn btn-primary"><i class="fas fa-plus"></i> New Product</a>
        <?php else: ?>
        <a href="products.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back to List</a>
        <?php endif; ?>
    </div>

    <?php if($action == 'add' || $action == 'edit'): ?>
    <div class="admin-card">
        <form method="POST">
            <input type="hidden" name="id" value="<?php echo $edit_product['id'] ?? 0; ?>">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                <div class="form-group">
                    <label>Product Name</label>
                    <input type="text" name="name" class="form-control" value="<?php echo $edit_product['name'] ?? ''; ?>" required>
                </div>
                <div class="form-group">
                    <label>Category</label>
                    <select name="category_id" class="form-control" required>
                        <?php foreach($categories as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>" <?php echo (isset($edit_product) && $edit_product['category_id'] == $cat['id']) ? 'selected' : ''; ?>><?php echo $cat['name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Price (₹)</label>
                    <input type="number" step="0.01" name="price" class="form-control" value="<?php echo $edit_product['price'] ?? ''; ?>" required>
                </div>
                <div class="form-group">
                    <label>Discount (%)</label>
                    <input type="number" name="discount" class="form-control" value="<?php echo $edit_product['discount'] ?? 0; ?>">
                </div>
                <div class="form-group">
                    <label>Stock Quantity</label>
                    <input type="number" name="stock" class="form-control" value="<?php echo $edit_product['stock'] ?? ''; ?>" required>
                </div>
                <div class="form-group">
                    <label>Image Filename</label>
                    <input type="text" name="image_filename" class="form-control" placeholder="tomato.jpg" value="<?php echo $edit_product['image'] ?? ''; ?>">
                </div>
            </div>
            <div class="form-group" style="margin-top: 1.5rem;">
                <label>Description</label>
                <textarea name="description" class="form-control" rows="5"><?php echo $edit_product['description'] ?? ''; ?></textarea>
            </div>
            <div class="form-group" style="display: flex; align-items: center; gap: 1rem; margin-top: 2rem;">
                <input type="checkbox" name="is_available" style="width: 20px; height: 20px;" <?php echo (!isset($edit_product) || $edit_product['is_available']) ? 'checked' : ''; ?>>
                <label style="margin: 0; font-weight: 700;">Available for Sale</label>
            </div>
            <button type="submit" class="btn btn-primary btn-lg" style="margin-top: 2rem;">Save Product</button>
        </form>
    </div>
    <?php else: ?>
    <div class="admin-card" style="padding: 0;">
        <table class="admin-table">
            <thead>
                <tr>
                    <th style="padding-left: 2.5rem;">Product</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Status</th>
                    <th style="padding-right: 2.5rem;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($products as $p): ?>
                <tr>
                    <td style="padding-left: 2.5rem;">
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <img src="../assets/images/<?php echo $p['image'] ?: 'default.png'; ?>" style="width: 50px; height: 50px; border-radius: 1rem; object-fit: cover; background: #f1f5f9;">
                            <div>
                                <div style="font-weight: 700;"><?php echo htmlspecialchars($p['name']); ?></div>
                                <div style="font-size: 0.75rem; color: #64748b;">ID: #<?php echo $p['id']; ?></div>
                            </div>
                        </div>
                    </td>
                    <td><span style="font-weight: 600; font-size: 0.85rem; color: var(--primary); background: var(--primary-light); padding: 0.4rem 0.8rem; border-radius: 2rem;"><?php echo $p['category']; ?></span></td>
                    <td style="font-weight: 700;"><?php echo formatCurrency($p['price']); ?></td>
                    <td>
                        <span style="font-weight: 700; <?php echo $p['stock'] < 10 ? 'color: #ef4444;' : ''; ?>">
                            <?php echo $p['stock']; ?>
                        </span>
                    </td>
                    <td>
                        <span class="badge" style="background: <?php echo $p['is_available'] ? '#dcfce7' : '#fee2e2'; ?>; color: <?php echo $p['is_available'] ? '#166534' : '#991b1b'; ?>;">
                            <?php echo $p['is_available'] ? 'Active' : 'Hidden'; ?>
                        </span>
                    </td>
                    <td style="padding-right: 2.5rem;">
                        <div style="display: flex; gap: 0.5rem;">
                            <a href="?action=edit&id=<?php echo $p['id']; ?>" class="btn btn-secondary btn-sm" style="padding: 0.5rem;"><i class="fas fa-edit"></i></a>
                            <a href="?action=delete&id=<?php echo $p['id']; ?>" class="btn btn-secondary btn-sm" style="padding: 0.5rem; color: #ef4444;" onclick="return confirm('Delete this product?')"><i class="fas fa-trash"></i></a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<?php include_once 'includes/footer.php'; ?>
