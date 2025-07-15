<?php
require_once __DIR__ . '/../../config/config.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hostel Management</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/vendor/bootstrap/css/bootstrap.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- <link rel="stylesheet" href="<?= BASE_URL . '/admin/assets/css/common_admin.css' ?>"> -->

    <style>
        :root {
            --primary-dark: #2c3e50;
            --secondary-dark: #1a2530;
            --highlight-blue: #3c8dbc;
        }
        
        .admin-header {
            background: var(--primary-dark);
            color: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .admin-header .navbar-brand {
            color: white;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }
        
        .admin-header .navbar-brand:hover {
            color: var(--highlight-blue);
        }
        
        .admin-footer {
            background: var(--secondary-dark);
            color: rgba(255,255,255,0.7);
            border-top: 1px solid rgba(255,255,255,0.1);
            font-size: 0.9rem;
        }
        
        .admin-footer small {
            color: rgba(255,255,255,0.6);
        }
        
        .fixed-header {
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1030;
        }
        
        .fixed-footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            z-index: 1030;
        }
        
        main {
            padding-top: 60px; /* Header height */
            padding-bottom: 60px; /* Footer height */
            min-height: 80vh;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <nav class="navbar navbar-expand-lg navbar-dark admin-header fixed-header">
        <div class="container">
            <a class="navbar-brand" href="<?= BASE_URL ?>/admin/dashboard_admin.php">
                <i class="bi bi-shield-lock me-2"></i>Hostel Management System
            </a>
        </div>
    </nav>

    
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelector('.navbar-brand')?.addEventListener('click', function () {
            // window.location.href = BASE_URL + '/admin/login_admin.php';
            window.location.href = "<?= BASE_URL ?>/admin/login_admin.php";
        });
    });
</script>


    

    <!-- Local jQuery -->
    <script src="<?= BASE_URL ?>/vendor/jquery/jquery.min.js"></script>
    <!-- Local Bootstrap Bundle JS -->
    <script src="<?= BASE_URL ?>/vendor/bootstrap/js/bootstrap.bundle.js"></script>
