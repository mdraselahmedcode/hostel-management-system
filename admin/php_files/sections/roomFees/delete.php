<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../../../../config/config.php';
require_once BASE_PATH . '/config/db.php';
// only admin will get access
require_once BASE_PATH . '/config/auth.php';

require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid or missing fee ID.']);
    exit;
}

$feeId = (int) $_POST['id'];

// Check if fee record exists
$checkSql = "SELECT id FROM room_fees WHERE id = ?";
$stmt = $conn->prepare($checkSql);
$stmt->bind_param('i', $feeId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Fee record not found.']);
    exit;
}

// Proceed to delete
$deleteSql = "DELETE FROM room_fees WHERE id = ?";
$deleteStmt = $conn->prepare($deleteSql);
$deleteStmt->bind_param('i', $feeId);

if ($deleteStmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Fee record deleted successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to delete fee record.']);
}
?>
