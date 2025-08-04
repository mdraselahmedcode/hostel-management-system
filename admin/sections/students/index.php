<?php
require_once __DIR__ . '/../../../config/config.php';
require_once BASE_PATH . '/config/db.php';
include BASE_PATH . '/includes/slide_message.php';
require_once BASE_PATH . '/config/auth.php';

require_admin();

// Fetch hostels for dropdown
$hostels = [];
$result = $conn->query("SELECT id, hostel_name FROM hostels ORDER BY hostel_name ASC");
if ($result && $result->num_rows > 0) {
    $hostels = $result->fetch_all(MYSQLI_ASSOC);
}

require_once BASE_PATH . '/admin/includes/header_admin.php';
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

        .btn-outline-primary {
            color: var(--primary-color) !important;
            border-color: var(--primary-color) !important;
            background-color: transparent !important;
            transition: color 0.3s ease, background-color 0.3s ease, border-color 0.3s ease;
        }

        .btn-outline-primary:hover,
        .btn-outline-primary:focus,
        .btn-outline-primary:active {
            color: var(--primary-text) !important;
            background-color: var(--primary-color) !important;
            border-color: var(--primary-hover) !important;
            box-shadow: 0 0 0 0.2rem rgba(44, 62, 80, 0.25) !important;
        }

        .card.bg-primary {
            background-color: var(--primary-color) !important;
            color: var(--primary-text) !important;
        }
    </style>
</head>

<div class="content container-fluid mt-5">
    <div class="row full-height">
        <?php require_once BASE_PATH . '/admin/includes/sidebar_admin.php'; ?>

        <main class="col-md-10 ms-sm-auto px-md-4 py-4">
            <a href="javascript:history.back()" class="btn btn-secondary mb-3">Back</a>

            <div class="card shadow-sm">
                <div class="card-body">
                    <!-- Title + Add New button side by side -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2>Manage Students</h2>
                        <a href="add.php" class="btn btn-primary">+ Add New Student</a>
                    </div>

                    <!-- Search bar -->
                    <div class="mb-3">
                        <input type="text" id="searchInput" class="form-control" placeholder="Search by Varsity ID, Email, or Phone">
                    </div>

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

                    <!-- Students table will be loaded here -->
                    <div id="studentsTable" class="table-responsive"></div>
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
        const checkedIn = $('#checkedInFilter').val();
        const search = $('#searchInput').val().trim();

        const url = "<?= BASE_URL . '/admin/php_files/sections/students/fetch_students.php' ?>";

        $.get(url, {
            approval,
            hostel_id: hostelId,
            verification,
            checked_in: checkedIn,
            search,
            _: new Date().getTime()  // cache buster
        }, function(html) {
            $('#studentsTable').html(html);
        });
    }

    $('#approvalFilter, #hostelFilter, #verificationFilter, #checkedInFilter').on('change', loadStudents);
    $('#searchInput').on('input', loadStudents);

    $(document).ready(function() {
        loadStudents();

        $(document).on('click', '.delete-student', function(e) {
            e.preventDefault();
            const id = $(this).data('id');
            if (confirm('Are you sure you want to delete this student?')) {
                $.ajax({
                    url: '<?= BASE_URL ?>/admin/php_files/sections/students/delete.php',
                    method: 'POST',
                    data: { id: id },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            showSlideMessage(response.message, 'success');
                            loadStudents();
                        } else {
                            showSlideMessage(response.message || 'Failed to delete student.', 'danger');
                        }
                    },
                    error: function() {
                        showSlideMessage('Error deleting student.', 'danger');
                    }
                });
            }
        });
    });
</script>

<?php require_once BASE_PATH . '/admin/includes/footer_admin.php'; ?>
