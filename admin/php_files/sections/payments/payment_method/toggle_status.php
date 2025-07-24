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

// Get current status
$stmt = $conn->prepare("SELECT active FROM payment_methods WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Payment method not found']);
    exit;
}

$current = $result->fetch_assoc();
$newStatus = (int)!$current['active'];

// Update status
$update = $conn->prepare("UPDATE payment_methods SET active = ? WHERE id = ?");
$update->bind_param("ii", $newStatus, $id);
$update->execute();

echo json_encode([
    'success' => true,
    'message' => 'Payment method status updated',
    'active' => $newStatus
]);
