<?php
require_once __DIR__ . '/../../../config/config.php';
include BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/config/auth.php';
require_once BASE_PATH . '/includes/slide_message.php'; 

// require_admin();
date_default_timezone_set('Asia/Dhaka'); // desired timezone

$payment_id = $_GET['id'] ?? 0;

require_student_or_admin(); 

if (!is_numeric($payment_id)) {
    header('Location: ' . BASE_URL . '/index.php');
    exit;
}

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
if (!$stmt) {
    die("Database error: " . $conn->error);
}

$stmt->bind_param("i", $payment_id);
$stmt->execute();
$payment = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$payment) {
    // echo json_encode(['success' => false ,'message' => 'Payment record not found']); 
    die('Payment record not found'); 
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Payment Receipt - City University Hostel</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <link rel="stylesheet" href="<?= BASE_URL ?>/admin/assets/css/receipt_payment.css">
</head>

<body>

    <div id="receipt" class="receipt-container">
        <div class="header">
            <h1>CITY UNIVERSITY</h1>
            <p>Hostel Management System</p>
        </div>

        <div class="receipt-title"> MONTHLY PAYMENT REPORT</div>

        <div class="receipt-number">Receipt No: RCPT-<?= str_pad($payment['id'], 5, '0', STR_PAD_LEFT) ?></div>

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
                    <th>Latest Payment Date</th>
                    <td><?= date('d M Y', strtotime($payment['updated_at'])) ?></td>
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
                    <td>Room Fee</td>
                    <td class="amount"><?= number_format($payment['room_fee_price'], 2) ?></td>
                </tr>
                <?php $p = $payment;
                $dueDateObj = new DateTime($p['due_date']);
                $lateFeeAppliedDateObj = $p['late_fee_applied_date'] ? new DateTime($p['late_fee_applied_date']) : new DateTime('9999-12-31');
                $today = new DateTime();

                $lateFeeApplies = in_array($p['payment_status'], ['unpaid', 'partial']) && $today > $dueDateObj && $today >= $lateFeeAppliedDateObj;
                // $actualDue = $p['amount_due'] + ($lateFeeApplies ? $p['late_fee'] : 0);
                // $balance = $actualDue - $p['amount_paid'];
                $balance = $p['balance'];  // added later
                $period = date('F Y', mktime(0, 0, 0, $p['month'], 1, $p['year']));
                $is_late_fee_taken = ($p['is_late_fee_taken'] === 1 && $lateFeeApplies) ? 'Yes' : 'Not yet';

                switch ($p['payment_status']) {
                    case 'paid':
                        $statusClass = 'paid';
                        break;
                    case 'unpaid':
                        $statusClass = 'unpaid';
                        break;
                    case 'partial':
                        $statusClass = 'partial';
                        break;
                    case 'late':
                        $statusClass = 'late';
                        break;
                    default:
                        $statusClass = '';
                        break;
                }
                ?>
                <?php if (($payment['late_fee'] > 0 && $lateFeeApplies) || $payment['is_late_fee_taken']): ?>
                <tr>
                    <td>Late Fee</td>
                    <td class="amount"><?= number_format($payment['late_fee'], 2) ?></td>
                </tr>
                <?php endif; ?>

                <?php 
                    $total_payable = $payment['amount_due']; 
                    // if(($payment['late_fee'] > 0 && $lateFeeApplies) || $payment['is_late_fee_taken']) {
                    //     $total_payable += $payment['late_fee']; 
                    // }
                    $paid_amount_color = 'black';
                    if($payment['amount_paid'] >= $total_payable ) {
                        $paid_amount_color = "green"; 
                    } else if($payment['amount_paid'] < $total_payable && $payment['amount_paid'] > 0) {
                        $paid_amount_color = 'blue';
                    } else if($payment['amount_paid'] <= 0) {
                        $paid_amount_color = 'red'; 
                    }
                    // $lateFeeApplies && !$payment['is_late_fee_taken'] ? $payment['late_fee'] : 0;
                ?>

                <tr>
                    <td>Payable</td>
                    <td class="amount"><?= number_format($total_payable, 2) ?></td>
                </tr>
                
                <?php
                    $red_if_zero_paid =  ($payment['amount_paid'] <= 0) ? 'red' : ''
                ?>
                <tr class="total-row">
                    <td>TOTAL PAID</td>
                    <td class="amount" style="color: <?= $paid_amount_color ?>; "><span class="text-primary" style=" font-size: 10px">৳</span><?= number_format($payment['amount_paid'], 2) ?></td>
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
            This is a computer generated receipt. No signature required for verification.
        </div>
    </div>

    <button class="btn-download" onclick="downloadPDF()">Download as PDF</button>

    <script>
        function downloadPDF() {
            const element = document.getElementById('receipt');
            const opt = {
                margin: 10,
                filename: 'hostel_payment_receipt_<?= $payment_id ?>.pdf',
                image: {
                    type: 'jpeg',
                    quality: 0.98
                },
                html2canvas: {
                    scale: 2,
                    logging: true,
                    useCORS: true
                },
                jsPDF: {
                    unit: 'mm',
                    format: 'a4',
                    orientation: 'portrait'
                }
            };

            // New Promise-based usage:
            html2pdf().set(opt).from(element).save();
        }
    </script>

</body>

</html>