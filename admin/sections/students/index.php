<?php
session_start();
require_once __DIR__ . '/../../../config/config.php';
require_once BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/admin/php_files/auth_check_admin.php';

// Fetch hostels for dropdown
$hostels = [];
$result = $conn->query("SELECT id, hostel_name FROM hostels ORDER BY hostel_name ASC");
if ($result && $result->num_rows > 0) {
    $hostels = $result->fetch_all(MYSQLI_ASSOC);
}

require_once BASE_PATH . '/admin/includes/header_admin.php';
?>

<div class="content container-fluid">
    <div class="row full-height">
        <?php require_once BASE_PATH . '/admin/includes/sidebar_admin.php'; ?>

        <main class="col-md-10 ms-sm-auto px-md-4 py-4">
            <a href="<?= BASE_URL . '/admin/dashboard_admin.php' ?>" class="btn btn-secondary mb-3">Back</a>

            <div class="card shadow-sm">
                <div class="card-body">
                    <!-- Title + Add New button side by side -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2>Manage Students</h2>
                        <a href="add.php" class="btn btn-primary">+ Add New Student</a>
                    </div>

                    <!-- Filters -->
                    <div class="row mb-4">
                        <!-- Filters -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <select id="hostelFilter" class="form-select">
                                    <option value="all">All Hostels</option>
                                    <?php foreach ($hostels as $hostel): ?>
                                        <option value="<?= $hostel['id'] ?>"><?= htmlspecialchars($hostel['hostel_name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <select id="checkedInFilter" class="form-select">
                                    <option value="all">All Check-In Status</option>
                                    <option value="checked_in">Checked In</option>
                                    <option value="not_checked_in">Not Checked In</option>
                                </select>
                            </div>


                            <div class="col-md-3">
                                <select id="approvalFilter" class="form-select">
                                    <option value="all">All Status</option>
                                    <option value="approved">Approved</option>
                                    <option value="requested">Requested</option>
                                </select>
                            </div>


                            <div class="col-md-3">
                                <select id="verificationFilter" class="form-select">
                                    <option value="all">All Verification</option>
                                    <option value="verified">Verified</option>
                                    <option value="unverified">Unverified</option>
                                </select>
                            </div>
                        </div>

                    </div>

                    <!-- Students table will be loaded here -->
                    <div id="studentsTable" class="table-responsive"></div>

                    <div id="deleteMessage" class="alert d-none mt-3"></div>
                </div>
            </div>

        </main>
    </div>
</div>

<script>
    function loadStudents() {
        const approval = $('#approvalFilter').val();
        const hostelId = $('#hostelFilter').val();
        const verification = $('#verificationFilter').val();
        const checkedIn = $('#checkedInFilter').val(); // new

        const url = "<?= BASE_URL . '/admin/php_files/sections/students/fetch_students.php' ?>";

        $.get(url, {
            approval,
            hostel_id: hostelId,
            verification,
            checked_in: checkedIn
        }, function(html) {
            $('#studentsTable').html(html);
        });
    }

    $('#approvalFilter, #hostelFilter, #verificationFilter, #checkedInFilter').on('change', loadStudents);

    function showMessage(type, message) {
        const alertDiv = $('#deleteMessage');
        alertDiv.removeClass('alert-success alert-danger d-none')
            .addClass('alert-' + type)
            .text(message)
            .show();
        setTimeout(() => alertDiv.fadeOut(), 3000);
    }

    $(document).ready(function() {
        loadStudents();

        $(document).on('click', '.delete-student', function(e) {
            e.preventDefault();
            const id = $(this).data('id');
            if (confirm('Are you sure you want to delete this student?')) {
                $.ajax({
                    url: '<?= BASE_URL ?>/admin/php_files/sections/students/delete.php',
                    method: 'POST',
                    data: {
                        id: id
                    },
                    success: function(response) {
                        if (response.success) {
                            showMessage('success', response.message);
                            loadStudents();
                        } else {
                            showMessage('danger', response.message);
                        }
                    },
                    error: function() {
                        showMessage('danger', 'Error deleting student');
                    }
                })
            }
        });
    });
</script>


<?php require_once BASE_PATH . '/admin/includes/footer_admin.php'; ?>