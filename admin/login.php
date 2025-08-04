<?php
require_once __DIR__ . '/../config/config.php';
require_once BASE_PATH . '/config/auth.php';
include BASE_PATH . '/includes/slide_message.php';


// Redirect if logged in
if (is_student_logged_in()) {
    header("Location: " . BASE_URL . "/student/dashboard.php");
    exit;
}

if (is_admin_logged_in()) {
    header("Location: " . BASE_URL . "/admin/dashboard.php");
    exit;
}


require_once BASE_PATH . '/includes/header.php';
?>

<head>
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
    </style>
</head>

<div class="content container mt-5 d-block">
    <div class="row justify-content-center d-flex align-items-center h-100">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Admin Login</h4>
                </div>
                <div class="card-body">
                    <form id="adminLoginForm">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email address</label>
                            <input type="email" id="email" name="email" class="form-control" required>
                        </div>

                        <div class="mb-3 position-relative">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <input type="password" id="password" name="password" class="form-control" required>
                                <span class="input-group-text toggle-password" style="cursor: pointer;">
                                    <i class="fas fa-eye"></i>
                                </span>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Login</button>
                    </form>
                    <div id="showMessage" class="mt-3"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Load JavaScript files here instead of in footer -->
<script src="<?= BASE_URL ?>/vendor/jquery/jquery.min.js"></script>
<script src="<?= BASE_URL ?>/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

<script>
    $(document).ready(function() {
        // Password toggle functionality
        $('.toggle-password').click(function() {
            const passwordInput = $('#password');
            const icon = $(this).find('i');

            if (passwordInput.attr('type') === 'password') {
                passwordInput.attr('type', 'text');
                icon.removeClass('fa-eye').addClass('fa-eye-slash');
            } else {
                passwordInput.attr('type', 'password');
                icon.removeClass('fa-eye-slash').addClass('fa-eye');
            }
        });

        // Login form submission
        $('#adminLoginForm').on('submit', function(e) {
            e.preventDefault();
            const formData = $(this).serialize();

            $.ajax({
                url: 'php_files/login_admin_handler.php',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(res) {
                    if (res.success) {
                        showSlideMessage(res.message || 'Login successful! Redirecting...', 'success');
                        setTimeout(function() {
                            window.location.href = res.redirect || 'dashboard.php';
                        }, 1500);
                    } else {
                        const errorMsg = res.message || 'Login failed. Please try again.';
                        showSlideMessage(errorMsg, 'danger');
                    }
                },
                error: function(xhr, status, error) {
                    let errorMsg = 'An error occurred. Please try again.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    showSlideMessage(errorMsg, 'danger');
                }
            });
        });

    });
</script>

<?php
require_once BASE_PATH . '/includes/footer_admin_login.php';
?>