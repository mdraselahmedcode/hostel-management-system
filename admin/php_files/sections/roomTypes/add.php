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


$type_name = htmlspecialchars(trim($_POST['type_name'] ?? ''));
$lower_type_name = strtolower($type_name); 
$default_capacity = (int) ($_POST['default_capacity'] ?? 0);
$buffer_limit     = (int) ($_POST['buffer_limit'] ?? 0);
$hostel_id        = (int) ($_POST['hostel_id'] ?? 0);

// Basic validation
if (!$type_name || $default_capacity <= 0 || $buffer_limit < 0 || $hostel_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'All fields including hostel selection are required and must be valid.']);
    exit;
}

// Check for duplicate type name for the same hostel
$checkStmt = $conn->prepare("
    SELECT id 
    FROM room_types 
    WHERE LOWER(type_name) = ? AND hostel_id = ? 
    LIMIT 1
");
$checkStmt->bind_param("si", $lower_type_name, $hostel_id);
$checkStmt->execute();
$result = $checkStmt->get_result();
$checkStmt->close();

if ($result->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Room type already exists for this hostel.']);
    exit;
}

// Insert room type with hostel_id
$stmt = $conn->prepare("
    INSERT INTO room_types (type_name, default_capacity, buffer_limit, hostel_id)
    VALUES (?, ?, ?, ?)
");
$stmt->bind_param("siii", $lower_type_name, $default_capacity, $buffer_limit, $hostel_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Room type added successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to add room type.']);
}

$stmt->close();
$conn->close();
