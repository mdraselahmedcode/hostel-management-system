<?php 
    session_start(); 
    require_once __DIR__ . '/../config/config.php'; 
    require_once __DIR__ . '/../config/db.php'; 

    require_once BASE_PATH . '/student/includes/header_student.php'; 
?>


<div class="content container-fluid">
    <div class="row full-height">
        <!-- sidebar -->
        <?php 
            require_once BASE_PATH . '/student/includes/sidebar_student.php'; 
        ?>

        <!-- Main Content -->
         <main class="col-md-10 ms-sm mb-4">
            <!-- Welcome Card -->
            <div class="make card shadow-sm mb-4">
                <div class="card-body">
                    <h1 class="mb-4">Welcome to the Student Dashboard</h1>
                    <p><strong>Name:</strong><?= htmlspecialchars($_SESSION['student']['first_name']. ' ' . $_SESSION['student']['last_name']) ?></p>
                    <p><strong>Email: </strong><?= htmlspecialchars($_SESSION['student']['email']) ?></p>
                    <a href="<?= BASE_URL . '/student/php_files/logout_student_handler.php' ?>" class="btn btn-danger mt-3">Logout</a>
                </div>
            </div>

            <!-- Dashboard Section Cards -->
            <div class="row mb-5">
                <div class="col-md-3 mb-3">
                    <a href="<?= BASE_URL . '/student/sections/student_profile/student_profile.php' ?>" class="text-decoration-none">
                        <div class="card text-center shadow-sm hover-card">
                            <div class="card-body">
                                <h5 class="card-title">My Profile</h5>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-3 mb-3">
                    <a href="<?= BASE_URL . '/student/sections/room/room_details.php' ?>" class="text-decoration-none">
                        <div class="card text-center shadow-sm hover-card">
                            <div class="card-body">
                                <h5 class="card-title">Room Details</h5>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-3 mb-3">
                    <a href="#" class="text-decoration-none">
                        <div class="card text-center shadow-sm hover-card">
                            <div class="card-body">
                                <h5 class="card-title">Payment History</h5>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-3 mb-3">
                    <a href="#" class="text-decoration-none">
                        <div class="card text-center shadow-sm hover-card">
                            <div class="card-body">
                                <h5 class="card-title">Outstanding Dues</h5>
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
        require_once BASE_PATH . '/student/includes/footer_student.php';

    ?>