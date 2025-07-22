<?php
require_once __DIR__ . '/../../../../config/config.php';
include BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/config/auth.php';

require_admin();

header('Content-Type: application/json');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid payment ID']);
    exit;
}

$payment_id = (int)$_GET['id'];

$query = "SELECT * FROM student_payments WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $payment_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Payment not found']);
    exit;
}

$payment = $result->fetch_assoc();

echo json_encode([
    'success' => true,
    'data' => [
        'id' => $payment['id'],
        'amount_due' => $payment['amount_due'],
        'month' => $payment['month'],
        'amount_paid' => $payment['amount_paid'],
        'late_fee' => $payment['late_fee'],
        'due_date' => $payment['due_date'],
        'late_fee_applied_date' => $payment['late_fee_applied_date'],
        'payment_status' => $payment['payment_status']
    ]
]);

$stmt->close();
$conn->close();
?>