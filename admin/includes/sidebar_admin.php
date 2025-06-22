<?php

$currentSection = '';
if (strpos($_SERVER['REQUEST_URI'], '/hostels/') !== false) {
    $currentSection = 'hostels';
} elseif (strpos($_SERVER['REQUEST_URI'], '/floors/') !== false) {
    $currentSection = 'floors';
} elseif (strpos($_SERVER['REQUEST_URI'], '/rooms/') !== false) {
    $currentSection = 'rooms';
} elseif (strpos($_SERVER['REQUEST_URI'], '/roomTypes/') !== false) {
    $currentSection = 'roomTypes';
} elseif (strpos($_SERVER['REQUEST_URI'], '/roomFees/') !== false) {
    $currentSection = 'roomFees';
} elseif (strpos($_SERVER['REQUEST_URI'], '/admins/') !== false) {
    $currentSection = 'admins';
} elseif (strpos($_SERVER['REQUEST_URI'], '/students/') !== false) {
    $currentSection = 'students';
}
?>

<nav class="col-md-2 d-none d-md-block bg-light sidebar py-4 shadow-sm">
    <div class="sidebar-sticky">
        <h5 class="text-center mb-4" style="cursor: pointer">Admin Panel</h5>
        <ul class="nav flex-column">
            <li class="dashboard nav-item">
                <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'dashboard_admin.php' ? 'active' : '' ?>" href="<?= BASE_URL ?>/admin/dashboard_admin.php">
                    Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $currentSection === 'hostels' ? 'active' : '' ?>" href="<?= BASE_URL . '/admin/sections/hostels/index.php' ?>">Hostels</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $currentSection === 'floors' ? 'active' : '' ?>" href="<?= BASE_URL . '/admin/sections/floors/index.php' ?>">Floors</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $currentSection === 'rooms' ? 'active' : '' ?>" href="<?= BASE_URL . '/admin/sections/rooms/index.php' ?>">Rooms</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $currentSection === 'roomTypes' ? 'active' : '' ?>" href="<?= BASE_URL . '/admin/sections/roomTypes/index.php' ?>">Room Types</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $currentSection === 'roomFees' ? 'active' : '' ?>" href="<?= BASE_URL . '/admin/sections/roomFees/index.php' ?>">Fees</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $currentSection === 'admins' ? 'active' : '' ?>" href="<?= BASE_URL . '/admin/sections/admins/index.php' ?>">Admins</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $currentSection === 'students' ? 'active' : '' ?>" href="<?= BASE_URL . '/admin/sections/students/index.php' ?>">Students</a>
            </li>
            <li class="nav-item mt-3">
                <a class="nav-link text-danger" href="php_files/logout_admin_handler.php">Logout</a>
            </li>
        </ul>
    </div>
</nav>