<?php
require_once __DIR__ . '/../../../../config/config.php';
require_once BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/config/auth.php';
require_once BASE_PATH . '/includes/slide_message.php'; 

require_admin();

// Get request ID from URL
$request_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$request_id) {
    header("Location: index.php");
    exit();
}

// Function to get badge class by status
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

// Fetch the room change request details
$sql = "SELECT r.*, 
               s.first_name, s.last_name, s.varsity_id, s.contact_number, s.email,
               cr.id AS current_room_id,
               cr.room_number AS current_room, cr.floor_id AS current_floor_id,
               pr.room_number AS preferred_room, pr.floor_id AS preferred_floor_id,
               h.hostel_name, f.floor_number,
               a.firstname AS admin_first, a.lastname AS admin_last
        FROM room_change_requests r
        JOIN students s ON r.student_id = s.id
        JOIN rooms cr ON s.room_id = cr.id
        LEFT JOIN rooms pr ON r.preferred_room_id = pr.id
        LEFT JOIN hostels h ON pr.hostel_id = h.id OR cr.hostel_id = h.id
        LEFT JOIN floors f ON pr.floor_id = f.id OR cr.floor_id = f.id
        LEFT JOIN admins a ON r.reviewed_by = a.id
        WHERE r.id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $request_id);
$stmt->execute();
$result = $stmt->get_result();
$request = $result->fetch_assoc();
$stmt->close();

if (!$request) {
    header("Location: index.php");
    exit();
}

// Fetch current room details
$current_room_sql = "SELECT r.room_number, f.floor_number, h.hostel_name
                     FROM rooms r
                     JOIN floors f ON r.floor_id = f.id
                     JOIN hostels h ON r.hostel_id = h.id
                     WHERE r.id = ?";
$stmt = $conn->prepare($current_room_sql);
$stmt->bind_param('i', $request['current_room_id']);

