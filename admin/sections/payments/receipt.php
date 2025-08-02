<?php
require_once __DIR__ . '/../../../config/config.php';
include BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/config/auth.php';

require_admin();
date_default_timezone_set('Asia/Dhaka'); // desired timezone

$payment_id = $_GET['payment_id'] ?? 0;
$transaction_id = $_GET['transaction_id'] ?? 0;

if (!is_numeric($transaction_id) || !is_numeric($payment_id)) {
    header("Location: " . BASE_URL . '/admin/sections/payments/view.php?id=' . $payment_id);
    exit;
}

// Fetch payment + student info
$payment_query = "SELECT sp.*, s.first_name, s.last_name, s.varsity_id,
                  h.hostel_name, r.room_number, rt.type_name as room_type,
                  a.firstname as admin_first, a.lastname as admin_last,
                  rf.price AS room_fee_price
                  FROM student_payments sp
                  JOIN students s ON sp.student_id = s.id
                  JOIN hostels h ON sp.hostel_id = h.id 
                  JOIN rooms r ON sp.room_id = r.id
                  JOIN room_types rt ON sp.room_type_id = rt.id
                  JOIN admins a ON sp.created_by = a.id
                  LEFT JOIN room_fees rf ON sp.room_type_id = rf.room_type_id
                  WHERE sp.id = ?";

$stmt = $conn->prepare($payment_query);
$stmt->bind_param("i", $payment_id);
$stmt->execute();
$payment = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$payment) {
    die("Payment record not found.");
}

// Fetch single transaction
$txn_query = "SELECT pt.* , pm.name, pm.display_name, pm.account_number
              FROM payment_transactions pt
              JOIN payment_methods pm ON pt.payment_method_id = pm.id
              WHERE pt.id = ? AND pt.payment_id = ?";

$txn_stmt = $conn->prepare($txn_query);
$txn_stmt->bind_param("ii", $transaction_id, $payment_id);
$txn_stmt->execute();
$transaction = $txn_stmt->get_result()->fetch_assoc();
$txn_stmt->close();

if (!$transaction) {
    die("Transaction not found.");
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Transaction Receipt - City University Hostel</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <link rel="stylesheet" href="<?= BASE_URL ?>/admin/assets/css/receipt_payment.css">
</head>
<body>

<div id="receipt" class="receipt-container">
    <div class="header">
        <h1>CITY UNIVERSITY</h1>
        <p>Hostel Management System</p>
    </div>

    <div class="receipt-title">TRANSACTION RECEIPT</div>
    <div class="receipt-number">Receipt No: TXN-<?= str_pad($transaction['id'], 6, '0', STR_PAD_LEFT) ?></div>

    <div class="receipt-details">
        <table>
            <tr>
                <th>Student Name</th>
                <td><?= htmlspecialchars($payment['first_name'] . ' ' . $payment['last_name']) ?></td>
            </tr>
            <tr>
                <th>Varsity ID</th>
                <td><?= htmlspecialchars($payment['varsity_id']) ?></td>
            </tr>
            <tr>
                <th>Hostel & Room</th>
                <td><?= htmlspecialchars($payment['hostel_name']) ?> - Room <?= htmlspecialchars($payment['room_number']) ?> (<?= htmlspecialchars($payment['room_type']) ?>)</td>
            </tr>
            <tr>
                <th>Payment Period</th>
                <td><?= date('F Y', mktime(0, 0, 0, $payment['month'], 1, $payment['year'])) ?></td>
            </tr>
            <tr>
                <th>Transaction Date</th>
                <td><?= date('d M Y, h:i A', strtotime($transaction['payment_date'])) ?></td>
            </tr>
            <tr>
                <th>Payment Method</th>
                <td><?= htmlspecialchars($transaction['display_name']) ?></td>
            </tr>
        </table>
    </div>

    <table class="payment-table">
        <thead>
            <tr>
                <th>Description</th>
                <th>Amount (BDT)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Payment for <?= date('F Y', mktime(0, 0, 0, $payment['month'], 1, $payment['year'])) ?></td>
                <td class="amount"><?= number_format($transaction['amount'], 2) ?></td>
            </tr>
        </tbody>
    </table>

    <div class="signature-section">
        <div class="signature">
            <div class="signature-line"></div>
            <p>Student Signature</p>
        </div>
        <div class="signature">
            <div class="signature-line"></div>
            <p>Issued By: <?= htmlspecialchars($payment['admin_first'] . ' ' . $payment['admin_last']) ?></p>  
        </div>
    </div>

    <div class="footer">
        This is a computer-generated receipt. No signature required for verification.
    </div>
</div>

<button class="btn-download" onclick="downloadPDF()">Download as PDF</button>

<script>
    function downloadPDF() {
        const element = document.getElementById('receipt');
        const opt = {
            margin: 10,
            filename: 'transaction_receipt_<?= $transaction_id ?>.pdf',
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2, logging: true, useCORS: true },
            jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
        };
        html2pdf().set(opt).from(element).save();
    }
</script>

</body>
</html>
