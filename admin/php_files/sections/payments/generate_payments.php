<?php 

require_once __DIR__ . '/../../../../config/config.php';
include BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/config/auth.php';

date_default_timezone_set("Asia/Dhaka");

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

        // Calculate total overpaid balance from previous months (negative balances)
        $prevBalStmt = $conn->prepare("
            SELECT SUM(balance) AS total_negative_balance
            FROM student_payments
            WHERE student_id = ? 
              AND balance < 0 
              AND (year < ? OR (year = ? AND month < ?))
        ");
        $prevBalStmt->bind_param("iiii", $student['student_id'], $year, $year, $month);
        $prevBalStmt->execute();
        $prevBalResult = $prevBalStmt->get_result();
        $prev_balance = 0.00;
        if ($prevBalRow = $prevBalResult->fetch_assoc()) {
            $prev_balance = isset($prevBalRow['total_negative_balance']) ? (float)$prevBalRow['total_negative_balance'] : 0.00;
        }
        $prevBalStmt->close();

        // Late fee logic: store late_fee, add to balance only if date reached
        $effective_late_fee = 0.00;
        if ($late_fee > 0 && $late_fee_applied_date) {
            $today = date('Y-m-d');
            if ($today >= $late_fee_applied_date) {
                $effective_late_fee = $late_fee;
            }
        }
        $student_price = isset($student['price']) ? (float)$student['price'] : 0.00;
        $student_price_with_late_fee = $student_price + $effective_late_fee;

        $amount_due = $student_price_with_late_fee;
        $initial_amount_paid = 0.00;
        $initial_payment_status = 'unpaid';
        $new_balance = $amount_due; // since nothing is paid yet

        $o_p_balance_added = 0.00;


        // Apply previous overpayment to current payment
        if ($prev_balance < 0) {
            $overpaid_amount = abs($prev_balance);

            $applied_to_current = min($overpaid_amount, $student_price + $effective_late_fee);
            $o_p_balance_added = $applied_to_current;


            // Reduce overpayment from previous payments in chronological order
            $remaining_to_apply = $applied_to_current;

            $stmt = $conn->prepare("
                SELECT id, balance
                FROM student_payments
                WHERE student_id = ? AND balance < 0 AND (year < ? OR (year = ? AND month < ?))
                ORDER BY year ASC, month ASC
                FOR UPDATE
            ");
            $stmt->bind_param("iiii", $student['student_id'], $year, $year, $month);
            $stmt->execute();
            $overpaidRows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $stmt->close();

            foreach ($overpaidRows as $row) {
                if ($remaining_to_apply <= 0) break;

                $current_overpay = abs($row['balance']);
                $deduct = min($remaining_to_apply, $current_overpay);
                $new_balance = -1 * ($current_overpay - $deduct);

                $updateStmt = $conn->prepare("
                    UPDATE student_payments SET balance = ?, updated_at = NOW()
                    WHERE id = ?
                ");
                $updateStmt->bind_param("di", $new_balance, $row['id']);
                $updateStmt->execute();
                $updateStmt->close();

                $remaining_to_apply -= $deduct;
            }

            // Now set amount_paid and balance for new payment record
            $initial_amount_paid = $applied_to_current;
            $new_balance = ($student_price + $effective_late_fee) - $initial_amount_paid;

            if ($initial_amount_paid >= ($student_price + $effective_late_fee)) {
                $initial_payment_status = 'paid';
            } elseif ($initial_amount_paid > 0) {
                $initial_payment_status = 'partial';
            } else {
                $initial_payment_status = 'unpaid';
            }
        }

        $is_late = (strtotime(date('Y-m-d')) >= strtotime($late_fee_applied_date)) ? 1 : 0;


        $insert = $conn->prepare("
            INSERT INTO student_payments (
                student_id, hostel_id, room_id, room_type_id, room_fee_id,
                year, month, amount_due, balance, o_p_balance_added,
                late_fee, late_fee_applied_date, is_late, payment_status, due_date,
                created_at, updated_at, created_by
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW(), ?)
        ");


        $late_fee_applied_date_param = $late_fee_applied_date ?: null;

        $insert->bind_param(
            "iiiiiidddddsissi", 
            $student['student_id'],
            $student['hostel_id'],
            $student['room_id'],
            $student['room_type_id'],
            $student['room_fee_id'],
            $year,
            $month,
            $amount_due,
            $new_balance,
            $o_p_balance_added,
            $late_fee,
            $late_fee_applied_date_param,
            $is_late,
            $initial_payment_status,
            $due_date,
            $admin_id
        );


        if ($insert->execute()) {
            $generated_count++;

                $new_payment_id = $insert->insert_id;

                // If this new payment was marked late and already fully paid, mark late fee as taken
                if ($is_late && $initial_payment_status === 'paid') {
                    $updateLateFee = $conn->prepare("
                        UPDATE student_payments
                        SET is_late_fee_taken = 1
                        WHERE id = ?
                    ");
                    $updateLateFee->bind_param("i", $new_payment_id);
                    $updateLateFee->execute();
                    $updateLateFee->close();
                }


            // Fetch all overpaid rows (negative balances)
            $stmt = $conn->prepare("
                SELECT id, year, month, balance
                FROM student_payments
                WHERE student_id = ? AND balance < 0
                ORDER BY year ASC, month ASC
            ");
            $stmt->bind_param("i", $student['student_id']);
            $stmt->execute();
            $overpaidPayments = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $stmt->close();

            // Fetch all underpaid rows excluding the newly inserted payment (to avoid double applying)
            $stmt = $conn->prepare("
                SELECT id, year, month, amount_paid, balance, payment_status, is_late, is_late_fee_taken
                FROM student_payments
                WHERE student_id = ? AND balance > 0 AND NOT (year = ? AND month = ?)
                ORDER BY year ASC, month ASC
            ");
            $stmt->bind_param("iii", $student['student_id'], $year, $month);
            $stmt->execute();
            $underpaidPayments = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $stmt->close();

            foreach ($overpaidPayments as $overpaid) {
                $overpaid_amount = abs($overpaid['balance']);

                foreach ($underpaidPayments as &$underpaid) {
                    // Skip same month-year (already excluded by query, but double check)
                    if ($overpaid['year'] == $underpaid['year'] && $overpaid['month'] == $underpaid['month']) {
                        continue;
                    }

                    if ($overpaid_amount <= 0) break;

                    $apply_amount = min($overpaid_amount, $underpaid['balance']);
                    $new_paid = $underpaid['amount_paid'] + $apply_amount;
                    $new_balance = $underpaid['balance'] - $apply_amount;
                    

                    if ($new_balance <= 0) {
                        $new_status = 'paid';
                    } elseif ($new_paid > 0) {
                        $new_status = 'partial';
                    } else {
                        $new_status = 'unpaid';
                    }

                    // Calculate new o_p_balance_added for this underpaid payment
                    $new_o_p_balance_added = (float) $underpaid['o_p_balance_added'] + $apply_amount;

                    // Update underpaid payment row with o_p_balance_added
                    $stmt = $conn->prepare("
                        UPDATE student_payments
                        SET amount_paid = ?, balance = ?, payment_status = ?, o_p_balance_added = ?, updated_by = ?, updated_at = NOW()
                        WHERE id = ?
                    ");
                    $stmt->bind_param("dssdii", $new_paid, $new_balance, $new_status, $new_o_p_balance_added, $admin_id, $underpaid['id']);
                    $stmt->execute();
                    $stmt->close();


                    // After updating payment to 'paid', re-check late condition and mark if needed
                    if ($new_status === 'paid') {
                        // Re-fetch is_late directly from DB to be 100% sure
                        $lateCheckStmt = $conn->prepare("
                            SELECT is_late FROM student_payments WHERE id = ?
                        ");
                        $lateCheckStmt->bind_param("i", $underpaid['id']);
                        $lateCheckStmt->execute();
                        $lateCheckResult = $lateCheckStmt->get_result();
                        $row = $lateCheckResult->fetch_assoc();
                        $lateCheckStmt->close();

                        if (!empty($row['is_late'])) {
                            $updateLateFee = $conn->prepare("
                                UPDATE student_payments
                                SET is_late_fee_taken = 1
                                WHERE id = ?
                            ");
                            $updateLateFee->bind_param("i", $underpaid['id']);
                            $updateLateFee->execute();
                            $updateLateFee->close();
                        }
                    }



                    // Update in-memory values
                    $underpaid['amount_paid'] = $new_paid;
                    $underpaid['balance'] = $new_balance;
                    $underpaid['payment_status'] = $new_status;

                    $overpaid_amount -= $apply_amount;
                }

                // Update overpaid row with remaining overpayment balance (negative)
                $remaining = -1 * $overpaid_amount;
                $stmt = $conn->prepare("
                    UPDATE student_payments
                    SET balance = ?, updated_at = NOW()
                    WHERE id = ?
                ");
                $stmt->bind_param("di", $remaining, $overpaid['id']);
                $stmt->execute();
                $stmt->close();
            }
        }

        $insert->close();

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
