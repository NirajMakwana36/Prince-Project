<?php
$page_title = 'Categories';
include_once 'includes/header.php';

$action = $_GET['action'] ?? 'list';

// Handle Add/Edit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = intval($_POST['id'] ?? 0);
    $name = sanitize($_POST['name']);
    $desc = sanitize($_POST['description']);
    $image = sanitize($_POST['image_filename'] ?? '');

    if ($id > 0) {
        $stmt = $conn->prepare("UPDATE categories SET name=?, description=?, image=? WHERE id=?");
        $stmt->bind_param("sssi", $name, $desc, $image, $id);
    } else {
        $stmt = $conn->prepare("INSERT INTO categories (name, description, image) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $desc, $image);
    }

    if ($stmt->execute()) {
        header("Location: categories.php?msg=success");
        exit;
    }
}

// Handle Delete
if ($action == 'delete' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $conn->query("DELETE FROM categories WHERE id = $id");
    header("Location: categories.php?msg=deleted");
    exit;
}

$categories = getAllCategories($conn);
$edit_cat = null;
if ($action == 'edit' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $edit_cat = $conn->query("SELECT * FROM categories WHERE id = $id")->fetch_assoc();
}
?>

<div class="">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 3rem;">
        <div>
            <h1 style="font-size: 2.5rem;"><?php echo $action == 'list' ? 'Categories' : ($action == 'add' ? 'Add Category' : 'Edit Category'); ?></h1>
            <p style="color: #64748b;">Organize your product catalog efficiently.</p>
        </div>
        <?php if($action == 'list'): ?>
        <a href="?action=add" class="btn btn-primary"><i class="fas fa-plus"></i> New Category</a>
        <?php else: ?>
        <a href="categories.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back to List</a>
        <?php endif; ?>
    </div>

    <?php if($action == 'add' || $action == 'edit'): ?>
    <div class="admin-card">
        <form method="POST">
            <input type="hidden" name="id" value="<?php echo $edit_cat['id'] ?? 0; ?>">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                <div class="form-group">
                    <label>Category Name</label>
                    <input type="text" name="name" class="form-control" value="<?php echo $edit_cat['name'] ?? ''; ?>" required>
                </div>
                <div class="form-group">
                    <label>Image Filename</label>
                    <input type="text" name="image_filename" class="form-control" placeholder="veg.jpg" value="<?php echo $edit_cat['image'] ?? ''; ?>">
                </div>
            </div>
            <div class="form-group" style="margin-top: 1.5rem;">
                <label>Description</label>
                <textarea name="description" class="form-control" rows="4"><?php echo $edit_cat['description'] ?? ''; ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary btn-lg" style="margin-top: 2rem;">Save Category</button>
        </form>
    </div>
    <?php else: ?>
    <div class="admin-card" style="padding: 0;">
        <table class="admin-table">
            <thead>
                <tr>
                    <th style="padding-left: 2.5rem;">Category</th>
                    <th>Description</th>
                    <th>Products</th>
                    <th style="padding-right: 2.5rem;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($categories as $c): 
                    $pid = $c['id'];
                    $count = $conn->query("SELECT COUNT(*) as total FROM products WHERE category_id = $pid")->fetch_assoc()['total'];
                ?>
                <tr>
                    <td style="padding-left: 2.5rem;">
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <?php 
                            $img_src = (strpos($c['image'], 'http') === 0) ? $c['image'] : '../assets/images/' . ($c['image'] ?: 'default-cat.png');
                            ?>
                            <img src="<?php echo $img_src; ?>" style="width: 50px; height: 50px; border-radius: 1rem; object-fit: cover; background: #f1f5f9;">
                            <span style="font-weight: 700; font-size: 1.1rem;"><?php echo htmlspecialchars($c['name']); ?></span>
                        </div>
                    </td>
                    <td style="color: #64748b; max-width: 400px;"><?php echo htmlspecialchars($c['description']); ?></td>
                    <td><span style="font-weight: 700; background: #eff6ff; color: #1e40af; padding: 0.5rem 1rem; border-radius: 2rem;"><?php echo $count; ?> Items</span></td>
                    <td style="padding-right: 2.5rem;">
                        <div style="display: flex; gap: 0.5rem;">
                            <a href="?action=edit&id=<?php echo $c['id']; ?>" class="btn btn-secondary btn-sm" style="padding: 0.5rem;"><i class="fas fa-edit"></i></a>
                            <a href="?action=delete&id=<?php echo $c['id']; ?>" class="btn btn-secondary btn-sm" style="padding: 0.5rem; color: #ef4444;" onclick="return confirm('Delete this category?')"><i class="fas fa-trash"></i></a>
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
