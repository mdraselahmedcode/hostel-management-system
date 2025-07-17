<?php
require_once __DIR__ . '/../../config/config.php';
require_once BASE_PATH . '/includes/header.php';
include BASE_PATH . '/includes/slide_message.php';

$token = $_GET['token'] ?? '';
?>

<div class="content container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0">Reset Password</h4>
                </div>
                <div class="card-body">
                    <form id="resetPasswordForm">
                        <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

                        <div class="mb-3">
                            <label for="password" class="form-label">New Password</label>
                            <div class="input-group">
                                <input type="password" name="password" id="password" class="form-control" required>
                                <button type="button" class="btn btn-outline-secondary" id="togglePassword">
                                    <i class="fas fa-eye" id="eyeIcon"></i>
                                </button>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success w-100">Reset Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Toggle password visibility
document.getElementById('togglePassword').addEventListener('click', function () {
    const passwordInput = document.getElementById('password');
    const eyeIcon = document.getElementById('eyeIcon');
    const isPassword = passwordInput.type === 'password';
    passwordInput.type = isPassword ? 'text' : 'password';
    eyeIcon.classList.toggle('fa-eye');
    eyeIcon.classList.toggle('fa-eye-slash');
});

$('#resetPasswordForm').submit(function (e) {
    e.preventDefault();
    const formData = $(this).serialize();

    $.ajax({
        url: '<?= BASE_URL ?>/student/php_files/sections/student_profile/reset_password_handler.php',
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function (res) {
            showSlideMessage(res.message, res.success ? 'success' : 'danger');

            if (res.success) {
                $('#resetPasswordForm')[0].reset();
                setTimeout(() => {
                    window.location.href = '<?= BASE_URL ?>/student/login.php';
                }, 3000);
            }
        },
        error: function () {
            showSlideMessage('Something went wrong. Please try again.', 'danger');
        }
    });
});
</script>

<?php require_once BASE_PATH . '/includes/footer.php'; ?>
