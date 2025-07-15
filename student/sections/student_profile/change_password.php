<?php
session_start();
require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../config/db.php';
require_once BASE_PATH . '/student/includes/header_student.php';

if (!isset($_SESSION['student'])) {
    header('Location: ../../login_student.php');
    exit;
}
?>

<!-- Modified container with vertical centering -->
<div class="container-fluid d-flex align-items-center justify-content-center" style="min-height: calc(100vh - 119px);">
    <div class="row justify-content-center w-100">
        <div class="col-md-4">
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
                    <div id="showMessage" class="mt-3"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap Icons CDN (if not already included) -->
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
            var alertBox = $('#showMessage');
            if (res.success) {
                alertBox.removeClass('alert-danger').addClass('alert alert-success').text(res.message).show();
                $('#changePasswordForm')[0].reset();
            } else {
                alertBox.removeClass('alert-success').addClass('alert alert-danger').text(res.message).show();
            }
        },
        error: function() {
            $('#showMessage').removeClass('alert-success').addClass('alert alert-danger').text('An error occurred.').show();
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