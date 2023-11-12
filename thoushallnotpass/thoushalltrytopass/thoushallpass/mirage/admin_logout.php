<?php
session_start();
unset($_SESSION['authenticated']);
session_destroy();

header("Location: ../../admin_login.php");
exit;
?>
