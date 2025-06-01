<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../../../../config/config.php';
require_once BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/admin/php_files/auth_check_admin.php';

// Check for valid ID passed via POST (AJAX)
if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid floor ID.'
    ]);
    exit;
}

$floorId = (int) $_POST['id'];

// Check if floor has rooms
$checkRoomsSql = "SELECT COUNT(*) as total FROM rooms WHERE floor_id = ?";
$stmt = $conn->prepare($checkRoomsSql);
$stmt->bind_param("i", $floorId);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if ($data['total'] > 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Cannot delete floor with existing rooms.'
    ]);
    exit;
}

// Delete floor
$deleteSql = "DELETE FROM floors WHERE id = ?";
$stmt = $conn->prepare($deleteSql);
$stmt->bind_param("i", $floorId);

if ($stmt->execute()) {
    echo json_encode([
        'success' => true,
        'message' => 'Floor deleted successfully.'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to delete floor. Please try again.'
    ]);
}
exit;
