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
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-light bg-light fixed-header">
        <div class="container">
            <a class="navbar-brand" style="cursor: pointer">Hostel Management</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <!-- <div class="collapse navbar-collapse" id="mainNavbar">
                <ul class="navbar-nav ms-auto">
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
                        <a class="nav-link <?= $currentSection == 'payment' ? 'active' : '' ?>" href="<?= BASE_URL . '/student/sections/payment/payment_view.php' ?>">Payment</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $currentSection == 'complaints' ? 'active' : '' ?>" href="<?= BASE_URL . '/student/sections/complaints/index.php' ?>">Complaints</a>
                    </li>
                </ul>
            </div> -->
        </div>
    </nav>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelector('.navbar-brand')?.addEventListener('click', function () {
                window.location.href = "<?= BASE_URL ?>/student/dashboard.php";
            });
        });
    </script>

    <!-- Local jQuery -->
    <script src="<?= BASE_URL ?>/vendor/jquery/jquery.min.js"></script>
    <!-- Local Bootstrap Bundle JS -->
    <script src="<?= BASE_URL ?>/vendor/bootstrap/js/bootstrap.bundle.js"></script>
