<?php
session_start();
require_once __DIR__ . '/../../../../config/config.php';
require_once BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/admin/php_files/auth_check_admin.php';

// Set JSON header
header('Content-Type: application/json');

// Validate that request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

// Validate and sanitize inputs
$room_id       = isset($_POST['room_id']) ? (int) $_POST['room_id'] : 0;
$room_number   = isset($_POST['room_number']) ? trim($_POST['room_number']) : '';
$max_capacity  = isset($_POST['max_capacity']) ? (int) $_POST['max_capacity'] : 0;
$room_type_id  = isset($_POST['room_type_id']) ? (int) $_POST['room_type_id'] : 0;
$floor_id      = isset($_POST['floor_id']) ? (int) $_POST['floor_id'] : 0;

// Check required fields
if ($room_id <= 0 || empty($room_number) || $max_capacity <= 0 || $room_type_id <= 0 || $floor_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'All fields are required and must be valid.']);
    exit;
}

// Escape input
$room_number = $conn->real_escape_string($room_number);

// Check if the room exists
$checkSql = "SELECT id FROM rooms WHERE id = $room_id";
$checkResult = $conn->query($checkSql);

if (!$checkResult || $checkResult->num_rows !== 1) {
    echo json_encode(['success' => false, 'message' => 'Room not found.']);
    exit;
}

// Update the room
$updateSql = "
    UPDATE rooms
    SET room_number = '$room_number',
        max_capacity = $max_capacity,
        room_type_id = $room_type_id,
        floor_id = $floor_id
    WHERE id = $room_id
";

if ($conn->query($updateSql)) {
    echo json_encode(['success' => true, 'message' => 'Room updated successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update room: ' . $conn->error]);
}
