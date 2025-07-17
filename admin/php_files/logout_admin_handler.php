<?php
    include __DIR__ . '/../../config/config.php';  // Adjust path to config.php
    // only admin will get access
    require_once BASE_PATH . '/config/auth.php';

    require_admin();

    session_destroy(); 
    header("Location: " . BASE_URL . "/admin/login.php");
    exit; 
?>