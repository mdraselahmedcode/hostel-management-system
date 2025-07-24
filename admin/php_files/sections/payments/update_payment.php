<?php
require_once __DIR__ . '/../../../../config/config.php';
include BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/config/auth.php';

require_admin();

$admin_id = $_SESSION['admin']['id']; 

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Add 'month' to required fields
$required_fields = ['payment_id', 'amount_due', 'amount_paid', 'due_date', 'payment_status', 'month'];
foreach ($required_fields as $field) {
    if (empty($_POST[$field]) && $_POST[$field] !== '0') { // allow zero if applicable
        echo json_encode(['success' => false, 'message' => "Missing required field: $field"]);
        exit;
    }
}

$payment_id = (int)$_POST['payment_id'];
$amount_due = (float)$_POST['amount_due'];
$amount_paid = (float)$_POST['amount_paid'];
$late_fee = isset($_POST['late_fee']) ? (float)$_POST['late_fee'] : 0;
$due_date = $_POST['due_date'];
$late_fee_applied_date = !empty($_POST['late_fee_applied_date']) ? $_POST['late_fee_applied_date'] : null;
$payment_status = $_POST['payment_status'];
$month = (int)$_POST['month'];

// Validate payment status
$allowed_statuses = ['paid', 'unpaid', 'partial', 'late'];
if (!in_array($payment_status, $allowed_statuses)) {
    echo json_encode(['success' => false, 'message' => 'Invalid payment status']);
    exit;
}

// Validate month (1-12)
if ($month < 1 || $month > 12) {
    echo json_encode(['success' => false, 'message' => 'Invalid month']);
    exit;
}

// Validate dates
if (!strtotime($due_date)) {
    echo json_encode(['success' => false, 'message' => 'Invalid due date']);
    exit;
}

if ($late_fee_applied_date && !strtotime($late_fee_applied_date)) {
    echo json_encode(['success' => false, 'message' => 'Invalid late fee applied date']);
    exit;
}

// Update payment in database
$query = "UPDATE student_payments SET 
          amount_due = ?, 
          amount_paid = ?, 
          late_fee = ?, 
          due_date = ?, 
          late_fee_applied_date = ?, 
          payment_status = ?, 
          month = ?,
          updated_at = NOW(),
          updated_by = ?
          WHERE id = ?";

$stmt = $conn->prepare($query);
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Prepare failed: ' . $conn->error]);
    exit;
}

$stmt->bind_param(
    "ddssssiii",
    $amount_due,
    $amount_paid,
    $late_fee,
    $due_date,
    $late_fee_applied_date,
    $payment_status,
    $month,
    $admin_id,
    $payment_id
);

if ($stmt->execute()) {
    // After updating current payment, check if there is overpayment
    $overpayment = $amount_paid - $amount_due;

    if ($overpayment > 0) {
        // Get student_id for this payment
        $getStudentStmt = $conn->prepare("SELECT student_id, year, month FROM student_payments WHERE id = ?");
        $getStudentStmt->bind_param("i", $payment_id);
        $getStudentStmt->execute();
        $result = $getStudentStmt->get_result();
        $paymentData = $result->fetch_assoc();
        $getStudentStmt->close();

        if ($paymentData) {
            $student_id = $paymentData['student_id'];
            $year = $paymentData['year'];
            $month = $paymentData['month'];

            // Find previous payments with negative balances
            $prevPaymentsStmt = $conn->prepare("
                SELECT id, amount_due, amount_paid
                FROM student_payments
                WHERE student_id = ? AND (year < ? OR (year = ? AND month < ?))
                ORDER BY year ASC, month ASC
            ");
            $prevPaymentsStmt->bind_param("iiii", $student_id, $year, $year, $month);
            $prevPaymentsStmt->execute();
            $prevPaymentsResult = $prevPaymentsStmt->get_result();

            $remainingOverpayment = $overpayment;

            while ($row = $prevPaymentsResult->fetch_assoc()) {
                $payment_id_to_update = $row['id'];
                $due = (float)$row['amount_due'];
                $paid = (float)$row['amount_paid'];
                $payment_balance = $due - $paid;

                if ($payment_balance < 0) {
                    // This payment has overpayment
                    $update_amount_paid = $due; // Set paid = due (balance 0)
                    $update_payment_status = 'paid';

                    $updateStmt = $conn->prepare("
                        UPDATE student_payments
                        SET amount_paid = ?, payment_status = ?, updated_at = NOW()
                        WHERE id = ?
                    ");
                    $updateStmt->bind_param("dsi", $update_amount_paid, $update_payment_status, $payment_id_to_update);
                    $updateStmt->execute();
                    $updateStmt->close();

                    $remainingOverpayment -= abs($payment_balance);

                    if ($remainingOverpayment <= 0) break;
                }
            }
            $prevPaymentsStmt->close();
        }
    }

    echo json_encode(['success' => true, 'message' => 'Payment updated successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update payment: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
