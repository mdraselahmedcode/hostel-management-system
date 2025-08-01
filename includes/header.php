<?php
require_once __DIR__ . '/../config/config.php';
$currentPage = basename($_SERVER['SCRIPT_NAME']); // More accurate for subfolders
$currentDir   = basename(dirname($_SERVER['SCRIPT_NAME']));
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>University Hostel Management System</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/vendor/bootstrap/css/bootstrap.min.css" />
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet" />


    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/common.css" />

    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        .nav-link.active {
            font-weight: 600;
            position: relative;
            color: #fff !important;
        }


        .dropdown-menu {
            border: none;
            box-shadow: 0 0.75rem 1.5rem rgba(0, 0, 0, 0.15);
            border-radius: 0.5rem;
            padding: 0.5rem 0;
        }

        .dropdown-item {
            padding: 0.5rem 1.25rem;
            transition: background-color 0.2s ease;
        }

        .dropdown-item:hover {
            background-color: #f0f0f0;
            color: #0d6efd;
        }

        .dropdown-item.active,
        .dropdown-item:active {
            background-color: #0d6efd;
            color: #fff !important;
            font-weight: 500;
        }

        .navbar-toggler {
            border: none;
            font-size: 1.25rem;
        }

        .navbar-toggler:focus {
            box-shadow: none;
        }
    </style>
</head>

<body>
    <!-- Main Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="<?= BASE_URL ?>">
                <i class="fas fa-home-alt me-2"></i>
                <span class="fw-bold">University Hostels</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNavbar">
                <ul class="navbar-nav ms-auto gap-1">
                    <li class="nav-item">
                        <a class="nav-link <?= $currentPage === 'index.php' ? 'active' : '' ?>" href="<?= BASE_URL ?>/index.php">
                            <i class="fas fa-home me-1"></i> Home
                        </a>
                    </li>

                    <!-- Admin Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?= $currentDir === 'admin' && $currentPage === 'login.php' ? 'active' : '' ?>" href="#" id="adminDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-shield me-1"></i> Admin
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item <?= $currentDir === 'admin' && $currentPage === 'login.php' ? 'active' : '' ?>" href="<?= BASE_URL ?>/admin/login.php">Login</a>
                            </li>
                          
                            <!-- <li>
                                <a class="dropdown-item" href="<?= BASE_URL ?>/admin/help.php">Help</a>
                            </li> -->
                        </ul>
                    </li>

                    <!-- Student Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?= $currentDir === 'student' && in_array($currentPage, ['login.php', 'register.php']) || $currentPage === 'checkStatus.php' ? 'active' : '' ?>" href="#" id="studentDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-graduate me-1"></i> Student
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item <?= $currentDir === 'student' && $currentPage === 'login.php' ? 'active' : '' ?>" href="<?= BASE_URL ?>/student/login.php">Login</a>
                            </li>
                            <li>
                                <a class="dropdown-item <?= $currentDir === 'student' && $currentPage === 'register.php' ? 'active' : '' ?>" href="<?= BASE_URL ?>/student/register.php">Apply for Hostel</a>
                            </li>
                            <li>
                                <a class="dropdown-item <?= $currentPage === 'checkStatus.php' ? 'active' : '' ?>" href="<?= BASE_URL ?>/student/sections/checkStatus.php">Check Status</a>
                            </li>
                            <li><a class="dropdown-item" href="<?= BASE_URL . '/student/guidelines.php' ?>">Guidelines</a></li>
                        </ul>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link <?= $currentPage === 'about.php' ? 'active' : '' ?>" href="<?= BASE_URL . '/about.php' ?>">
                            <i class="fas fa-info-circle me-1"></i> About
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link <?= $currentPage === 'contact.php' ? 'active' : '' ?>" href="<?= BASE_URL . '/contact.php' ?>">
                            <i class="fas fa-phone-alt me-1"></i> Contact
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- JS -->
    <script src="<?= BASE_URL ?>/vendor/jquery/jquery.min.js"></script>
    <script src="<?= BASE_URL ?>/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <script>
        $(document).ready(function () {
            $('.dropdown-menu').on('click', function (e) {
                e.stopPropagation();
            });

            $('.dropdown-item.active').closest('.dropdown').find('.nav-link').addClass('active');
        });
    </script>
<!-- </body>

</html> -->
