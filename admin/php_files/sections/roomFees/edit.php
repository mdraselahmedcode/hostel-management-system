<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../../../../config/config.php';
require_once BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/admin/php_files/auth_check_admin.php';

// Get data
$fee_id         = isset($_POST['id']) ? intval($_POST['id']) : 0;
$hostel_id      = isset($_POST['hostel_id']) ? intval($_POST['hostel_id']) : 0;
$room_type_id   = isset($_POST['room_type_id']) ? intval($_POST['room_type_id']) : 0;
$price          = isset($_POST['price']) ? trim($_POST['price']) : '';
$billing_cycle  = isset($_POST['billing_cycle']) ? trim($_POST['billing_cycle']) : '';
$effective_from = isset($_POST['effective_from']) ? trim($_POST['effective_from']) : '';

// Basic validations
if (!$fee_id || !$hostel_id || !$room_type_id || $price === '' || !$billing_cycle || !$effective_from) {
    echo json_encode(['success' => false, 'message' => 'All fields are required.']);
    exit;
}

// Validate price
if (!is_numeric($price) || floatval($price) < 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid price.']);
    exit;
}

// Validate billing cycle
$valid_cycles = ['monthly', 'quarterly', 'yearly'];
if (!in_array($billing_cycle, $valid_cycles)) {
    echo json_encode(['success' => false, 'message' => 'Invalid billing cycle.']);
    exit;
}

// Validate effective_from
$date = DateTime::createFromFormat('Y-m-d', $effective_from);
if (!$date || $date->format('Y-m-d') !== $effective_from) {
    echo json_encode(['success' => false, 'message' => 'Invalid date format.']);
    exit;
}

// ✅ Check for duplicate combination excluding current ID
$dupStmt = $conn->prepare("
    SELECT id FROM room_fees 
    WHERE hostel_id = ? AND room_type_id = ? AND id != ?
");
$dupStmt->bind_param("iii", $hostel_id, $room_type_id, $fee_id);
$dupStmt->execute();
$dupStmt->store_result();

if ($dupStmt->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'A fee already exists for this hostel and room type.']);
    $dupStmt->close();
    exit;
}
$dupStmt->close();

// ✅ Perform update
$update = $conn->prepare("
    UPDATE room_fees 
    SET hostel_id = ?, room_type_id = ?, price = ?, billing_cycle = ?, effective_from = ?
    WHERE id = ?
");
$update->bind_param("iisssi", $hostel_id, $room_type_id, $price, $billing_cycle, $effective_from, $fee_id);

if ($update->execute()) {
    echo json_encode(['success' => true, 'message' => 'Room fee updated successfully.']);
} else {
    error_log("Update error: " . $update->error);
    echo json_encode(['success' => false, 'message' => 'Failed to update room fee.']);
}

$update->close();
$conn->close();
