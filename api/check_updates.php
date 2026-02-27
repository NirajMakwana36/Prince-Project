<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include_once '../config/db_config.php';

header('Content-Type: application/json');

$response = [
    'has_updates' => false,
    'last_time' => ''
];

$role = $_SESSION['user_role'] ?? '';
$user_id = $_SESSION['user_id'] ?? 0;
$client_last_time = $_GET['last_time'] ?? '2000-01-01 00:00:00';

$max_time = '2000-01-01 00:00:00';

if ($role == 'admin') {
    $o_res = $conn->query("SELECT MAX(updated_at) as t FROM orders");
    $n_res = $conn->query("SELECT MAX(created_at) as t FROM notifications WHERE role = 'admin'");
    $o_time = $o_res->fetch_assoc()['t'] ?? '2000-01-01 00:00:00';
    $n_time = $n_res->fetch_assoc()['t'] ?? '2000-01-01 00:00:00';
    $max_time = max($o_time, $n_time);

} elseif ($role == 'delivery' && $user_id > 0) {
    $o_res = $conn->query("SELECT MAX(updated_at) as t FROM orders WHERE (delivery_partner_id = $user_id) OR (delivery_partner_id IS NULL OR delivery_partner_id = 0)");
    $n_res = $conn->query("SELECT MAX(created_at) as t FROM notifications WHERE role = 'delivery' AND user_id = $user_id");
    $o_time = $o_res->fetch_assoc()['t'] ?? '2000-01-01 00:00:00';
    $n_time = $n_res->fetch_assoc()['t'] ?? '2000-01-01 00:00:00';
    $max_time = max($o_time, $n_time);

} elseif ($role == 'customer' && $user_id > 0) {
    $oid = intval($_GET['order_id'] ?? 0);
    $o_time = '2000-01-01 00:00:00';
    if ($oid > 0) {
        $o_res = $conn->query("SELECT updated_at as t FROM orders WHERE id = $oid AND user_id = $user_id");
        if($o_res && $row = $o_res->fetch_assoc()) {
            $o_time = $row['t'] ?? '2000-01-01 00:00:00';
        }
    }
    $n_res = $conn->query("SELECT MAX(created_at) as t FROM notifications WHERE role = 'customer' AND user_id = $user_id");
    $n_time = $n_res->fetch_assoc()['t'] ?? '2000-01-01 00:00:00';
    $max_time = max($o_time, $n_time);
}

if (strtotime($max_time) > strtotime($client_last_time)) {
    $response['has_updates'] = true;
}
$response['last_time'] = $max_time;

echo json_encode($response);
