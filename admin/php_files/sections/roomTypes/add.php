<?php
session_start();
require_once __DIR__ . '/../../../../config/config.php';
require_once BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/admin/php_files/auth_check_admin.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$type_name        = trim($_POST['type_name'] ?? '');
$default_capacity = (int) ($_POST['default_capacity'] ?? 0);
$buffer_limit     = (int) ($_POST['buffer_limit'] ?? 0);

// Basic validation
if (!$type_name || $default_capacity <= 0 || $buffer_limit < 0) {
    echo json_encode(['success' => false, 'message' => 'All fields are required and must be valid.']);
    exit;
}

// Check for duplicate type name
$checkStmt = $conn->prepare("SELECT id FROM room_types WHERE type_name = ? LIMIT 1");
$checkStmt->bind_param("s", $type_name);
$checkStmt->execute();
$result = $checkStmt->get_result();
$checkStmt->close();

if ($result->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Room type name already exists.']);
    exit;
}

// Insert room type
$stmt = $conn->prepare("
    INSERT INTO room_types (type_name, default_capacity, buffer_limit)
    VALUES (?, ?, ?)
");
$stmt->bind_param("sii", $type_name, $default_capacity, $buffer_limit);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Room type added successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to add room type.']);
}

$stmt->close();
$conn->close();
