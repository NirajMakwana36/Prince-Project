<?php
$page_title = 'User Management';
include_once 'includes/header.php';

// Handle Role Updates
if (isset($_POST['update_role'])) {
    $target_id = intval($_POST['user_id']);
    $new_role = sanitize($_POST['role']);
    
    if ($target_id != $_SESSION['user_id']) { // Don't let admin change their own role to something else
        $stmt = $conn->prepare("UPDATE users SET role = ? WHERE id = ?");
        $stmt->bind_param("si", $new_role, $target_id);
        if ($stmt->execute()) {
            $_SESSION['message'] = "User role updated successfully!";
            $_SESSION['msg_type'] = "success";
        }
    }
}

// Handle User Deletion
if (isset($_GET['delete'])) {
    $target_id = intval($_GET['delete']);
    if ($target_id != $_SESSION['user_id']) {
        $conn->query("DELETE FROM users WHERE id = $target_id");
        $_SESSION['message'] = "User deleted successfully!";
        $_SESSION['msg_type'] = "success";
    }
}

// Pagination
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$per_page = 20;
$offset = ($page - 1) * $per_page;

$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$where = $search ? "WHERE name LIKE '%$search%' OR email LIKE '%$search%'" : "";

$users = $conn->query("SELECT * FROM users $where ORDER BY created_at DESC LIMIT $per_page OFFSET $offset")->fetch_all(MYSQLI_ASSOC);
$total_users = $conn->query("SELECT COUNT(*) as count FROM users $where")->fetch_assoc()['count'];
$total_pages = ceil($total_users / $per_page);
?>

<div class="animate-fade">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 3rem;">
        <div>
            <h1 style="font-size: 2.5rem;">User <span style="color: var(--primary);">Management</span></h1>
            <p style="color: #64748b;">Control access rights and manage all platform users.</p>
        </div>
        <form action="" method="GET" style="display: flex; gap: 1rem;">
            <input type="text" name="search" placeholder="Search name or email..." class="form-control" style="width: 300px;" value="<?php echo $search; ?>">
            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
        </form>
    </div>

    <?php if(isset($_SESSION['message'])): ?>
        <div class="alert alert-<?php echo $_SESSION['msg_type']; ?>" style="margin-bottom: 2rem;">
            <?php echo $_SESSION['message']; unset($_SESSION['message']); unset($_SESSION['msg_type']); ?>
        </div>
    <?php endif; ?>

    <div class="admin-card" style="padding: 0; overflow: hidden;">
        <table class="admin-table">
            <thead>
                <tr>
                    <th style="padding-left: 2.5rem;">Member</th>
                    <th>Email & Phone</th>
                    <th>Current Role</th>
                    <th>Joined Date</th>
                    <th style="padding-right: 2.5rem; text-align: right;">Modify Permissions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($users as $user): ?>
                <tr>
                    <td style="padding-left: 2.5rem;">
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <div style="width: 45px; height: 45px; border-radius: 12px; background: <?php echo $user['role'] == 'admin' ? '#fee2e2' : ($user['role'] == 'delivery' ? '#fef3c7' : '#e0f2fe'); ?>; color: var(--secondary); display: flex; align-items: center; justify-content: center; font-weight: 800;">
                                <i class="fas <?php echo $user['role'] == 'admin' ? 'fa-user-shield' : ($user['role'] == 'delivery' ? 'fa-truck' : 'fa-user'); ?>"></i>
                            </div>
                            <div>
                                <div style="font-weight: 700;"><?php echo htmlspecialchars($user['name']); ?></div>
                                <div style="font-size: 0.75rem; color: #64748b;">ID: #<?php echo $user['id']; ?></div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div style="font-weight: 500;"><?php echo htmlspecialchars($user['email']); ?></div>
                        <div style="font-size: 0.8rem; color: #64748b;"><?php echo htmlspecialchars($user['phone']); ?></div>
                    </td>
                    <td>
                        <span class="badge <?php echo $user['role'] == 'admin' ? 'badge-danger' : ($user['role'] == 'delivery' ? 'badge-warning' : 'badge-success'); ?>">
                            <?php echo strtoupper($user['role']); ?>
                        </span>
                    </td>
                    <td><?php echo date('d M, Y', strtotime($user['created_at'])); ?></td>
                    <td style="padding-right: 2.5rem; text-align: right;">
                        <form method="POST" style="display: inline-flex; gap: 0.5rem; align-items: center;">
                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                            <select name="role" class="form-control" style="width: 140px; padding: 0.4rem 0.8rem; font-size: 0.85rem;">
                                <option value="customer" <?php echo $user['role'] == 'customer' ? 'selected' : ''; ?>>Customer</option>
                                <option value="delivery" <?php echo $user['role'] == 'delivery' ? 'selected' : ''; ?>>Delivery</option>
                                <option value="admin" <?php echo $user['role'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
                            </select>
                            <button type="submit" name="update_role" class="btn btn-primary" style="padding: 0.4rem 0.8rem;"><i class="fas fa-save"></i></button>
                            <?php if($user['id'] != $_SESSION['user_id']): ?>
                                <a href="?delete=<?php echo $user['id']; ?>" class="btn btn-secondary" style="color: #ef4444; padding: 0.4rem 0.8rem;" onclick="return confirm('Absolutely delete this user?')"><i class="fas fa-user-xmark"></i></a>
                            <?php endif; ?>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php if($total_pages > 1): ?>
        <div style="display: flex; justify-content: center; gap: 0.5rem; padding: 2.5rem; background: #f8fafc; border-top: 1px solid var(--border);">
            <?php for($i=1; $i<=$total_pages; $i++): ?>
                <a href="?page=<?php echo $i; ?>&search=<?php echo $search; ?>" class="page-btn <?php echo $page == $i ? 'active' : ''; ?>"><?php echo $i; ?></a>
            <?php endfor; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<style>
    .badge-danger { background: #fee2e2; color: #ef4444; }
    .form-control:focus { border-color: var(--primary); outline: none; box-shadow: 0 0 0 3px rgba(251, 191, 36, 0.1); }
</style>

<?php include_once 'includes/footer.php'; ?>
