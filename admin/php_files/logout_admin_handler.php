<?php
include __DIR__ . '/../../config/config.php';  
require_once BASE_PATH . '/config/auth.php';


// Start session
require_admin(); 

// Save the flash message temporarily
$flashMessage = 'You have been logged out successfully.';

// Destroy the current session
session_unset();
session_destroy();

// Start a new session and set the flash message again
session_start();
$_SESSION['logout_success'] = $flashMessage;

// Redirect to login page
header("Location: " . BASE_URL . "/admin/login.php");
exit;
