<?php
$page_title = 'Notifications';
if (session_status() === PHP_SESSION_NONE) session_start();
include_once $_SERVER['DOCUMENT_ROOT'] . '/CoGroCart/config/functions.php';

if (!isDeliveryPartner()) {
    header("Location: " . BASE_URL . "delivery/login.php");
    exit;
}
$user_id = $_SESSION['user_id'];

// Mark all as read
if (isset($_GET['mark_read'])) {
    $conn->query("UPDATE notifications SET is_read = 1 WHERE role = 'delivery' AND user_id = $user_id");
    echo "<script>location.href='notifications.php';</script>";
}

$notifications = $conn->query("SELECT * FROM notifications WHERE role = 'delivery' AND user_id = $user_id ORDER BY created_at DESC LIMIT 50")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications - Partner</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Outfit:wght@600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --p: #fbbf24; --s: #111; --white: #fff; --bg: #f8fafc; }
        body { margin: 0; font-family: 'Inter', sans-serif; background: var(--bg); color: #334155; }
        .ops-nav { background: var(--p); padding: 1.5rem 3%; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); position: sticky; top: 0; z-index: 100; }
        .ops-logo { font-family: 'Outfit'; font-size: 1.5rem; font-weight: 900; color: #111; display: flex; align-items: center; gap: 0.5rem; text-decoration: none; }
        .ops-container { max-width: 1400px; margin: 3rem auto; padding: 0 2rem; }
        .order-card { background: var(--white); border-radius: 2rem; border: 1px solid #e2e8f0; padding: 2.5rem; margin-bottom: 2rem; transition: 0.3s; }
        .btn-secondary { background: #f1f5f9; color: var(--s); border: 1px solid #e2e8f0; padding: 0.75rem 1.5rem; border-radius: 1rem; text-decoration: none; font-weight: 600; font-size: 0.9rem; transition: 0.3s; }
        .btn-secondary:hover { background: #e2e8f0; }
    </style>
</head>
<body>
    <nav class="ops-nav">
        <a href="index.php" class="ops-logo"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
    </nav>
    <div class="ops-container">
        <div class="order-card" style="margin-top:2rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; border-bottom: 2px solid #f1f5f9; padding-bottom: 1.5rem;">
                <div>
                    <h1 style="font-size: 2rem; margin: 0; font-family:'Outfit'">My Notifications</h1>
                    <p style="color: #64748b; margin:0.5rem 0 0;">Recent task assignments and updates.</p>
                </div>
                <a href="?mark_read=1" class="btn-secondary"><i class="fas fa-check-double"></i> Mark All as Read</a>
            </div>

            <?php if(empty($notifications)): ?>
                <div style="text-align: center; padding: 4rem; color: #94a3b8;">
                    <i class="far fa-bell-slash" style="font-size: 3rem; margin-bottom: 1rem; color: #cbd5e1;"></i>
                    <h3>You're all caught up!</h3>
                </div>
            <?php else: ?>
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <?php foreach($notifications as $n): ?>
                        <a href="<?php echo BASE_URL; ?>api/read_notification.php?id=<?php echo $n['id']; ?>" style="text-decoration: none; color: inherit; display: flex; align-items: flex-start; gap: 1.5rem; padding: 1.5rem; border-radius: 1rem; border: 1px solid #e2e8f0; background: <?php echo $n['is_read'] ? 'white' : '#fef3c7'; ?>; transition: 0.3s;">
                            <div style="width: 48px; height: 48px; border-radius: 50%; background: <?php echo $n['is_read'] ? '#e2e8f0' : '#f59e0b'; ?>; color: <?php echo $n['is_read'] ? '#64748b' : '#111'; ?>; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; flex-shrink: 0;">
                                <i class="fas fa-bell"></i>
                            </div>
                            <div style="flex: 1;">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.25rem;">
                                    <span style="font-weight: <?php echo $n['is_read'] ? '600' : '800'; ?>; color: var(--s); font-size: 1.1rem;"><?php echo htmlspecialchars($n['title']); ?></span>
                                    <span style="font-size: 0.8rem; color: #94a3b8; font-weight: 600;"><?php echo formatDate($n['created_at']); ?></span>
                                </div>
                                <p style="color: #64748b; font-size: 0.95rem; margin-top: 0.5rem; line-height: 1.5;"><?php echo htmlspecialchars($n['message']); ?></p>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
