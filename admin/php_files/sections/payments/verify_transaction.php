<?php
require_once __DIR__ . '/../../../../config/config.php';
require_once BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/config/auth.php';

header('Content-Type: application/json');

require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $txn_id = (int)($_POST['txn_id'] ?? 0);
    $action = $_POST['action'] ?? '';
    $admin_id = $_SESSION['admin']['id'] ?? 0;

    if (!$txn_id || !in_array($action, ['verify', 'reject'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid input.']);
        exit;
    }

    $verified_status = $action === 'verify' ? 'verified' : 'rejected';

    // Update transaction
    $stmt = $conn->prepare("
        UPDATE payment_transactions 
        SET verification_status = ?, verified_by = ?, updated_at = NOW()
        WHERE id = ?
    ");
    $stmt->bind_param("sii", $verified_status, $admin_id, $txn_id);
    $stmt->execute();

    if ($action === 'verify') {
        $txn = $conn->query("SELECT payment_id, amount FROM payment_transactions WHERE id = $txn_id")->fetch_assoc();

        if ($txn) {
            $update_payment = $conn->prepare("
                UPDATE student_payments 
                SET amount_paid = amount_paid + ?,
                    payment_status = CASE 
                        WHEN amount_paid + ? >= amount_due THEN 'paid'
                        ELSE 'partial'
                    END,
                    updated_at = NOW()
                WHERE id = ?
            ");
            $update_payment->bind_param("ddi", $txn['amount'], $txn['amount'], $txn['payment_id']);
            $update_payment->execute();
        }
    }

    echo json_encode(['success' => true, 'message' => "Payment $verified_status successfully."]);
    exit;
}
