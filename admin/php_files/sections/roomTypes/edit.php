<?php
require_once __DIR__ . '/../../../../config/config.php';
require_once BASE_PATH . '/config/db.php';

header('Content-Type: application/json');

$id = $_POST['id'] ?? '';
$type_name = trim($_POST['type_name'] ?? '');
$default_capacity = $_POST['default_capacity'] ?? '';
$buffer_limit = $_POST['buffer_limit'] ?? '';

if (!is_numeric($id) || $type_name === '' || !is_numeric($default_capacity) || !is_numeric($buffer_limit)) {
    echo json_encode(['success' => false, 'message' => 'Invalid or missing input.']);
    exit;
}

$stmt = $conn->prepare("
    UPDATE room_types 
    SET type_name = ?, default_capacity = ?, buffer_limit = ? WHERE id = ?
");
$stmt->bind_param("siii", $type_name, $default_capacity, $buffer_limit, $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Room type updated successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update room type.']);
}
