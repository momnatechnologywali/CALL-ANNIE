<?php
// logout.php - Fixed version
session_start();
session_destroy();
header('Location: index.php');
exit;
?>
