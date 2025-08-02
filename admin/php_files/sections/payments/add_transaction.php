<?php
require_once __DIR__ . '/../../../../config/config.php';
require_once BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/config/auth.php';
require_once BASE_PATH . '/admin/php_files/sections/payments/updatelateStatus.php'; 
// Only allow admin access
require_admin();

header('Content-Type: application/json');
date_default_timezone_set('Asia/Dhaka'); 


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$verified_by_admin = $_SESSION['admin']['id']; 

$student_id = $_POST['student_id'] ?? null; 
$payment_id = $_POST['payment_id'] ?? null;
$amount = $_POST['amount'] ?? null;
$payment_method_id = $_POST['payment_method_id'] ?? null;
$verification_status = strtolower($_POST['verification_status'] ?? '');
$month = $_POST['month'] ?? null;
$year = $_POST['year'] ?? null;
$notes = trim($_POST['notes'] ?? null);
$reference_code = trim($_POST['reference_code'] ?? null);


$transaction_id = $_POST['transaction_id'] ?? null;
$receipt_number = $_POST['receipt_number'] ?? null;
$sender_name = $_POST['sender_name'] ?? null;
$screenshot_path = $_POST['screenshot_path'] ?? null;
$verified_by = $verified_by_admin; 


$payment_date = date('Y-m-d H:i:s');


// Assume $conn is your mysqli connection and $student_id is already available

$depositor_phone = isset($_POST['phone_number']) ? preg_replace('/\D/', '', $_POST['phone_number']) : null;

if (!$depositor_phone && $student_id) {
    // Get phone number from database
    $stmt = $conn->prepare("SELECT contact_number FROM students WHERE id = ?");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    if (!$stmt->execute()) {
    echo json_encode(['success' => false, 'message' => 'Server Error.' ]);
    $stmt->close();
    exit;
}

    $stmt->bind_result($contact_number_from_db);
    if ($stmt->fetch() && !empty($contact_number_from_db)) {
        $depositor_phone = preg_replace('/\D/', '', $contact_number_from_db);
    }

    $stmt->close();
}

// Now validate the phone number
if (!$depositor_phone || !preg_match('/^01[0-9]{9}$/', $depositor_phone)) {
    echo json_encode(['success' => false, 'message' => 'Invalid or missing phone number.']);
    exit;
}



if (!is_numeric($amount) || $amount <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid amount.']);
    exit;
}



// Optional: Validate
if (!$student_id || !$payment_id || !$amount || !$payment_date || !$payment_method_id || !$verification_status || !$month || !$year) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}


// check if month and year are valid
if(!is_numeric($month) || !is_numeric($year) || $month < 1 || $month > 12 || $year < 2000 || $year > date('Y')) {
    echo json_encode(['success' => false, 'message' => 'Invalid month or year']); 
    exit; 
}

if (!is_numeric($student_id) || !is_numeric($payment_id)) {
    echo json_encode(['success' => false, 'message' => 'Invalid student or payment ID.']);
    exit;
}


// Update Late Status for the student
if($student_id) {
    updateLateStatus($student_id); 
} else {
    updateLateStatus(); // Update for all students
}


// check if verification_status is valid
$valid_statuses = ['rejected', 'pending', 'verified'];

if (!in_array($verification_status, $valid_statuses)) {
    echo json_encode(['success' => false, 'message' => 'Invalid verification status']);
    exit;
}


// Check if the payment is already paid, including month and year
$stmt = $conn->prepare("SELECT payment_status, due_date, is_late, late_fee_applied_date, year, month FROM student_payments WHERE id = ?");
$stmt->bind_param("i", $payment_id);
$stmt->execute();
$result = $stmt->get_result();
$payment = $result->fetch_assoc();
$stmt->close();

if (!$payment) {
    echo json_encode(['success' => false, 'message' => 'Payment not found.']);
    exit;
}


