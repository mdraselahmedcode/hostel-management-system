<?php
require_once __DIR__ . '/../../../config/config.php';
require_once BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/config/auth.php';
require_once BASE_PATH . '/includes/slide_message.php';
require_once BASE_PATH . '/student/includes/header_student.php';

require_student();

$student_id = $_SESSION['student']['id'];

// Get and sanitize filter
$status = isset($_GET['status']) ? trim($_GET['status']) : '';
$allowed_statuses = ['pending', 'approved', 'rejected', 'cancelled'];
$use_status_filter = in_array($status, $allowed_statuses);

// Prepare base query
$sql = "
    SELECT rcr.*, r.room_number, h.hostel_name
    FROM room_change_requests rcr
    LEFT JOIN rooms r ON rcr.preferred_room_id = r.id
    LEFT JOIN hostels h ON r.hostel_id = h.id
    WHERE rcr.student_id = ?
";

if ($use_status_filter) {
    $sql .= " AND rcr.status = ?";
}

$sql .= " ORDER BY rcr.requested_at DESC";

$stmt = $conn->prepare($sql);

if ($use_status_filter) {
    $stmt->bind_param("is", $student_id, $status);
} else {
    $stmt->bind_param("i", $student_id);
}

$stmt->execute();
$requests = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

function badgeClassByStatus($status) {
    return match($status) {
        'pending' => 'bg-secondary',
        'approved' => 'bg-success',
        'rejected' => 'bg-danger',
        'cancelled' => 'bg-warning text-dark',
        default => 'bg-secondary',
    };
}

function reasonLabel($reason) {
    return ucfirst(str_replace('_', ' ', $reason));
}
?>

<div class="content container-fluid">
    <div class="row full-height">
        <!-- Sidebar -->
        <?php include BASE_PATH . '/student/includes/sidebar_student.php'; ?>

        <!-- Main content -->
        <main class="col-12 col-md-10 col-lg-10 py-5 pt-3" style="max-height: calc(100vh - 142.75px); overflow-y:auto;">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>My Room Change Requests</h2>
                <a href="create.php" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> New Request
                </a>
            </div>

            <div class="card mb-4">
                <div class="d-flex flex-wrap justify-content-between align-items-center">
                    <h5 class="mb-2 mb-md-0 p-3">Requests List</h5>
                    <div class="btn-group btn-group-sm">
                        <a href="?status=" class="btn btn-outline-primary <?= empty($status) ? 'active' : '' ?>">All</a>
                        <a href="?status=pending" class="btn btn-outline-secondary <?= $status === 'pending' ? 'active' : '' ?>">Pending</a>
                        <a href="?status=approved" class="btn btn-outline-success <?= $status === 'approved' ? 'active' : '' ?>">Approved</a>
                        <a href="?status=rejected" class="btn btn-outline-danger <?= $status === 'rejected' ? 'active' : '' ?>">Rejected</a>
                        <a href="?status=cancelled" class="btn btn-outline-warning <?= $status === 'cancelled' ? 'active' : '' ?>">Cancelled</a>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <?php if (empty($requests)): ?>
                    <div class="alert alert-info">No room change requests found.</div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Preferred Room</th>
                                    <th>Hostel</th>
                                    <th>Reason</th>
                                    <th>Status</th>
                                    <th>Requested At</th>
                                    <th>Reviewed At</th>
                                    <th>Admin Comment</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($requests as $req): ?>
                                    <tr>
                                        <td><?= $req['id'] ?></td>
                                        <td><?= $req['preferred_room_id'] ? htmlspecialchars($req['room_number']) : '<em>Not specified</em>' ?></td>
                                        <td><?= $req['hostel_name'] ? htmlspecialchars($req['hostel_name']) : '<em>-</em>' ?></td>
                                        <td><?= reasonLabel($req['reason']) ?></td>
                                        <td>
                                            <span class="badge <?= badgeClassByStatus($req['status']) ?>">
                                                <?= ucfirst($req['status']) ?>
                                            </span>
                                        </td>
                                        <td><?= date('d M Y', strtotime($req['requested_at'])) ?></td>
                                        <td><?= $req['reviewed_at'] ? date('d M Y', strtotime($req['reviewed_at'])) : '<em>Not reviewed</em>' ?></td>
                                        <td><?= htmlspecialchars($req['admin_comment']) ?: '<em>-</em>' ?></td>
                                        <td class="d-flex gap-2">
                                            <a href="view.php?id=<?= $req['id'] ?>" class="btn btn-sm btn-primary" title="View Request">
                                                <i class="bi bi-eye"></i>
                                            </a>

                                            <?php if ($req['status'] === 'pending'): ?>
                                                <button
                                                    class="btn btn-sm btn-danger delete-request-btn"
                                                    data-id="<?= $req['id'] ?>"
                                                    title="Delete Request">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</div>

<script>
$(document).ready(function() {
    

    // Handle room change request delete button
    $(document).on('click', '.delete-request-btn', function (e) {
        e.preventDefault();

        const requestId = $(this).data('id');
        const $row = $(this).closest('tr');

        if (!confirm('Are you sure you want to delete this room change request?')) return;

        $.ajax({
            url: '<?= BASE_URL . '/student/php_files/sections/room_change_request/delete.php' ?>',
            type: 'POST',
            data: { id: requestId },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    showSlideMessage(response.message, 'success');
                    $row.fadeOut(300, function () {
                        $(this).remove();
                    });
                } else {
                    showSlideMessage(response.message, 'danger');
                }
            },
            error: function () {
                showSlideMessage('Error occurred while trying to delete the request.', 'danger');
            }
        });
    });

});
</script>

<?php require_once BASE_PATH . '/student/includes/footer_student.php'; ?>
