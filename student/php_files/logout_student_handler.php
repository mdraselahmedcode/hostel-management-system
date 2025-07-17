<?php
require_once __DIR__ . '/../../config/config.php';
require_once BASE_PATH . '/config/auth.php'; 

require_student(); 

session_unset();
session_destroy();

// Redirect to login page
header('Location: ' . dirname(dirname($_SERVER['REQUEST_URI'])) . '/login.php');
exit;