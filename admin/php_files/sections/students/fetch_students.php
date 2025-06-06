<?php
require_once __DIR__ . '/../../../../config/config.php';
require_once BASE_PATH . '/config/db.php';

$approval = $_GET['approval'] ?? 'all';
$hostelId = $_GET['hostel_id'] ?? 'all';

$sql = "
    SELECT 
        students.id,
        students.first_name,
        students.last_name,
        students.email,
        students.contact_number AS phone,
        students.is_approved AS status,
        hostels.hostel_name
    FROM students
    LEFT JOIN hostels ON students.hostel_id = hostels.id
    WHERE 1
";

if ($approval !== 'all') {
    $status = $conn->real_escape_string($approval);
    // Your 'approval' filter might be 'approved' or 'requested' - adjust accordingly
    // Assuming status is stored as boolean is_approved:
    if ($status === 'approved') {
        $sql .= " AND students.is_approved = 1";
    } elseif ($status === 'requested') {
        $sql .= " AND students.is_approved = 0";
    }
}

if ($hostelId !== 'all') {
    $hostelId = (int)$hostelId;
    $sql .= " AND students.hostel_id = $hostelId";
}

$sql .= " ORDER BY students.id DESC";

$result = $conn->query($sql);

?>

<table class="table table-bordered table-striped">
    <thead class="table-dark">
        <tr>
            <th>#</th>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Status</th>
            <th>Hostel</th>
            <th>Edit</th>
            <th>Delete</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($result && $result->num_rows > 0): ?>
            <?php $i = 1; ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $i++ ?></td>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><?= htmlspecialchars($row['phone']) ?></td>
                    <td><?= $row['status'] ? 'Approved' : 'Requested' ?></td>
                    <td><?= htmlspecialchars($row['hostel_name'] ?? 'N/A') ?></td>
                    <td>
                        <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-danger delete-student" data-id="<?= $row['id'] ?>">Delete</button>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="9" class="text-center">No students found.</td>
            </tr>
        <?php endif; ?>
    </tbody>

</table>