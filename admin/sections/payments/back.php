<?php
require_once __DIR__ . '/../../../config/config.php';
include BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/config/auth.php';

require_admin();

$payment_id = $_GET['id'] ?? 0;
$selected_month = isset($_GET['month_filter']) ? (int)$_GET['month_filter'] : null;
$selected_year = isset($_GET['year_filter']) ? (int)$_GET['year_filter'] : null;

// Get current payment details
$payment_query = "SELECT sp.*, s.first_name, s.last_name, s.varsity_id, s.contact_number, 
                  h.hostel_name, r.room_number, rt.type_name as room_type
                  FROM student_payments sp
                  JOIN students s ON sp.student_id = s.id
                  JOIN hostels h ON sp.hostel_id = h.id
                  JOIN rooms r ON sp.room_id = r.id
                  JOIN room_types rt ON sp.room_type_id = rt.id
                  WHERE sp.id = ?";

$stmt = $conn->prepare($payment_query);
$stmt->bind_param("i", $payment_id);
$stmt->execute();
$payment = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$payment) {
    echo "Payment record not found.";
    exit;
}

$student_id = $payment['student_id'];

// Build query for all payments with optional month/year filter
$all_payments_query = "
    SELECT sp.*, h.hostel_name, r.room_number, rt.type_name AS room_type
    FROM student_payments sp
    JOIN hostels h ON sp.hostel_id = h.id
    JOIN rooms r ON sp.room_id = r.id
    JOIN room_types rt ON sp.room_type_id = rt.id
    WHERE sp.student_id = ?
";

// Add month/year filter conditions if specified
$conditions = [];
$params = [$student_id];
$types = "i";

if ($selected_month !== null) {
    $conditions[] = "sp.month = ?";
    $params[] = $selected_month;
    $types .= "i";
}

if ($selected_year !== null) {
    $conditions[] = "sp.year = ?";
    $params[] = $selected_year;
    $types .= "i";
}

if (!empty($conditions)) {
    $all_payments_query .= " AND " . implode(" AND ", $conditions);
}

$all_payments_query .= " ORDER BY sp.year DESC, sp.month DESC";

$stmt = $conn->prepare($all_payments_query);

if (count($params) > 1) {
    $stmt->bind_param($types, ...$params);
} else {
    $stmt->bind_param($types, $student_id);
}

$stmt->execute();
$all_payments = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Get transactions for this payment
$transactions_query = "SELECT pt.*, pm.display_name as payment_method 
                       FROM payment_transactions pt
                       JOIN payment_methods pm ON pt.payment_method_id = pm.id
                       WHERE pt.payment_id = ? 
                       ORDER BY pt.payment_date DESC";

$stmt = $conn->prepare($transactions_query);
$stmt->bind_param("i", $payment_id);
$stmt->execute();
$transactions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

require_once BASE_PATH . '/admin/includes/header_admin.php';
?>

