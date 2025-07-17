<?php
require_once __DIR__ . '/../../../../config/config.php';
require_once BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/config/auth.php';

require_admin();

$approval = $_GET['approval'] ?? 'all';
$hostelId = $_GET['hostel_id'] ?? 'all';
$verification = $_GET['verification'] ?? 'all';
$checkedIn = $_GET['checked_in'] ?? 'all';
$search = trim($_GET['search'] ?? '');

$whereClauses = [];
$params = [];
$types = '';

// Approval filter
if ($approval === 'approved') {
    $whereClauses[] = "students.is_approved = 1";
} elseif ($approval === 'requested') {
    $whereClauses[] = "students.is_approved = 0";
}

// Verification filter
if ($verification === 'verified') {
    $whereClauses[] = "students.is_verified = 1";
} elseif ($verification === 'unverified') {
    $whereClauses[] = "students.is_verified = 0";
}

// Hostel filter
if ($hostelId !== 'all') {
    $whereClauses[] = "students.hostel_id = ?";
    $params[] = $hostelId;
    $types .= 'i';
}

// Check-in filter
if ($checkedIn === 'checked_in') {
    $whereClauses[] = "students.is_checked_in = 1";
} elseif ($checkedIn === 'not_checked_in') {
    $whereClauses[] = "students.is_checked_in = 0";
}

// Search filter - varsity_id, email, contact_number
if (!empty($search)) {
    $whereClauses[] = "(students.varsity_id LIKE CONCAT('%', ?, '%') 
        OR students.email LIKE CONCAT('%', ?, '%') 
        OR students.contact_number LIKE CONCAT('%', ?, '%'))";
    $params[] = $search;
    $params[] = $search;
    $params[] = $search;
    $types .= 'sss';
}

// Build WHERE SQL
$whereSQL = '';
if (!empty($whereClauses)) {
    $whereSQL = 'WHERE ' . implode(' AND ', $whereClauses);
}

$sql = "
    SELECT students.*, hostels.hostel_name, rooms.room_number
    FROM students
    LEFT JOIN hostels ON students.hostel_id = hostels.id
    LEFT JOIN rooms ON students.room_id = rooms.id
    $whereSQL
    ORDER BY students.created_at DESC
";

$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<div style="max-height: 400px; overflow-y: auto;">
    <table class="table table-bordered table-hover">
        <thead>
            <tr>
                <th>#</th>
                <th>Image</th>
                <th>Name</th>
                <th>Varsity ID</th>
                <th>Email</th>
                <th>Hostel</th>
                <th>Room</th>
                <th>Verified</th>
                <th>Approved</th>
                <th>Checked In</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php $serial = 1; ?>
                <?php while ($student = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $serial++ ?></td>
                        <td>
                            <?php if ($student['profile_image_url']): ?>
                                <img src="<?= htmlspecialchars($student['profile_image_url']) ?>" alt="Profile" width="50" height="50">
                            <?php else: ?>
                                N/A
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?></td>
                        <td><?= htmlspecialchars($student['varsity_id']) ?></td>
                        <td><?= htmlspecialchars($student['email']) ?></td>
                        <td><?= htmlspecialchars($student['hostel_name'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($student['room_number'] ?? 'N/A') ?></td>
                        <td>
                            <?= $student['is_verified'] ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-secondary">No</span>' ?>
                        </td>
                        <td>
                            <?= $student['is_approved'] ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-warning text-dark">No</span>' ?>
                        </td>
                        <td>
                            <?= $student['is_checked_in'] ? '<span class="badge bg-success">Checked In</span>' : '<span class="badge bg-danger">Not Checked In</span>' ?>
                        </td>
                        <td>
                            <a href="view.php?id=<?= $student['id'] ?>" class="btn btn-sm btn-info text-light">View</a>
                            <a href="edit.php?id=<?= $student['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                            <button class="btn btn-sm btn-danger delete-student" data-id="<?= $student['id'] ?>">Delete</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="11" class="text-center">No students found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
$stmt->close();
$conn->close();
?>
