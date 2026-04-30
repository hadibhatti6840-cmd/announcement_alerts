<?php
require_once 'includes/functions.php';
redirectDashboard(); // Redirect if already logged in
header("Location: login.php");
exit();
?>
