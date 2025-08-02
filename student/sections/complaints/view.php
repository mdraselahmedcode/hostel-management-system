<?php
require_once __DIR__ . '/../../../config/config.php';
include BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/config/auth.php';
include BASE_PATH . '/includes/slide_message.php';

// Ensure student is logged in
require_student();

$complaint_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$student_id = $_SESSION['student']['id'];

// Get complaint details (with prepared statement)
$complaint = null;
$stmt = $conn->prepare("
    SELECT c.*, 
    cat.name AS category_name, 
    h.hostel_name,
    r.room_number, 
    a.firstname AS admin_firstname, 
    a.lastname AS admin_lastname,
    fl.floor_name,
    fl.floor_number
    FROM complaints c
    JOIN complaint_categories cat ON c.category_id = cat.id
    JOIN hostels h ON c.hostel_id = h.id
    LEFT JOIN rooms r ON c.room_id = r.id
    LEFT JOIN floors fl ON r.floor_id = fl.id
    LEFT JOIN admins a ON c.resolved_by = a.id
    WHERE c.id = ? AND c.student_id = ?
");
$stmt->bind_param("ii", $complaint_id, $student_id);
$stmt->execute();
$result = $stmt->get_result();
$complaint = $result->fetch_assoc();
$stmt->close();

if (!$complaint) {
    header("Location: index.php");
    exit();
}

// Get attachments
$stmt = $conn->prepare("SELECT * FROM complaint_attachments WHERE complaint_id = ?");
$stmt->bind_param("i", $complaint_id);
$stmt->execute();
$attachments = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Get comments
$stmt = $conn->prepare("
    SELECT cc.*, 
           CASE 
               WHEN cc.user_type = 'student' THEN CONCAT(s.first_name, ' ', s.last_name)
               ELSE CONCAT(a.firstname, ' ', a.lastname)
           END AS user_name,
           CASE 
               WHEN cc.user_type = 'student' THEN 'Student'
               ELSE 'Admin'
           END AS user_role
    FROM complaint_comments cc
    LEFT JOIN students s ON cc.user_type = 'student' AND cc.user_id = s.id
    LEFT JOIN admins a ON cc.user_type = 'admin' AND cc.user_id = a.id
    WHERE cc.complaint_id = ?
    ORDER BY cc.created_at ASC
");
$stmt->bind_param("i", $complaint_id);
$stmt->execute();
$comments = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Handle new comment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_comment'])) {
    $comment = trim($_POST['comment']);

    if (!empty($comment)) {
        $stmt = $conn->prepare("
            INSERT INTO complaint_comments (complaint_id, user_id, user_type, comment)
            VALUES (?, ?, 'student', ?)
        ");
        $stmt->bind_param("iis", $complaint_id, $student_id, $comment);
        if ($stmt->execute()) {
            $_SESSION['success'] = "Comment added successfully!";
            $stmt->close();
            header("Location: view.php?id=$complaint_id");
            exit();
        } else {
            $error = "Failed to add comment: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $error = "Comment cannot be empty.";
    }
}

include BASE_PATH . '/student/includes/header_student.php';
?>

<div class="content container mt-4" style="max-height: calc(100vh - 142.75px);">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Complaint #<?= $complaint['id'] ?>: <?= htmlspecialchars($complaint['title']) ?></h2>
        <a href="index.php" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to Complaints
        </a>
    </div>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
        <script>
            $(document).ready(function() {
                showSlideMessage(<?= json_encode($_SESSION['success']) ?>, 'success');
            });
        </script>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>


    <div class="row">
        <div id="scroll-container" class="col-md-8 shadow-sm" style="max-height: 650px; overflow-y: auto;">
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Details</h5>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h6>Description:</h6>
                        <p><?= nl2br(htmlspecialchars($complaint['description'])) ?></p>
                    </div>

                    <?php if (!empty($attachments)): ?>
                        <div class="mb-4">
                            <h6>Attachments:</h6>
                            <div class="row">
                                <?php foreach ($attachments as $attachment): ?>
                                    <div class="col-md-3 mb-3">
                                        <div class="card">
                                            <?php if (strpos($attachment['file_type'], 'image/') === 0): ?>
                                                <img src="<?= BASE_URL . $attachment['file_path'] ?>" class="card-img-top" alt="<?= htmlspecialchars($attachment['file_name']) ?>">
                                            <?php else: ?>
                                                <div class="card-body text-center">
                                                    <i class="bi bi-file-earmark-text fs-1"></i>
                                                </div>
                                            <?php endif; ?>
                                            <div class="card-footer">
                                                <small class="text-muted"><?= htmlspecialchars($attachment['file_name']) ?></small>
                                                <a href="<?= BASE_URL . $attachment['file_path'] ?>" target="_blank" class="btn btn-sm btn-outline-primary float-end">
                                                    <i class="bi bi-download"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="mb-4">
                        <h6>Comments:</h6>
                        <?php if (empty($comments)): ?>
                            <p class="text-muted">No comments yet.</p>
                        <?php else: ?>
                            <div class="comment-section">
                                <?php foreach ($comments as $comment): ?>
                                    <div class="card mb-3">
                                        <div class="card-header">
                                            <strong><?= htmlspecialchars($comment['user_name']) ?></strong>
                                            <small class="text-light float-end">
                                                <?= date('d M Y H:i', strtotime($comment['created_at'])) ?>
                                                (<?= $comment['user_role'] ?>)
                                            </small>
                                        </div>
                                        <div class="card-body">
                                            <p><?= nl2br(htmlspecialchars($comment['comment'])) ?></p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if ($complaint['status'] !== 'resolved' && $complaint['status'] !== 'rejected'): ?>
                        <form method="POST">
                            <div class="mb-3">
                                <label for="comment" class="form-label">Add Comment</label>
                                <textarea class="form-control" style="resize: none;" id="comment" name="comment" rows="5"></textarea>
                            </div>
                            <button type="submit" name="add_comment" class="btn btn-primary">
                                <i class="bi bi-chat-left-text"></i> Add Comment
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Complaint Information</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <th>Status:</th>
                            <td>
                                <span class="badge 
                                    <?= $complaint['status'] === 'pending' ? 'bg-secondary' : ($complaint['status'] === 'in_progress' ? 'bg-info text-dark' : ($complaint['status'] === 'resolved' ? 'bg-success' : 'bg-danger')) ?>">
                                    <?= ucfirst(str_replace('_', ' ', $complaint['status'])) ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Priority:</th>
                            <td>
                                <span class="badge 
                                    <?= $complaint['priority'] === 'urgent' ? 'bg-danger' : ($complaint['priority'] === 'high' ? 'bg-warning text-dark' : ($complaint['priority'] === 'medium' ? 'bg-primary' : 'bg-secondary')) ?>">
                                    <?= ucfirst($complaint['priority']) ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Category:</th>
                            <td><?= htmlspecialchars($complaint['category_name']) ?></td>
                        </tr>
                        <tr>
                            <th>Submitted:</th>
                            <td><?= date('d M Y H:i', strtotime($complaint['created_at'])) ?></td>
                        </tr>
                        <?php if ($complaint['status'] === 'resolved'): ?>
                            <tr>
                                <th>Resolved:</th>
                                <td>
                                    <?= date('d M Y H:i', strtotime($complaint['resolved_at'])) ?>
                                    <br><small>by <?= htmlspecialchars($complaint['admin_firstname'] . ' ' . $complaint['admin_lastname']) ?></small>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </table>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5>Hostel Information</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <th>Hostel:</th>
                            <td><?= htmlspecialchars($complaint['hostel_name']) ?></td>
                        </tr>
                        <tr>
                            <th>Room:</th>
                            <td><?= $complaint['room_number'] ?? 'N/A' ?></td>
                        </tr>
                        <tr>
                            <th>Floor:</th>
                            <td><?= $complaint['floor_name'] . ' (' . $complaint['floor_number'] . ')' ?? 'N/A' ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        var $container = $('#scroll-container');
        var $comments = $container.find('.comment-section .card');
        if ($comments.length > 0) {
            $container.scrollTop($container[0].scrollHeight);
        }
    });
</script>


<?php require_once BASE_PATH . '/student/includes/footer_student.php'; ?>