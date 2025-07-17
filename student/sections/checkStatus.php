<?php
require_once __DIR__ . '/../../config/config.php';
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

<!-- Make BASE_URL available to JS -->
<script>
    const BASE_URL = "<?= BASE_URL ?>";
</script>

<div class="content container mt-4">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h3><i class="fas fa-user-clock"></i> Application Status Check</h3>
        </div>
        <div class="card-body">
            <form id="statusCheckForm">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" id="email" class="form-control"
                               placeholder="Your registered email" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Varsity ID</label>
                        <input type="text" name="varsity_id" id="varsity_id" class="form-control"
                               placeholder="Your university ID" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary mt-3">
                    <i class="fas fa-search"></i> Check Status
                </button>
            </form>

            <div id="statusResult" class="mt-4 d-none"></div>
        </div>
    </div>
</div>

<!-- Hidden Templates -->
<div id="statusTemplate" class="d-none">
    <div class="alert" id="statusAlert">
        <h4 class="alert-heading" id="studentName"></h4>

        <div class="d-flex flex-wrap gap-2 mb-3" id="statusBadges"></div>

        <div class="row">
            <div class="col-md-6">
                <p><strong>Varsity ID:</strong> <span id="displayVarsityId"></span></p>
                <p><strong>Applied On:</strong> <span id="createdAt"></span></p>
            </div>
            <div class="col-md-6">
                <p><strong>Last Updated:</strong> <span id="updatedAt"></span></p>
                <p><strong>Email:</strong> <span id="displayEmail"></span></p>
            </div>
        </div>

        <hr>
        <p class="mb-0" id="nextSteps"></p>
    </div>
</div>

<!-- Badge Templates -->
<span class="badge d-none" id="verifiedBadge">
    <i class="fas fa-hourglass"></i> Not Verified
</span>
<span class="badge d-none" id="approvedBadge">
    <i class="fas fa-hourglass"></i> Pending Approval
</span>
<span class="badge d-none" id="checkedInBadge">
    <i class="fas fa-bed"></i> Checked In
</span>

<!-- Error Template -->
<div class="alert alert-danger d-none" id="errorTemplate">
    <i class="fas fa-exclamation-circle"></i>
    <span id="errorMessage"></span>
</div>

<?php require_once BASE_PATH . '/includes/footer.php'; ?>

