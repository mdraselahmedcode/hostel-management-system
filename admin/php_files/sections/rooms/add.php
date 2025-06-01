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

$room_number = trim($_POST['room_number'] ?? '');
$max_capacity = (int) ($_POST['max_capacity'] ?? 0);
$room_type_id = (int) ($_POST['room_type_id'] ?? 0);
$floor_id = (int) ($_POST['floor_id'] ?? 0);
$hostel_id = (int) ($_POST['hostel_id'] ?? 0);

// Basic validation
if (!$room_number || !$max_capacity || !$room_type_id || !$floor_id || !$hostel_id) {
    echo json_encode(['success' => false, 'message' => 'All fields are required.']);
    exit;
}

// Insert into DB
$stmt = $conn->prepare("INSERT INTO rooms (room_number, max_capacity, room_type_id, floor_id) VALUES (?, ?, ?, ?)");
$stmt->bind_param('siii', $room_number, $max_capacity, $room_type_id, $floor_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Room added successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to add room.']);
}
