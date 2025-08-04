<?php
ob_start();

require_once __DIR__ . '/../../../config/config.php';
include BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/config/auth.php';
require_once BASE_PATH . '/includes/slide_message.php';

require_admin();

$complaint_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// Get complaint details
$complaint = null;
$stmt = $conn->prepare("
    SELECT c.*, cat.name AS category_name, h.hostel_name, 
           s.first_name, s.last_name, s.varsity_id, s.contact_number, s.email,
           r.room_number, a.firstname AS admin_firstname, a.lastname AS admin_lastname,
           fl.floor_number, fl.floor_name
    FROM complaints c
    JOIN complaint_categories cat ON c.category_id = cat.id
    JOIN hostels h ON c.hostel_id = h.id
    JOIN students s ON c.student_id = s.id
    LEFT JOIN rooms r ON c.room_id = r.id
    LEFT JOIN floors fl ON r.floor_id = fl.id
    LEFT JOIN admins a ON c.resolved_by = a.id
    WHERE c.id = ?
");
$stmt->bind_param('i', $complaint_id);
$stmt->execute();
$result = $stmt->get_result();
$complaint = $result->fetch_assoc();
$stmt->close();

if (!$complaint) {
    header("Location: index.php");
    exit();
}

require_once BASE_PATH . '/admin/includes/header_admin.php';

// Get attachments
$stmt = $conn->prepare("SELECT * FROM complaint_attachments WHERE complaint_id = ?");
$stmt->bind_param('i', $complaint_id);
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
$stmt->bind_param('i', $complaint_id);
$stmt->execute();
$comments = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $status = trim($_POST['update_status']);
    $comment = trim($_POST['comment'] ?? '');
    $admin_id = $_SESSION['admin']['id'];

    $conn->begin_transaction();
    try {
        if ($status === 'resolved') {
            $stmt = $conn->prepare("UPDATE complaints SET status = ?, resolved_at = NOW(), resolved_by = ? WHERE id = ?");
            $stmt->bind_param('sii', $status, $admin_id, $complaint_id);
        } else {
            $stmt = $conn->prepare("UPDATE complaints SET status = ?, resolved_at = NULL, resolved_by = NULL WHERE id = ?");
            $stmt->bind_param('si', $status, $complaint_id);
        }

        if (!$stmt->execute()) {
            throw new Exception("Failed to update complaint status");
        }
        $stmt->close();

        if (!empty($comment)) {
            $stmt = $conn->prepare("INSERT INTO complaint_comments (complaint_id, user_id, user_type, comment) VALUES (?, ?, 'admin', ?)");
            $stmt->bind_param('iis', $complaint_id, $admin_id, $comment);
            if (!$stmt->execute()) {
                throw new Exception("Failed to add comment");
            }
            $stmt->close();
        }

        // Handle file upload
        if (!empty($_FILES['attachment']['name'])) {
            $upload_dir = BASE_PATH . '/uploads/complaints/' . $complaint_id . '/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $file_name = basename($_FILES['attachment']['name']);
            $file_type = $_FILES['attachment']['type'];
            $file_size = (int) $_FILES['attachment']['size'];
            $relative_path = '/uploads/complaints/' . $complaint_id . '/' . $file_name;

            if (!move_uploaded_file($_FILES['attachment']['tmp_name'], $upload_dir . $file_name)) {
                throw new Exception("Failed to upload file");
            }

            $stmt = $conn->prepare("
                INSERT INTO complaint_attachments (complaint_id, file_path, file_name, file_type, file_size) 
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->bind_param('isssi', $complaint_id, $relative_path, $file_name, $file_type, $file_size);
            if (!$stmt->execute()) {
                throw new Exception("Failed to save attachment record");
            }
            $stmt->close();
        }

        $conn->commit();
        $_SESSION['success'] = "Complaint status updated successfully!";
        header("Location: view.php?id=$complaint_id");
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        $error = $e->getMessage();
    }
}

// Handle new comment only
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_comment'])) {
    $comment = trim($_POST['comment']);
    $admin_id = $_SESSION['admin']['id'];

    $stmt = $conn->prepare("INSERT INTO complaint_comments (complaint_id, user_id, user_type, comment) VALUES (?, ?, 'admin', ?)");
    $stmt->bind_param('iis', $complaint_id, $admin_id, $comment);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Comment added successfully!";
        $stmt->close();
        header("Location: view.php?id=$complaint_id");
        exit();
    } else {
        $error = "Failed to add comment: " . $conn->error;
        $stmt->close();
    }
}
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

        <main class="col-md-10 ms-sm-auto px-md-4 py-4" style="max-height: calc(100vh - 118.44px);" >
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Complaint Details</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="index.php" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Complaints
                    </a>
                </div>
            </div>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>




            <div class="row">
                <div id="scroll-container" class="col-md-8" style="max-height: 720px; overflow-y: auto">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5>Complaint #<?= $complaint['id'] ?>: <?= htmlspecialchars($complaint['title']) ?></h5>
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
                                                    <small class="text-muted float-end">
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

                            <form method="POST" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label for="comment" class="form-label">Add Comment</label>
                                    <textarea class="form-control" style="resize: none;" id="comment" name="comment" rows="3"></textarea>
                                </div>

                                <div class="mb-3">
                                    <label for="attachment" class="form-label">Attachment (optional)</label>
                                    <input type="file" class="form-control" id="attachment" name="attachment">
                                </div>

                                <div class="d-flex justify-content-between">
                                    <button type="submit" name="add_comment" class="btn btn-primary">
                                        <i class="bi bi-chat-left-text"></i> Add Comment
                                    </button>

                                    <div class="dropdown">
                                        <button class="btn btn-secondary dropdown-toggle" type="button" id="statusDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                            Update Status
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="statusDropdown">
                                            <li>
                                                <button type="submit" name="update_status" value="pending" class="dropdown-item">Pending</button>
                                            </li>
                                            <li>
                                                <button type="submit" name="update_status" value="in_progress" class="dropdown-item">In Progress</button>
                                            </li>
                                            <li>
                                                <button type="submit" name="update_status" value="resolved" class="dropdown-item">Resolved</button>
                                            </li>
                                            <li>
                                                <button type="submit" name="update_status" value="rejected" class="dropdown-item">Rejected</button>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5>Complaint Details</h5>
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
                            <h5>Student Information</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm">
                                <tr>
                                    <th>Name:</th>
                                    <td><?= htmlspecialchars($complaint['first_name'] . ' ' . $complaint['last_name']) ?></td>
                                </tr>
                                <tr>
                                    <th>Varsity ID:</th>
                                    <td><?= htmlspecialchars($complaint['varsity_id']) ?></td>
                                </tr>
                                <tr>
                                    <th>Email:</th>
                                    <td><?= htmlspecialchars($complaint['email']) ?></td>
                                </tr>
                                <tr>
                                    <th>Phone:</th>
                                    <td><?= htmlspecialchars($complaint['contact_number']) ?></td>
                                </tr>
                                <tr>
                                    <th>Hostel:</th>
                                    <td><?= htmlspecialchars($complaint['hostel_name']) ?></td>
                                </tr>
                                <tr>
                                    <th>Floor:</th>
                                    <td><?= $complaint['floor_name'] . ' (' . $complaint['floor_number'] . ')' ?? 'N/A' ?></td>
                                </tr>
                                <tr>
                                    <th>Room:</th>
                                    <td><?= $complaint['room_number'] ?? 'N/A' ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
<script src="<?= BASE_URL . '/vendor/bootstrap/js/bootstrap.min.js' ?>"></script>

<!-- Inside the <body> section or near bottom -->
<?php if (isset($_SESSION['success'])): ?>
    <script>
        $(document).ready(function() {
            showSlideMessage("<?= htmlspecialchars($_SESSION['success'], ENT_QUOTES) ?>", "success");
        });
    </script>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<script>
    $(document).ready(function () {
        var $container = $('#scroll-container');
        var $comments = $container.find('.comment-section .card');
        if ($comments.length > 0) {
            $container.scrollTop($container[0].scrollHeight);
        }
    });
</script>



<?php include BASE_PATH . '/admin/includes/footer_admin.php'; ?>