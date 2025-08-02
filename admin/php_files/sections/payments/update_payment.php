<?php
require_once __DIR__ . '/../../../../config/config.php';
require_once BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/config/auth.php';

require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate inputs
    $payment_id = isset($_POST['payment_id']) ? intval($_POST['payment_id']) : 0;
    $amount_due = isset($_POST['amount_due']) ? floatval($_POST['amount_due']) : 0;
    $amount_paid = isset($_POST['amount_paid']) ? floatval($_POST['amount_paid']) : 0;
    $balance = isset($_POST['balance']) ? floatval($_POST['balance']) : 0;
    $o_p_balance_added = isset($_POST['o_p_balance_added']) ? floatval($_POST['o_p_balance_added']) : 0;
    $late_fee = isset($_POST['late_fee']) ? floatval($_POST['late_fee']) : 0;
    $due_date = isset($_POST['due_date']) ? $_POST['due_date'] : null;
    $late_fee_applied_date = isset($_POST['late_fee_applied_date']) && $_POST['late_fee_applied_date'] !== '' ? $_POST['late_fee_applied_date'] : null;
    $is_late_fee_taken = isset($_POST['is_late_fee_taken']) ? 1 : 0; // checkbox
    $payment_status = isset($_POST['payment_status']) ? $_POST['payment_status'] : 'unpaid';
    $month = isset($_POST['month']) ? intval($_POST['month']) : 0;
    $year = isset($_POST['year']) ? intval($_POST['year']) : 0;

    // Basic validation
    $errors = [];
    if ($payment_id <= 0) $errors[] = "Invalid payment ID.";
    if ($amount_due < 0) $errors[] = "Amount due must be zero or positive.";
    if ($amount_paid < 0) $errors[] = "Amount paid must be zero or positive.";
    if ($balance < 0) $errors[] = "Balance must be zero or positive.";
    if ($late_fee < 0) $errors[] = "Late fee must be zero or positive.";
    if (empty($due_date)) $errors[] = "Due date is required.";
    if ($month < 1 || $month > 12) $errors[] = "Invalid month selected.";
    if ($year < 2000 || $year > 2100) $errors[] = "Invalid year.";

    if (!in_array($payment_status, ['paid', 'unpaid', 'partial', 'late'])) {
        $errors[] = "Invalid payment status.";
    }

    if (!empty($errors)) {
        // Handle errors (return JSON or redirect with errors)
        echo json_encode(['success' => false, 'errors' => $errors]);
        exit;
    }

    // Prepare and execute update query
    $stmt = $conn->prepare("UPDATE student_payments SET
        amount_due = ?,
        amount_paid = ?,
        balance = ?,
        o_p_balance_added = ?,
        late_fee = ?,
        due_date = ?,
        late_fee_applied_date = ?,
        is_late_fee_taken = ?,
        payment_status = ?,
        month = ?,
        year = ?,
        updated_at = CURRENT_TIMESTAMP,
        updated_by = ?
        WHERE id = ?
    ");

    $admin_id = $_SESSION['admin_id'] ?? null; // get admin id from session

    // Bind parameters (s for string, i for int, d for double)
    // due_date and late_fee_applied_date are strings or NULL
    $stmt->bind_param(
        "ddddsssisiiii",
        $amount_due,
        $amount_paid,
        $balance,
        $o_p_balance_added,
        $late_fee,
        $due_date,
        $late_fee_applied_date,
        $is_late_fee_taken,
        $payment_status,
        $month,
        $year,
        $admin_id,
        $payment_id
    );


    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Payment updated successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update payment.', 'error' => $stmt->error]);
    }

    $stmt->close();
    exit;
}

// If GET or other request method, respond with error or redirect
echo json_encode(['success' => false, 'message' => 'Method not allowed']);
exit;
