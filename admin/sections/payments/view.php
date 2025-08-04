<?php
require_once __DIR__ . '/../../../config/config.php';
include BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/config/auth.php';
include BASE_PATH . '/includes/slide_message.php';
require_once BASE_PATH . '/admin/php_files/sections/payments/updateLateStatus.php';

date_default_timezone_set("Asia/Dhaka");

// Only allow admin access
require_admin();

$payment_id = $_GET['id'] ?? 0;
// $student_id = $_GET['student_id'] ?? 0;

$selected_month = $_GET['month'] ?? null;
$selected_year = $_GET['year'] ?? null;

// Get current payment details
$payment_query = "SELECT sp.*, s.first_name, s.last_name, s.varsity_id, s.contact_number, 
                  h.hostel_name, h.hostel_type, r.room_number, rt.type_name as room_type,
                  a.firstname AS updated_by_firstname, a.lastname AS updated_by_last_name,
                  f.id AS floor_id, f.floor_number AS floor_number, f.floor_name AS floor_name
                  FROM student_payments sp
                  JOIN students s ON sp.student_id = s.id
                  JOIN hostels h ON sp.hostel_id = h.id
                  JOIN rooms r ON sp.room_id = r.id
                  JOIN room_types rt ON sp.room_type_id = rt.id
                  JOIN floors f ON r.floor_id = f.id
                  LEFT JOIN admins a ON sp.updated_by = a.id
                  WHERE sp.id = ?";

$stmt = $conn->prepare($payment_query);
$stmt->bind_param("i", $payment_id);
$stmt->execute();
$payment = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$payment) {
    $error = urlencode("Payment not found");
    header("Location: " . BASE_URL . "/admin/sections/payments/index.php");
    exit;
}

//  update is late status
updateLateStatus($payment['student_id']);



$student_id = $payment['student_id'];

$all_payments_query = "
    SELECT 
        sp.*, 
        h.hostel_name, 
        r.room_number, 
        rt.type_name AS room_type, 
        s.first_name, 
        s.last_name,
        rf.id AS room_fee_id,
        rf.price AS room_fee_price
    FROM student_payments sp
    JOIN hostels h ON sp.hostel_id = h.id
    JOIN rooms r ON sp.room_id = r.id
    JOIN room_types rt ON sp.room_type_id = rt.id
    JOIN students s ON sp.student_id = s.id
    JOIN room_fees rf ON rf.room_type_id = sp.room_type_id AND rf.hostel_id = sp.hostel_id
    WHERE sp.student_id = ?
";



// Add month/year filter conditions if specified
$conditions = [];
$params = [$student_id];
$types = "i";

if ($selected_month !== null && $selected_month !== '') {
    $conditions[] = "sp.month = ?";
    $params[] = $selected_month;
    $types .= "i";
}

