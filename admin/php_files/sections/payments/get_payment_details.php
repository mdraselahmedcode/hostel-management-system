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
    'payment' => [  // match your JS naming convention if you want
        'id' => $payment['id'],
        'amount_due' => (float)$payment['amount_due'],
        'amount_paid' => (float)$payment['amount_paid'],
        'balance' => (float)$payment['balance'],
        'o_p_balance_added' => (float)$payment['o_p_balance_added'],
        'late_fee' => (float)$payment['late_fee'],
        'due_date' => $payment['due_date'],
        'late_fee_applied_date' => $payment['late_fee_applied_date'],
        'is_late_fee_taken' => (bool)$payment['is_late_fee_taken'],
        'payment_status' => $payment['payment_status'],
        'month' => (int)$payment['month'],
        'year' => (int)$payment['year'],
    ]
]);

$stmt->close();
$conn->close();
