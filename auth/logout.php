<?php
// auth/logout.php
require_once '../includes/security.php';
session_start();
session_destroy();   // Destroy all session data

// Redirect to home page
header("Location: ../public/index.php");
exit;
?>