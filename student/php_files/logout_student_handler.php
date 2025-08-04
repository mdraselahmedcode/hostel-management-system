<?php
require_once __DIR__ . '/../../config/config.php';
require_once BASE_PATH . '/config/auth.php'; 

require_student(); 

// Save the flash message temporarily
$flashMessage = 'You have been logged out successfully.';

// Destroy the current session
session_unset();
session_destroy();

// Start a new session and set the flash message again
session_start();
$_SESSION['logout_success'] = $flashMessage;

// Redirect to student login page
header('Location: ' . dirname(dirname($_SERVER['REQUEST_URI'])) . '/login.php');
exit;
