<?php
require_once __DIR__ . '/../../../config/config.php';
include BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/config/auth.php';

require_admin();

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$payment_id = $_GET['id'];

// Step 1: Fetch late_fee_applied_date and late_fee
$fetch_query = "SELECT amount_due, late_fee, late_fee_applied_date FROM student_payments WHERE id = ?";
$stmt = $conn->prepare($fetch_query);
$stmt->bind_param("i", $payment_id);
$stmt->execute();
$stmt->bind_result($amount_due, $late_fee, $late_fee_applied_date);
$stmt->fetch();
$stmt->close();

// Step 2: Determine if late fee should be applied
$today = date('Y-m-d');
$final_amount_paid = $amount_due;

if (!empty($late_fee_applied_date) && $today >= $late_fee_applied_date) {
    $final_amount_paid += $late_fee;
}

// Step 3: Update payment status with correct amount_paid
$update_query = "UPDATE student_payments 
                 SET payment_status = 'paid', 
                     amount_paid = ?, 
                     updated_at = NOW()
                 WHERE id = ?";
$stmt = $conn->prepare($update_query);
$stmt->bind_param("di", $final_amount_paid, $payment_id);
$stmt->execute();
$stmt->close();

// Step 4: Mark all related transactions as verified
$verify_txns = "UPDATE payment_transactions 
                SET verification_status = 'verified',
                    verified_by = ?,
                    updated_at = NOW()
                WHERE payment_id = ?";
$stmt = $conn->prepare($verify_txns);
$admin_id = $_SESSION['admin']['id'];
$stmt->bind_param("ii", $admin_id, $payment_id);
$stmt->execute();
$stmt->close();

$_SESSION['success'] = "Payment verified successfully";
header("Location: view.php?id=" . $payment_id);
exit();
