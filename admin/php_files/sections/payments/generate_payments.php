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
    // Check if payment already exists
    $check = $conn->prepare("
        SELECT id FROM student_payments 
        WHERE student_id = ? AND year = ? AND month = ?
    ");
    $check->bind_param("iii", $student['student_id'], $year, $month);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows === 0) {
        // Insert new payment
        $insert = $conn->prepare("
            INSERT INTO student_payments (
                student_id, hostel_id, room_id, room_type_id, room_fee_id,
                year, month, amount_due, late_fee, late_fee_applied_date, payment_status, due_date,
                created_at, updated_at, created_by
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'unpaid', ?, NOW(), NOW(), ?)
        ");

        // Make sure null values are passed properly
        $late_fee_applied_date_param = $late_fee_applied_date ?: null;

        $insert->bind_param(
            "iiiiiiddsssi",
            $student['student_id'],
            $student['hostel_id'],
            $student['room_id'],
            $student['room_type_id'],
            $student['room_fee_id'],
            $year,
            $month,
            $student['price'],             
            $late_fee,
            $late_fee_applied_date_param, 
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
