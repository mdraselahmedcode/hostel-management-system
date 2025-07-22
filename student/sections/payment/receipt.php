<?php
require_once __DIR__ . '/../../../config/config.php';
require_once BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/config/auth.php';
require_once BASE_PATH . '/student/sections/payment/helpers.php';

// Validate access
if (!is_student_logged_in() || !isset($_GET['txn_id'])) {
    header('HTTP/1.1 403 Forbidden');
    die('<div class="alert alert-danger text-center mt-5">Unauthorized access.</div>');
}

$txn_id = (int)$_GET['txn_id'];
$student_id = $_SESSION['student']['id'];

// Fetch transaction with student validation
$stmt = $conn->prepare("
    SELECT 
        t.*, 
        p.student_id, 
        p.amount_due, 
        p.month, 
        p.year,
        s.first_name, 
        s.last_name, 
        s.varsity_id,
        h.hostel_name, 
        r.room_number, 
        rt.type_name AS room_type,
        a.firstname AS verified_by_firstname, 
        a.lastname AS verified_by_lastname,
        (SELECT SUM(amount) FROM payment_transactions WHERE payment_id = p.id) AS amount_paid
    FROM payment_transactions t
    JOIN student_payments p ON t.payment_id = p.id
    JOIN students s ON p.student_id = s.id
    JOIN hostels h ON p.hostel_id = h.id
    JOIN rooms r ON p.room_id = r.id
    JOIN room_types rt ON p.room_type_id = rt.id
    LEFT JOIN admins a ON t.verified_by = a.id
    WHERE t.id = ? AND p.student_id = ?
");
$stmt->bind_param("ii", $txn_id, $student_id);
$stmt->execute();
$result = $stmt->get_result();
$txn = $result->fetch_assoc();
$stmt->close();

if (!$txn) {
    header('HTTP/1.1 404 Not Found');
    echo '<div class="alert alert-danger text-center mt-5">Receipt not found or access denied.</div>';
    exit;
}

$month_name = DateTime::createFromFormat('!m', $txn['month'])->format('F');
$balance = $txn['amount_due'] - $txn['amount_paid'];
$receipt_number = 'RCPT-' . str_pad($txn_id, 6, '0', STR_PAD_LEFT);

// Set proper headers for PDF download if needed
if (isset($_GET['download'])) {
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="receipt_' . $receipt_number . '.pdf"');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Receipt #<?= $receipt_number ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/fontawesome/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .receipt-container {
            max-width: 800px;
            margin: 30px auto;
            padding: 30px;
            background: white;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            border-radius: 5px;
        }
        .receipt-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #eee;
        }
        .receipt-title {
            color: #2c3e50;
            font-weight: 700;
        }
        .receipt-subtitle {
            color: #7f8c8d;
            font-size: 1.1rem;
        }
        .receipt-logo {
            max-height: 80px;
            margin-bottom: 15px;
        }
        .receipt-body {
            margin-bottom: 30px;
        }
        .receipt-section {
            margin-bottom: 25px;
        }
        .receipt-footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #eee;
            font-size: 0.9rem;
            color: #7f8c8d;
        }
        .amount-due {
            font-size: 1.2rem;
            font-weight: bold;
        }
        .verification-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
        }
        .verified {
            background-color: #d4edda;
            color: #155724;
        }
        .pending {
            background-color: #fff3cd;
            color: #856404;
        }
        @media print {
            body {
                background: none;
            }
            .receipt-container {
                box-shadow: none;
                margin: 0;
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
            @page {
                size: auto;
                margin: 10mm;
            }
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <div class="receipt-header">
            <img src="<?= BASE_URL ?>/assets/images/city-university-logo.png" alt="University Logo" class="receipt-logo">
            <h2 class="receipt-title">City University Hostel Management</h2>
            <p class="receipt-subtitle">Official Payment Receipt</p>
        </div>

        <div class="receipt-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="receipt-section">
                        <h5><i class="fas fa-user-graduate mr-2"></i> Student Information</h5>
                        <p>
                            <strong>Name:</strong> <?= htmlspecialchars($txn['first_name'] . ' ' . $txn['last_name']) ?><br>
                            <strong>ID:</strong> <?= htmlspecialchars($txn['varsity_id']) ?><br>
                            <strong>Hostel:</strong> <?= htmlspecialchars($txn['hostel_name']) ?><br>
                            <strong>Room:</strong> <?= htmlspecialchars($txn['room_number']) ?> (<?= htmlspecialchars($txn['room_type']) ?>)
                        </p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="receipt-section">
                        <h5><i class="fas fa-receipt mr-2"></i> Receipt Details</h5>
                        <p>
                            <strong>Receipt #:</strong> <?= $receipt_number ?><br>
                            <strong>Date:</strong> <?= date('M j, Y h:i A', strtotime($txn['payment_date'])) ?><br>
                            <strong>Method:</strong> <?= ucwords(str_replace('_', ' ', $txn['payment_method'])) ?><br>
                            <strong>Transaction ID:</strong> <?= $txn['transaction_id'] ? htmlspecialchars($txn['transaction_id']) : 'N/A' ?>
                        </p>
                    </div>
                </div>
            </div>

            <div class="receipt-section">
                <h5><i class="fas fa-calendar-alt mr-2"></i> Billing Information</h5>
                <p>
                    <strong>Period:</strong> <?= $month_name . ' ' . $txn['year'] ?><br>
                    <strong>Amount Due:</strong> <?= formatCurrency($txn['amount_due']) ?><br>
                    <strong>Amount Paid:</strong> <?= formatCurrency($txn['amount']) ?><br>
                    <strong>Balance:</strong> <?= formatCurrency($balance) ?>
                </p>
            </div>

            <div class="receipt-section">
                <h5><i class="fas fa-check-circle mr-2"></i> Payment Status</h5>
                <p>
                    <?php if ($txn['verified_by_firstname']): ?>
                        <span class="verification-badge verified">
                            <i class="fas fa-check"></i> Verified
                        </span><br>
                        <strong>Verified By:</strong> <?= htmlspecialchars($txn['verified_by_firstname'] . ' ' . $txn['verified_by_lastname']) ?><br>
                        <!-- <strong>Date:</strong> <?= $txn['verified_at'] ? date('M j, Y h:i A', strtotime($txn['verified_at'])) : 'N/A' ?> -->
                    <?php else: ?>
                        <span class="verification-badge pending">
                            <i class="fas fa-clock"></i> Pending Verification
                        </span>
                    <?php endif; ?>
                </p>
            </div>

            <?php if (!empty($txn['payment_notes'])): ?>
            <div class="receipt-section">
                <h5><i class="fas fa-sticky-note mr-2"></i> Notes</h5>
                <p><?= nl2br(htmlspecialchars($txn['payment_notes'])) ?></p>
            </div>
            <?php endif; ?>
        </div>

        <div class="receipt-footer text-center">
            <p>
                This is an official receipt from City University Hostel Management System.<br>
                For any queries, please contact the hostel administration office.<br>
                <small>Generated on: <?= date('M j, Y h:i A') ?></small>
            </p>
        </div>

        <div class="text-center mt-4 no-print">
            <button onclick="window.print()" class="btn btn-primary mr-2">
                <i class="fas fa-print"></i> Print Receipt
            </button>
            <a href="<?= BASE_URL ?>/student/sections/payment/payment_view.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Payments
            </a>
            <?php if ($balance > 0): ?>
                <a href="<?= BASE_URL ?>/student/sections/payment/payment_view.php?payment_id=<?= $txn['payment_id'] ?>" 
                   class="btn btn-success ml-2">
                    <i class="fas fa-money-bill-wave"></i> Pay Balance
                </a>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Auto-print if print parameter is set
        if (window.location.search.includes('print=true')) {
            window.print();
        }
        
        // Auto-download as PDF if download parameter is set
        if (window.location.search.includes('download=true')) {
            // You would implement PDF generation here, possibly using a library like jsPDF
            // For now, we'll just show an alert
            alert('PDF download would be generated here in a real implementation');
        }
    </script>
</body>
</html>