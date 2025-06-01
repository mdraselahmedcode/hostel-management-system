<?php
// session_start();
require_once __DIR__ . '/../../config/config.php';  // Adjust path to config.php

if (!isset($_SESSION['admin'])) {
    // Use URL base path for redirect
    header('Location: ' . BASE_URL . '/admin/login_admin.php');
    exit;
}
