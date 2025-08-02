<?php
require_once __DIR__ . '/../../../../config/config.php';
require_once BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/config/auth.php';

require_student();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method.'
    ]);
    exit;
}

$id = $_POST['id'] ?? null;

if (!$id) {
    echo json_encode([
        'success' => false,
        'message' => 'Request ID is required.'
    ]);
    exit;
}
$student_id = $_SESSION['student']['id']; 

// First, check if the request is still pending
$stmt = $conn->prepare("SELECT status FROM room_change_requests WHERE id = ? AND student_id = ?");
$stmt->bind_param('ii', $id, $student_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Request not found.'
    ]);
    exit;
}

$request = $result->fetch_assoc();

if ($request['status'] !== 'pending') {
    echo json_encode([
        'success' => false,
        'message' => 'Only pending requests can be deleted.'
    ]);
    exit;
}

// Delete the request
$deleteStmt = $conn->prepare("DELETE FROM room_change_requests WHERE id = ? AND student_id = ?");
$deleteStmt->bind_param('ii', $id, $_SESSION['student_id']);

if ($deleteStmt->execute()) {
    echo json_encode([
        'success' => true,
        'message' => 'Room change request deleted successfully.'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to delete the request.'
    ]);
}
