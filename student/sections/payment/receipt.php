<?php
require_once __DIR__ . '/../../../config/config.php';
require_once BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/config/auth.php';
require_once BASE_PATH . '/student/sections/payment/helpers.php';
require_once BASE_PATH . '/includes/slide_message.php';


// Validate access
if (!is_student_logged_in() || !isset($_GET['txn_id'])) {
    header("Location: " . BASE_URL . "/student/dashboard.php");
    exit;
}

$txn_id = (int)$_GET['txn_id'];
$student_id = $_SESSION['student']['id'];

// Fetch transaction with student validation
$stmt = $conn->prepare("
    SELECT 
        t.*, 
        pm.id,
        pm.display_name AS payment_method,
        p.student_id, 
        p.amount_due, 
        p.balance,
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
    JOIN payment_methods pm ON t.payment_method_id = pm.id
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
    header('Location: ' . BASE_URL . '/student/sections/payment/payment_view.php');
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
            background-color: #f5f7fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .receipt-container {
            max-width: 800px;
            margin: 40px auto;
            padding: 30px;
            background: white;
            box-shadow: 0 5px 30px rgba(0, 0, 0, 0.08);
            border-radius: 12px;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .receipt-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        }

        .receipt-title {
            color: #2c3e50;
            font-weight: 700;
            font-size: 24px;
            margin-bottom: 5px;
        }

        .receipt-subtitle {
            color: #7f8c8d;
            font-size: 1rem;
            font-weight: 400;
        }

        .receipt-logo {
            max-height: 70px;
            margin-bottom: 15px;
        }

        .receipt-body {
            margin-bottom: 20px;
        }

        .receipt-section {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f9fafc;
            border-radius: 6px;
            border-left: 3px solid #3498db;
        }

        .receipt-section h5 {
            color: #2c3e50;
            font-weight: 600;
            margin-bottom: 10px;
            font-size: 1rem;
            display: flex;
            align-items: center;
        }

        .receipt-section h5 i {
            margin-right: 8px;
            color: #3498db;
            font-size: 0.9rem;
        }

        .receipt-section p {
            margin-bottom: 4px;
            color: #34495e;
            font-size: 0.9rem;
        }

        .receipt-section strong {
            color: #2c3e50;
            min-width: 100px;
            display: inline-block;
            font-weight: 500;
        }

        .receipt-footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid rgba(0, 0, 0, 0.1);
            font-size: 0.8rem;
            color: #7f8c8d;
            text-align: center;
            line-height: 1.5;
        }

        .verification-badge {
            display: inline-flex;
            align-items: center;
            padding: 4px 8px;
            border-radius: 15px;
            font-size: 0.75rem;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .verification-badge i {
            margin-right: 5px;
            font-size: 0.7rem;
        }

        .verified {
            background-color: #e8f5e9;
            color: #2e7d32;
            border: 1px solid #c8e6c9;
        }

        .pending {
            background-color: #fff8e1;
            color: #ff8f00;
            border: 1px solid #ffe0b2;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-top: 20px;
        }

        .btn-print,
        .btn-back,
        .btn-pay {
            padding: 8px 15px;
            font-size: 0.85rem;
            border-radius: 5px;
        }

        @media print {
            body {
                background: none;
                font-size: 10pt;
            }

            .receipt-container {
                box-shadow: none;
                margin: 0;
                padding: 10px;
                border: none;
                max-width: 100%;
                transform: scale(0.95);
                transform-origin: top center;
            }

            .no-print {
                display: none !important;
            }

            .receipt-section {
                background: none;
                border-left: none;
                padding: 5px 0;
                margin-bottom: 8px;
            }

            .receipt-header {
                margin-bottom: 10px;
                padding-bottom: 5px;
            }

            .receipt-title {
                font-size: 16pt;
            }

            .receipt-section h5 {
                font-size: 11pt;
                margin-bottom: 4px;
            }

            .receipt-section p {
                font-size: 9pt;
                line-height: 1.4;
            }

            .verification-badge i {
                display: none;
            }

            @page {
                size: A4 portrait;
                margin: 10mm;
            }
        }


        @media (max-width: 768px) {
            .receipt-container {
                padding: 20px;
                margin: 20px;
            }

            .receipt-title {
                font-size: 20px;
            }

            .action-buttons {
                flex-direction: column;
                gap: 8px;
            }
        }

        .total-box {
            background-color: #ecf0f1;
            padding: 10px;
            border-radius: 5px;
            font-size: 0.95rem;
            font-weight: 500;
            margin-top: 5px;
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
                        <h5><i class="fas fa-user-graduate"></i> Student Information</h5>
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
                        <h5><i class="fas fa-receipt"></i> Receipt Details</h5>
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
                <h5><i class="fas fa-calendar-alt"></i> Billing Information</h5>
                <p><strong>Billing Period:</strong> <?= $month_name . ' ' . $txn['year'] ?></p>
                <div class="total-box">
                    <p><strong>Amount Due:</strong> <?= formatCurrency($txn['amount_due']) ?></p>
                    <p><strong>Amount Paid:</strong> <?= formatCurrency($txn['amount']) ?></p>
                    <p><strong>Outstanding Balance:</strong> <?= formatCurrency($txn['balance']) ?></p>
                </div>
            </div>


            <div class="receipt-section">
                <h5><i class="fas fa-check-circle"></i> Payment Status</h5>
                <p>
                    <?php if ($txn['verified_by_firstname']): ?>
                        <span class="verification-badge verified">
                            <i class="fas fa-check"></i> Verified
                        </span><br>
                        <strong>Verified By:</strong> <?= htmlspecialchars($txn['verified_by_firstname'] . ' ' . $txn['verified_by_lastname']) ?>
                    <?php else: ?>
                        <span class="verification-badge pending">
                            <i class="fas fa-clock"></i> Pending Verification
                        </span>
                    <?php endif; ?>
                </p>
            </div>

            <?php if (!empty($txn['payment_notes'])): ?>
                <div class="receipt-section">
                    <h5><i class="fas fa-sticky-note"></i> Notes</h5>
                    <p><?= nl2br(htmlspecialchars($txn['payment_notes'])) ?></p>
                </div>
            <?php endif; ?>
        </div>

        <div class="receipt-footer">
            <p>
                This is an official receipt from City University Hostel Management System.<br>
                For any queries, please contact the hostel administration office.<br>
                <small>Generated on: <?= date('M j, Y h:i A') ?></small>
            </p>
        </div>

        <div class="no-print">
            <div class="action-buttons">
                <button onclick="window.print()" class="btn btn-primary btn-print">
                    <i class="fas fa-print"></i> Print Receipt
                </button>
                <a href="<?= BASE_URL ?>/student/sections/payment/payment_view.php" class="btn btn-secondary btn-back">
                    <i class="fas fa-arrow-left"></i> Back to Payments
                </a>
                <?php if ($balance > 0): ?>
                    <a href="<?= BASE_URL ?>/student/sections/payment/payment_view.php?payment_id=<?= $txn['payment_id'] ?>"
                        class="btn btn-success btn-pay">
                        <i class="fas fa-money-bill-wave"></i> Pay Balance
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        if (window.location.search.includes('print=true')) {
            window.print();
        }

        if (window.location.search.includes('download=true')) {
            alert('PDF download would be generated here in a real implementation');
        }
    </script>
</body>

</html>