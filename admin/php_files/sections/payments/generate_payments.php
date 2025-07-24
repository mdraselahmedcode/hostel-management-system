<?php 



require_once __DIR__ . '/../../../../config/config.php';
include BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/config/auth.php';

ob_clean();
header('Content-Type: application/json');

// Only allow admin access
require_admin();

// Get POST data safely
$due_date = isset($_POST['due_date']) ? trim($_POST['due_date']) : null;
$late_fee_applied_date = isset($_POST['late_fee_applied_date']) ? trim($_POST['late_fee_applied_date']) : null;
$late_fee = isset($_POST['late_fee']) ? (float) $_POST['late_fee'] : 0;
$month = isset($_POST['month']) ? (int) $_POST['month'] : date('n');
$year = isset($_POST['year']) ? (int) $_POST['year'] : date('Y');

// Validate due_date format
if (!$due_date || !DateTime::createFromFormat('Y-m-d', $due_date)) {
    echo json_encode(['success' => false, 'message' => 'Invalid or missing due date.']);
    exit;
}

// Optional: Validate late_fee_applied_date if provided
if ($late_fee_applied_date && !DateTime::createFromFormat('Y-m-d', $late_fee_applied_date)) {
    echo json_encode(['success' => false, 'message' => 'Invalid late fee applied date.']);
    exit;
}

// Validate month and year
if (!$month || !$year) {
    echo json_encode(['success' => false, 'message' => 'Missing required input (month/year).']);
    exit;
}

// Get active students
$active_students = $conn->query("
    SELECT s.id AS student_id, s.hostel_id, s.room_id, r.room_type_id, rf.id AS room_fee_id, rf.price
    FROM students s
    JOIN rooms r ON s.room_id = r.id
    JOIN room_fees rf ON r.room_type_id = rf.room_type_id AND s.hostel_id = rf.hostel_id
    WHERE s.is_checked_in = 1
      AND s.is_checked_out = 0
      AND s.is_approved = 1
      AND rf.effective_from <= CURDATE()
    ORDER BY rf.effective_from DESC
");

$generated_count = 0;
$skipped = [];

$admin_id = $_SESSION['admin']['id'] ?? 1;

while ($student = $active_students->fetch_assoc()) {
    // Check if payment already exists for this month/year
    $check = $conn->prepare("
        SELECT id FROM student_payments 
        WHERE student_id = ? AND year = ? AND month = ?
    ");
    $check->bind_param("iii", $student['student_id'], $year, $month);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows === 0) {
        // Calculate balance from previous payments (all months before current one)
        $balance = 0.00;
        $balStmt = $conn->prepare("
            SELECT 
                SUM(amount_due - amount_paid) AS balance
            FROM student_payments
            WHERE student_id = ? AND (year < ? OR (year = ? AND month < ?))
        ");
        $balStmt->bind_param("iiii", $student['student_id'], $year, $year, $month);
        $balStmt->execute();
        $balResult = $balStmt->get_result();
        $balRow = $balResult->fetch_assoc();
        if ($balRow && $balRow['balance'] !== null) {
            $balance = (float)$balRow['balance'];
        }
        $balStmt->close();

        $initial_amount_paid = 0.00;
        $initial_payment_status = 'unpaid';

        if ($balance < 0) {
            $overpaid_amount = abs($balance);

            // Fetch previous payments with overpayment to update them
            $prevPaymentsStmt = $conn->prepare("
                SELECT id, amount_due, amount_paid
                FROM student_payments
                WHERE student_id = ? AND (year < ? OR (year = ? AND month < ?))
                ORDER BY year ASC, month ASC
            ");
            $prevPaymentsStmt->bind_param("iiii", $student['student_id'], $year, $year, $month);
            $prevPaymentsStmt->execute();
            $prevPaymentsResult = $prevPaymentsStmt->get_result();

            while ($row = $prevPaymentsResult->fetch_assoc()) {
                $payment_id_to_update = $row['id'];
                $due = (float)$row['amount_due'];
                $paid = (float)$row['amount_paid'];
                $payment_balance = $due - $paid;

                if ($payment_balance < 0) {
                    // This payment has overpayment
                    $update_amount_paid = $due; // Set paid = due (balance 0)
                    $update_payment_status = 'paid';

                    // Update this payment
                    $updateStmt = $conn->prepare("
                        UPDATE student_payments
                        SET amount_paid = ?, payment_status = ?, updated_at = NOW()
                        WHERE id = ?
                    ");
                    $updateStmt->bind_param("dsi", $update_amount_paid, $update_payment_status, $payment_id_to_update);
                    $updateStmt->execute();
                    $updateStmt->close();

                    // Reduce the overpaid_amount by the amount fixed here
                    $overpaid_amount -= abs($payment_balance);

                    // If fully cleared the overpaid amount, break
                    if ($overpaid_amount <= 0) break;
                }
            }
            $prevPaymentsStmt->close();

            // Now apply the overpaid_amount (capped by current month price) as amount_paid in new payment
            $initial_amount_paid = min(abs($balance), $student['price']);
            if ($initial_amount_paid >= $student['price']) {
                $initial_payment_status = 'paid';
            } else {
                $initial_payment_status = 'partial';
            }
        }

        // Insert new payment record with calculated amount_paid and payment_status
        $insert = $conn->prepare("
            INSERT INTO student_payments (
                student_id, hostel_id, room_id, room_type_id, room_fee_id,
                year, month, amount_due, amount_paid, late_fee, late_fee_applied_date,
                payment_status, due_date, created_at, updated_at, created_by
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW(), ?)
        ");

        $late_fee_applied_date_param = $late_fee_applied_date ?: null;

        $insert->bind_param(
            "iiiiiiiiddsssi",
            $student['student_id'],
            $student['hostel_id'],
            $student['room_id'],
            $student['room_type_id'],
            $student['room_fee_id'],
            $year,
            $month,
            $student['price'],
            $initial_amount_paid,
            $late_fee,
            $late_fee_applied_date_param,
            $initial_payment_status,
            $due_date,
            $admin_id
        );

        if ($insert->execute()) {
            $generated_count++;
        }
    } else {
        $skipped[] = $student['student_id'];
    }
}

// Final response
$message = $generated_count > 0 
    ? "Payments generated for " . date('F', mktime(0, 0, 0, $month, 1)) . " $year. New payments created: $generated_count."
    : "No new payments to generate for " . date('F', mktime(0, 0, 0, $month, 1)) . " $year. All payments are already up to date.";

echo json_encode([
    "success" => true,
    "message" => $message,
    "generated" => $generated_count,
    "skipped" => $skipped
]);
exit;
