<?php
require_once __DIR__ . '/config/config.php';
include BASE_PATH . '/config/auth.php';
http_response_code(404); // Set proper HTTP status code
// Set proper 404 header
// header("HTTP/1.0 404 Not Found");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Not Found | University Hostel Management System</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/vendor/bootstrap/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
        }

        .error-container {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .error-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 40px;
            max-width: 600px;
            width: 90%;
        }

        .error-icon {
            font-size: 5rem;
            color: #dc3545;
            margin-bottom: 20px;
        }

        .error-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: #343a40;
            margin-bottom: 15px;
        }

        .error-message {
            font-size: 1.1rem;
            color: #6c757d;
            margin-bottom: 30px;
        }

        .btn-primary {
            background-color: #0d6efd;
            border-color: #0d6efd;
            padding: 10px 25px;
            font-weight: 500;
        }

        .btn-primary:hover {
            background-color: #0b5ed7;
            border-color: #0a58ca;
        }

        .btn-success {
            background-color: #198754;
            border-color: #198754;
        }

        .btn-success:hover {
            background-color: #157347;
            border-color: #146c43;
        }
    </style>
</head>

<body>
    <div class="error-container">
        <div class="error-card">
            <div class="error-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <h1 class="error-title">404 - Page Not Found</h1>
            <p class="error-message">
                Uh-oh! This part of the hostel is off-limits or under maintenance.<br>
            </p>

            <div class="d-flex flex-column gap-3">
                <?php if (is_admin_logged_in()): ?>
                    <a href="<?= BASE_URL ?>/admin/dashboard.php" class="btn btn-success">
                        <i class="fas fa-tachometer-alt me-2"></i> Go to Admin Dashboard
                    </a>
                <?php elseif (is_student_logged_in()): ?>
                    <a href="<?= BASE_URL ?>/student/dashboard.php" class="btn btn-success">
                        <i class="fas fa-tachometer-alt me-2"></i> Go to Student Dashboard
                    </a>
                <?php endif; ?>

                <?php if (!is_admin_logged_in() && !is_student_logged_in()): ?>
                    <div class="d-flex justify-content-center gap-3">
                        <a href="<?= BASE_URL ?>" class="btn btn-primary">
                            <i class="fas fa-home me-2"></i> Return Home
                        </a>
                        <a href="<?= BASE_URL ?>/contact.php" class="btn btn-outline-primary">
                            <i class="fas fa-envelope me-2"></i> Contact Support
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Include JavaScript files if needed -->
    <script src="<?= BASE_URL ?>/vendor/jquery/jquery.min.js"></script>
    <script src="<?= BASE_URL ?>/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>