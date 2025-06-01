<!-- Sidebar -->

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
                <a class="nav-link" href="./sections/hostels/hostels_admin.php">Hostels</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="./sections/rooms/rooms_admin.php">Rooms</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="./sections/admins/admins_admin.php">Admins</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="./sections/students/students_admin.php">Students</a>
            </li>
            <li class="nav-item mt-3">
                <a class="nav-link text-danger" href="php_files/logout_admin_handler.php">Logout</a>
            </li>
        </ul>
    </div>
</nav>