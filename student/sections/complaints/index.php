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
$allowed_statuses = ['pending', 'in_progress', 'resolved', 'rejected'];
$use_status_filter = in_array($status, $allowed_statuses);

// Prepare base query
$sql = "
    SELECT c.*, cat.name AS category_name, h.hostel_name, r.room_number
    FROM complaints c
    JOIN complaint_categories cat ON c.category_id = cat.id
    JOIN hostels h ON c.hostel_id = h.id
    LEFT JOIN rooms r ON c.room_id = r.id
    WHERE c.student_id = ?
";

if ($use_status_filter) {
    $sql .= " AND c.status = ?";
}

$sql .= "
    ORDER BY 
        CASE c.priority
            WHEN 'urgent' THEN 1
            WHEN 'high' THEN 2
            WHEN 'medium' THEN 3
            WHEN 'low' THEN 4
        END, c.created_at DESC
";

$stmt = $conn->prepare($sql);
if ($use_status_filter) {
    $stmt->bind_param("is", $student_id, $status);
} else {
    $stmt->bind_param("i", $student_id);
}
$stmt->execute();
$complaints = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
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
    </style>
</head>

<div class="content container-fluid">
    <div class="row full-height">
        <!-- Sidebar -->
        <?php include BASE_PATH . '/student/includes/sidebar_student.php'; ?>

        <!-- Main content -->
        <main class="col-12 col-md-10 col-lg-10 py-5 pt-3" style="max-height: calc(100vh - 142.75px); overflow-y:auto;">
            <div class="mb-3 ">
                <a href="javascript:history.back()" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back
                </a>
            </div>
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>My Complaints</h2>
                <a href="create.php" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> New Complaint
                </a>
            </div>

            <div class="card mb-4">
                <div class="d-flex flex-wrap justify-content-between align-items-center">
                    <h5 class="mb-2 mb-md-0 p-3">Complaints List</h5>
                    <div class="btn-group btn-group-sm">
                        <a href="?status=" class="btn btn-outline-primary <?= empty($status) ? 'active' : '' ?>">All</a>
                        <a href="?status=pending" class="btn btn-outline-warning <?= $status === 'pending' ? 'active' : '' ?>">Pending</a>
                        <a href="?status=in_progress" class="btn btn-outline-info <?= $status === 'in_progress' ? 'active' : '' ?>">In Progress</a>
                        <a href="?status=resolved" class="btn btn-outline-success <?= $status === 'resolved' ? 'active' : '' ?>">Resolved</a>
                        <a href="?status=rejected" class="btn btn-outline-danger <?= $status === 'rejected' ? 'active' : '' ?>">Rejected</a>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <?php if (empty($complaints)): ?>
                    <div class="alert alert-info">No complaints found.</div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Title</th>
                                    <th>Category</th>
                                    <th>Priority</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($complaints as $complaint): ?>
                                    <tr>
                                        <td><?= $complaint['id'] ?></td>
                                        <td><?= htmlspecialchars($complaint['title']) ?></td>
                                        <td><?= htmlspecialchars($complaint['category_name']) ?></td>
                                        <td>
                                            <span class="badge 
                          <?= $complaint['priority'] === 'urgent' ? 'bg-danger' : ($complaint['priority'] === 'high' ? 'bg-warning text-dark' : ($complaint['priority'] === 'medium' ? 'bg-primary' : 'bg-secondary')) ?>">
                                                <?= ucfirst($complaint['priority']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge 
                          <?= $complaint['status'] === 'pending' ? 'bg-secondary' : ($complaint['status'] === 'in_progress' ? 'bg-info text-dark' : ($complaint['status'] === 'resolved' ? 'bg-success' : 'bg-danger')) ?>">
                                                <?= ucfirst(str_replace('_', ' ', $complaint['status'])) ?>
                                            </span>
                                        </td>
                                        <td><?= date('d M Y', strtotime($complaint['created_at'])) ?></td>


                                        <td class="d-flex gap-2">
                                            <a href="view.php?id=<?= $complaint['id'] ?>" class="btn btn-sm btn-primary">
                                                <i class="bi bi-eye"></i> View
                                            </a>

                                            <?php if ($complaint['status'] === 'pending'): ?>
                                                <button
                                                    class="btn btn-sm btn-danger delete-complaint-btn"
                                                    data-id="<?= $complaint['id'] ?>">
                                                    <i class="bi bi-trash"></i> Delete
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
    </div>
    </main>
</div>
</div>


<script>
    $(document).ready(function() {
        $('.delete-complaint-btn').on('click', function(e) {
            e.preventDefault();

            const complaintId = $(this).data('id');
            const $row = $(this).closest('tr');

            if (!confirm('Are you sure you want to delete this complaint?')) return;

            $.ajax({
                url: '<?= BASE_URL . '/student/php_files/sections/complaints/delete.php' ?>',
                type: 'POST',
                data: {
                    id: complaintId
                },
                success: function(response) {
                    if (response.success) {
                        showSlideMessage(response.message, 'success');
                        $row.fadeOut(300, function() {
                            $(this).remove();
                        });
                    } else {
                        showSlideMessage(response.message, 'danger');
                    }
                },
                error: function() {
                    showSlideMessage('An error occurred while deleting the complaint.', 'danger');
                }
            });
        });
    });
</script>


<?php require_once BASE_PATH . '/student/includes/footer_student.php'; ?>