$stmt->execute();
$current_room = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Fetch preferred room details (if specified)
$preferred_room = null;
if ($request['preferred_room_id']) {
    $preferred_room_sql = "SELECT r.room_number, f.floor_number, h.hostel_name
                           FROM rooms r
                           JOIN floors f ON r.floor_id = f.id
                           JOIN hostels h ON r.hostel_id = h.id
                           WHERE r.id = ?";
    $stmt = $conn->prepare($preferred_room_sql);
    $stmt->bind_param('i', $request['preferred_room_id']);
    $stmt->execute();
    $preferred_room = $stmt->get_result()->fetch_assoc();
    $stmt->close();
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
                <h1 class="h2">Room Change Request #<?= $request['id'] ?></h1>
                <span class="badge <?= badgeClassByStatus($request['status']) ?>">
                    <?= ucfirst($request['status']) ?>
                </span>
            </div>
            
            <div style="max-height: 700px; overflow-y: auto; overflow-x: hidden;">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="bi bi-person-fill me-2"></i>Student Information
                            </div>
                            <div class="card-body">
                                <dl class="row">
                                    <dt class="col-sm-4">Name</dt>
                                    <dd class="col-sm-8"><?= htmlspecialchars($request['first_name'] . ' ' . $request['last_name']) ?></dd>

                                    <dt class="col-sm-4">Student ID</dt>
                                    <dd class="col-sm-8"><?= htmlspecialchars($request['varsity_id']) ?></dd>

                                    <dt class="col-sm-4">Contact</dt>
                                    <dd class="col-sm-8"><?= htmlspecialchars($request['contact_number']) ?></dd>

                                    <dt class="col-sm-4">Email</dt>
                                    <dd class="col-sm-8"><?= htmlspecialchars($request['email']) ?></dd>
                                </dl>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="bi bi-house-door-fill me-2"></i>Current Room
                            </div>
                            <div class="card-body">
                                <?php if ($current_room): ?>
                                    <dl class="row">
                                        <dt class="col-sm-4">Hostel</dt>
                                        <dd class="col-sm-8"><?= htmlspecialchars($current_room['hostel_name']) ?></dd>

                                        <dt class="col-sm-4">Floor</dt>
                                        <dd class="col-sm-8"><?= htmlspecialchars($current_room['floor_number']) ?></dd>

                                        <dt class="col-sm-4">Room Number</dt>
                                        <dd class="col-sm-8"><?= htmlspecialchars($current_room['room_number']) ?></dd>
                                    </dl>
                                <?php else: ?>
                                    <p class="text-danger mb-0">Current room information not found.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="bi bi-info-circle-fill me-2"></i>Request Details
                            </div>
                            <div class="card-body">
                                <dl class="row">
                                    <dt class="col-sm-4">Request Date</dt>
                                    <dd class="col-sm-8"><?= date('M d, Y h:i A', strtotime($request['requested_at'])) ?></dd>

                                    <dt class="col-sm-4">Reason</dt>
                                    <dd class="col-sm-8"><?= ucfirst(str_replace('_', ' ', $request['reason'])) ?></dd>

                                    <dt class="col-sm-4">Details</dt>
                                    <dd class="col-sm-8"><?= $request['details'] ? nl2br(htmlspecialchars($request['details'])) : '<em>No additional details provided</em>' ?></dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="bi bi-house-up-fill me-2"></i>Preferred Room
                            </div>
                            <div class="card-body">
                                <?php if ($preferred_room): ?>
                                    <dl class="row">
                                        <dt class="col-sm-4">Hostel</dt>
                                        <dd class="col-sm-8"><?= htmlspecialchars($preferred_room['hostel_name']) ?></dd>

                                        <dt class="col-sm-4">Floor</dt>
                                        <dd class="col-sm-8"><?= htmlspecialchars($preferred_room['floor_number']) ?></dd>

                                        <dt class="col-sm-4">Room Number</dt>
                                        <dd class="col-sm-8"><?= htmlspecialchars($preferred_room['room_number']) ?></dd>
                                    </dl>
                                <?php else: ?>
                                    <p class="text-muted">No specific room preference selected</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if ($request['status'] === 'pending'): ?>
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="bi bi-gear-fill me-2"></i>Admin Actions
                        </div>
                        <div class="card-body">
                            <form method="POST" id="process-request-form">
                                <input type="hidden" name="request_id" value="<?= $request['id'] ?>">

                                <div class="mb-3">
                                    <label for="action" class="form-label">Action</label>
                                    <select class="form-select" id="action" name="action" required>
                                        <option value="">-- Select action --</option>
                                        <option value="approve">Approve</option>
                                        <option value="reject">Reject</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="admin_comment" class="form-label">Comments</label>
                                    <textarea class="form-control" style="resize: none;" id="admin_comment" name="admin_comment" rows="3"></textarea>
                                </div>

                                <button type="submit" class="btn btn-primary">Submit</button>
                            </form>
                        </div>
                    </div>
                <?php elseif ($request['status'] !== 'pending' && $request['admin_comment']): ?>
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="bi bi-chat-square-text-fill me-2"></i>Admin Comments
                        </div>
                        <div class="card-body">
                            <p><?= nl2br(htmlspecialchars($request['admin_comment'])) ?></p>
                            <?php if ($request['reviewed_at']): ?>
                                <small class="text-muted">
                                    Processed by <?= htmlspecialchars($request['admin_first'] . ' ' . $request['admin_last']) ?>
                                    on <?= date('M d, Y h:i A', strtotime($request['reviewed_at'])) ?>
                                </small>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</div>


<script>
$(document).ready(function() {
    $('#process-request-form').on('submit', function(e) {
        e.preventDefault(); // prevent page reload

        const $form = $(this);
        const $submitBtn = $form.find('button[type="submit"]');

        $submitBtn.prop('disabled', true); // disable while submitting

        $.ajax({
            url: '<?= BASE_URL . '/admin/php_files/sections/room_change_request/process_request.php' ?>', 
            type: 'POST',
            data: $form.serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showSlideMessage(response.message, 'success');
                    location.reload(); // reload on success
                } else {
                    showSlideMessage('Error: ' + response.message, 'danger');
                }
            },
            error: function(xhr, status, error) {
                showSlideMessage('An AJAX error occurred: ' + error, 'danger');
            },
            complete: function() {
                $submitBtn.prop('disabled', false); // re-enable button
            }
        });
    });
});
</script>



<?php include BASE_PATH . '/admin/includes/footer_admin.php'; ?>