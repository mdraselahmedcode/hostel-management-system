<?php 
session_start();    
require_once __DIR__ . '/../config/config.php';
require_once BASE_PATH . '/includes/header.php'; 
require_once BASE_PATH . '/admin/php_files/guest_only_check_admin.php'; 
?>

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

            // Clear previous messages
            $('#showMessage').html('');

            $.ajax({
                url: 'php_files/login_admin_handler.php',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(res) {
                    if (res.success) {
                        $('#showMessage').html('<div class="alert alert-success">Login successful! Redirecting...</div>');
                        setTimeout(function() {
                            window.location.href = res.redirect || 'dashboard_admin.php';
                        }, 1000);
                    } else {
                        let errorMsg = res.message || 'Login failed. Please try again.';
                        $('#showMessage').html('<div class="alert alert-danger">' + errorMsg + '</div>');
                    }
                },
                error: function(xhr, status, error) {
                    let errorMsg = 'An error occurred. Please try again.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    $('#showMessage').html('<div class="alert alert-danger">' + errorMsg + '</div>');
                }
            });
        });
    });
</script>

<?php 
require_once BASE_PATH . '/admin/includes/footer_admin.php'; 
?>