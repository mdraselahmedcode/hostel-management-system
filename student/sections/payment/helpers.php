<?php
require_once __DIR__ . '/../../../config/config.php'; 
include BASE_PATH . '/config/db.php'; 
use Dompdf\Dompdf; 

function formatCurrency($amount) {
    return CURRENCY_SYMBOL . number_format($amount, 2);
}

function getStatusBadge($status) {
    switch ($status) {
        case 'paid': return 'success';
        case 'partial': return 'info';
        case 'late': return 'danger';
        case 'unpaid': return 'warning';
        default: return 'secondary';
    }
}

function generateMonthlyPayments() {
    global $conn;
    $current_date = date('Y-m-d');
    $current_month = date('m');
    $current_year = date('Y');

    // Get all active student assignments
    $query = "
        SELECT s.id AS student_id, s.hostel_id, s.room_id, r.room_type_id
        FROM students s
        JOIN rooms r ON s.room_id = r.id
        WHERE s.is_checked_in = 1 AND s.is_approved = 1
    ";
    $assignments = []; 
    $result = $conn->query($query);
    while ($row = $result->fetch_assoc()) {
        $assignments[] = $row;
    }

    foreach ($assignments as $assignment) {
        // Get current room fee
        $stmt = $conn->prepare("
            SELECT * FROM room_fees 
            WHERE hostel_id = ? AND room_type_id = ?
            AND effective_from <= ?
            ORDER BY effective_from DESC
            LIMIT 1
        ");
        $stmt->bind_param("iis", 
            $assignment['hostel_id'], 
            $assignment['room_type_id'], 
            $current_date
        );
        $stmt->execute();
        $fee_result = $stmt->get_result();
        $room_fee = $fee_result->fetch_assoc();
        $stmt->close();

        if (!$room_fee) continue;

        // Check if payment already exists
        $stmt = $conn->prepare("
            SELECT id FROM student_payments 
            WHERE student_id = ? AND room_id = ? AND year = ? AND month = ?
        ");
        $stmt->bind_param("iiii", 
            $assignment['student_id'], 
            $assignment['room_id'], 
            $current_year, 
            $current_month
        );
        $stmt->execute();
        $stmt->store_result();
        $exists = $stmt->num_rows > 0;
        $stmt->close();

        if (!$exists) {
            $due_date = date('Y-m-05', strtotime('+1 month'));

            // Insert new payment
            $stmt = $conn->prepare("
                INSERT INTO student_payments 
                (student_id, hostel_id, room_id, room_type_id, room_fee_id,
                 year, month, amount_due, due_date, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            $stmt->bind_param("iiiiiiids", 
                $assignment['student_id'], 
                $assignment['hostel_id'], 
                $assignment['room_id'], 
                $assignment['room_type_id'], 
                $room_fee['id'], 
                $current_year, 
                $current_month, 
                $room_fee['price'], 
                $due_date
            );
            $stmt->execute();
            $stmt->close();
        }
    }
}

function checkLatePayments() {
    global $conn;
    $today = date('Y-m-d');

    // Update unpaid or partial payments to late if due date passed
    $stmt = $conn->prepare("
        UPDATE student_payments 
        SET payment_status = 'late',
            late_fee = amount_due * 0.05,
            updated_at = NOW()
        WHERE payment_status IN ('unpaid', 'partial')
        AND due_date < ?
        AND (payment_status != 'late' OR updated_at < DATE_SUB(NOW(), INTERVAL 1 MONTH))
    ");
    $stmt->bind_param("s", $today);
    $stmt->execute();
    $affected = $stmt->affected_rows;
    $stmt->close();

    return $affected;
}





