<!-- ../includes/header.php -->
<?php
// include('./php_files/auth_check_admin.php');
require_once __DIR__ . '/../../config/config.php';  // Adjust path as needed

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hostel Management</title>
    <!-- Local Bootstrap CSS -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/admin/assets/css/common_admin.css">
</head>

<body>

    <!-- ../includes/header.php -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light fixed-header">
        <div class="container">
            <a class="navbar-brand" style="cursor: pointer">Hostel Management</a>
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
