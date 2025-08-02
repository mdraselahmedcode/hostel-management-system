<?php
require_once __DIR__ . '/../../config/config.php';
require_once BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/config/auth.php';

require_student(); 

// Get the current folder name (section) from the URL path to identify active link correctly
// $currentSection = basename(dirname($_SERVER['SCRIPT_NAME']));

$currentPage = basename($_SERVER['SCRIPT_NAME']);

?>
<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<style>
    .sidebar {
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        border-right: 1px solid rgba(0, 0, 0, 0.08);
        box-shadow: 2px 0 10px rgba(0, 0, 0, 0.03);
        transition: all 0.3s ease;
    }
    .sidebar:hover {
        box-shadow: 2px 0 15px rgba(0, 0, 0, 0.05);
    }
    .sidebar-sticky {
        position: sticky;
        top: 0;
        height: auto;
        overflow-y: auto;
        padding-top: 1rem;
        scrollbar-width: thin;
    }
    .sidebar-sticky::-webkit-scrollbar {
        width: 5px;
    }
    .sidebar-sticky::-webkit-scrollbar-thumb {
        background-color: rgba(0, 0, 0, 0.1);
        border-radius: 10px;
    }
    .nav-link {
        border-radius: 8px;
        margin: 0.25rem 0.75rem;
        padding: 0.75rem 1rem;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        color: #495057;
        display: flex;
        align-items: center;
        position: relative;
        overflow: hidden;
    }
    .nav-link:hover {
        background-color: rgba(13, 110, 253, 0.08);
        color: #0d6efd;
        transform: translateX(4px);
    }
    .nav-link:hover i {
        color: #0d6efd;
    }
    .nav-link.active {
        background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
        color: white !important;
        font-weight: 500;
        box-shadow: 0 4px 12px rgba(13, 110, 253, 0.25);
    }
    .nav-link.active::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        height: 100%;
        width: 3px;
        background-color: #ffffff;
        border-radius: 0 3px 3px 0;
    }
    .nav-link i {
        width: 24px;
        text-align: center;
        font-size: 1.1rem;
        transition: all 0.2s ease;
        margin-right: 12px;
    }
    .sidebar-header {
        padding: 1.25rem 1.5rem;
        margin-bottom: 1rem;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }
    .sidebar-header h5 {
        font-weight: 600;
        letter-spacing: 0.5px;
    }
    .logout-link {
        border-top: 1px solid rgba(0, 0, 0, 0.05);
        padding-top: 1rem;
        margin-top: 1rem;
    }
    .logout-link .nav-link {
        color: #dc3545;
    }
    .logout-link .nav-link:hover {
        background-color: rgba(220, 53, 69, 0.1);
    }
    .nav-item {
        position: relative;
    }
    .nav-item:not(.logout-link)::after {
        content: '';
        display: block;
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(0, 0, 0, 0.05), transparent);
        margin: 5px 1rem;
    }
    .nav-item:last-child::after {
        display: none;
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
                <a class="nav-link <?= $currentSection == '' || $currentSection == 'student' ? 'active' : '' ?>" href="<?= BASE_URL ?>/student/dashboard.php">
                    <i class="bi bi-speedometer2"></i>Dashboard
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link <?= $currentSection == 'student_profile' ? 'active' : '' ?>" href="<?= BASE_URL ?>/student/sections/student_profile/student_profile.php">
                    <i class="bi bi-person-circle"></i>My Profile
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link <?= $currentSection == 'room' ? 'active' : '' ?>" href="<?= BASE_URL ?>/student/sections/room/room_details.php">
                    <i class="bi bi-house-door"></i>Room Details
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?= $currentSection == 'student_profile' ? 'active' : '' ?>" href="<?= BASE_URL ?>/student/sections/student_profile/change_password.php">
                    <i class="bi bi-shield-lock"></i>Change Password
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link <?= $currentSection == 'room' ? 'active' : '' ?>" href="<?= BASE_URL ?>/student/sections/room/room_change.php">
                    <i class="bi bi-arrow-left-right"></i>Room Change
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link <?= $currentSection == 'payment' ? 'active' : '' ?>" href="<?= BASE_URL ?>/student/sections/payment/payment_view.php">
                    <i class="bi bi-receipt"></i>Payment View
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link <?= $currentSection == 'complaints' ? 'active' : '' ?>" href="<?= BASE_URL ?>/student/sections/complaints/index.php">
                    <i class="bi bi-megaphone"></i>Complaints
                </a>
            </li>
            
            <li class="nav-item logout-link">
                <a class="nav-link" href="<?= BASE_URL ?>/student/php_files/logout_student_handler.php">
                    <i class="bi bi-box-arrow-right"></i>Logout
                </a>
            </li>
        </ul>
    </div>
</nav>
