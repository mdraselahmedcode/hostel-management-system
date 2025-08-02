<?php
require_once __DIR__ . '/../../../../config/config.php';
require_once BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/config/auth.php';

header('Content-Type: application/json');


// Ensure time matches your local time (e.g., Dhaka)
date_default_timezone_set('Asia/Dhaka');


if (!is_student_logged_in()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

$requiredFields = ['payment_id', 'amount', 'payment_method_id', 'reference_code', 'sender_mobile', 'sender_name'];
$errors = [];

// Validate required fields
foreach ($requiredFields as $field) {
    if (empty($_POST[$field])) {
        $errors[] = "Missing required field: $field";
    }
}

if (!is_numeric($_POST['amount']) || floatval($_POST['amount']) <= 0) {
    $errors[] = "Amount must be a valid positive number";
}

if (!empty($errors)) {
    echo json_encode(['success' => false, 'errors' => $errors]);
    exit;
}

// Sanitize input
$payment_id         = intval($_POST['payment_id']);
$amount             = floatval($_POST['amount']);
$payment_method_id  = intval($_POST['payment_method_id']);
$reference_code     = $_POST['reference_code'] ?? null;
$transaction_id     = $_POST['transaction_id'] ?? null;
$receipt_number     = $_POST['receipt_number'] ?? null;
$sender_mobile      = $_POST['sender_mobile'] ?? null;
$sender_name        = $_POST['sender_name'] ?? null;
$notes              = $_POST['notes'] ?? null;

// var_dump(
//     'Payment Id: ' . $payment_id . "<br/>" . 
//     'amount: ' . $amount . "<br/>" . 
//     'Payment method id: ' . $payment_method_id . "<br/>" . 
//     'Reference code: ' . $reference_code . "<br/>" . 
//     'Transaction id: ' . $transaction_id . "</br>" . 
//     'Receipt number: ' . $receipt_number . "</br>" . 
//     'Sender number: ' . $sender_mobile . "</br>" . 
//     'Sender name: ' . $sender_name . "<br/>" . 
//     'Notes: ' . $notes . "<br/>"
// );
// die(); 


$payment_date = date('Y-m-d H:i:s'); // Example: 2025-07-24 23:58:00

// Handle file upload
$screenshot_path = null;
if (isset($_FILES['screenshot']) && $_FILES['screenshot']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = BASE_PATH . '/uploads/screenshots/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

    $filename = uniqid('ss_') . '_' . basename($_FILES['screenshot']['name']);
    $targetPath = $uploadDir . $filename;

    if (move_uploaded_file($_FILES['screenshot']['tmp_name'], $targetPath)) {
        $screenshot_path = 'uploads/screenshots/' . $filename;
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to upload screenshot.']);
        exit;
    }
}

try {
    $stmt = $conn->prepare("INSERT INTO payment_transactions (
        payment_id, amount, payment_date, payment_method_id,
        reference_code, transaction_id, receipt_number,
        sender_mobile, sender_name, screenshot_path,
        verification_status, verified_by, notes
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $verification_status = 'pending';
    $verified_by = NULL;

    $stmt->bind_param(
        'idsisssssssss',
        $payment_id, $amount, $payment_date, $payment_method_id,
        $reference_code, $transaction_id, $receipt_number,
        $sender_mobile, $sender_name, $screenshot_path,
        $verification_status, $verified_by, $notes
    );



    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Transaction saved successfully and pending verification.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to save transaction.']);
    }

    $stmt->close();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