<div class="content container-fluid mt-5">
    <div class="row full-height">
        <?php require_once BASE_PATH . '/admin/includes/sidebar_admin.php'; ?>

        <main class="col-md-10 ms-sm-auto px-md-4 py-4" style="overflow-y: auto; max-height: calc(100vh - 95px)">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Payment Details</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="<?= BASE_URL ?>/admin/sections/payments/index.php" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Payments
                    </a>
                </div>
            </div>

            <!-- Add month/year filter form -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="get" class="row g-3">
                        <input type="hidden" name="id" value="<?= $payment_id ?>">
                        <div class="col-md-3">
                            <label for="month_filter" class="form-label">Filter by Month</label>
                            <select class="form-select" id="month_filter" name="month_filter">
                                <option value="">All Months</option>
                                <?php for ($m = 1; $m <= 12; $m++): ?>
                                    <option value="<?= $m ?>" <?= $selected_month === $m ? 'selected' : '' ?>>
                                        <?= date('F', mktime(0, 0, 0, $m, 1)) ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="year_filter" class="form-label">Filter by Year</label>
                            <select class="form-select" id="year_filter" name="year_filter">
                                <option value="">All Years</option>
                                <?php 
                                $current_year = date('Y');
                                for ($y = $current_year; $y >= $current_year - 5; $y--): ?>
                                    <option value="<?= $y ?>" <?= $selected_year === $y ? 'selected' : '' ?>>
                                        <?= $y ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-funnel"></i> Apply Filter
                            </button>
                            <?php if ($selected_month || $selected_year): ?>
                                <a href="view.php?id=<?= $payment_id ?>" class="btn btn-outline-secondary ms-2">
                                    <i class="bi bi-x-circle"></i> Clear
                                </a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>

            <div class="row">
                <!-- Current payment details -->
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5>Payment Information</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm">
                                <tr>
                                    <th>Student:</th>
                                    <td><?= htmlspecialchars($payment['first_name'] . ' ' . $payment['last_name']) ?></td>
                                </tr>
                                <tr>
                                    <th>Varsity ID:</th>
                                    <td><?= htmlspecialchars($payment['varsity_id']) ?></td>
                                </tr>
                                <tr>
                                    <th>Contact:</th>
                                    <td><?= htmlspecialchars($payment['contact_number']) ?></td>
                                </tr>
                                <tr>
                                    <th>Hostel:</th>
                                    <td><?= htmlspecialchars($payment['hostel_name']) ?></td>
                                </tr>
                                <tr>
                                    <th>Room:</th>
                                    <td><?= htmlspecialchars($payment['room_number'] . ' (' . $payment['room_type'] . ')') ?></td>
                                </tr>
                                <tr>
                                    <th>Period:</th>
                                    <td><?= date('F Y', mktime(0, 0, 0, $payment['month'], 1, $payment['year'])) ?></td>
                                </tr>
                                <tr>
                                    <th>Due Date:</th>
                                    <td><?= date('d M Y', strtotime($payment['due_date'])) ?></td>
                                </tr>
                                <tr>
                                    <th>Late Fee Apply Date:</th>
                                    <td><?= $payment['late_fee_applied_date'] ? date('d M Y', strtotime($payment['late_fee_applied_date'])) : '-' ?></td>
                                </tr>
                                <tr>
                                    <th>Late Fee:</th>
                                    <td>৳<?= number_format($payment['late_fee'], 2) ?></td>
                                </tr>

                                <?php
                                $today = new DateTime();
                                $late_fee_date = $payment['late_fee_applied_date'] ? new DateTime($payment['late_fee_applied_date']) : null;
                                $late_fee_applies = $late_fee_date && $today >= $late_fee_date;

                                $actual_due = $payment['amount_due'];
                                if ($late_fee_applies) {
                                    $actual_due += $payment['late_fee'];
                                }
                                $balance = $actual_due - $payment['amount_paid'];
                                ?>

                                <tr>
                                    <th>Amount Due:</th>
                                    <td>৳<?= number_format($actual_due, 2) ?></td>
                                </tr>
                                <tr>
                                    <th>Amount Paid:</th>
                                    <td>৳<?= number_format($payment['amount_paid'], 2) ?></td>
                                </tr>
                                <tr>
                                    <th>Balance:</th>
                                    <td>৳<?= number_format($balance, 2) ?></td>
                                </tr>

                                <tr>
                                    <th>Status:</th>
                                    <td>
                                        <span class="payment-status status-<?= $payment['payment_status'] ?>">
                                            <?= ucfirst($payment['payment_status']) ?>
                                            <?= $late_fee_applies && $payment['late_fee'] > 0 ? '(Late)' : '' ?>
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Payment transactions -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5>Payment Transactions</h5>
                            <a href="add_transaction.php?payment_id=<?= $payment['id'] ?>" class="btn btn-sm btn-primary">
                                <i class="bi bi-plus"></i> Add Payment
                            </a>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($transactions)): ?>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Method</th>
                                                <th>Amount</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($transactions as $txn): ?>
                                                <tr>
                                                    <td><?= date('d M Y', strtotime($txn['payment_date'])) ?></td>
                                                    <td><?= htmlspecialchars($txn['payment_method']) ?></td>
                                                    <td>৳<?= number_format($txn['amount'], 2) ?></td>
                                                    <td>
                                                        <span class="badge bg-<?=
                                                            $txn['verification_status'] == 'verified' ? 'success' : 
                                                            ($txn['verification_status'] == 'pending' ? 'warning' : 'danger')
                                                        ?>">
                                                            <?= ucfirst($txn['verification_status']) ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <a href="view_transaction.php?id=<?= $txn['id'] ?>" class="btn btn-sm btn-info" title="View">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                        <?php if ($txn['verification_status'] == 'pending'): ?>
                                                            <a href="verify_transaction.php?id=<?= $txn['id'] ?>" class="btn btn-sm btn-success" title="Verify">
                                                                <i class="bi bi-check-circle"></i>
                                                            </a>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-info">No transactions found for this payment.</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- All payments summary for the student with filter indicator -->
            <div class="card mt-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5>All Payments for <?= htmlspecialchars($payment['first_name'] . ' ' . $payment['last_name']) ?></h5>
                    <?php if ($selected_month || $selected_year): ?>
                        <span class="badge bg-info">
                            Filter: 
                            <?= $selected_month ? date('F', mktime(0, 0, 0, $selected_month, 1)) : '' ?>
                            <?= $selected_year ? $selected_year : '' ?>
                        </span>
                    <?php endif; ?>
                </div>
                <div class="card-body table-responsive">
                    <!-- [Rest of your payments table remains the same] -->
                </div>
            </div>
        </main>
    </div>
</div>

<?php require_once BASE_PATH . '/admin/includes/footer_admin.php'; ?>