<?php
session_start(); 
require_once __DIR__ . '/../../../../config/config.php'; 
require_once BASE_PATH . '/config/db.php'; 
require_once BASE_PATH . '/admin/php_files/auth_check_admin.php'; 

header('Content-Type: application/json'); 

// Validate and sanitize ID
if (!isset($_POST['id']) || !is_numeric($_POST['id']) || (int)$_POST['id'] <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid room ID.'
    ]);
    exit; 
}

$room_id = (int) $_POST['id']; 

// Check if room exists
$checkStmt = $conn->prepare("SELECT id FROM rooms WHERE id = ?");
$checkStmt->bind_param("i", $room_id); 
$checkStmt->execute(); 
$result = $checkStmt->get_result(); 
$checkStmt->close(); 

if ($result->num_rows !== 1) {
    echo json_encode([
        'success' => false,
        'message' => 'Room not found'
    ]);
    exit; 
}

// Delete room
$deleteStmt = $conn->prepare("DELETE FROM rooms WHERE id = ?");
$deleteStmt->bind_param("i", $room_id); 

if ($deleteStmt->execute()) {
    echo json_encode([
        'success' => true,
        'message' => 'Room deleted successfully.'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to delete room: ' . $conn->error
    ]);
}

// Always close resources
$deleteStmt->close(); 
$conn->close(); 
exit;
