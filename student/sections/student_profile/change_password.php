<?php
require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../config/db.php';
require_once BASE_PATH . '/student/includes/header_student.php';

require_once BASE_PATH . '/config/auth.php';
include BASE_PATH . '/includes/slide_message.php';


require_student();

?>

<div class="content container-fluid">
    <div class="row full-height">
        <!-- Sidebar -->
        <?php require_once BASE_PATH . '/student/includes/sidebar_student.php'; ?>

        <!-- Main Content -->
        <main class="col-12 col-md-10 ms-sm">
            <div class="mb-3 mt-3">
                <a href="javascript:history.back()" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back
                </a>
            </div>
            <div class="d-flex mt-5 justify-content-center" style="min-height: calc(100vh - 119px);">
                <div class="w-100 px-3" style="max-width: 500px;">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Change Password</h5>
                        </div>
                        <div class="card-body">
                            <form id="changePasswordForm">
                                <div class="mb-3">
                                    <label for="oldPassword" class="form-label">Current Password</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="oldPassword" name="old_password" required>
                                        <span class="input-group-text bg-white">
                                            <i class="bi bi-eye toggle-password" data-target="#oldPassword" style="cursor:pointer;"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="newPassword" class="form-label">New Password</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="newPassword" name="new_password" required>
                                        <span class="input-group-text bg-white">
                                            <i class="bi bi-eye toggle-password" data-target="#newPassword" style="cursor:pointer;"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="confirmPassword" class="form-label">Confirm New Password</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="confirmPassword" name="confirm_password" required>
                                        <span class="input-group-text bg-white">
                                            <i class="bi bi-eye toggle-password" data-target="#confirmPassword" style="cursor:pointer;"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">Change Password</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Bootstrap Icons CDN -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<script>
    $('#changePasswordForm').on('submit', function(e) {
        e.preventDefault();
        var formData = $(this).serialize();

        $.ajax({
            url: '<?= BASE_URL . '/student/php_files/sections/student_profile/change_password.php' ?>',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    showSlideMessage(res.message, 'success');
                    $('#changePasswordForm')[0].reset();
                } else {
                    showSlideMessage(res.message || 'Password change failed.', 'danger');
                }
            },
            error: function() {
                showSlideMessage('An error occurred while processing the request.', 'danger');
            }
        });
    });


    // Show/hide password functionality
    $('.input-group').on('click', '.toggle-password', function() {
        var input = $($(this).data('target'));
        var icon = $(this);
        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            icon.removeClass('bi-eye').addClass('bi-eye-slash');
        } else {
            input.attr('type', 'password');
            icon.removeClass('bi-eye-slash').addClass('bi-eye');
        }
    });
</script>

<?php require_once BASE_PATH . '/student/includes/footer_student.php'; ?>