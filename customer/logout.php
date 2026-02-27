<?php
session_start();
include_once $_SERVER['DOCUMENT_ROOT'] . '/CoGroCart/config/db_config.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/CoGroCart/config/functions.php';

// Destroy session
session_destroy();

// Redirect to home
header("Location: " . BASE_URL);
exit;
?>
