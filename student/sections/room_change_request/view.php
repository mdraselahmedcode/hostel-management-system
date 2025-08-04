<?php
require_once __DIR__ . '/../../../config/config.php';
require_once BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/config/auth.php';
require_once BASE_PATH . '/includes/slide_message.php';

require_student(); // Ensure only logged-in students can access

$student_id = $_SESSION['student']['id'];
$request_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$request_id) {
    header("Location: index.php");
    exit();
}

function badgeClassByStatus($status) {
    return match ($status) {
        'pending'   => 'bg-secondary',
        'approved'  => 'bg-success',
        'rejected'  => 'bg-danger',
        'cancelled' => 'bg-warning text-dark',
        default     => 'bg-info'
    };
}

// ✅ First fetch the request with student ownership
$sql = "SELECT r.*, s.first_name, s.last_name, s.varsity_id, s.contact_number, s.email,
               cr.id AS current_room_id, cr.room_number AS current_room_number,
               pr.id AS preferred_room_id, pr.room_number AS preferred_room_number,
               a.firstname AS admin_first, a.lastname AS admin_last
        FROM room_change_requests r
        JOIN students s ON r.student_id = s.id
        JOIN rooms cr ON s.room_id = cr.id
        LEFT JOIN rooms pr ON r.preferred_room_id = pr.id
        LEFT JOIN admins a ON r.reviewed_by = a.id
        WHERE r.id = ? AND r.student_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param('ii', $request_id, $student_id);
$stmt->execute();
$result = $stmt->get_result();
$request = $result->fetch_assoc();
$stmt->close();

if (!$request) {
    header("Location: index.php");
    exit();
}

// ✅ Get current room details
$current_room = null;
if ($request['current_room_id']) {
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
}

// ✅ Get preferred room details
$preferred_room = null;
if (!empty($request['preferred_room_id'])) {
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

include BASE_PATH . '/student/includes/header_student.php';
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

<div class="content container-fluid ">
    <div class="row full-height">
        <?php include BASE_PATH . '/student/includes/sidebar_student.php'; ?>

        <main class="col-md-10 ms-sm-auto px-md-4 py-4">
            <div class="mb-4">
                <a href="javascript:history.back()" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back
                </a>
            </div>

            <div class="d-flex justify-content-between align-items-center pb-2 mb-3 border-bottom">
                <h1 class="h4">Room Change Request #<?= $request['id'] ?></h1>
                <span class="badge <?= badgeClassByStatus($request['status']) ?>">
                    <?= ucfirst($request['status']) ?>
                </span>
            </div>

            <div  style="max-height: 700px; overflow-y: auto; overflow-x: hidden;">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header bg-primary">
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
                                    <p class="text-danger mb-0">Current room info not found.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header bg-primary">
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
                                    <p class="text-muted">No preferred room selected.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header bg-primary">
                        <i class="bi bi-info-circle-fill me-2"></i>Request Details
                    </div>
                    <div class="card-body">
                        <dl class="row">
                            <dt class="col-sm-4">Requested On</dt>
                            <dd class="col-sm-8"><?= date('M d, Y h:i A', strtotime($request['requested_at'])) ?></dd>

                            <dt class="col-sm-4">Reason</dt>
                            <dd class="col-sm-8"><?= ucfirst(str_replace('_', ' ', $request['reason'])) ?></dd>

                            <dt class="col-sm-4">Details</dt>
                            <dd class="col-sm-8"><?= $request['details'] ? nl2br(htmlspecialchars($request['details'])) : '<em>No additional details provided</em>' ?></dd>
                        </dl>
                    </div>
                </div>

                <?php if ($request['status'] !== 'pending' && $request['admin_comment']): ?>
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="bi bi-chat-square-text-fill me-2"></i>Admin Feedback
                        </div>
                        <div class="card-body">
                            <p><?= nl2br(htmlspecialchars($request['admin_comment'])) ?></p>
                            <?php if ($request['reviewed_at']): ?>
                                <small class="text-muted">
                                    Processed on <?= date('M d, Y h:i A', strtotime($request['reviewed_at'])) ?>
                                </small>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</div>

<?php include BASE_PATH . '/student/includes/footer_student.php'; ?>
