<?php
require_once __DIR__ . '/../../config/config.php';
require_once BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/config/auth.php';

require_student(); 

$currentPage = basename($_SERVER['PHP_SELF']);

?>
<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<style>
    .sidebar {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-right: 1px solid rgba(0, 0, 0, 0.05);
    }
    .sidebar-sticky {
        position: sticky;
        top: 0;
        /* height: 100vh; */
        overflow-y: auto;
        padding-top: 1rem;
    }
    .nav-link {
        border-radius: 0.5rem;
        margin: 0.25rem 0.5rem;
        padding: 0.75rem 1rem;
        transition: all 0.2s ease;
        color: #495057;
    }
    .nav-link:hover {
        background-color: rgba(13, 110, 253, 0.1);
        transform: translateX(3px);
    }
    .nav-link.active {
        background-color: #0d6efd;
        color: white !important;
        font-weight: 500;
        box-shadow: 0 2px 8px rgba(13, 110, 253, 0.2);
    }
    .nav-link i {
        width: 20px;
        text-align: center;
    }
    .sidebar-header {
        padding: 1rem;
        margin-bottom: 1.5rem;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }
    .logout-link {
        border-top: 1px solid rgba(0, 0, 0, 0.05);
        padding-top: 1rem;
        margin-top: 1rem;
    }
</style>

<nav class="col-md-2 d-none d-md-block sidebar py-3">
    <div class="sidebar-sticky">
        <div class="sidebar-header text-center">
            <h5 class="mb-0 fw-bold text-primary">
                <i class="bi bi-person-vcard me-2"></i>Student Panel
            </h5>
        </div>
        
        <ul class="nav flex-column px-2">
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
                    <i class="bi bi-house-door me-2"></i>Room Details
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link <?= $currentPage == 'room_change.php' ? 'active' : '' ?>" href="<?= BASE_URL ?>/student/sections/room/room_change.php">
                    <i class="bi bi-arrow-left-right me-2"></i>Room Change
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link <?= $currentPage == 'payment_history.php' ? 'active' : '' ?>" href="<?= BASE_URL ?>/student/sections/student_profile/payment_history.php">
                    <i class="bi bi-receipt me-2"></i>Payment History
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link <?= $currentPage == 'outstanding_dues.php' ? 'active' : '' ?>" href="<?= BASE_URL ?>/student/sections/payment/outstanding_dues.php">
                    <i class="bi bi-credit-card me-2"></i>Outstanding Dues
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link <?= $currentPage == 'complaints.php' ? 'active' : '' ?>" href="<?= BASE_URL ?>/student/sections/student_profile/complaints.php">
                    <i class="bi bi-megaphone me-2"></i>Complaints
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link <?= $currentPage == 'change_password.php' ? 'active' : '' ?>" href="<?= BASE_URL ?>/student/sections/student_profile/change_password.php">
                    <i class="bi bi-shield-lock me-2"></i>Change Password
                </a>
            </li>
            
            <li class="nav-item logout-link">
                <a class="nav-link text-danger" href="<?= BASE_URL ?>/student/php_files/logout_student_handler.php">
                    <i class="bi bi-box-arrow-right me-2"></i>Logout
                </a>
            </li>
        </ul>
    </div>
</nav>