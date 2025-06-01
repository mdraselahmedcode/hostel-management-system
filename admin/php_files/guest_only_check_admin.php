<?php
require_once __DIR__ . '/../../config/config.php'; // Adjust relative path to config.php

// Now use BASE_PATH for includes, filesystem access, etc.
require_once BASE_PATH . '/config/db.php'; // example

// Use BASE_URL for URL redirects and routing
$currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$indexPaths = [BASE_URL . '/', BASE_URL . '/index.php'];
$loginPath = BASE_URL . '/admin/login_admin.php';

if (isset($_SESSION['admin'])) {
    if (in_array($currentPath, $indexPaths, true) || $currentPath === $loginPath) {
        header("Location: " . BASE_URL . "/admin/dashboard_admin.php");
        exit;
    }
}
