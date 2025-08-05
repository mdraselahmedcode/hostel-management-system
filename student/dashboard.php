<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/db.php';
require_once BASE_PATH . '/config/auth.php';


require_student();
require_once BASE_PATH . '/student/includes/header_student.php';

$currentSection = 'dashboard';
?>

<style>

        :root {
            --primary-color: #394e63ff;
            --primary-hover: #1c2935ff;
            --primary-text: #ffffff;
        }

        .bg-primary {
            background-color: var(--primary-color) !important;
        }

        .text-primary {
            color: var(--primary-color) !important;
        }

        .btn-primary {
            background-color: var(--primary-color) !important;
            border-color: var(--primary-color) !important;
            color: var(--primary-text) !important;
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }

        .btn-primary:hover {
            background-color: var(--primary-hover) !important;
            border-color: var(--primary-hover) !important;
        }

        .btn-primary:focus,
        .btn-primary:active {
            box-shadow: 0 0 0 0.2rem rgba(44, 62, 80, 0.5) !important;
        }

        a.text-primary:hover,
        a.text-primary:focus {
            color: var(--primary-hover) !important;
            text-decoration: underline;
        }

        .card-header.bg-primary {
            background-color: var(--primary-color) !important;
            color: var(--primary-text) !important;
        }

        .btn-outline-primary {
            color: var(--primary-color) !important;
            border-color: var(--primary-color) !important;
            background-color: transparent !important;
            transition: color 0.3s ease, background-color 0.3s ease, border-color 0.3s ease;
        }

        .btn-outline-primary:hover,
        .btn-outline-primary:focus,
        .btn-outline-primary:active {
            color: var(--primary-text) !important;
            background-color: var(--primary-color) !important;
            border-color: var(--primary-hover) !important;
            box-shadow: 0 0 0 0.2rem rgba(44, 62, 80, 0.25) !important;
        }
    
    
    body {
        overflow: hidden;
        background-color: #f8f9fa;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .main-content-wrapper {
        display: flex;
        height: calc(100vh - 119px);
    }

    .main-content {
        flex: 1;
        overflow-y: auto;
        padding: 25px;
        background: #fdfdfd;
    }

    .hover-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border-radius: 12px;
    }

    .hover-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 24px rgba(0, 0, 0, 0.1);
    }

    .card-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #333;
    }

    .card-body i {
        display: block;
        margin-bottom: 10px;
    }

    .main-content::-webkit-scrollbar {
        width: 8px;
    }

    .main-content::-webkit-scrollbar-track {
        background: #eee;
    }

    .main-content::-webkit-scrollbar-thumb {
        background: #bbb;
        border-radius: 10px;
    }

    .main-content::-webkit-scrollbar-thumb:hover {
        background: #888;
    }

    .welcome-card h2 {
        font-weight: 700;
        color: #0d6efd;
    }

    .btn-outline-danger {
        transition: all 0.2s ease;
    }

    .btn-outline-danger:hover {
        background: #dc3545;
        color: #fff;
    }

    @media (max-width: 768px) {
        .card-title {
            font-size: 1rem;
        }
    }
</style>

<div class="main-content-wrapper">
    <?php require_once BASE_PATH . '/student/includes/sidebar_student.php'; ?>

    <main class="main-content">
        <!-- Welcome Card -->
        <div class="card shadow-sm mb-5 bg-white welcome-card">
            <div class="card-body">
                <h2 class="mb-3 text-success">Welcome, <?= htmlspecialchars($_SESSION['student']['first_name'] . ' ' . $_SESSION['student']['last_name']) ?> 👋</h2>
                <p class="mb-1"><strong>Full Name:</strong> <?= htmlspecialchars($_SESSION['student']['first_name'] . ' ' . $_SESSION['student']['last_name']) ?></p>
                <p class="mb-3"><strong>Email:</strong> <?= htmlspecialchars($_SESSION['student']['email']) ?></p>

                <!-- Right-aligned logout button -->
                <div class="text-end mt-3">
                    <a href="<?= BASE_URL . '/student/php_files/logout_student_handler.php' ?>" class="btn btn-outline-danger">Logout</a>
                </div>
            </div>
        </div>


        <!-- Dashboard Cards -->
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-5 g-4"> <!-- Increased cols-md to 5 -->
            <div class="col">
                <a href="<?= BASE_URL . '/student/sections/student_profile/student_profile.php' ?>" class="text-decoration-none">
                    <div class="card text-center shadow-sm border-0 h-100 hover-card bg-white">
                        <div class="card-body">
                            <i class="bi bi-person-circle fs-1 text-primary"></i>
                            <h6 class="card-title">My Profile</h6>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col">
                <a href="<?= BASE_URL . '/student/sections/room/room_details.php' ?>" class="text-decoration-none">
                    <div class="card text-center shadow-sm border-0 h-100 hover-card bg-white">
                        <div class="card-body">
                            <i class="bi bi-house-door fs-1 text-success"></i>
                            <h6 class="card-title">Room Details</h6>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col">
                <a href="<?= BASE_URL ?>/student/sections/payment/payment_view.php" class="text-decoration-none">
                    <div class="card text-center shadow-sm border-0 h-100 hover-card bg-white">
                        <div class="card-body">
                            <i class="bi bi-credit-card fs-1 text-warning"></i>
                            <h6 class="card-title">Payment View</h6>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col">
                <a href="<?= BASE_URL ?>/student/sections/room_change_request/index.php" class="text-decoration-none">
                    <div class="card text-center shadow-sm border-0 h-100 hover-card bg-white">
                        <div class="card-body">
                            <i class="bi bi-arrow-repeat fs-1 text-info"></i>
                            <h6 class="card-title">Room Change</h6>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col">
                <a href="<?= BASE_URL ?>/student/sections/complaints/index.php" class="text-decoration-none">
                    <div class="card text-center shadow-sm border-0 h-100 hover-card bg-white">
                        <div class="card-body">
                            <i class="bi bi-exclamation-triangle fs-1 text-danger"></i>
                            <h6 class="card-title">Complaints</h6>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </main>
</div>

<?php require_once BASE_PATH . '/student/includes/footer_student.php'; ?>