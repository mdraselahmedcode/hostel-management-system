<?php
require_once __DIR__ . '/../../../../config/config.php';
require_once BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/config/auth.php'; 
// only allow admin access
require_admin(); 

header('Content-Type: application/json');

// Only allow POST method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

// Validate and sanitize ID
$payment_id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
if ($payment_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid payment ID.']);
    exit;
}

// Check if payment exists
$stmt = $conn->prepare("SELECT id FROM student_payments WHERE id = ?");
$stmt->bind_param("i", $payment_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Payment not found.']);
    exit;
}
$stmt->close();

// Delete the payment
$deleteStmt = $conn->prepare("DELETE FROM student_payments WHERE id = ?");
$deleteStmt->bind_param("i", $payment_id);

if ($deleteStmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Payment deleted successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to delete payment.']);
}
$deleteStmt->close();
$conn->close();
