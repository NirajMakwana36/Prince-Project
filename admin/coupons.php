<?php
$page_title = 'Coupons';
include_once 'includes/header.php';

$action = $_GET['action'] ?? 'list';

// Handle Add/Edit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = intval($_POST['id'] ?? 0);
    $code = sanitize(strtoupper($_POST['code']));
    $type = sanitize($_POST['discount_type']);
    $val = floatval($_POST['discount_value']);
    $min = floatval($_POST['min_purchase']);
    $expiry = sanitize($_POST['expiry_date']);
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    if ($id > 0) {
        $stmt = $conn->prepare("UPDATE coupons SET code=?, discount_type=?, discount_value=?, min_purchase=?, expiry_date=?, is_active=? WHERE id=?");
        $stmt->bind_param("ssddsii", $code, $type, $val, $min, $expiry, $is_active, $id);
    } else {
        $stmt = $conn->prepare("INSERT INTO coupons (code, discount_type, discount_value, min_purchase, expiry_date, is_active) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssddsi", $code, $type, $val, $min, $expiry, $is_active);
    }

    if ($stmt->execute()) {
        header("Location: coupons.php?msg=success");
        exit;
    }
}

// Handle Delete
if ($action == 'delete' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $conn->query("DELETE FROM coupons WHERE id = $id");
    header("Location: coupons.php?msg=deleted");
    exit;
}

$coupons = $conn->query("SELECT * FROM coupons ORDER BY created_at DESC")->fetch_all(MYSQLI_ASSOC);
$edit_coupon = null;
if ($action == 'edit' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $edit_coupon = $conn->query("SELECT * FROM coupons WHERE id = $id")->fetch_assoc();
}
?>

<div class="">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 3rem;">
        <div>
            <h1 style="font-size: 2.5rem;"><?php echo $action == 'list' ? 'Promo Coupons' : ($action == 'add' ? 'Create Coupon' : 'Edit Coupon'); ?></h1>
            <p style="color: #64748b;">Manage discounts and promotional codes.</p>
        </div>
        <?php if($action == 'list'): ?>
        <a href="?action=add" class="btn btn-primary"><i class="fas fa-plus"></i> New Coupon</a>
        <?php else: ?>
        <a href="coupons.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back to List</a>
        <?php endif; ?>
    </div>

    <?php if($action == 'add' || $action == 'edit'): ?>
    <div class="admin-card">
        <form method="POST">
            <input type="hidden" name="id" value="<?php echo $edit_coupon['id'] ?? 0; ?>">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                <div class="form-group">
                    <label>Coupon Code</label>
                    <input type="text" name="code" class="form-control" placeholder="E.g. FIRST50" value="<?php echo $edit_coupon['code'] ?? ''; ?>" required>
                </div>
                <div class="form-group">
                    <label>Discount Type</label>
                    <select name="discount_type" class="form-control">
                        <option value="percentage" <?php echo (isset($edit_coupon) && $edit_coupon['discount_type'] == 'percentage') ? 'selected' : ''; ?>>Percentage (%)</option>
                        <option value="fixed" <?php echo (isset($edit_coupon) && $edit_coupon['discount_type'] == 'fixed') ? 'selected' : ''; ?>>Fixed Amount (₹)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Discount Value</label>
                    <input type="number" step="0.01" name="discount_value" class="form-control" value="<?php echo $edit_coupon['discount_value'] ?? ''; ?>" required>
                </div>
                <div class="form-group">
                    <label>Min Purchase Requirement (₹)</label>
                    <input type="number" step="0.01" name="min_purchase" class="form-control" value="<?php echo $edit_coupon['min_purchase'] ?? '0'; ?>">
                </div>
                <div class="form-group">
                    <label>Expiry Date</label>
                    <input type="date" name="expiry_date" class="form-control" value="<?php echo $edit_coupon['expiry_date'] ?? ''; ?>">
                </div>
                <div class="form-group" style="display: flex; align-items: center; gap: 1rem; margin-top: 2rem;">
                    <input type="checkbox" name="is_active" style="width: 20px; height: 20px;" <?php echo (!isset($edit_coupon) || $edit_coupon['is_active']) ? 'checked' : ''; ?>>
                    <label style="margin: 0; font-weight: 700;">Coupon is Active</label>
                </div>
            </div>
            <button type="submit" class="btn btn-primary btn-lg" style="margin-top: 2rem;">Save Coupon</button>
        </form>
    </div>
    <?php else: ?>
    <div class="admin-card" style="padding: 0;">
        <table class="admin-table">
            <thead>
                <tr>
                    <th style="padding-left: 2.5rem;">Code</th>
                    <th>Discount</th>
                    <th>Requirement</th>
                    <th>Expiry</th>
                    <th>Status</th>
                    <th style="padding-right: 2.5rem;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($coupons as $c): ?>
                <tr>
                    <td style="padding-left: 2.5rem;"><strong style="color: var(--primary); font-family: 'Outfit'; font-size: 1.1rem;"><?php echo $c['code']; ?></strong></td>
                    <td style="font-weight: 700;"><?php echo $c['discount_type'] == 'percentage' ? $c['discount_value'].'%' : formatCurrency($c['discount_value']); ?></td>
                    <td>Min: <?php echo formatCurrency($c['min_purchase']); ?></td>
                    <td><?php echo $c['expiry_date'] ? date('M d, Y', strtotime($c['expiry_date'])) : 'Never'; ?></td>
                    <td>
                        <span class="badge" style="background: <?php echo $c['is_active'] ? '#dcfce7' : '#fee2e2'; ?>; color: <?php echo $c['is_active'] ? '#166534' : '#991b1b'; ?>;">
                            <?php echo $c['is_active'] ? 'Active' : 'Expired'; ?>
                        </span>
                    </td>
                    <td style="padding-right: 2.5rem;">
                        <div style="display: flex; gap: 0.5rem;">
                            <a href="?action=edit&id=<?php echo $c['id']; ?>" class="btn btn-secondary btn-sm" style="padding: 0.5rem;"><i class="fas fa-edit"></i></a>
                            <a href="?action=delete&id=<?php echo $c['id']; ?>" class="btn btn-secondary btn-sm" style="padding: 0.5rem; color: #ef4444;" onclick="return confirm('Delete this coupon?')"><i class="fas fa-trash"></i></a>
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
