<?php
session_start();
require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../config/db.php';
require_once BASE_PATH . '/student/includes/header_student.php';

// Ensure student is logged in
if (!isset($_SESSION['student']['id'])) {
    header('Location: ' . BASE_URL . '/student/login_student.php');
    exit;
}

$student_id = $_SESSION['student']['id'];

// Fetch room, hostel, floor, room type, and roommates
global $conn;
$room_details = null;
$roommates = [];

$sql = "SELECT s.id AS student_id, s.first_name, s.last_name, s.email, s.profile_image_url,
               r.id AS room_id, r.room_number, r.max_capacity, r.hostel_id AS room_hostel_id, r.room_type_id,
               f.id AS floor_id, f.floor_number, f.floor_name,
               h.id AS hostel_id, h.hostel_name, h.hostel_type, h.contact_number,
               rt.type_name AS room_type, rt.description AS room_type_desc
        FROM students s
        LEFT JOIN rooms r ON s.room_id = r.id
        LEFT JOIN floors f ON r.floor_id = f.id
        LEFT JOIN hostels h ON r.hostel_id = h.id
        LEFT JOIN room_types rt ON r.room_type_id = rt.id
        WHERE s.id = ?
        LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $student_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result && $result->num_rows > 0) {
    $room_details = $result->fetch_assoc();
}
$stmt->close();

// Fetch roommates (excluding self)
if ($room_details && $room_details['room_id']) {
    $sql_roommates = "SELECT id, first_name, last_name, email, profile_image_url
                      FROM students
                      WHERE room_id = ? AND id != ?";
    $stmt = $conn->prepare($sql_roommates);
    $stmt->bind_param('ii', $room_details['room_id'], $student_id);
    $stmt->execute();
    $roommates = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

// fetching related room fees detail
$room_fee = null;
if ($room_details && $room_details['room_hostel_id'] && $room_details['room_type_id']) {
    $sql_fee = "SELECT price, billing_cycle, effective_from FROM room_fees WHERE hostel_id = ? AND room_type_id = ? ORDER BY effective_from DESC LIMIT 1";
    $stmt = $conn->prepare($sql_fee);
    $stmt->bind_param('ii', $room_details['room_hostel_id'], $room_details['room_type_id']);
    $stmt->execute();
    $fee_result = $stmt->get_result();
    if ($fee_result && $fee_result->num_rows > 0) {
        $room_fee = $fee_result->fetch_assoc();
    }
    $stmt->close();
}

?>

<div class="content container-fluid">
    <div class="row full-height">
        <?php require_once BASE_PATH . '/student/includes/sidebar_student.php'; ?>
        <main class="col-md-10 ms-sm mb-4">
            <h2 class="mb-4">Room Details</h2>
            <?php if ($room_details && $room_details['room_id']): ?>
                <div class="card mb-4 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Room: <?= htmlspecialchars($room_details['room_number']) ?> (<?= htmlspecialchars($room_details['room_type']) ?>)</h5>
                        <p><strong>Hostel:</strong> <?= htmlspecialchars($room_details['hostel_name']) ?> (<?= htmlspecialchars(ucfirst($room_details['hostel_type'])) ?>)</p>
                        <p><strong>Floor:</strong> <?= htmlspecialchars($room_details['floor_name']) ?> (<?= htmlspecialchars($room_details['floor_number']) ?>)</p>
                        <p><strong>Room Capacity:</strong> <?= htmlspecialchars($room_details['max_capacity']) ?></p>
                        <p><strong>Hostel Contact:</strong> <?= htmlspecialchars($room_details['contact_number']) ?></p>
                        <p><strong>Room Type Description:</strong> <?= htmlspecialchars($room_details['room_type_desc']) ?></p>

                        <?php if ($room_fee): ?>
                            <hr>
                            <h6>Room Fee Details</h6>
                            <p><strong>Fee:</strong> <?= number_format($room_fee['price'], 2) ?> BDT</p>
                            <p><strong>Billing Cycle:</strong> <?= htmlspecialchars(ucfirst($room_fee['billing_cycle'])) ?></p>
                            <p><strong>Effective From:</strong> <?= htmlspecialchars($room_fee['effective_from']) ?></p>
                        <?php else: ?>
                            <p class="text-danger">No fee information found for this room type.</p>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card mb-4 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Roommates</h5>
                        <?php if (count($roommates) > 0): ?>
                            <ul class="list-group">
                                <?php foreach ($roommates as $mate): ?>
                                    <li class="list-group-item d-flex align-items-center">
                                        <?php if ($mate['profile_image_url']): ?>
                                            <img src="<?= htmlspecialchars($mate['profile_image_url']) ?>" alt="Profile" class="rounded-circle me-2" width="40" height="40">
                                        <?php else: ?>
                                            <span class="me-2"><i class="bi bi-person-circle" style="font-size: 2rem;"></i></span>
                                        <?php endif; ?>
                                        <span><?= htmlspecialchars($mate['first_name'] . ' ' . $mate['last_name']) ?> (<?= htmlspecialchars($mate['email']) ?>)</span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p>No roommates assigned yet.</p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-warning">You are not currently assigned to any room.</div>
            <?php endif; ?>
        </main>
    </div>
</div>

<?php require_once BASE_PATH . '/student/includes/footer_student.php'; ?>