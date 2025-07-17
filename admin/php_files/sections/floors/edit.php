<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../../../../config/config.php';
require_once BASE_PATH . '/config/db.php';
// only admin will get access
require_once BASE_PATH . '/config/auth.php';

require_admin();

// Helper function to send JSON and exit
function jsonResponse($success, $message) {
    echo json_encode(['success' => $success, 'message' => $message]);
    exit;
}

// Validate request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, 'Invalid request method.');
}

// CSRF Token validation
if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    jsonResponse(false, 'Invalid CSRF token.');
}

// Validate and sanitize inputs
$floor_id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$hostel_id = isset($_POST['hostel_id']) ? intval($_POST['hostel_id']) : 0;
$floor_number = isset($_POST['floor_number']) ? intval($_POST['floor_number']) : null;
$floor_name = isset($_POST['floor_name']) ? trim($_POST['floor_name']) : '';

if (!$floor_id || !$hostel_id || $floor_number === null || $floor_name === '') {
    jsonResponse(false, 'All fields are required.');
}

// Check if floor exists
$checkSql = "SELECT id FROM floors WHERE id = ?";
$checkStmt = $conn->prepare($checkSql);
$checkStmt->bind_param('i', $floor_id);
$checkStmt->execute();
$checkStmt->store_result();
if ($checkStmt->num_rows === 0) {
    jsonResponse(false, 'Floor not found.');
}
$checkStmt->close();

// Check if another floor with the same number exists in the same hostel
$duplicateCheckSql = "SELECT id FROM floors WHERE floor_number = ? AND hostel_id = ? AND id != ?";
$duplicateCheckStmt = $conn->prepare($duplicateCheckSql);
$duplicateCheckStmt->bind_param('iii', $floor_number, $hostel_id, $floor_id);
$duplicateCheckStmt->execute();
$duplicateCheckStmt->store_result();

if ($duplicateCheckStmt->num_rows > 0) {
    jsonResponse(false, 'Another floor with this number already exists in the selected hostel.');
}
$duplicateCheckStmt->close();

// Update floor
$updateSql = "UPDATE floors SET hostel_id = ?, floor_number = ?, floor_name = ? WHERE id = ?";
$stmt = $conn->prepare($updateSql);
if (!$stmt) {
    jsonResponse(false, 'Database error: ' . $conn->error);
}
$stmt->bind_param('iisi', $hostel_id, $floor_number, $floor_name, $floor_id);

if ($stmt->execute()) {
    jsonResponse(true, 'Floor updated successfully.');
} else {
    jsonResponse(false, 'Failed to update floor.');
}
?>
