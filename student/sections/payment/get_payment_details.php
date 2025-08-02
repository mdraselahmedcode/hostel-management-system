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
           s.varsity_id,
           fl.id AS floor_id,
           fl.floor_number AS floor_number,
           fl.floor_name AS floor_name
    FROM student_payments p
    JOIN hostels h ON p.hostel_id = h.id
    JOIN rooms r ON p.room_id = r.id
    JOIN floors fl ON r.floor_id = fl.id
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
    header('Location: ' . BASE_URL . '/student/sections/payment/payment_view.php');
    exit;
}

// Get payment transactions with method name
$stmt = $conn->prepare("
    SELECT t.*, 
           a.firstname AS verified_by_firstname,
           a.lastname AS verified_by_lastname,
           pm.display_name AS payment_method,
           sp.id AS student_payment_id,
           sp.month AS payment_month,
           sp.year AS payment_year
    FROM payment_transactions t
    LEFT JOIN student_payments sp ON t.payment_id = sp.id 
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
$late_fee = 0;
$late_fee = ($payment['late_fee'] > 0 && $payment['is_late'] == 1) ? $payment['late_fee'] : 0;
?>

<div class="payment-details">
    <div class="row mb-4">
        <div class="col-md-6">
            <h4>Payment Details for <?= $month_name ?> <?= $payment['year'] ?></h4>
            <p class="mb-1"><strong>Student:</strong> <?= htmlspecialchars($payment['student_name']) ?></p>
            <p class="mb-1"><strong>University ID:</strong> <?= htmlspecialchars($payment['varsity_id']) ?></p>
            <p class="mb-1"><strong>Hostel:</strong> <?= htmlspecialchars($payment['hostel_name']) ?></p>
            <div class="d-flex">
                <p class="mb-1"><strong>Floor:</strong> <?= htmlspecialchars($payment['floor_name']) . ' ' . '('  ?> <strong><?= $payment['floor_number'] ?></strong> <?= ')' ?></p>
            </div>
            <p class="mb-1"><strong>Room:</strong> <?= htmlspecialchars($payment['room_number']) ?> (<?= htmlspecialchars($payment['room_type']) ?>)</p>
        </div>
        <div class="col-md-6 text-end">
            <span class="badge bg-<?= getStatusBadge($payment['payment_status']) ?> fs-6">
                <?= ucfirst($payment['payment_status']) ?>
            </span>
            <p class="mb-1"><strong>Due Date:</strong> <?= date('M j, Y', strtotime($payment['due_date'])) ?></p>
            <p class="mb-1">
                <strong>Late Fee:</strong> <?= formatCurrency($late_fee) ?>
            </p>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-light">
                <div class="card-body text-center">
                    <h5 class="card-title">Amount Due</h5>
                    <h4 class="card-text text-primary"><?= formatCurrency($payment['amount_due']) ?></h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-light">
                <div class="card-body text-center">
                    <h5 class="card-title">Amount Paid</h5>
                    <h4 class="card-text text-success"><?= formatCurrency($payment['amount_paid']) ?></h4>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-light">
                <div class="card-body text-center">
                    <h5 class="card-title">Carried Over</h5>
                    <h4 class="card-text text-info"><?= formatCurrency($payment['o_p_balance_added']) ?></h4>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-light">
                <div class="card-body text-center">
                    <h5 class="card-title">Balance</h5>
                    <h4 class="card-text text-danger"><?= formatCurrency($payment['balance']) ?></h4>
                </div>
            </div>
        </div>
    </div>

    <?php if (!empty($transactions)): ?>
        <h5 class="mb-3">Payment Transactions</h5>
        <div class="table-responsive" style="max-height: 300px; overflow-y: auto">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Period</th>
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
                            <td>
                                <?= date('F Y', mktime(0, 0, 0, $txn['payment_month'], 1, $txn['payment_year'])) ?>
                            </td>
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
                                <?php if ($payment_id && $txn['verification_status'] === 'verified'): ?>
                                    <a href="<?= BASE_URL ?>/student/sections/payment/receipt.php?txn_id=<?= $txn['id'] ?>"
                                        target="_blank" class="btn btn-sm btn-outline-primary">
                                        View
                                    </a>
                                <?php elseif ($payment_id && $txn['verification_status'] === 'pending'): ?>
                                    <span class="text-warning">Pending</span>
                                <?php elseif ($payment_id && $txn['verification_status'] === 'rejected'): ?>
                                    <span class="text-danger">Rejected</span>
                                <?php else: ?>
                                    <span class="text-muted">N/A</span>
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