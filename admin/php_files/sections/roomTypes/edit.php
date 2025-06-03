<?php
require_once __DIR__ . '/../../../../config/config.php';
require_once BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/admin/includes/csrf.php';

session_start(); 

header('Content-Type: application/json');

// validate csrf token
if (!validate_csrf_token($_POST['csrf_token'] ?? '')) {
    echo json_encode(['success' => false, 'message' => 'Invalid CSRF token.']);
    exit;
}
// Unset token after validation (to prevent reuse)
unset_csrf_token();

$roomTypeId = $_POST['id'] ?? '';
$hostelId = $_POST['hostel_id'] ?? '' ; 
$type_name = trim($_POST['type_name'] ?? '');
$default_capacity = $_POST['default_capacity'] ?? '';
$buffer_limit = $_POST['buffer_limit'] ?? '';

if (!is_numeric($roomTypeId) || !is_numeric($hostelId) || $type_name === '' || !is_numeric($default_capacity) || !is_numeric($buffer_limit)) {
    echo json_encode(['success' => false, 'message' => 'Invalid or missing input.']);
    exit;
}

$stmt = $conn->prepare("
    UPDATE room_types 
    SET type_name = ?, default_capacity = ?, buffer_limit = ? WHERE id = ? AND hostel_id = ?
");
$stmt->bind_param("siiii", $type_name, $default_capacity, $buffer_limit, $roomTypeId, $hostelId);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Room type updated successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update room type.']);
}