if ($selected_year !== null && $selected_year !== '') {
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



// transaction data fetching
$transactions = [];
if ($payment_id) {
    $stmt = $conn->prepare("
        SELECT t.*, pm.name, pm.display_name, pm.account_number, pm.active, pm.created_at,
        sp.o_p_balance_added AS o_p_balance_added
        FROM payment_transactions t
        JOIN payment_methods pm ON t.payment_method_id = pm.id
        JOIN student_payments sp ON t.payment_id = sp.id
        WHERE t.payment_id = ?
        ORDER BY t.payment_date DESC
    ");
    $stmt->bind_param("i", $payment_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $transactions = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}





require_once BASE_PATH . '/admin/includes/header_admin.php';
?>

<head>
    <link rel="stylesheet" href="<?= BASE_URL . '/admin/assets/css/payment_view.css'?>">
    <style>
        :root {
            --primary-color: #394e63ff;
            --primary-hover: #1c2935ff;
            --primary-text: #ffffff;
        }

        .bg-primary {
            background-color: var(--primary-color) !important;
        }

        .text-primary {
            color: var(--primary-color) !important;
        }

        .btn-primary {
            background-color: var(--primary-color) !important;
            border-color: var(--primary-color) !important;
            color: var(--primary-text) !important;
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }

        .btn-primary:hover {
            background-color: var(--primary-hover) !important;
            border-color: var(--primary-hover) !important;
        }

        .btn-primary:focus,
        .btn-primary:active {
            box-shadow: 0 0 0 0.2rem rgba(44, 62, 80, 0.5) !important;
        }

        a.text-primary:hover,
        a.text-primary:focus {
            color: var(--primary-hover) !important;
            text-decoration: underline;
        }

        .card-header.bg-primary {
            background-color: var(--primary-color) !important;
            color: var(--primary-text) !important;
        }

        .btn-outline-primary {
            color: var(--primary-color) !important;
            border-color: var(--primary-color) !important;
            background-color: transparent !important;
            transition: color 0.3s ease, background-color 0.3s ease, border-color 0.3s ease;
        }

        .btn-outline-primary:hover,
        .btn-outline-primary:focus,
        .btn-outline-primary:active {
            color: var(--primary-text) !important;
            background-color: var(--primary-color) !important;
            border-color: var(--primary-hover) !important;
            box-shadow: 0 0 0 0.2rem rgba(44, 62, 80, 0.25) !important;
        }

        .card.bg-primary {
            background-color: var(--primary-color) !important;
            color: var(--primary-text) !important;
        }
    </style>
</head>
<div class="content container-fluid mt-5">
    <div class="row full-height">
        <?php require_once BASE_PATH . '/admin/includes/sidebar_admin.php'; ?>

        <main class="col-md-10 ms-sm-auto px-md-4 py-4" style="overflow-y: auto; max-height: calc(100vh - 95px)">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Payment Details</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="<?= BASE_URL . '/admin/sections/payments/index.php' ?>" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Payments
                    </a>
                </div>
            </div>

            <div class="row">
                <!-- Current payment details -->
                <div class="col-md-6" style="max-height: 300px; overflow-y: auto">
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Payment Information</h5>
                            <span class="badge bg-primary px-3 py-2 fs-6 shadow-sm">
                                <?= date('F Y', mktime(0, 0, 0, $payment['month'], 1, $payment['year'])) ?>
                            </span>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm">
                                <tr>
                                    <th>Status:</th>
                                    <td>
                                        <?php
                                        $statusClass = '';
                                        switch ($payment['payment_status']) {
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
                                        }

                                        $dueDateObj = new DateTime($payment['due_date']);
                                        $lateFeeAppliedDateObj = $payment['late_fee_applied_date'] ? new DateTime($payment['late_fee_applied_date']) : new DateTime('9999-12-31');
                                        $today = new DateTime();
                                        $lateFeeApplies = in_array($payment['payment_status'], ['unpaid', 'partial']) && $today > $dueDateObj && $today >= $lateFeeAppliedDateObj;
                                        ?>
                                        <span class="payment-status <?= $statusClass ?>">
                                            <?= ucfirst($payment['payment_status']) ?>
                                            <?= $lateFeeApplies && $payment['late_fee'] > 0 ? ' (Late)' : '' ?>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Student:</th>
                                    <td style="display: flex; justify-content: start; align-items: center;">
                                        <a href="<?= BASE_URL . '/admin/sections/students/view.php' ?>?id=<?= urlencode($payment['student_id']) ?>" class="btn btn-sm btn-outline-primary">
                                        <span><?= htmlspecialchars($payment['first_name'] . ' ' . $payment['last_name']) ?></span>
                                            <!-- View Profile -->
                                        </a>
                                    </td>
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
                                    <td><?= htmlspecialchars($payment['hostel_name'] . ' (' . $payment['hostel_type'] . ') ') ?></td>
                                </tr>
                                <tr>
                                    <th>Floor Number:</th>
                                    <td><?= htmlspecialchars($payment['floor_name'] . ' (' . $payment['floor_number'] . ') ') ?></td>
                                </tr>

                                <tr>
                                    <th>Room:</th>
                                    <td><?= htmlspecialchars($payment['room_number'] . ' (' . $payment['room_type'] . ')') ?></td>
                                </tr>
                                <tr>
                                    <th>Period:</th>
                                    <td><?= date('F Y', mktime(0, 0, 0, $payment['month'], 1, $payment['year'])) ?></td>
                                </tr>

                                <?php
                                    $today = new DateTime();

                                    $actual_due = $payment['amount_due'];
                                    
                                    $late_fee_applied = 0;
                                    if ($payment['is_late']) {
                                        $late_fee_applied = $payment['late_fee']; 
                                    }
                                ?>

                                <tr>
                                    <th>Amount Due:</th>
                                    <td>৳<?= number_format($actual_due, 2) ?></td>
                                </tr>

                                <tr>
                                    <th>Due Date:</th>
                                    <td><?= date('d M Y', strtotime($payment['due_date'])) ?></td>
                                </tr>

                                <tr>
                                    <th>Late Fee Applied:</th>
                                    <td>৳<?= number_format($late_fee_applied, 2) ?> </td> 
                                </tr>
                                
                                <tr>
                                    <th>Balance:</th>
                                    <td>৳<?= number_format($payment['balance'], 2) ?></td>
                                </tr>

                                <tr>
                                    <th>Amount Paid:</th>
                                    <td>৳<?= number_format($payment['amount_paid'], 2) ?></td>
                                </tr>

                                <tr>
                                    <th>Last Updated By:</th>
                                    <td>
                                        <?php if (!empty($payment['updated_by_firstname']) || !empty($payment['updated_by_last_name'])): ?>
                                            <?= htmlspecialchars(trim($payment['updated_by_firstname'] . ' ' . $payment['updated_by_last_name'])) ?>
                                        <?php else: ?>
                                            N/A
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Payment transactions -->
                <div class="col-md-6" style="max-height: 300px; overflow-y: auto">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5>Payment Transactions</h5>
                            <!-- Add Payment Button -->
                            <a href="#" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addPaymentModal" data-payment-id="<?= $payment['id'] ?>" data-student-id="<?= $student_id ?>">
                                <i class="bi bi-plus"></i> Add Payment
                            </a>

                            <!-- Add Payment Modal -->
                            <div class="modal fade" id="addPaymentModal" tabindex="-1" aria-labelledby="addPaymentModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <form id="addPaymentForm">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Add Payment</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <input type="hidden" name="payment_id" id="modal_payment_id">
                                                <input type="hidden" name="student_id" id="modal_student_id">

                                                <div class="mb-3">
                                                    <label for="amount" class="form-label">Amount</label>
                                                    <input type="number" name="amount" class="form-control" required>
                                                </div>

                                                <div class="row">
                                                    <!-- ✅ Month Field -->
                                                    <div class="mb-3 col-md-6">
                                                        <label for="month" class="form-label">Month</label>
                                                        <select name="month" class="form-select" required>
                                                            <option value="">Select Month</option>
                                                            <?php
                                                            foreach (range(1, 12) as $m):
                                                                $monthName = date('F', mktime(0, 0, 0, $m, 1));
                                                            ?>
                                                                <option value="<?= $m ?>"><?= $monthName ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>

                                                    <!-- ✅ Year Field -->
                                                    <div class="mb-3 col-md-6">
                                                        <label for="year" class="form-label">Year</label>
                                                        <select name="year" class="form-select" required>
                                                            <option value="">Select Year</option>
                                                            <?php
                                                            $currentYear = date('Y');
                                                            foreach (range($currentYear - 5, $currentYear + 2) as $year): // Adjust range as needed
                                                            ?>
                                                                <option value="<?= $year ?>"><?= $year ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="payment_method_id" class="form-label">Payment Method</label>
                                                    <select name="payment_method_id" class="form-select" required>
                                                        <option value="">Select method</option>
                                                        <?php
                                                        // You must fetch payment methods from DB
                                                        $methods = $conn->query("SELECT id, display_name FROM payment_methods WHERE active = 1");
                                                        while ($row = $methods->fetch_assoc()):
                                                        ?>
                                                            <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['display_name']) ?></option>
                                                        <?php endwhile; ?>
                                                    </select>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="verification_status" class="form-label">Verification Status</label>
                                                    <select name="verification_status" class="form-select" required>
                                                        <option value="">Select status</option>
                                                        <option value="pending">Pending</option>
                                                        <option value="verified">Verified</option>
                                                        <option value="rejected">Rejected</option>
                                                    </select>
                                                </div>

                                                <!-- Add Phone Number Field -->
                                                <div class="mb-3">
                                                    <label for="phone_number" class="form-label">Phone Number</label>
                                                    <input type="text" name="phone_number" id="phone_number" class="form-control" pattern="^\d{10,15}$" title="Enter a valid phone number">
                                                </div>


                                                <div class="mb-3">
                                                    <label for="reference_code" class="form-label">Reference Code (optional)</label>
                                                    <input type="text" name="reference_code" class="form-control">
                                                </div>

                                                <div class="mb-3">
                                                    <label for="notes" class="form-label">Notes</label>
                                                    <textarea name="notes" class="form-control" rows="3" style="resize: none;"></textarea>
                                                </div>
                                            </div>

                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-success">Submit</button>
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
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
                                                <th>Carried Over</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($transactions as $txn): ?>
                                                <tr>
                                                    <td><?= date('d M Y', strtotime($txn['payment_date'])) ?></td>
                                                    <td><?= htmlspecialchars($txn['name']) ?></td>
                                                    <td>৳<?= number_format($txn['amount'], 2) ?></td>
                                                    <td>
                                                        ৳<?= number_format($txn['o_p_balance_added'], 2) ?>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-<?=
                                                                                $txn['verification_status'] == 'verified' ? 'success' : ($txn['verification_status'] == 'pending' ? 'warning' : 'danger')
                                                                                ?>">
                                                            <?= ucfirst($txn['verification_status']) ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <a href="view_transaction.php?id=<?= $txn['id'] ?>" class="btn btn-sm btn-info" title="View">
                                                            <i class="bi bi-eye"></i>
                                                        </a>

                                                        <!-- View Receipt -->
                                                        <a href="<?= BASE_URL ?>/admin/sections/payments/receipt.php?payment_id=<?= $payment['id'] ?>&transaction_id=<?= $txn['id'] ?>"
                                                            class="btn btn-sm btn-outline-primary" title="View Receipt">
                                                            <i class="bi bi-receipt"></i>
                                                        </a>

                                                        <!-- Verify Transaction -->
                                                        <?php if ($txn['verification_status'] == 'pending'): ?>
                                                            <button
                                                                class="btn btn-sm btn-success verify-btn"
                                                                data-id="<?= $txn['id'] ?>"
                                                                data-action="verify"
                                                                title="Verify Payment Request">
                                                                <i class="bi bi-check-circle"></i>
                                                            </button>
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

            <!-- All payments summary for the student -->
            <div class="card mt-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5>All Payments for <span class="text-success shadow-sm px-2 ms-2"><?= htmlspecialchars($payment['first_name'] . ' ' . $payment['last_name']) ?></span></h5>
                    </div>
                    <div>
                        <form method="get" class="form-inline">
                            <input type="hidden" name="id" value="<?= $payment_id ?>">
                            <div class="input-group">
                                <select class="form-select" name="month">
                                    <option value="">All Months</option>
                                    <?php for ($m = 1; $m <= 12; $m++): ?>
                                        <option value="<?= $m ?>" <?= $selected_month == $m ? 'selected' : '' ?>>
                                            <?= date('F', mktime(0, 0, 0, $m, 1)) ?>
                                        </option>
                                    <?php endfor; ?>
                                </select>
                                <select class="form-select" name="year">
                                    <option value="">All Years</option>
                                    <?php
                                    $current_year = date('Y');
                                    for ($y = $current_year; $y >= $current_year - 5; $y--): ?>
                                        <option value="<?= $y ?>" <?= $selected_year == $y ? 'selected' : '' ?>>
                                            <?= $y ?>
                                        </option>
                                    <?php endfor; ?>
                                </select>
                                <button type="submit" class="btn btn-primary">Filter</button>
                                <?php if ($selected_month || $selected_year): ?>
                                    <a href="view.php?id=<?= $payment_id ?>" class="btn btn-outline-secondary">Clear</a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="card-body table-responsive" style="max-height: 300px; overflow-y: auto">
                    <table class="table table-striped table-sm">
                        <thead>
                            <tr>
                                <th>Period</th>
                                <th>Due Date</th>
                                <th>Room Fee</th>
                                <th>Late Fee</th>
                                <th>Is Late</th>
                                <th>Amount Due</th>
                                <th>L.F. Applied Date</th>
                                <th>Late Fee Paid</th>
                                <th>Amount Paid</th>
                                <th>Balance</th>
                                <th>Carried Over</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($all_payments as $p):
                                $dueDateObj = new DateTime($p['due_date']);
                                $lateFeeAppliedDateObj = $p['late_fee_applied_date'] ? new DateTime($p['late_fee_applied_date']) : new DateTime('9999-12-31');
                                $today = new DateTime();
                                $balance = $p['balance'];  // added later
                                $period = date('F Y', mktime(0, 0, 0, $p['month'], 1, $p['year']));

                                // Check if payment is late (only if due date is in the past)
                                $lateFeeApplies_for_status = in_array($p['payment_status'], ['unpaid', 'partial']) && $today > $dueDateObj && $today >= $lateFeeAppliedDateObj;

                                // Only consider late fee taken status if due date is past
                                $lateFeeApplies_for_is_late_fee_taken = ($today > $dueDateObj && $today >= $lateFeeAppliedDateObj);

                                $is_late_fee_taken = ($p['is_late_fee_taken'] && $p['is_late'] && $p['payment_status'] === 'paid') ? 'Yes' : '—';

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
                                <tr>
                                    <td><?= $period ?></td>
                                    <td><?= $dueDateObj->format('d M Y') ?></td>
                                    <!-- <td>৳<?= number_format($actualDue, 2) ?></td> -->
                                    <td>৳<?= number_format($p['room_fee_price'], 2) ?></td>
                                    <td>
                                        <?php if ($p['late_fee'] > 0 && $p['is_late']): ?>
                                            ৳<?= number_format($p['late_fee'], 2) ?>
                                        <?php else: ?>
                                            —
                                        <?php endif; ?>
                                    </td>
                                    <td><?= $p['is_late'] ? 'Yes' : 'No' ?></td>
                                    <td>৳<?= number_format($p['amount_due'], 2) ?></td>
                                    <td><?= $lateFeeAppliedDateObj->format('d M Y') ?></td>
                                    <td><?=
                                        $is_late_fee_taken
                                        ?></td>
                                    <td>৳<?= number_format($p['amount_paid'], 2) ?></td>
                                    <td>৳<?= number_format($balance, 2) ?></td>
                                    <td>৳<?= number_format($p['o_p_balance_added'], 2) ?></td>
                                    <td>
                                        <span class="payment-status <?= $statusClass ?>">
                                            <?= ucfirst($p['payment_status']) ?>
                                            <?= $lateFeeApplies_for_status ? ' (Late)' : '' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-1">
                                            <!-- View Payment (Eye Icon) -->
                                            <a href="<?= BASE_URL ?>/admin/sections/payments/view.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-info" title="View Payment Details">
                                                <i class="bi bi-eye"></i>
                                            </a>


                                            <!-- Edit Payment -->
                                            <button class="btn btn-sm btn-warning edit-payment" title="Edit Payment" data-id="<?= $p['id'] ?>">
                                                <i class="bi bi-pencil"></i>
                                            </button>

                                            <!-- Delete Payment -->
                                            <button class="btn btn-sm btn-danger delete-payment" title="Delete Payment" data-id="<?= $p['id'] ?>">
                                                <i class="bi bi-trash"></i>
                                            </button>

                                            <!-- View Receipt -->
                                            <a href="<?= BASE_URL ?>/admin/sections/payments/monthly_payment_report.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-outline-primary" title="Monthly Payment Report">
                                                <i class="bi bi-receipt"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

            </div>

        </main>
    </div>
</div>


<!-- Edit Payment Modal -->
<div class="modal fade" id="editPaymentModal" tabindex="-1" aria-labelledby="editPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editPaymentModalLabel">Edit Payment Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editPaymentForm" style="max-height: 60vh; overflow-y: auto;">
                <div class="modal-body">
                    <input type="hidden" name="payment_id" id="edit_payment_id">

                    <div class="mb-3">
                        <label for="edit_amount_due" class="form-label">Amount Due</label>
                        <input type="number" class="form-control" id="edit_amount_due" name="amount_due" step="0.01" min="0" required>
                    </div>

                    <div class="mb-3">
                        <label for="edit_amount_paid" class="form-label">Amount Paid</label>
                        <input type="number" class="form-control" id="edit_amount_paid" name="amount_paid" step="0.01" min="0" required>
                    </div>

                    <div class="mb-3">
                        <label for="edit_balance" class="form-label">Balance</label>
                        <input type="number" class="form-control" id="edit_balance" name="balance" step="0.01" min="0" required>
                    </div>

                    <div class="mb-3">
                        <label for="edit_op_balance" class="form-label">O/P Balance Added</label>
                        <input type="number" class="form-control" id="edit_op_balance" name="o_p_balance_added" step="0.01" min="0">
                    </div>

                    <div class="mb-3">
                        <label for="edit_late_fee" class="form-label">Late Fee</label>
                        <input type="number" class="form-control" id="edit_late_fee" name="late_fee" step="0.01" min="0">
                    </div>

                    <div class="mb-3">
                        <label for="edit_due_date" class="form-label">Due Date</label>
                        <input type="date" class="form-control" id="edit_due_date" name="due_date" required>
                    </div>

                    <div class="mb-3">
                        <label for="edit_late_fee_applied_date" class="form-label">Late Fee Apply Date</label>
                        <input type="date" class="form-control" id="edit_late_fee_applied_date" name="late_fee_applied_date">
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="edit_is_late_fee_taken" name="is_late_fee_taken">
                        <label class="form-check-label" for="edit_is_late_fee_taken">Late Fee Taken</label>
                    </div>

                    <div class="mb-3">
                        <label for="edit_payment_status" class="form-label">Payment Status</label>
                        <select class="form-select" id="edit_payment_status" name="payment_status" required>
                            <option value="paid">Paid</option>
                            <option value="unpaid">Unpaid</option>
                            <option value="partial">Partial</option>
                            <option value="late">Late</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="edit_month" class="form-label">Month</label>
                        <select id="edit_month" name="month" class="form-select" required>
                            <option value="">Select Month</option>
                            <?php
                            $months = [
                                1 => 'January',
                                2 => 'February',
                                3 => 'March',
                                4 => 'April',
                                5 => 'May',
                                6 => 'June',
                                7 => 'July',
                                8 => 'August',
                                9 => 'September',
                                10 => 'October',
                                11 => 'November',
                                12 => 'December'
                            ];
                            foreach ($months as $num => $name) {
                                echo "<option value=\"$num\">$name</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="edit_year" class="form-label">Year</label>
                        <input type="number" class="form-control" id="edit_year" name="year" min="2000" max="2100" required>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>




<script>
    $(document).ready(function() {
        // Handle edit button click
        $('.edit-payment').click(function() {
            const paymentId = $(this).data('id');

            // Fetch payment details via AJAX
            $.ajax({
                url: '<?= BASE_URL ?>/admin/php_files/sections/payments/get_payment_details.php',
                type: 'GET',
                data: {
                    id: paymentId
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        const data = response.payment; // updated key

                        // Populate the modal with payment data
                        $('#edit_payment_id').val(data.id);
                        $('#edit_amount_due').val(data.amount_due);
                        $('#edit_amount_paid').val(data.amount_paid);
                        $('#edit_balance').val(data.balance);
                        $('#edit_op_balance').val(data.o_p_balance_added);
                        $('#edit_late_fee').val(data.late_fee);
                        $('#edit_due_date').val(data.due_date);
                        $('#edit_late_fee_applied_date').val(data.late_fee_applied_date);
                        $('#edit_is_late_fee_taken').prop('checked', data.is_late_fee_taken);
                        $('#edit_payment_status').val(data.payment_status);
                        $('#edit_month').val(data.month);
                        $('#edit_year').val(data.year);

                        // Show the modal
                        $('#editPaymentModal').modal('show');
                    } else {
                        showSlideMessage(response.message || 'Failed to load payment details', 'danger');
                    }
                },
                error: function() {
                    showSlideMessage('Error fetching payment details', 'danger');
                }
            });
        });



        // Handle delete button click
        $('.delete-payment').click(function() {
            const paymentId = $(this).data('id');
            const $button = $(this); // Store reference to the clicked button
            const $row = $button.closest('tr'); // Store reference to the row

            // Confirmation prompt
            const confirmDelete = confirm('Are you sure you want to delete this payment?');

            if (!confirmDelete) return; // Cancel if user clicked "Cancel"

            // Proceed with deletion
            $.ajax({
                url: '<?= BASE_URL . '/admin/php_files/sections/payments/single_payment_delete.php' ?>',
                type: 'POST',
                data: {
                    id: paymentId
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        showSlideMessage(response.message, 'success');

                        // Smooth fade out, then remove the row
                        $row.fadeOut(300, function() {
                            $(this).remove();
                            location.reload(); // Reload the page to reflect changes
                        });
                    } else {
                        showSlideMessage(response.message || 'Failed to delete payment', 'danger');
                    }
                },
                error: function() {
                    showSlideMessage('Error deleting payment', 'danger');
                }
            });
        });


        // Handle form submission
        $('#editPaymentForm').submit(function(e) {
            e.preventDefault();

            const formData = $(this).serialize();
            const $submitBtn = $(this).find('button[type="submit"]');
            $submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...');

            $.ajax({
                url: '<?= BASE_URL ?>/admin/php_files/sections/payments/update_payment.php',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        showSlideMessage(response.message, 'success');
                        $('#editPaymentModal').modal('hide');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showSlideMessage(response.message || 'Failed to update payment', 'danger');
                        $submitBtn.prop('disabled', false).text('Save Changes');
                    }
                },
                error: function() {
                    showSlideMessage('Error updating payment', 'danger');
                    $submitBtn.prop('disabled', false).text('Save Changes');
                }
            });
        });


        // Add payment
        // Set payment ID and student ID in modal
        $('#addPaymentModal').on('show.bs.modal', function(event) {
            const button = $(event.relatedTarget);
            const paymentId = button.data('payment-id');
            const studentId = button.data('student-id'); // ✅ get student ID

            $('#modal_payment_id').val(paymentId);
            $('#modal_student_id').val(studentId); // ✅ set student ID
        });


        // Handle form submission
        $('#addPaymentForm').on('submit', function(e) {
            e.preventDefault();
            const formData = $(this).serialize();

            $.ajax({
                url: '<?= BASE_URL ?>/admin/php_files/sections/payments/add_transaction.php',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(res) {
                    if (res.success) {
                        showSlideMessage(res.message, 'success');
                        setTimeout(function() {
                            location.reload(); // Optional: Refresh the page or update part of it
                        }, 1500);
                    } else {
                        showSlideMessage(res.message || 'Failed to add Payment', 'danger');
                    }
                },
                error: function() {
                    showSlideMessage('Server error occured.', 'danger');
                }
            });
        });


        $('.verify-btn').on('click', function() {
            const txnId = $(this).data('id');
            const action = $(this).data('action');

            $.ajax({
                url: '<?= BASE_URL . '/admin/php_files/sections/payments/verify_transaction.php' ?>',
                type: 'POST',
                data: {
                    txn_id: txnId,
                    action
                },
                success: function(response) {
                    if (response.success) {
                        showSlideMessage(response.message, 'success');
                        setTimeout(() => {
                            location.reload(); // Reload the page to reflect changes
                        }, 1500);
                    } else {
                        showSlideMessage(response.message || 'Failed to verify Payment', 'danger');
                    }
                },
                error: function() {
                    showSlideMessage('Server error. Please try again.', 'error');
                }
            });
        });

    });
</script>



<?php require_once BASE_PATH . '/admin/includes/footer_admin.php'; ?>