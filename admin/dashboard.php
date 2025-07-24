<?php
require_once __DIR__ . '/../config/db.php';
require_once BASE_PATH . '/config/auth.php';

require_admin();

require_once BASE_PATH . '/admin/includes/header_admin.php';
?>

<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?= BASE_URL . '/admin/assets/css/dashboard_admin.css' ?>">
</head>



<div class="content container-fluid mt-5">
    <div class="row full-height">
        <!-- Sidebar -->
        <?php require_once BASE_PATH . '/admin/includes/sidebar_admin.php'; ?>

        <!-- Main Content -->
        <main class="col-md-10 ms-sm-auto px-md-4 py-4" style="overflow-y: auto; max-height: calc(100vh - 95px)">
            <!-- Welcome Card -->
            <div class="card welcome-card shadow-sm mb-5">
                <div class="card-body">
                    <h1>Welcome to Admin Dashboard</h1>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Name:</strong> <?= htmlspecialchars($_SESSION['admin']['firstname'] . ' ' . $_SESSION['admin']['lastname']) ?></p>
                            <p><strong>Email:</strong> <?= htmlspecialchars($_SESSION['admin']['email']) ?></p>
                            <!-- <p><strong>Last Login:</strong> <?= date('M j, Y g:i a', strtotime($_SESSION['admin']['last_login'] ?? 'now')) ?></p> -->
                        </div>
                        <div class="col-md-6 text-md-end">
                            <a href="php_files/logout_admin_handler.php" class="btn logout-btn text-white mt-md-0">
                                <i class="bi bi-box-arrow-right me-2"></i>Logout
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dashboard Section Cards -->
            <div class="row" style="">
                <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                    <a href="<?= BASE_URL . '/admin/sections/hostels/index.php' ?>" class="text-decoration-none">
                        <div class="card dashboard-card">
                            <div class="card-body text-center">
                                <i class="bi bi-building card-icon"></i>
                                <h5 class="card-title">Hostels</h5>
                                <p class="card-text">Manage all hostel records and information</p>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                    <a href="<?= BASE_URL . '/admin/sections/floors/index.php' ?>" class="text-decoration-none">
                        <div class="card dashboard-card">
                            <div class="card-body text-center">
                                <i class="bi bi-layer-forward card-icon"></i>
                                <h5 class="card-title">Floors</h5>
                                <p class="card-text">Manage floor configurations and details</p>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                    <a href="<?= BASE_URL . '/admin/sections/rooms/index.php' ?>" class="text-decoration-none">
                        <div class="card dashboard-card">
                            <div class="card-body text-center">
                                <i class="bi bi-door-open card-icon"></i>
                                <h5 class="card-title">Rooms</h5>
                                <p class="card-text">Manage room assignments and status</p>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                    <a href="<?= BASE_URL . '/admin/sections/roomTypes/index.php' ?>" class="text-decoration-none">
                        <div class="card dashboard-card">
                            <div class="card-body text-center">
                                <i class="bi bi-grid card-icon"></i>
                                <h5 class="card-title">Room Types</h5>
                                <p class="card-text">Configure different room types</p>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                    <a href="<?= BASE_URL . '/admin/sections/roomFees/index.php' ?>" class="text-decoration-none">
                        <div class="card dashboard-card">
                            <div class="card-body text-center">
                                <i class="bi bi-cash-stack card-icon"></i>
                                <h5 class="card-title">Fees</h5>
                                <p class="card-text">Manage fee structures and payments</p>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                    <a href="<?= BASE_URL . '/admin/sections/admins/index.php' ?>" class="text-decoration-none">
                        <div class="card dashboard-card">
                            <div class="card-body text-center">
                                <i class="bi bi-people card-icon"></i>
                                <h5 class="card-title">Admins</h5>
                                <p class="card-text">Manage administrator accounts</p>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                    <a href="<?= BASE_URL . '/admin/sections/students/index.php' ?>" class="text-decoration-none">
                        <div class="card dashboard-card">
                            <div class="card-body text-center">
                                <i class="bi bi-person-vcard card-icon"></i>
                                <h5 class="card-title">Students</h5>
                                <p class="card-text">Manage student records and information</p>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                    <a href="<?= BASE_URL . '/admin/sections/payments/index.php' ?>" class="text-decoration-none">
                        <div class="card dashboard-card">
                            <div class="card-body text-center">
                                <i class="bi bi-credit-card-2-front card-icon"></i>
                                <h5 class="card-title">Payments</h5>
                                <p class="card-text">View and verify student payment records</p>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                    <a href="<?= BASE_URL . '/admin/sections/payments/payment_method/index.php' ?>" class="text-decoration-none">
                        <div class="card dashboard-card">
                            <div class="card-body text-center">
                                <i class="bi bi-gear card-icon"></i>
                                <h5 class="card-title">Payment Methods</h5>
                                <p class="card-text">Add, edit, or remove accepted payment methods</p>
                            </div>
                        </div>
                    </a>
                </div>

            </div>
        </main>
    </div>
</div>

<?php require_once BASE_PATH . '/admin/includes/footer_admin.php'; ?>