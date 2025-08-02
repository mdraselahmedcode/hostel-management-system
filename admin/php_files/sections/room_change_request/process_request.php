<?php
ob_start();

require_once __DIR__ . '/../../../../config/config.php';
require_once BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/config/auth.php';
require_admin();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method.'
    ]);
    exit();
}

// Get form data
$request_id = isset($_POST['request_id']) ? (int)$_POST['request_id'] : 0;
$action = isset($_POST['action']) ? trim($_POST['action']) : '';
$admin_comment = isset($_POST['admin_comment']) ? trim($_POST['admin_comment']) : '';
$admin_id = $_SESSION['admin']['id'] ?? 0;

// Validate inputs
if (!$request_id || !in_array($action, ['approve', 'reject'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request parameters.'
    ]);
    exit();
}

// Start transaction
$conn->begin_transaction();

try {
    // 1. Get the current request details
    $sql = "SELECT r.*, s.room_id AS current_room_id, r.preferred_room_id
            FROM room_change_requests r
            JOIN students s ON r.student_id = s.id
            WHERE r.id = ? AND r.status = 'pending' FOR UPDATE";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $request_id);
    $stmt->execute();
    $request = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$request) {
        throw new Exception("Request not found or already processed.");
    }

    $new_status = ($action === 'approve') ? 'approved' : 'rejected';

    if ($action === 'approve' && $request['preferred_room_id']) {
        $capacity_sql = "SELECT r.max_capacity, 
                                (SELECT COUNT(*) FROM students WHERE room_id = ? AND is_checked_in = 1) AS current_occupancy
                         FROM rooms r
                         WHERE r.id = ?";

        $stmt = $conn->prepare($capacity_sql);
        $stmt->bind_param('ii', $request['preferred_room_id'], $request['preferred_room_id']);
        $stmt->execute();
        $room = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($room['current_occupancy'] >= $room['max_capacity']) {
            throw new Exception("Preferred room is at full capacity.");
        }
    }

    // Update the request status
    $update_sql = "UPDATE room_change_requests 
                   SET status = ?, 
                       admin_comment = ?,
                       reviewed_by = ?,
                       reviewed_at = CURRENT_TIMESTAMP
                   WHERE id = ?";

    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param('ssii', $new_status, $admin_comment, $admin_id, $request_id);
    $stmt->execute();
    $stmt->close();

    // If approved, update student and log change
    if ($action === 'approve' && $request['preferred_room_id']) {
        $floor_sql = "SELECT floor_id FROM rooms WHERE id = ?";
        $stmt = $conn->prepare($floor_sql);
        $stmt->bind_param('i', $request['preferred_room_id']);
        $stmt->execute();
        $floor = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        $update_student_sql = "UPDATE students 
                               SET room_id = ?, 
                                   floor_id = ?,
                                   hostel_id = (SELECT hostel_id FROM rooms WHERE id = ?)
                               WHERE id = ?";

        $stmt = $conn->prepare($update_student_sql);
        $stmt->bind_param('iiii', $request['preferred_room_id'], $floor['floor_id'], $request['preferred_room_id'], $request['student_id']);
        $stmt->execute();
        $stmt->close();

        $log_sql = "INSERT INTO student_room_history 
                    (student_id, from_room_id, to_room_id, changed_by, change_reason)
                    VALUES (?, ?, ?, ?, ?)";

        $reason = "Room change request #$request_id approved by admin";
        $stmt = $conn->prepare($log_sql);
        $stmt->bind_param('iiiis', $request['student_id'], $request['current_room_id'], $request['preferred_room_id'], $admin_id, $reason);
        $stmt->execute();
        $stmt->close();
    }

    $conn->commit();

    $action_past = ($action === 'approve') ? 'approved' : 'rejected';
    echo json_encode([
        'success' => true,
        'message' => "Request #$request_id has been $action_past successfully."
    ]);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode([
        'success' => false,
        'message' => 'Error processing request: ' . $e->getMessage()
    ]);
}
?>
