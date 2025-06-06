<?php
session_start();
require_once __DIR__ . '/../../../config/config.php';
require_once BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/admin/php_files/auth_check_admin.php';

// Get student ID from GET or POST
$studentId = 0;
if (isset($_GET['id'])) {
    $studentId = (int) $_GET['id'];
} elseif (isset($_POST['student_id'])) {
    $studentId = (int) $_POST['student_id'];
}

if ($studentId <= 0) die("Invalid student ID.");

// Handle POST update
// Handle POST update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $approval_status = $_POST['approval_status'] ?? 'requested';
    $hostel_id = isset($_POST['hostel_id']) ? (int) $_POST['hostel_id'] : null;
    $password = trim($_POST['password'] ?? '');

    if ($full_name === '' || $email === '' || !in_array($approval_status, ['requested', 'approved'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Please fill all required fields correctly.'
        ]);
        exit;
    }

    $names = explode(' ', $full_name, 2);
    $first_name = $names[0];
    $last_name = $names[1] ?? '';

    $is_approved = $approval_status === 'approved' ? 1 : 0;

    // Build dynamic query
    if ($password !== '') {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE students SET first_name = ?, last_name = ?, email = ?, is_approved = ?, hostel_id = ?, password = ? WHERE id = ?");
        $stmt->bind_param("sssiiis", $first_name, $last_name, $email, $is_approved, $hostel_id, $hashed_password, $studentId);
    } else {
        $stmt = $conn->prepare("UPDATE students SET first_name = ?, last_name = ?, email = ?, is_approved = ?, hostel_id = ? WHERE id = ?");
        $stmt->bind_param("sssiii", $first_name, $last_name, $email, $is_approved, $hostel_id, $studentId);
    }

    $updated = $stmt->execute();
    $stmt->close();

    echo json_encode([
        'success' => $updated,
        'message' => $updated ? 'Student updated successfully.' : 'Failed to update student. Please try again.'
    ]);
    exit;
}


// Fetch student data for GET
$stmt = $conn->prepare("
    SELECT students.*, hostels.hostel_name
    FROM students
    LEFT JOIN hostels ON students.hostel_id = hostels.id
    WHERE students.id = ?
");
$stmt->bind_param("i", $studentId);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();
$stmt->close();

if (!$student) die("Student not found.");

// Combine first and last name for the form field
$student['full_name'] = trim($student['first_name'] . ' ' . $student['last_name']);

// Convert is_approved TINYINT to approval_status string for form
$student['approval_status'] = $student['is_approved'] == 1 ? 'approved' : 'requested';

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
            <a href="<?= BASE_URL . '/admin/sections/students/index.php' ?>" class="btn btn-secondary mb-3">Back to Student List</a>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="mb-4">Edit Student</h2>

                    <form id="editStudentForm" method="POST">
                        <!-- Passing editable student ID -->
                        <input type="hidden" name="student_id" value="<?= $studentId ?>">

                        <div class="mb-3">
                            <label for="full_name" class="form-label">Full Name</label>
                            <input type="text" name="full_name" id="full_name" class="form-control"
                                value="<?= htmlspecialchars($student['full_name']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" name="email" id="email" class="form-control"
                                value="<?= htmlspecialchars($student['email']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">New Password <small>(leave blank to keep unchanged)</small></label>
                            <input type="password" name="password" id="password" class="form-control" placeholder="Enter new password (optional)">
                        </div>


                        <div class="mb-3">
                            <label for="approval_status" class="form-label">Approval Status</label>
                            <select name="approval_status" id="approval_status" class="form-select" required>
                                <option value="requested" <?= $student['approval_status'] === 'requested' ? 'selected' : '' ?>>Requested</option>
                                <option value="approved" <?= $student['approval_status'] === 'approved' ? 'selected' : '' ?>>Approved</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="hostel_id" class="form-label">Hostel</label>
                            <?php if (empty($hostels)): ?>
                                <div class="alert alert-warning">
                                    No hostels found. Please add hostels first.
                                </div>
                            <?php else: ?>
                                <select name="hostel_id" id="hostel_id" class="form-select" required>
                                    <option value="">-- Select Hostel --</option>
                                    <?php foreach ($hostels as $hostel): ?>
                                        <option value="<?= $hostel['id'] ?>" <?= $student['hostel_id'] == $hostel['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($hostel['hostel_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            <?php endif; ?>
                        </div>

                        <button type="submit" class="btn btn-primary">Update Student</button>
                    </form>

                    <div id="showMessage" class="mt-3"></div>
                </div>
            </div>
        </main>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('#editStudentForm').on('submit', function(e) {
            e.preventDefault();

            const submitBtn = $(this).find('button[type="submit"]');
            submitBtn.prop('disabled', true).text('Updating...');

            $.ajax({
                url: '', // Same page POST
                method: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    const msgClass = response.success ? 'success' : 'danger';
                    $('#showMessage').html(`<div class="alert alert-${msgClass}">${response.message}</div>`);

                    if (response.success) {
                        setTimeout(() => {
                            window.location.href = '<?= BASE_URL . '/admin/sections/students/index.php' ?>';
                        }, 2000);
                    }

                    submitBtn.prop('disabled', false).text('Update Student');
                },
                error: function() {
                    $('#showMessage').html('<div class="alert alert-danger">An error occurred. Please try again.</div>');
                    submitBtn.prop('disabled', false).text('Update Student');
                }
            });
        });
    });
</script>

<?php require_once BASE_PATH . '/admin/includes/footer_admin.php'; ?>