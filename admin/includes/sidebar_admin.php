<?php
require_once __DIR__ . '/../../config/config.php';
require_once BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/config/auth.php';

require_admin();

// Detect current section based on URL
$uri = $_SERVER['REQUEST_URI'];
$sections = [
    'hostels' => strpos($uri, '/hostels/') !== false,
    'floors' => strpos($uri, '/floors/') !== false,
    'rooms' => strpos($uri, '/rooms/') !== false,
    'roomTypes' => strpos($uri, '/roomTypes/') !== false,
    'roomFees' => strpos($uri, '/roomFees/') !== false,
    'admins' => strpos($uri, '/admins/') !== false,
    'students' => strpos($uri, '/students/') !== false,
    'complaints' => strpos($uri, '/complaints/') !== false,
    'payments' => strpos($uri, '/payments/index.php') !== false, 
    'payment_methods' => strpos($uri, '/payment_method/') !== false,
    'payment_report' => strpos($uri, '/payment_report_generation.php') !== false 
];

$currentSection = array_search(true, $sections) ?: '';
?>


<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?= BASE_URL . '/admin/assets/css/sidebar_admin.css' ?>">
</head>


<nav class="col-md-2 d-none d-md-block admin-sidebar">
    <div class="sidebar-sticky">
        <div class="sidebar-header text-center">
            <h5 class="mb-0">
                <i class="bi bi-shield-lock me-2"></i>Admin Panel
            </h5>
        </div>

        <ul class="nav flex-column px-2">
            <li class="nav-item">
                <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>"
                    href="<?= BASE_URL ?>/admin/dashboard.php">
                    <i class="bi bi-speedometer2"></i>Dashboard
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?= $currentSection === 'hostels' ? 'active' : '' ?>"
                    href="<?= BASE_URL ?>/admin/sections/hostels/index.php">
                    <i class="bi bi-building"></i>Hostels
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?= $currentSection === 'floors' ? 'active' : '' ?>"
                    href="<?= BASE_URL ?>/admin/sections/floors/index.php">
                    <i class="bi bi-layer-forward"></i>Floors
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?= $currentSection === 'rooms' ? 'active' : '' ?>"
                    href="<?= BASE_URL ?>/admin/sections/rooms/index.php">
                    <i class="bi bi-door-open"></i>Rooms
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?= $currentSection === 'roomTypes' ? 'active' : '' ?>"
                    href="<?= BASE_URL ?>/admin/sections/roomTypes/index.php">
                    <i class="bi bi-grid"></i>Room Types
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?= $currentSection === 'roomFees' ? 'active' : '' ?>"
                    href="<?= BASE_URL ?>/admin/sections/roomFees/index.php">
                    <i class="bi bi-cash-stack"></i>Fees
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?= $currentSection === 'admins' ? 'active' : '' ?>"
                    href="<?= BASE_URL ?>/admin/sections/admins/index.php">
                    <i class="bi bi-people"></i>Admins
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?= $currentSection === 'students' ? 'active' : '' ?>"
                    href="<?= BASE_URL ?>/admin/sections/students/index.php">
                    <i class="bi bi-person-vcard"></i>Students
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?= $currentSection === 'complaints' ? 'active' : '' ?>"
                    href="<?= BASE_URL ?>/admin/sections/complaints/index.php">
                    <i class="bi bi-person-vcard"></i>Complaints
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?= $currentSection === 'payment_methods' ? 'active' : '' ?>"
                    href="<?= BASE_URL ?>/admin/sections/payments/payment_method/index.php">
                    <i class="bi bi-credit-card"></i>Payment Methods
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?= $currentSection === 'payments' ? 'active' : '' ?>"
                    href="<?= BASE_URL ?>/admin/sections/payments/index.php">
                    <i class="bi bi-credit-card-2-front"></i>Payments
                </a>
            </li>



            <li class="nav-item">
                <a class="nav-link <?= $currentSection === 'payment_report' ? 'active' : '' ?>"
                    href="<?= BASE_URL ?>/admin/sections/payments/generate_payment_report.php">
                    <i class="bi bi-file-earmark-text"></i>Generate Report
                </a>
            </li>




            <li class="nav-item logout-link">
                <a class="nav-link py-2" href="<?= BASE_URL ?>/admin/php_files/logout_admin_handler.php">
                    <i class="bi bi-box-arrow-right"></i>Logout
                </a>
            </li>
        </ul>
    </div>
</nav>