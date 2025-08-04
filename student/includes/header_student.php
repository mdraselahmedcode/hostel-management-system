<?php
require_once __DIR__ . '/../../config/config.php';

// Get current folder (section) name from URL path
$currentSection = basename(dirname($_SERVER['SCRIPT_NAME']));
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Hostel Management</title>
    <!-- Local Bootstrap CSS -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/vendor/bootstrap/css/bootstrap.min.css" />
    <link rel="stylesheet" href="<?= BASE_URL ?>/student/assets/css/common_student.css" />

    <style>
        .navbar-nav.d-md-none li a {
            padding-left: 20px;
        }

        .navbar {
            background: linear-gradient(135deg, #d2dde2 20%, #ffffff 90%);

        }
    </style>
</head>

<body>

    <nav class="navbar  navbar-expand-md navbar-light  fixed-header">
        <div class="container-fluid ">
            <a class="navbar-brand ms-5" style="cursor: pointer; ">City University Hostel</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse  " id="mainNavbar">
                <ul class="navbar-nav ms-auto d-md-none ">
                    <li class="nav-item">
                        <a class="nav-link <?= $currentSection == '' || $currentSection == 'student' ? 'active' : '' ?>" href="<?= BASE_URL . '/student/dashboard.php' ?>">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $currentSection == 'student_profile' ? 'active' : '' ?>" href="<?= BASE_URL . '/student/sections/student_profile/student_profile.php' ?>">My Profile</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $currentSection == 'room' ? 'active' : '' ?>" href="<?= BASE_URL . '/student/sections/room/room_details.php' ?>">Room Detail</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $currentSection == 'room_change_request' ? 'active' : '' ?>" href="<?= BASE_URL . '/student/sections/room_change_request/index.php' ?>">Room Change</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $currentSection == 'complaints' ? 'active' : '' ?>" href="<?= BASE_URL . '/student/sections/complaints/index.php' ?>">Complaints</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $currentSection == 'payment' ? 'active' : '' ?>" href="<?= BASE_URL . '/student/sections/payment/payment_view.php' ?>">Payment</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelector('.navbar-brand')?.addEventListener('click', function() {
                window.location.href = "<?= BASE_URL ?>/student/dashboard.php";
            });
        });
    </script>

    <!-- Local jQuery -->
    <script src="<?= BASE_URL ?>/vendor/jquery/jquery.min.js"></script>
    <!-- Local Bootstrap Bundle JS -->
    <script src="<?= BASE_URL ?>/vendor/bootstrap/js/bootstrap.bundle.js"></script>