<?php
$page_title = 'Notifications';
include_once 'includes/header.php';

// Mark all as read
if (isset($_GET['mark_read'])) {
    $conn->query("UPDATE notifications SET is_read = 1 WHERE role = 'admin'");
    echo "<script>location.href='notifications.php';</script>";
}

$notifications = $conn->query("SELECT * FROM notifications WHERE role = 'admin' ORDER BY created_at DESC LIMIT 50")->fetch_all(MYSQLI_ASSOC);
?>

<div class="admin-card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; border-bottom: 2px solid #f1f5f9; padding-bottom: 1.5rem;">
        <div>
            <h1 style="font-size: 2rem;">Notifications</h1>
            <p style="color: #64748b;">Important updates and recent activity logs.</p>
        </div>
        <a href="?mark_read=1" class="btn btn-secondary"><i class="fas fa-check-double"></i> Mark All as Read</a>
    </div>

    <?php if(empty($notifications)): ?>
        <div style="text-align: center; padding: 4rem; color: #94a3b8;">
            <i class="far fa-bell-slash" style="font-size: 3rem; margin-bottom: 1rem; color: #cbd5e1;"></i>
            <h3>You're all caught up!</h3>
            <p>No new notifications at the moment.</p>
        </div>
    <?php else: ?>
        <div style="display: flex; flex-direction: column; gap: 1rem;">
            <?php foreach($notifications as $n): ?>
                <a href="<?php echo BASE_URL; ?>api/read_notification.php?id=<?php echo $n['id']; ?>" style="text-decoration: none; color: inherit; display: flex; align-items: flex-start; gap: 1.5rem; padding: 1.5rem; border-radius: 1rem; border: 1px solid var(--border); background: <?php echo $n['is_read'] ? 'white' : '#f8fafc'; ?>; transition: var(--transition);">
                    <div style="width: 48px; height: 48px; border-radius: 50%; background: <?php echo $n['is_read'] ? '#e2e8f0' : 'var(--primary-light)'; ?>; color: <?php echo $n['is_read'] ? '#64748b' : 'var(--primary-dark)'; ?>; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; flex-shrink: 0;">
                        <i class="fas fa-bell"></i>
                    </div>
                    <div style="flex: 1;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.25rem;">
                            <span style="font-weight: <?php echo $n['is_read'] ? '600' : '800'; ?>; color: var(--secondary); font-size: 1.1rem;"><?php echo htmlspecialchars($n['title']); ?></span>
                            <span style="font-size: 0.8rem; color: #94a3b8; font-weight: 600;"><?php echo formatDate($n['created_at']); ?></span>
                        </div>
                        <p style="color: #64748b; font-size: 0.95rem; margin-top: 0.5rem; line-height: 1.5;"><?php echo htmlspecialchars($n['message']); ?></p>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include_once 'includes/footer.php'; ?>
