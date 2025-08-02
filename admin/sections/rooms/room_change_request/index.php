<?php
ob_start();
require_once __DIR__ . '/../../../../config/config.php';
require_once BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/config/auth.php';
require_once BASE_PATH . '/includes/slide_message.php'; 

require_admin();

// Filters
$status = isset($_GET['status']) ? trim($_GET['status']) : '';
$hostel_id = isset($_GET['hostel_id']) ? (int) $_GET['hostel_id'] : 0;

$where = [];
$params = [];

if (!empty($status)) {
    $where[] = 'r.status = ?';
    $params[] = $status;
}
if ($hostel_id > 0) {
    $where[] = 'h.id = ?';
    $params[] = $hostel_id;
}

$where_sql = count($where) ? 'WHERE ' . implode(' AND ', $where) : '';

$sql = "
    SELECT r.*, s.first_name, s.last_name, s.varsity_id,
           h.hostel_name, fl.floor_number, rm.room_number
    FROM room_change_requests r
    JOIN students s ON r.student_id = s.id
    LEFT JOIN rooms rm ON r.preferred_room_id = rm.id
    LEFT JOIN floors fl ON rm.floor_id = fl.id
    LEFT JOIN hostels h ON rm.hostel_id = h.id
    $where_sql
    ORDER BY r.requested_at DESC
";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $types = str_repeat('s', count($params));
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$requests = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$hostels = $conn->query("SELECT id, hostel_name FROM hostels ORDER BY hostel_name")->fetch_all(MYSQLI_ASSOC);

// Function to determine badge class based on status
function badgeClassByStatus($status)
{
    switch ($status) {
        case 'pending':
            return 'bg-secondary';
        case 'approved':
            return 'bg-success';
        case 'rejected':
            return 'bg-danger';
        case 'cancelled':
            return 'bg-warning text-dark';
        default:
            return 'bg-info';
    }
}

include BASE_PATH . '/admin/includes/header_admin.php';
?>

<div class="content container-fluid mt-5">
    <div class="row full-height">
        <?php include BASE_PATH . '/admin/includes/sidebar_admin.php'; ?>

        <main class="col-md-10 ms-sm-auto px-md-4 py-4">
            <div class="mb-4">
                <a href="javascript:history.back()" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back
                </a>
            </div>

            <div class="d-flex justify-content-between align-items-center pb-2 mb-3 border-bottom">
                <h1 class="h2">Room Change Requests</h1>
                <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#filterModal">
                    <i class="bi bi-funnel"></i> Filter
                </button>
            </div>

            <!-- Filter Modal -->
            <div class="modal fade" id="filterModal" tabindex="-1">
                <div class="modal-dialog">
                    <form class="modal-content" method="GET">
                        <div class="modal-header">
                            <h5 class="modal-title">Filter Requests</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select name="status" id="status" class="form-select">
                                    <option value="">All Statuses</option>
                                    <option value="pending" <?= $status === 'pending' ? 'selected' : '' ?>>Pending</option>
                                    <option value="approved" <?= $status === 'approved' ? 'selected' : '' ?>>Approved</option>
                                    <option value="rejected" <?= $status === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="hostel_id" class="form-label">Hostel</label>
                                <select name="hostel_id" id="hostel_id" class="form-select">
                                    <option value="0">All Hostels</option>
                                    <?php foreach ($hostels as $hostel): ?>
                                        <option value="<?= $hostel['id'] ?>" <?= $hostel_id == $hostel['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($hostel['hostel_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Apply</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Requests Table -->
            <div class="card">
                <div class="card-header">
                    Room Change Requests List
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Student</th>
                                    <th>Hostel</th>
                                    <th>Floor</th>
                                    <th>Room</th>
                                    <th>Reason</th>
                                    <th>Status</th>
                                    <th>Requested At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($requests as $req): ?>
                                    <tr>
                                        <td><?= $req['id'] ?></td>
                                        <td><?= htmlspecialchars($req['first_name'] . ' ' . $req['last_name']) ?><br><small class="text-muted">ID: <?= $req['varsity_id'] ?></small></td>
                                        <td><?= htmlspecialchars($req['hostel_name'] ?? 'N/A') ?></td>
                                        <td><?= htmlspecialchars($req['floor_number'] ?? '-') ?></td>
                                        <td><?= htmlspecialchars($req['room_number'] ?? '-') ?></td>
                                        <td><?= htmlspecialchars($req['reason']) ?></td>
                                        <td>
                                            <span class="badge <?= badgeClassByStatus($req['status']) ?>">
                                                <?= ucfirst($req['status']) ?>
                                            </span>
                                        </td>
                                        <td><?= date('d M Y', strtotime($req['requested_at'])) ?></td>
                                        <td>
                                            <a href="view.php?id=<?= $req['id'] ?>" class="btn btn-sm btn-primary">
                                                <i class="bi bi-eye"></i> View
                                            </a>

                                            <button class="btn btn-sm btn-danger delete-request-btn"
                                                data-id="<?= $req['id'] ?>">
                                                <i class="bi bi-trash"></i> Delete
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
$(document).ready(function () {
    $('.delete-request-btn').on('click', function () {
        const requestId = $(this).data('id');
        const $row = $(this).closest('tr'); // get the table row of the button

        if (!confirm('Are you sure you want to delete this room change request?')) return;

        $.ajax({
            url: '<?= BASE_URL . '/admin/php_files/sections/room_change_request/delete.php' ?>',
            type: 'POST',
            data: { id: requestId },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    showSlideMessage(response.message, 'success');

                    // Fade out the row
                    $row.fadeOut(500, function () {
                        $(this).remove(); // remove from DOM after fade
                    });

                    // Optionally reload after a delay
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showSlideMessage(response.message || 'Something went wrong', 'danger');
                }
            },
            error: function () {
                showSlideMessage('AJAX error occurred.', 'danger');
            }
        });
    });
});
</script>



<?php
require_once BASE_PATH . '/admin/includes/footer_admin.php';
ob_end_flush();
?>