<script>
$(document).ready(function () {
    $('#statusCheckForm').submit(function (e) {
        e.preventDefault();
        $('#statusResult').addClass('d-none').empty();

        const email = $('#email').val();
        const varsityId = $('#varsity_id').val();

        // Require both email and varsity ID for better validation (adjust if needed)
        if (!email || !varsityId) {
            showError('Please enter both email and varsity ID.');
            return;
        }

        const $submitBtn = $(this).find('button[type="submit"]');
        $submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Checking...');

        $.ajax({
            url: BASE_URL + '/student/php_files/check_status_handler.php',
            type: 'POST',
            data: { email, varsity_id: varsityId },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    displayStatus(response.data);
                } else {
                    showError(response.message || 'No application found');
                }
            },
            error: function (xhr) {
                let msg = 'Server error. Please try again.';
                if (xhr.status === 400) {
                    msg = 'Invalid input: ' + (xhr.responseJSON?.message || '');
                } else if (xhr.status === 404) {
                    msg = 'No application found with those credentials';
                }
                showError(msg);
            },
            complete: function () {
                $submitBtn.prop('disabled', false).html('<i class="fas fa-search"></i> Check Status');
            }
        });
    });

    function displayStatus(data) {
        const $result = $('#statusTemplate').clone().removeClass('d-none').removeAttr('id');
        const $badges = $result.find('#statusBadges').empty();

        $result.find('#studentName').text(data.first_name + ' ' + data.last_name);
        $result.find('#displayVarsityId').text(data.varsity_id);
        $result.find('#displayEmail').text(data.email);
        $result.find('#createdAt').text(formatDate(data.created_at));
        $result.find('#updatedAt').text(formatDate(data.updated_at));

        addBadge($badges, 'verified', data.is_verified);
        addBadge($badges, 'approved', data.is_approved);
        if (data.is_checked_in) {
            addBadge($badges, 'checkedIn', true);
        }

        const $statusAlert = $result.find('#statusAlert');
        const $nextSteps = $result.find('#nextSteps');

        if (!data.is_verified) {
            $statusAlert.removeClass().addClass('alert alert-warning');
            $nextSteps.html(`
                <div class="d-flex align-items-start flex-column">
                    <div>
                        <i class="fas fa-envelope text-warning me-2"></i>
                        <strong>Email not verified:</strong> Please check your email to verify your account.
                    </div>
                    <button id="resendVerificationBtn" class="btn btn-sm btn-outline-warning mt-2">
                        <i class="fas fa-paper-plane"></i> Resend verification email
                    </button>
                    <div id="resendMessage" class="mt-2"></div>
                </div>
            `);

            // Attach click handler for resend verification button
            // Inside $(document).ready()
            $('#statusResult').off('click', '#resendVerificationBtn').on('click', '#resendVerificationBtn', function () {
                console.log('Resend button clicked');
                const $btn = $(this);
                $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Sending...');
                $('#resendMessage').removeClass('text-success text-danger').text('');

                $.ajax({
                    url: BASE_URL + '/student/php_files/resend_verification.php',
                    type: 'POST',
                    data: {
                        email: $('#email').val(),       // or you can pass from saved data object if accessible
                        varsity_id: $('#varsity_id').val()
                    },
                    dataType: 'json',
                    success: function (res) {
                        console.log('Resend success response:', res);
                        if (res.success) {
                            $('#resendMessage').addClass('text-success').text(res.message);
                        } else {
                            $('#resendMessage').addClass('text-danger').text(res.message || 'Failed to resend verification email.');
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('Resend error:', status, error);
                        $('#resendMessage').addClass('text-danger').text('Server error. Please try again later.');
                    },
                    complete: function () {
                        $btn.prop('disabled', false).html('<i class="fas fa-paper-plane"></i> Resend verification email');
                    }
                });
            });



        } else if (!data.is_approved) {
            $statusAlert.removeClass().addClass('alert alert-info');
            $nextSteps.html(`
                <i class="fas fa-hourglass-half text-info"></i>
                Your email is verified. Application is pending admin approval.
            `);
        } else {
            $statusAlert.removeClass().addClass('alert alert-success');
            $nextSteps.html(`
                <i class="fas fa-check-circle text-success"></i>
                Your application is fully approved!
                ${data.is_checked_in ? 'You are currently checked in.' : 'You may now proceed to check-in.'}
                <div class="mt-3">
                    <a href="${BASE_URL}/student/login.php" class="btn btn-success btn-sm">
                        <i class="fas fa-sign-in-alt"></i> Student Login
                    </a>
                </div>
            `);
        }


        $('#statusResult').html($result).removeClass('d-none');
    }

    function addBadge($container, type, isActive) {
        const $badge = $('#' + type + 'Badge').clone().removeClass('d-none');
        const icon = isActive ? 'fas fa-check' : 'fas fa-hourglass';
        const label =
            type === 'verified' ? (isActive ? 'Verified' : 'Not Verified') :
            type === 'approved' ? (isActive ? 'Approved' : 'Pending Approval') :
            'Checked In';
        const color = isActive ? 'bg-success' : 'bg-secondary';

        $badge.find('i').attr('class', icon);
        $badge.text(' ' + label);
        $badge.removeClass('bg-success bg-secondary').addClass(color);
        $container.append($badge);
    }

    function showError(message) {
        const $error = $('#errorTemplate').clone().removeClass('d-none').removeAttr('id');
        $error.find('#errorMessage').text(message);
        $('#statusResult').html($error).removeClass('d-none');
    }

    function formatDate(date) {
        return new Date(date).toLocaleDateString('en-US', {
            year: 'numeric', month: 'short', day: 'numeric'
        });
    }
});
</script>
