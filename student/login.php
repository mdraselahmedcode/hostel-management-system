<?php
require_once __DIR__ . '/../config/config.php';
require_once BASE_PATH . '/config/auth.php';

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
include BASE_PATH . '/includes/slide_message.php';
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
                    <h4 class="mb-0">Student Login</h4>
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
                    <div class="d-flex justify-content-between mt-3">
                        <a href="<?= BASE_URL . '/student/sections/forgot_password.php' ?>" class="text-primary">Forgot Password?</a>
                        <a href="<?= BASE_URL . '/student/sections/checkStatus.php' ?>" class="text-primary">Check Application Status</a>
                    </div>
                    <div class="text-center mt-3">
                        <p>Don't have an account? <a href="<?= BASE_URL . '/student/register.php' ?>" class="text-primary">Apply for Hostel</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Password toggle functionality
    $(document).ready(function() {
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

        // Form submission
        $('#adminLoginForm').on('submit', function(e) {
            e.preventDefault();
            const formData = $(this).serialize();

            $.ajax({
                url: '<?= BASE_URL . '/student/php_files/login_student_handler.php' ?>',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(res) {
                    if (res.success) {
                        showSlideMessage(res.message + ' Redirecting...', 'success');
                        setTimeout(function() {
                            window.location.href = res.redirect || '<?= BASE_URL . '/student/dashboard.php' ?>';
                        }, 1000);
                    } else {
                        let errorMsg = res.message || 'Login failed. Please try again.';
                        showSlideMessage(errorMsg, 'danger');
                    }
                },
                error: function(xhr) {
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

<?php if (isset($_SESSION['logout_success'])): ?>
    <script>
        $(document).ready(function() {
            showSlideMessage("<?= addslashes($_SESSION['logout_success']) ?>", "success");
        });
    </script>
    <?php unset($_SESSION['logout_success']); ?>
<?php endif; ?>


<?php
require_once BASE_PATH . '/includes/footer_student_login.php';
?>