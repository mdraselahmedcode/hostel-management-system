<?php
require_once __DIR__ . '/../../../config/config.php';
require_once BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/config/auth.php';
require_once BASE_PATH . '/student/sections/payment/helpers.php';

// Include Dompdf namespace for PDF generation
use Dompdf\Dompdf;

header('Content-Type: application/json');

// Step 0: Check authentication
if (!is_student_logged_in()) {
    header('Location: ' . BASE_URL . '/student/login.php');
    exit;
}

// // Receipt PDF generation function
// function generateReceiptPDF($transaction_id, $file_path) {
//     require_once BASE_PATH . '/vendor/autoload.php'; // Dompdf autoload

//     $dompdf = new Dompdf();

//     // Capture HTML output of the receipt template
//     ob_start();
//     include BASE_PATH . "/student/sections/payment/receipt_template.php"; // This file should use $transaction_id
//     $html = ob_get_clean();

//     $dompdf->loadHtml($html);
//     $dompdf->setPaper('A4', 'portrait');
//     $dompdf->render();

//     // Save the PDF to the specified path
//     file_put_contents($file_path, $dompdf->output());
// }

// Step 1: Validate input
if (!isset($_POST['payment_id'])) {
    http_response_code(400); // Bad request
    echo json_encode(['success' => false, 'message' => 'Missing payment ID.']);
    exit;
}

$student_id = $_SESSION['student']['id'];
$payment_id = (int) $_POST['payment_id'];
$amount     = round((float) ($_POST['payment_amount'] ?? 0), 2);
$method     = $_POST['payment_method'] ?? '';
$txn_id     = !empty($_POST['transaction_id']) ? $_POST['transaction_id'] : null;
$notes      = !empty($_POST['payment_notes']) ? $_POST['payment_notes'] : null;

$conn->begin_transaction();

try {
    // Step 2: Check payment record and lock row
    $stmt = $conn->prepare("
        SELECT id, amount_due, amount_paid, payment_status 
        FROM student_payments 
        WHERE id = ? AND student_id = ? 
        FOR UPDATE
    ");
    $stmt->bind_param("ii", $payment_id, $student_id);
    $stmt->execute();
    $result  = $stmt->get_result();
    $payment = $result->fetch_assoc();
    $stmt->close();

    if (!$payment) {
        http_response_code(404); // Not found
        echo json_encode(['success' => false, 'message' => 'Invalid payment record.']);
        exit;
    }

    // Step 3: Validate payment amount
    $balance = round($payment['amount_due'] - $payment['amount_paid'], 2);

    if ($amount <= 0 || ($amount > $balance + 0.01)) {
        http_response_code(400); // Bad request
        echo json_encode([
            'success' => false,
            'message' => 'Invalid payment amount. Your current balance is ' . formatCurrency($balance) . '.'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Step 4: Insert into payment_transactions
    $stmt = $conn->prepare("
        INSERT INTO payment_transactions 
        (payment_id, amount, payment_date, payment_method, transaction_id, notes, created_at) 
        VALUES (?, ?, NOW(), ?, ?, ?, NOW())
    ");
    $stmt->bind_param("idsss", $payment_id, $amount, $method, $txn_id, $notes);
    $stmt->execute();

    // Get inserted transaction ID immediately after execute
    $transaction_id = $conn->insert_id;
    if (!$transaction_id || $transaction_id <= 0) {
        throw new Exception('Failed to retrieve transaction ID.');
    }
    $stmt->close();

    // Step 5: Update student_payments
    $new_amount_paid = round($payment['amount_paid'] + $amount, 2);
    $new_status      = ($new_amount_paid >= $payment['amount_due']) ? 'paid' : 'partial';

    $stmt = $conn->prepare("
        UPDATE student_payments 
        SET amount_paid = ?, payment_status = ?, updated_at = NOW() 
        WHERE id = ?
    ");
    $stmt->bind_param("dsi", $new_amount_paid, $new_status, $payment_id);
    $stmt->execute();
    $stmt->close();

    // Step 6: Generate and store receipt PDF
    $receipt_file_name = "receipt_txn_" . $transaction_id . ".pdf";
    $receipt_relative_path = 'uploads/receipts/' . $receipt_file_name;
    $receipt_full_path = BASE_PATH . '/' . $receipt_relative_path;

    // Ensure receipts directory exists and is writable
    if (!file_exists(dirname($receipt_full_path))) {
        if (!mkdir(dirname($receipt_full_path), 0755, true) && !is_dir(dirname($receipt_full_path))) {
            throw new Exception('Failed to create receipts directory.');
        }
    }

    // Generate the PDF receipt
    // generateReceiptPDF($transaction_id, $receipt_full_path);

    // Step 7: Insert receipt record
    $stmt = $conn->prepare("
        INSERT INTO payment_receipts 
        (transaction_id, receipt_path, generated_at, created_at) 
        VALUES (?, ?, NOW(), NOW())
    ");
    $stmt->bind_param("is", $transaction_id, $receipt_relative_path);
    $stmt->execute();
    $stmt->close();

    // Commit the transaction
    $conn->commit();

    // Return success response
    echo json_encode([
        'success' => true,
        'message' => "Payment of " . formatCurrency($amount) . " recorded successfully.",
        'status'  => $new_status,
        'new_amount_paid' => formatCurrency($new_amount_paid),
        'receipt_url' => BASE_URL . '/' . $receipt_relative_path
    ], JSON_UNESCAPED_UNICODE);
    exit;

} catch (Exception $e) {
    $conn->rollback();
    error_log("Payment error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred during payment processing. Please try again later.'
    ]);
    exit;
}
