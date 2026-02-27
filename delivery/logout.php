<?php
session_start();
session_destroy();
header("Location: " . rtrim(str_replace('\\', '/', dirname(dirname(__FILE__))), '/') . "/delivery/login.php");
exit;
?>
