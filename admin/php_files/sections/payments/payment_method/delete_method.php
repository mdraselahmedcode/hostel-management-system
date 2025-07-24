<?php
require_once __DIR__ . '/../../../../../config/config.php';
require_once BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/config/auth.php';

require_admin();
header('Content-Type: application/json');

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid ID']);
    exit;
}

// Check if the method exists
$stmt = $conn->prepare("SELECT id FROM payment_methods WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Payment method not found']);
    exit;
}

// Delete the method
$delete = $conn->prepare("DELETE FROM payment_methods WHERE id = ?");
$delete->bind_param("i", $id);
$delete->execute();

echo json_encode(['success' => true, 'message' => 'Payment method deleted successfully.']);
