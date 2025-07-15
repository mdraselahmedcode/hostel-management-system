<?php
$currentSection = '';
$uri = $_SERVER['REQUEST_URI'];
$sections = [
    'hostels' => strpos($uri, '/hostels/') !== false,
    'floors' => strpos($uri, '/floors/') !== false,
    'rooms' => strpos($uri, '/rooms/') !== false,
    'roomTypes' => strpos($uri, '/roomTypes/') !== false,
    'roomFees' => strpos($uri, '/roomFees/') !== false,
    'admins' => strpos($uri, '/admins/') !== false,
    'students' => strpos($uri, '/students/') !== false
];
$currentSection = array_search(true, $sections) ?: '';
?>

<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>

<style>
    .admin-sidebar {
        background: linear-gradient(180deg, #2c3e50 0%, #1a2530 100%);
        color: white;
        /* min-height: 100vh; */
        min-height: calc(100vh - 95px);
    }
    .sidebar-sticky {
        position: sticky;
        top: 0;
    }
    .sidebar-header {
        padding: 1.5rem 1rem;
        border-bottom: 1px solid rgba(255,255,255,0.1);
    }
    .sidebar-header h5 {
        font-weight: 600;
        letter-spacing: 0.5px;
    }
    .nav-link {
        color: #b8c7ce;
        padding: 0.75rem 1.5rem;
        margin: 0.15rem 0;
        border-left: 3px solid transparent;
        transition: all 0.3s ease;
    }
    .nav-link:hover {
        color: white;
        background: rgba(255,255,255,0.05);
        border-left-color: rgba(255,255,255,0.3);
    }
    .nav-link.active {
        color: white;
        background: rgba(255,255,255,0.1);
        border-left-color: #3c8dbc;
        font-weight: 500;
    }
    .nav-link i {
        width: 20px;
        margin-right: 10px;
        text-align: center;
    }
    .logout-link {
        border-top: 1px solid rgba(255,255,255,0.1);
        margin-top: 1rem;
        padding-top: 1rem;
    }
    .logout-link .nav-link {
        color: #ff6b6b;
    }
    .logout-link .nav-link:hover {
        color: #ff5252;
        background: rgba(255,82,82,0.1);
    }
</style>

<nav class="col-md-2 d-none d-md-block admin-sidebar">
    <div class="sidebar-sticky">
        <div class="sidebar-header text-center">
            <h5 class="mb-0">
                <i class="bi bi-shield-lock me-2"></i>Admin Panel
            </h5>
        </div>
        
        <ul class="nav flex-column px-2">
            <li class="nav-item">
                <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'dashboard_admin.php' ? 'active' : '' ?>" 
                   href="<?= BASE_URL ?>/admin/dashboard_admin.php">
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
            <li class="nav-item logout-link">
                <a class="nav-link" href="<?= BASE_URL ?>/admin/php_files/logout_admin_handler.php">
                    <i class="bi bi-box-arrow-right"></i>Logout
                </a>
            </li>
        </ul>
    </div>
</nav>