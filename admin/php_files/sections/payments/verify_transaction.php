<?php

require_once __DIR__ . '/../../../../config/config.php';
require_once BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/config/auth.php';
require_once BASE_PATH . '/admin/php_files/sections/payments/updatelateStatus.php'; 


header('Content-Type: application/json');
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$txn_id = (int)($_POST['txn_id'] ?? 0);
$action = $_POST['action'] ?? '';
$admin_id = $_SESSION['admin']['id'] ?? 0;

if (!$txn_id || !in_array($action, ['verify', 'reject'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid input.']);
    exit;
}

$verification_status = $action === 'verify' ? 'verified' : 'rejected';

// 🔍 Step 1: Get transaction info
$stmt = $conn->prepare("SELECT payment_id, amount FROM payment_transactions WHERE id = ?");
$stmt->bind_param("i", $txn_id);
$stmt->execute();
$txnData = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$txnData) {
    echo json_encode(['success' => false, 'message' => 'Transaction not found.']);
    exit;
}

$payment_id = (int)$txnData['payment_id'];
$txn_amount = (float)$txnData['amount'];



// 🔄 Step 2: Update verification status
$stmt = $conn->prepare("
    UPDATE payment_transactions 
    SET verification_status = ?, verified_by = ?, updated_at = NOW()
    WHERE id = ?
");
$stmt->bind_param("sii", $verification_status, $admin_id, $txn_id);
$stmt->execute();
$stmt->close();

// 🧮 Step 3: If verified, recalculate payment summary
if ($verification_status === 'verified') {
    // Fetch total verified
    $stmt = $conn->prepare("SELECT SUM(amount) as total_paid FROM payment_transactions WHERE payment_id = ? AND verification_status = 'verified'");
    $stmt->bind_param("i", $payment_id);
    $stmt->execute();
    $sumData = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $total_paid = (float)($sumData['total_paid'] ?? 0);

    // Get original student payment record
    $stmt = $conn->prepare("SELECT amount_due, balance, due_date, late_fee_applied_date, is_late, is_late_fee_taken, payment_status FROM student_payments WHERE id = ?");
    $stmt->bind_param("i", $payment_id);
    $stmt->execute();
    $payment = $stmt->get_result()->fetch_assoc();
    $stmt->close();


    if (!$payment) {
        echo json_encode(['success' => false, 'message' => 'Payment record not found.']);
        exit;
    }

    $balance_amount = $payment['balance']; 

    $due = (float)$payment['amount_due'];
    $balance = (float)$payment['balance'];
    $dueDate = new DateTime($payment['due_date']);
    $lateFeeDate = $payment['late_fee_applied_date'] ? new DateTime($payment['late_fee_applied_date']) : new DateTime('9999-12-31');
    $today = new DateTime();
    $is_late_fee_taken = (int)$payment['is_late_fee_taken'];

    // Check if late fee applies
    // $lateFeeApplies = in_array($payment['payment_status'], ['unpaid', 'partial']) && $today > $dueDate && $today >= $lateFeeDate;
    $lateFeeApplies = $payment['is_late'] && in_array($payment['payment_status'], ['unpaid', 'partial']);

    $update_is_late_fee_taken = $is_late_fee_taken;

    // Step 3: Determine payment status
        if ($total_paid >= $due) {
            $status = 'paid';
            if ($lateFeeApplies) {
                $update_is_late_fee_taken = 1;
            }
        } elseif ($total_paid > 0) {
            $status = 'partial';
        } else {
            $status = 'unpaid';
        }

    $update_balance = $balance_amount - $txn_amount; 

    // Update student_payments
    $stmt = $conn->prepare("UPDATE student_payments SET amount_paid = ?, balance = ?, payment_status = ?, is_late_fee_taken = ?, updated_by = ? WHERE id = ?");
    $stmt->bind_param("ddsiii", $total_paid, $update_balance, $status, $update_is_late_fee_taken, $admin_id, $payment_id);
    $stmt->execute();
    $stmt->close();

    echo json_encode(['success' => true, 'message' => 'Transaction verified and payment status updated.']);
    exit;
}

// 🟥 If rejected
if ($verification_status === 'rejected') {
    echo json_encode(['success' => true, 'message' => 'Transaction was rejected.']);
    exit;
}

// 🟨 Fallback
echo json_encode(['success' => true, 'message' => "Transaction marked as $verification_status."]);
exit;
