<?php
require_once __DIR__ . '/../../../../config/config.php';
include BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/config/auth.php';

ob_clean();
header('Content-Type: application/json');

// Only allow admin access
require_admin();

// Get POST data
$due_date = $_POST['due_date'] ?? null;
$late_fee_applied_date = $_POST['late_fee_applied_date'] ?? null;
$late_fee = isset($_POST['late_fee']) ? (float) $_POST['late_fee'] : 0;
$month = isset($_POST['month']) ? (int) $_POST['month'] : date('n');
$year = isset($_POST['year']) ? (int) $_POST['year'] : date('Y');

// Validate input
if (!$due_date || !$month || !$year) {
    echo json_encode(['success' => false, 'message' => 'Missing required input.']);
    exit;
}

$admin_id = $_SESSION['admin']['id'] ?? 1;

// Update only unpaid or partial payments for selected month & year
$update = $conn->prepare("
    UPDATE student_payments 
    SET 
        due_date = ?, 
        late_fee = ?, 
        late_fee_applied_date = ?, 
        updated_at = NOW(), 
        updated_by = ?
    WHERE 
        month = ? AND 
        year = ? AND 
        payment_status IN ('unpaid', 'partial')
");

$update->bind_param("sdssii", $due_date, $late_fee, $late_fee_applied_date, $admin_id, $month, $year);
$update->execute();

$affected_rows = $update->affected_rows;

// Response
$message = $affected_rows > 0
    ? "Successfully updated $affected_rows payment(s) for " . date('F', mktime(0, 0, 0, $month, 1)) . " $year."
    : "No payments were updated. Either none exist or all are already paid.";

echo json_encode([
    'success' => true,
    'updated' => $affected_rows,
    'message' => $message
]);
exit;
