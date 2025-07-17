<?php
require_once __DIR__ . '/../../config/config.php';
require_once BASE_PATH . '/includes/header.php';
?>

<!-- Include Slide Message Component -->
<?php include BASE_PATH . '/includes/slide_message.php'; ?>

<div class="content container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Forgot Password</h4>
                </div>
                <div class="card-body">
                    <form id="forgotPasswordForm">
                        <div class="mb-3">
                            <label for="email" class="form-label">Enter your registered email</label>
                            <input type="email" id="email" name="email" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Send Reset Link</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#forgotPasswordForm').submit(function(e) {
        e.preventDefault();
        const email = $('#email').val();
        
        $.ajax({
            url: '<?= BASE_URL ?>/student/php_files/sections/student_profile/forgot_password_handler.php',
            type: 'POST',
            data: { email: email },
            dataType: 'json',
            beforeSend: function() {
                // Show loading state if needed
                $('#forgotPasswordForm button[type="submit"]')
                    .prop('disabled', true)
                    .html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Sending...');
            },
            success: function(res) {
                // Show slide message
                showSlideMessage(res.message, res.success ? 'success' : 'error');
                
                // Reset form if successful
                if (res.success) {
                    $('#forgotPasswordForm')[0].reset();

                }
            },
            error: function(xhr) {
                let errorMsg = 'Server error. Please try again later.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                showSlideMessage(errorMsg, 'error');
            },
            complete: function() {
                // Reset button state
                $('#forgotPasswordForm button[type="submit"]')
                    .prop('disabled', false)
                    .text('Send Reset Link');
            }
        });
    });
});
</script>

<?php require_once BASE_PATH . '/includes/footer.php'; ?>