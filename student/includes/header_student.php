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
    <link rel="stylesheet" href="<?= BASE_URL ?>/student/assets/css/common_student.css">
</head>

<body>

    <!-- ../includes/header.php -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light fixed-header">
    <div class="container">
        <a class="navbar-brand" style="cursor: pointer">Hostel Management</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="<?= BASE_URL . '/index.php' ?>">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/admin/login_admin.php">Admin Login</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/student/login_student.php">Student Login</a>
                </li>
            </ul>
        </div>
    </div>
</nav>




    
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelector('.navbar-brand')?.addEventListener('click', function () {
            // window.location.href = BASE_URL + '/admin/login_admin.php';
            window.location.href = "<?= BASE_URL ?>/student/login_student.php";
        });
    });
</script>


    

    <!-- Local jQuery -->
    <script src="<?= BASE_URL ?>/vendor/jquery/jquery.min.js"></script>
    <!-- Local Bootstrap Bundle JS -->
    <script src="<?= BASE_URL ?>/vendor/bootstrap/js/bootstrap.bundle.js"></script>


    