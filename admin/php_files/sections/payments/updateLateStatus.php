<?php
require_once __DIR__ . '/../../../../config/config.php'; 
require_once BASE_PATH . '/config/db.php'; 
require_once BASE_PATH . '/config/auth.php'; 
// require_admin(); 

require_student_or_admin(); 

function updateLateStatus($student_id = null) {
    global $conn;

    $sql = "SELECT id, due_date, payment_status, late_fee_applied_date 
            FROM student_payments 
            WHERE payment_status IN ('unpaid', 'partial')";

    if ($student_id !== null) {
        $sql .= " AND student_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $student_id);
    } else {
        $stmt = $conn->prepare($sql);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $today = new DateTime();

    while ($row = $result->fetch_assoc()) {
        try {
            $due_date = new DateTime($row['due_date']);
            $late_fee_applied_date = new DateTime($row['late_fee_applied_date'] ?? '9999-12-31');

            $is_late = ($today > $due_date && $today >= $late_fee_applied_date) ? 1 : 0;

            $update = $conn->prepare("UPDATE student_payments SET is_late = ? WHERE id = ?");
            $update->bind_param("ii", $is_late, $row['id']);
            $update->execute();
            $update->close();
        } catch (Exception $e) {
            // Skip invalid dates
            continue;
        }
    }

    $stmt->close();
}
?>

