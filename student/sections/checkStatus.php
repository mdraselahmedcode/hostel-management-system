<?php
require_once __DIR__ . '/../../config/config.php';
require_once BASE_PATH . '/includes/header.php';
?>

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

            <div id="statusResult" class="mt-4 d-none">
                <!-- Results will be inserted here by JavaScript -->
            </div>
        </div>
    </div>
</div>

<!-- Status Template (hidden) -->
<div id="statusTemplate" class="d-none">
    <div class="alert" id="statusAlert">
        <h4 class="alert-heading" id="studentName"></h4>

        <div class="d-flex flex-wrap gap-2 mb-3" id="statusBadges">
            <!-- Badges will be inserted here -->
        </div>

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
    $(document).ready(function() {
        // Handle form submission
        $('#statusCheckForm').submit(function(e) {
            e.preventDefault();

            // Clear previous results
            $('#statusResult').addClass('d-none').empty();

            // Validate at least one field is filled
            if (!$('#email').val() && !$('#varsity_id').val()) {
                showError('Please enter either email or varsity ID');
                return;
            }

            // Show loading state
            const $submitBtn = $(this).find('button[type="submit"]');
            $submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Checking...');

            // Make AJAX request
            $.ajax({
                url: '<?= BASE_URL ?>/student/php_files/check_status_handler.php',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        displayStatus(response.data);
                    } else {
                        showError(response.message || 'No application found with those credentials');
                    }
                },
                error: function(xhr) {
                    let message = 'Server error. Please try again.';
                    if (xhr.status === 400) {
                        message = 'Invalid input: ' + (xhr.responseJSON?.message || 'check your details');
                    } else if (xhr.status === 404) {
                        message = 'No application found with those credentials';
                    }
                    showError(message);
                },
                complete: function() {
                    $submitBtn.prop('disabled', false).html('<i class="fas fa-search"></i> Check Status');
                }
            });
        });

        // Display status results
        function displayStatus(data) {
            const $result = $('#statusTemplate').clone().removeClass('d-none').removeAttr('id');
            const $badges = $result.find('#statusBadges').empty();

            // Set basic information
            $result.find('#studentName').text(data.first_name + ' ' + data.last_name);
            $result.find('#displayVarsityId').text(data.varsity_id);
            $result.find('#displayEmail').text(data.email);
            $result.find('#createdAt').text(formatDate(data.created_at));
            $result.find('#updatedAt').text(formatDate(data.updated_at));

            // Add status badges
            addBadge($badges, 'verified', data.is_verified);
            addBadge($badges, 'approved', data.is_approved);
            if (data.is_checked_in) {
                addBadge($badges, 'checkedIn', true);
            }

            // Set alert type and status message
            const $statusAlert = $result.find('#statusAlert');
            const $nextSteps = $result.find('#nextSteps');

            if (data.is_approved && data.is_verified) {
                // Fully approved and verified
                $statusAlert
                    .removeClass('alert-warning alert-info alert-danger')
                    .addClass('alert-success');
                
                $nextSteps.html(`
                    <i class="fas fa-check-circle text-success"></i>
                    Your application is fully approved! 
                    ${data.is_checked_in 
                        ? 'You are currently checked in.' 
                        : 'You may now proceed to check-in.'}
                `);
            } else if (data.is_approved && !data.is_verified) {
                // Approved but not verified
                $statusAlert
                    .removeClass('alert-success alert-danger alert-warning')
                    .addClass('alert-info');
                
                $nextSteps.html(`
                    <div class="d-flex align-items-start">
                        <i class="fas fa-envelope text-info mt-1 me-2"></i>
                        <div>
                            <strong>Approval pending verification:</strong> 
                            Your application has been approved, but you need to verify your email address.
                            <div class="mt-2">
                                <a href="resend_verification.php" class="btn btn-sm btn-outline-info">
                                    <i class="fas fa-paper-plane"></i> Resend verification email
                                </a>
                            </div>
                        </div>
                    </div>
                `);
            } else if (!data.is_approved && data.is_verified) {
                // Verified but not approved
                $statusAlert
                    .removeClass('alert-success alert-info alert-danger')
                    .addClass('alert-warning');
                
                $nextSteps.html(`
                    <i class="fas fa-hourglass-half text-warning"></i>
                    Your email is verified but application is still under review by administration.
                `);
            } else {
                // Neither approved nor verified
                $statusAlert
                    .removeClass('alert-success alert-info')
                    .addClass('alert-warning');
                
                $nextSteps.html(`
                    <i class="fas fa-info-circle"></i>
                    Your application is under review. 
                    ${data.is_verified 
                        ? '' 
                        : 'Please check your email to complete verification.'}
                `);
            }

            // Display result
            $('#statusResult').html($result).removeClass('d-none');
        }

        // Helper function to add status badges
        function addBadge($container, type, isActive) {
            const $badge = $('#' + type + 'Badge').clone().removeClass('d-none');
            const iconClass = isActive ? 'fas fa-check' : 'fas fa-hourglass';
            const bgClass = isActive ? 'bg-success' : 'bg-secondary';

            $badge.find('i').attr('class', iconClass);
            $badge.text(' ' + (isActive ?
                type === 'verified' ? 'Verified' :
                type === 'approved' ? 'Approved' :
                'Checked In' :
                type === 'verified' ? 'Not Verified' :
                'Pending Approval'));

            $badge.removeClass('bg-success bg-secondary').addClass(bgClass);
            $container.append($badge);
        }

        // Show error message
        function showError(message) {
            const $error = $('#errorTemplate').clone().removeClass('d-none').removeAttr('id');
            $error.find('#errorMessage').text(message);
            $('#statusResult').html($error).removeClass('d-none');
        }

        // Format date
        function formatDate(dateString) {
            return new Date(dateString).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
        }
    });
</script>