<?php
require_once __DIR__ . '/../../../config/config.php';
require_once BASE_PATH . '/config/auth.php';
include BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/student/sections/payment/helpers.php';


if (!is_student_logged_in() || !isset($_GET['payment_id'])) {
    header('Location: ' . BASE_URL . '/student/login.php');
    exit;
}


$payment_id = (int)$_GET['payment_id'];
$student_id = $_SESSION['student']['id'];

// Get payment info
$stmt = $conn->prepare("
    SELECT p.*, 
           h.hostel_name,
           r.room_number,
           rt.type_name AS room_type,
           CONCAT(s.first_name, ' ', s.last_name) AS student_name,
           s.varsity_id
    FROM student_payments p
    JOIN hostels h ON p.hostel_id = h.id
    JOIN rooms r ON p.room_id = r.id
    JOIN room_types rt ON p.room_type_id = rt.id
    JOIN students s ON p.student_id = s.id
    WHERE p.id = ? AND p.student_id = ?
");
$stmt->bind_param("ii", $payment_id, $student_id);
$stmt->execute();
$result = $stmt->get_result();
$payment = $result->fetch_assoc();
$stmt->close();

if (!$payment) {
    echo '<div class="alert alert-danger">Payment record not found</div>';
    exit;
}

// Get payment transactions with method name
$stmt = $conn->prepare("
    SELECT t.*, 
           a.firstname AS verified_by_firstname,
           a.lastname AS verified_by_lastname,
           pm.display_name AS payment_method
    FROM payment_transactions t
    LEFT JOIN admins a ON t.verified_by = a.id
    LEFT JOIN payment_methods pm ON t.payment_method_id = pm.id
    WHERE t.payment_id = ?
    ORDER BY t.payment_date DESC
");

$stmt->bind_param("i", $payment_id);
$stmt->execute();
$result = $stmt->get_result();

$transactions = [];
while ($row = $result->fetch_assoc()) {
    $transactions[] = $row;
}
$stmt->close();

$month_name = DateTime::createFromFormat('!m', $payment['month'])->format('F');
?>

<div class="payment-details">
    <div class="row mb-4">
        <div class="col-md-6">
            <h4>Payment Details for <?= $month_name ?> <?= $payment['year'] ?></h4>
            <p class="mb-1"><strong>Student:</strong> <?= htmlspecialchars($payment['student_name']) ?></p>
            <p class="mb-1"><strong>University ID:</strong> <?= htmlspecialchars($payment['varsity_id']) ?></p>
            <p class="mb-1"><strong>Hostel:</strong> <?= htmlspecialchars($payment['hostel_name']) ?></p>
            <p class="mb-1"><strong>Room:</strong> <?= htmlspecialchars($payment['room_number']) ?> (<?= htmlspecialchars($payment['room_type']) ?>)</p>
        </div>
        <div class="col-md-6 text-end">
            <span class="badge bg-<?= getStatusBadge($payment['payment_status']) ?> fs-6">
                <?= ucfirst($payment['payment_status']) ?>
            </span>
            <p class="mb-1"><strong>Due Date:</strong> <?= date('M j, Y', strtotime($payment['due_date'])) ?></p>
            <p class="mb-1"><strong>Late Fee:</strong> <?= formatCurrency($payment['late_fee']) ?></p>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-light">
                <div class="card-body text-center">
                    <h5 class="card-title">Amount Due</h5>
                    <h3 class="card-text text-primary"><?= formatCurrency($payment['amount_due']) ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-light">
                <div class="card-body text-center">
                    <h5 class="card-title">Amount Paid</h5>
                    <h3 class="card-text text-success"><?= formatCurrency($payment['amount_paid']) ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-light">
                <div class="card-body text-center">
                    <h5 class="card-title">Balance</h5>
                    <h3 class="card-text text-danger"><?= formatCurrency($payment['amount_due'] - $payment['amount_paid']) ?></h3>
                </div>
            </div>
        </div>
    </div>

    <?php if (!empty($transactions)): ?>
        <h5 class="mb-3">Payment Transactions</h5>
        <div class="table-responsive">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Transaction ID</th>
                        <th>Verified By</th>
                        <th>Receipt</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transactions as $txn): ?>
                        <tr>
                            <td><?= date('M j, Y H:i', strtotime($txn['payment_date'])) ?></td>
                            <td><?= formatCurrency($txn['amount']) ?></td>
                            <td><?= ucwords(str_replace('_', ' ', $txn['payment_method'] ?? 'N/A')) ?></td>
                            <td><?= $txn['transaction_id'] ?: 'N/A' ?></td>
                            <td>
                                <?php if ($txn['verified_by_firstname']): ?>
                                    <?= htmlspecialchars($txn['verified_by_firstname'] . ' ' . $txn['verified_by_lastname']) ?>
                                <?php else: ?>
                                    Pending verification
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($txn['receipt_number']): ?>
                                    <a href="<?= BASE_URL ?>/student/sections/payment/receipt.php?txn_id=<?= $txn['id'] ?>" 
                                       target="_blank" class="btn btn-sm btn-outline-primary">
                                        View
                                    </a>
                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info">No payment transactions found</div>
    <?php endif; ?>
</div>
