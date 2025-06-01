<?php
    include __DIR__ . '/../../config/config.php';  // Adjust path to config.php
    session_start(); 
    session_destroy(); 
    header("Location: " . BASE_URL . "/admin/login_admin.php");
    exit; 
?>