if ($payment['payment_status'] === 'paid' && $payment['month'] == $month && $payment['year'] == $year) {
    // Format month and year nicely, e.g., "April 2025"
    $monthName = DateTime::createFromFormat('!m', $payment['month'])->format('F');
    $year = $payment['year'];
    
    echo json_encode([
        'success' => false, 
        'message' => "Payment is fully paid for {$monthName} {$year}."
    ]);
    exit;
}


    // for is_late_fee_taken value update
    $dueDateObj = new DateTime($payment['due_date']);
    $lateFeeAppliedDateObj = $payment['late_fee_applied_date'] ? new DateTime($payment['late_fee_applied_date']) : new DateTime('9999-12-31');
    $today = new DateTime();
    // $lateFeeApplies = in_array($payment['payment_status'], ['unpaid', 'partial']) && $today > $dueDateObj && $today >= $lateFeeAppliedDateObj;
    $lateFeeApplies = $payment['is_late'] && in_array($payment['payment_status'], ['unpaid', 'partial']);

    $update_is_late_fee_taken = 0; 



try {
    // Step 0: Find the payment record for the specific month and year
    $stmt = $conn->prepare("SELECT id, amount_due, balance, month, year FROM student_payments WHERE student_id = ? AND month = ? AND year = ? LIMIT 1");
    $stmt->bind_param("iii", $student_id, $month, $year);
    $stmt->execute();
    $paymentRow = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$paymentRow) {
        echo json_encode(['success' => false, 'message' => "No payment record found for the selected month and year."]);
        exit;
    }

    $payment_id = $paymentRow['id'];
    $amount_due = $paymentRow['amount_due'];
    $balance_amount = $paymentRow['balance'];
    $paid_month_name = date('F', mktime(0, 0, 0, $paymentRow['month'], 1));




    // Step 1: Insert transaction
    // $stmt = $conn->prepare("INSERT INTO payment_transactions (
    //     payment_id, amount, payment_date, payment_method_id, reference_code, notes, verified_by, verification_status
    // ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    

    // $stmt->bind_param(   
    //     "idssssss", 
    //     $payment_id, 
    //     $amount, 
    //     $payment_date, 
    //     $payment_method_id, 
    //     $reference_code, 
    //     $notes, 
    //     $verified_by_admin, 
    //     $verification_status
    // );
    // $stmt->execute();
    // $stmt->close();


    $stmt = $conn->prepare("INSERT INTO payment_transactions (
        payment_id,
        amount,
        payment_date,
        payment_method_id,
        reference_code,
        transaction_id,
        receipt_number,
        sender_mobile, -- add this line
        sender_name,
        screenshot_path,
        verification_status,
        verified_by,
        notes
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param(
        "idsssssssssis",
        $payment_id,
        $amount,
        $payment_date,
        $payment_method_id,
        $reference_code,
        $transaction_id,
        $receipt_number,
        $depositor_phone,      // maps to sender_mobile
        $sender_name,
        $screenshot_path,
        $verification_status,
        $verified_by,
        $notes
    );



    $stmt->execute();
    $stmt->close();


    if($verification_status === 'verified') {
        // Step 2: Recalculate total verified payments for that payment_id
        $stmt = $conn->prepare("SELECT SUM(amount) as total_paid FROM payment_transactions WHERE payment_id = ? AND verification_status = 'verified'");
        $stmt->bind_param("i", $payment_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $total_paid = $result['total_paid'] ?? 0;
        $stmt->close();

        // Step 3: Determine payment status
        $update_balance = $balance_amount - $amount;

        if ($update_balance <= 0) {
            $status = 'paid';
            if($lateFeeApplies) {
                $update_is_late_fee_taken = 1;
            }
        }
        elseif ($total_paid > 0) {
            $status = 'partial';
        } else {
            $status = 'unpaid';
        }


        // Step 4: Update student_payments table
        $stmt = $conn->prepare("UPDATE student_payments SET amount_paid = ?, payment_status = ?, updated_by = ?, balance = ?, is_late_fee_taken = ? WHERE id = ?");
        $stmt->bind_param("dsiiii", $total_paid, $status, $verified_by_admin, $update_balance, $update_is_late_fee_taken, $payment_id);
        $stmt->execute();
        $stmt->close();
        

        echo json_encode([
            'success' => true,
            'message' => "Payment for <strong>{$paid_month_name}</strong> recorded successfully and verified."
        ]);
        exit; 

    } else if ($verification_status === 'pending') {
        echo json_encode([
            'success' => true,
            'message' => "Payment for <strong>{$paid_month_name}</strong> recorded, but pending verification."
        ]);
        exit; 
    } 
    else if($verification_status === 'rejected') {
        echo json_encode([
            'success' => false,
            'message' => "Payment for <strong>{$paid_month_name}</strong> was rejected."
        ]);
        exit;
    }


} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'An unexpected error occurred.']);
}





                                        
