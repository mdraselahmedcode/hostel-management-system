<?php 
require_once __DIR__ . '/../config/config.php'; 
require_once __DIR__ . '/../config/db.php'; 
require_once BASE_PATH . '/config/auth.php';

require_student(); 

require_once BASE_PATH . '/student/includes/header_student.php';
?>

<style>
    /* Main layout styles */
    body {
        overflow: hidden; /* Prevent whole page scrolling */
    }
    
    .main-content-wrapper {
        display: flex;
        height: calc(100vh - 119px); 
    }
    
    .main-content {
        flex: 1;
        overflow-y: auto; /* Enable scrolling only for main content */
        padding: 20px;
    }
    
    /* Card hover effect */
    .hover-card:hover {
        transform: translateY(-4px);
        transition: 0.3s ease;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
    }
    
    /* Custom scrollbar */
    .main-content::-webkit-scrollbar {
        width: 8px;
    }
    
    .main-content::-webkit-scrollbar-track {
        background: #f1f1f1;
    }
    
    .main-content::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 4px;
    }
    
    .main-content::-webkit-scrollbar-thumb:hover {
        background: #555;
    }
</style>

<div class="main-content-wrapper">
    <!-- Sidebar -->
    <?php require_once BASE_PATH . '/student/includes/sidebar_student.php'; ?>
    
    <!-- Main Content -->
    <main class="main-content">
        <!-- Welcome Card -->
        <div class="card shadow-sm mb-5 bg-white">
            <div class="card-body">
                <h2 class="mb-3 fw-bold text-primary">Welcome, <?= htmlspecialchars($_SESSION['student']['first_name']) ?> 👋</h2>
                <p class="mb-1"><strong>Full Name:</strong> <?= htmlspecialchars($_SESSION['student']['first_name'] . ' ' . $_SESSION['student']['last_name']) ?></p>
                <p class="mb-3"><strong>Email:</strong> <?= htmlspecialchars($_SESSION['student']['email']) ?></p>
                <a href="<?= BASE_URL . '/student/php_files/logout_student_handler.php' ?>" class="btn btn-outline-danger">Logout</a>
            </div>
        </div>

        <!-- Dashboard Section Cards -->
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-4">
            <div class="col">
                <a href="<?= BASE_URL . '/student/sections/student_profile/student_profile.php' ?>" class="text-decoration-none">
                    <div class="card text-center shadow-sm border-0 h-100 hover-card bg-white">
                        <div class="card-body">
                            <i class="bi bi-person-circle fs-1 text-primary mb-2"></i>
                            <h6 class="card-title">My Profile</h6>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col">
                <a href="<?= BASE_URL . '/student/sections/room/room_details.php' ?>" class="text-decoration-none">
                    <div class="card text-center shadow-sm border-0 h-100 hover-card bg-white">
                        <div class="card-body">
                            <i class="bi bi-house-door fs-1 text-success mb-2"></i>
                            <h6 class="card-title">Room Details</h6>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col">
                <a href="<?= BASE_URL ?>/student/sections/payment/payment_view.php" class="text-decoration-none">
                    <div class="card text-center shadow-sm border-0 h-100 hover-card bg-white">
                        <div class="card-body">
                            <i class="bi bi-credit-card fs-1 text-warning mb-2"></i>
                            <h6 class="card-title">Payment View</h6>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </main>
</div>

<?php require_once BASE_PATH . '/student/includes/footer_student.php'; ?>