<?php
session_start();
require_once __DIR__ . '/../../../config/config.php';
require_once BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/admin/php_files/auth_check_admin.php';  // keep your auth check

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
            <!-- Title + Add New button side by side -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2>Manage Students</h2>
                <a href="add.php" class="btn btn-primary">+ Add New Student</a>
            </div>

            <!-- Filters -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <select id="approvalFilter" class="form-select">
                        <option value="all">All Status</option>
                        <option value="approved">Approved</option>
                        <option value="requested">Requested</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <select id="hostelFilter" class="form-select">
                        <option value="all">All Hostels</option>
                        <?php foreach ($hostels as $hostel): ?>
                            <option value="<?= $hostel['id'] ?>"><?= htmlspecialchars($hostel['hostel_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Students table will be loaded here -->
            <div id="studentsTable"></div>
        </main>
    </div>
</div>

<script>
    function loadStudents() {
        const approval = $('#approvalFilter').val();
        const hostelId = $('#hostelFilter').val();

        const url = "<?= BASE_URL . '/admin/php_files/sections/students/fetch_students.php' ?>";

        $.get(url, { approval: approval, hostel_id: hostelId }, function(html) {
            $('#studentsTable').html(html);
        });
    }

    $('#approvalFilter, #hostelFilter').on('change', loadStudents);

    $(document).ready(function() {
        loadStudents();

        $(document).on('click', '.delete-student', function() {
            const id = $(this).data('id');
            if (confirm('Are you sure you want to delete this student?')) {
                $.post('delete.php', { id: id }, function(data) {
                    if (data.success) {
                        loadStudents();
                        alert(data.message);
                    } else {
                        alert('Delete failed: ' + data.message);
                    }
                }, 'json');
            }
        });
    });
</script>

<?php require_once BASE_PATH . '/admin/includes/footer_admin.php'; ?>
