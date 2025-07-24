<?php
require_once __DIR__ . '/../../../../config/config.php';
require_once BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/config/auth.php';

// Only allow admin access
require_admin();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$verified_by_admin = $_SESSION['admin']['id']; 

// $payment_month = $_POST['month'] ?? null; 
$student_id = $_POST['student_id'] ?? null; 
$payment_id = $_POST['payment_id'] ?? null;
$amount = $_POST['amount'] ?? null;
$payment_date = $_POST['payment_date'] ?? null;
$payment_method_id = $_POST['payment_method_id'] ?? null;
$reference_code = $_POST['reference_code'] ?? null;
$verification_status = $_POST['verification_status'] ?? '';
$notes = $_POST['notes'] ?? null;

// Optional: Validate
if (!$student_id || !$payment_id || !$amount || !$payment_date || !$payment_method_id || !$verification_status) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

try {
    // Step 0: Find the earliest unpaid/partial payment for this student
    $stmt = $conn->prepare("SELECT id, amount_due FROM student_payments WHERE student_id = ? AND payment_status IN ('unpaid', 'partial') ORDER BY year ASC, month ASC LIMIT 1");
    $stmt->bind_param("i", $student_id); // Make sure you have $student_id available
    $stmt->execute();
    $paymentRow = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$paymentRow) {
        throw new Exception("No unpaid or partially paid record found for this student.");
    }

    $payment_id = $paymentRow['id'];
    $amount_due = $paymentRow['amount_due'];


    // Step 1: Insert transaction
    // Step 1: Insert transaction
    $stmt = $conn->prepare("INSERT INTO payment_transactions (
        payment_id, amount, payment_date, payment_method_id, reference_code, notes, verified_by, verification_status
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param(
        "idssssss", 
        $payment_id, 
        $amount, 
        $payment_date, 
        $payment_method_id, 
        $reference_code, 
        $notes, 
        $verified_by_admin, 
        $verification_status // New variable
    );

    $stmt->execute();
    $stmt->close();


    // Step 2: Recalculate total verified payments for that payment_id
    $stmt = $conn->prepare("SELECT SUM(amount) as total_paid FROM payment_transactions WHERE payment_id = ? AND verification_status = 'verified'");
    $stmt->bind_param("i", $payment_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $total_paid = $result['total_paid'] ?? 0;
    $stmt->close();

    // Step 3: Determine payment status
    if ($total_paid >= $amount_due) {
        $status = 'paid';
    } elseif ($total_paid > 0) {
        $status = 'partial';
    } else {
        $status = 'unpaid';
    }

    // Step 4: Update student_payments table
    $stmt = $conn->prepare("UPDATE student_payments SET amount_paid = ?, payment_status = ?, updated_by = ? WHERE id = ?");
    $stmt->bind_param("dsii", $total_paid, $status, $verified_by_admin, $payment_id);
    $stmt->execute();
    $stmt->close();

    echo json_encode(['success' => true, 'updated_payment_id' => $payment_id]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
