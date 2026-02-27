<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include_once '../config/db_config.php';
include_once '../config/functions.php';

$id = intval($_GET['id'] ?? 0);
if ($id > 0) {
    if (isset($_SESSION['user_id'])) {
        $conn->query("UPDATE notifications SET is_read = 1 WHERE id = $id");
    } else {
        $conn->query("UPDATE notifications SET is_read = 1 WHERE id = $id AND role = 'admin'");
    }
    
    $notif = $conn->query("SELECT link, role FROM notifications WHERE id = $id")->fetch_assoc();
    if ($notif) {
        $base = BASE_URL;
        if ($notif['role'] == 'admin') {
            $url = $base . 'admin/' . $notif['link'];
        } elseif ($notif['role'] == 'customer') {
            $url = $base . 'customer/' . $notif['link'];
        } elseif ($notif['role'] == 'delivery') {
            $url = $base . 'delivery/' . $notif['link'];
        } else {
            $url = $base . ltrim($notif['link'], '/');
        }
        header("Location: " . $url);
        exit;
    }
}
header("Location: " . BASE_URL);
exit;
?>
