<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>

<nav class="col-md-2 d-none d-md-block bg-light sidebar py-4 shadow-sm">
    <div class="sidebar-sticky">
        <h5 class="text-center mb-4" style="cursor: pointer">Student Panel</h5>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?= $currentPage == 'dashboard.php' ? 'active' : '' ?>" href="<?= BASE_URL ?>/student/dashboard.php">
                    <i class="bi bi-speedometer2 me-2"></i>Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $currentPage == 'student_profile.php' ? 'active' : '' ?>" href="<?= BASE_URL ?>/student/sections/student_profile/student_profile.php">
                    <i class="bi bi-person-circle me-2"></i>My Profile
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $currentPage == 'room_details.php' ? 'active' : '' ?>" href="<?= BASE_URL ?>/student/sections/room/room_details.php">
                    <i class="bi bi-credit-card-fill me-2"></i>Room Details
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $currentPage == 'room_change.php' ? 'active' : '' ?>" href="<?= BASE_URL ?>/student/sections/student_profile/room_change.php">
                    <i class="bi bi-door-open-fill me-2"></i>Room Change Request
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $currentPage == 'payment_history.php' ? 'active' : '' ?>" href="<?= BASE_URL ?>/student/sections/student_profile/payment_history.php">
                    <i class="bi bi-credit-card-fill me-2"></i>Payment History
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $currentPage == 'outstanding_dues.php' ? 'active' : '' ?>" href="<?= BASE_URL ?>/student/sections/outstanding_dues/outstanding_dues.php">
                    <i class="bi bi-credit-card-fill me-2"></i>Outstanding Dues
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link <?= $currentPage == 'complaints.php' ? 'active' : '' ?>" href="<?= BASE_URL ?>/student/sections/student_profile/complaints.php">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>Complaints
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $currentPage == 'change_password.php' ? 'active' : '' ?>" href="<?= BASE_URL ?>/student/sections/student_profile/change_password.php">
                    <i class="bi bi-key-fill me-2"></i>Change Password
                </a>
            </li>
            <li class="nav-item mt-3">
                <a class="nav-link text-danger" href="<?= BASE_URL ?>/student/php_files/logout_student_handler.php">
                    <i class="bi bi-box-arrow-right me-2"></i>Logout
                </a>
            </li>
        </ul>
    </div>
</nav>