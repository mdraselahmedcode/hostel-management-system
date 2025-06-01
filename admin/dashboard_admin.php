<?php   
    session_start();
    require_once __DIR__ . '/../config/db.php';
    require_once BASE_PATH . '/admin/includes/header_admin.php';
    require_once BASE_PATH . '/admin/php_files/auth_check_admin.php'; 

?>

<div class="content container-fluid ">
    <div class="row full-height">
        <!-- Sidebar -->
         <?php 
            require_once BASE_PATH . '/admin/includes/sidebar_admin.php';
         ?>
         
        
         <!-- Main Content -->
         <main class="col-md-10 ms-sm-auto px-md-4 py-4">
            <!-- back button -->
             <a href="<?= BASE_URL . '/admin/dashboard_admin.php' ?>" class="btn btn-secondary mb-3">Back</a>
            <!-- Welcome Card -->
            <div class="make card shadow-sm mb-4">
                <div class="card-body">
                    <h1 class="mb-4">Welcome to the Admin Dashboard</h1>
                    <p><strong>Name:</strong> <?= htmlspecialchars($_SESSION['admin']['firstname'] . ' ' . $_SESSION['admin']['lastname']) ?></p>
                    <p><strong>Email:</strong> <?= htmlspecialchars($_SESSION['admin']['email']) ?></p>
                    <a href="php_files/logout_admin_handler.php" class="btn btn-danger mt-3">Logout</a>
                </div>
            </div>

            <!-- Dashboard Section Cards -->
            <div class="row mb-5">
                <div class="col-md-3 mb-3">
                    <a href="<?= BASE_URL . '/admin/sections/hostels/index.php' ?>" class="text-decoration-none">
                        <div class="card text-center shadow-sm hover-card">
                            <div class="card-body">
                                <h5 class="card-title">Hostels</h5>
                                <p class="card-text">Manage hostel records</p>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-3 mb-3">
                    <a href="<?= BASE_URL . '/admin/sections/floors/index.php' ?>" class="text-decoration-none">
                        <div class="card text-center shadow-sm hover-card">
                            <div class="card-body">
                                <h5 class="card-title">Floors</h5>
                                <p class="card-text">Manage floors </p>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-3 mb-3">
                    <a href="<?= BASE_URL . '/admin/sections/rooms/index.php' ?>" class="text-decoration-none">
                        <div class="card text-center shadow-sm hover-card">
                            <div class="card-body">
                                <h5 class="card-title">Rooms</h5>
                                <p class="card-text">Manage rooms</p>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-3 mb-3">
                    <a href="./sections/admins/admins_admin.php" class="text-decoration-none">
                        <div class="card text-center shadow-sm hover-card">
                            <div class="card-body">
                                <h5 class="card-title">Admins</h5>
                                <p class="card-text">Manage admin accounts</p>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-3 mb-3">
                    <a href="./sections/students/students_admin.php" class="text-decoration-none">
                        <div class="card text-center shadow-sm hover-card">
                            <div class="card-body">
                                <h5 class="card-title">Students</h5>
                                <p class="card-text">Manage student records</p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </main>
    </div>


    
</div>


    <?php 
        // include $_SERVER['DOCUMENT_ROOT'] . '/hostel-management-system/admin/includes/footer_admin.php'; 
        require_once BASE_PATH . '/admin/includes/footer_admin.php';

    ?>