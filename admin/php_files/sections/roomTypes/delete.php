<?php
session_start();
require_once __DIR__ . '/../../../../config/config.php';
require_once BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/admin/php_files/auth_check_admin.php';

header('Content-Type: application/json');

// Get the Room Type ID  and Hostel ID from POST and validate
$roomTypeID = $_POST['id'] ?? '';
$hostel_id = $_POST['hostel_id'] ?? ''; 


if (!is_numeric($roomTypeID)) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid room type ID.'
    ]);
    exit;
}

if(!is_numeric($hostel_id)) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid hostel ID.'
    ]);
    exit; 
}

// Optional: Check if the room type exists before deleting
$stmt = $conn->prepare("SELECT id FROM room_types WHERE id = ? AND hostel_id = ?");
$stmt->bind_param("ii", $roomTypeID, $hostel_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Room type not found.']);
    exit;
}

// Proceed to delete
$stmt = $conn->prepare("DELETE FROM room_types WHERE id = ? AND hostel_id = ?");
$stmt->bind_param("ii", $roomTypeID, $hostel_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Room type deleted successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to delete room type.']);
}
