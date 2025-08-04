<?php
require_once __DIR__ . '/../../../config/config.php';
include BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/config/auth.php';
require_admin();

// Fetch filters
$status = isset($_GET['status']) ? trim($_GET['status']) : '';
$hostel_id = isset($_GET['hostel_id']) ? (int) $_GET['hostel_id'] : 0;
$category_id = isset($_GET['category_id']) ? (int) $_GET['category_id'] : 0;
$priority = isset($_GET['priority']) ? trim($_GET['priority']) : '';

// Build WHERE clause
$where = [];
$params = [];

if (!empty($status)) {
    $where[] = 'c.status = ?';
    $params[] = $status;
}
if ($hostel_id > 0) {
    $where[] = 'c.hostel_id = ?';
    $params[] = $hostel_id;
}
if ($category_id > 0) {
    $where[] = 'c.category_id = ?';
    $params[] = $category_id;
}
if (!empty($priority)) {
    $where[] = 'c.priority = ?';
    $params[] = $priority;
}

$where_sql = count($where) ? 'WHERE ' . implode(' AND ', $where) : '';

$sql = "
    SELECT c.*, cat.name AS category_name, h.hostel_name, 
           s.first_name, s.last_name, s.varsity_id, r.room_number
    FROM complaints c
    JOIN complaint_categories cat ON c.category_id = cat.id
    JOIN hostels h ON c.hostel_id = h.id
    JOIN students s ON c.student_id = s.id
    LEFT JOIN rooms r ON c.room_id = r.id
    $where_sql
    ORDER BY 
        CASE c.priority 
            WHEN 'urgent' THEN 1
            WHEN 'high' THEN 2
            WHEN 'medium' THEN 3
            WHEN 'low' THEN 4
        END, 
        c.created_at DESC
";

// Prepare and execute
$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $types = str_repeat('s', count($params)); // All are strings or integers cast as strings for simplicity
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$complaints = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Fetch categories and hostels
$categories = $conn->query("SELECT id, name FROM complaint_categories ORDER BY name")->fetch_all(MYSQLI_ASSOC);
$hostels = $conn->query("SELECT id, hostel_name FROM hostels ORDER BY hostel_name")->fetch_all(MYSQLI_ASSOC);

include BASE_PATH . '/admin/includes/header_admin.php';
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
            <div class="mb-4">
                <a href="javascript:history.back()" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back
                </a>
            </div>
            
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Complaint Management</h1>

                <div class="btn-toolbar mb-2 mb-md-0">
                    <div class="btn-group me-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#filterModal">
                            <i class="bi bi-funnel"></i> Filter
                        </button>
                    </div>
                </div>
            </div>

            <!-- Filter Modal -->
            <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="filterModalLabel">Filter Complaints</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form method="GET" action="">
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-select" id="status" name="status">
                                        <option value="">All Statuses</option>
                                        <option value="pending" <?= $status === 'pending' ? 'selected' : '' ?>>Pending</option>
                                        <option value="in_progress" <?= $status === 'in_progress' ? 'selected' : '' ?>>In Progress</option>
                                        <option value="resolved" <?= $status === 'resolved' ? 'selected' : '' ?>>Resolved</option>
                                        <option value="rejected" <?= $status === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="hostel_id" class="form-label">Hostel</label>
                                    <select class="form-select" id="hostel_id" name="hostel_id">
                                        <option value="0">All Hostels</option>
                                        <?php foreach ($hostels as $hostel): ?>
                                            <option value="<?= $hostel['id'] ?>" <?= $hostel_id == $hostel['id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($hostel['hostel_name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="category_id" class="form-label">Category</label>
                                    <select class="form-select" id="category_id" name="category_id">
                                        <option value="0">All Categories</option>
                                        <?php foreach ($categories as $category): ?>
                                            <option value="<?= $category['id'] ?>" <?= $category_id == $category['id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($category['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="priority" class="form-label">Priority</label>
                                    <select class="form-select" id="priority" name="priority">
                                        <option value="">All Priorities</option>
                                        <option value="urgent" <?= $priority === 'urgent' ? 'selected' : '' ?>>Urgent</option>
                                        <option value="high" <?= $priority === 'high' ? 'selected' : '' ?>>High</option>
                                        <option value="medium" <?= $priority === 'medium' ? 'selected' : '' ?>>Medium</option>
                                        <option value="low" <?= $priority === 'low' ? 'selected' : '' ?>>Low</option>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Apply Filters</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <i class="bi bi-exclamation-triangle me-2"></i>Complaints List
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Title</th>
                                    <th>Student</th>
                                    <th>Hostel</th>
                                    <th>Room</th>
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
                                        <td>
                                            <?= htmlspecialchars($complaint['first_name'] . ' ' . $complaint['last_name']) ?>
                                            <br><small class="text-muted"><?= $complaint['varsity_id'] ?></small>
                                        </td>
                                        <td><?= htmlspecialchars($complaint['hostel_name']) ?></td>
                                        <td><?= $complaint['room_number'] ?? 'N/A' ?></td>
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
                                        <td>
                                            <a href="view.php?id=<?= $complaint['id'] ?>" class="btn btn-sm btn-primary">
                                                <i class="bi bi-eye"></i> View
                                            </a>
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

<?php include BASE_PATH . '/admin/includes/footer_admin.php'; ?>