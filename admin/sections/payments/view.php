<?php
require_once __DIR__ . '/../../../config/config.php';
include BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/config/auth.php';
include BASE_PATH . '/includes/slide_message.php';

require_admin();

$payment_id = $_GET['id'] ?? 0;
$selected_month = $_GET['month'] ?? null; // Initialize month filter
$selected_year = $_GET['year'] ?? null;   // Initialize year filter

// Get current payment details
$payment_query = "SELECT sp.*, s.first_name, s.last_name, s.varsity_id, s.contact_number, 
                  h.hostel_name, r.room_number, rt.type_name as room_type,
                  a.firstname AS updated_by_firstname, a.lastname AS updated_by_last_name
                  FROM student_payments sp
                  JOIN students s ON sp.student_id = s.id
                  JOIN hostels h ON sp.hostel_id = h.id
                  JOIN rooms r ON sp.room_id = r.id
                  JOIN room_types rt ON sp.room_type_id = rt.id
                  LEFT JOIN admins a ON sp.updated_by = a.id
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
require_once BASE_PATH . '/admin/includes/header_admin.php';
?>

<head>
    <style>
        /* Add this to your payment_index.css or in a style tag */
        .btn-sm.edit-payment {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
            line-height: 1.5;
            border-radius: 0.2rem;
        }

        /* Modal styling */
        #editPaymentModal .modal-body {
            padding: 1.5rem;
        }

        #editPaymentModal .form-control,
        #editPaymentModal .form-select {
            margin-bottom: 1rem;
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
                    <a href="<?= BASE_URL ?>/admin/sections/payments/index.php" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Payments
                    </a>
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
                                                                                $txn['verification_status'] == 'verified' ? 'success' : ($txn['verification_status'] == 'pending' ? 'warning' : 'danger')
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

            <!-- All payments summary for the student -->
            <div class="card mt-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5>All Payments for <?= htmlspecialchars($payment['first_name'] . ' ' . $payment['last_name']) ?></h5>
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
                <div class="card-body table-responsive">
                    <table class="table table-striped table-sm">
                        <thead>
                            <tr>
                                <th>Period</th>
                                <th>Due Date</th>
                                <th>Amount Due</th>
                                <th>Late Fee</th>
                                <th>Amount Paid</th>
                                <th>Balance</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($all_payments as $p):
                                $dueDateObj = new DateTime($p['due_date']);
                                $lateFeeAppliedDateObj = $p['late_fee_applied_date'] ? new DateTime($p['late_fee_applied_date']) : new DateTime('9999-12-31');
                                $today = new DateTime();

                                $lateFeeApplies = in_array($p['payment_status'], ['unpaid', 'partial']) && $today > $dueDateObj && $today >= $lateFeeAppliedDateObj;
                                $actualDue = $p['amount_due'] + ($lateFeeApplies ? $p['late_fee'] : 0);
                                $balance = $actualDue - $p['amount_paid'];
                                $statusClass = 'status-' . $p['payment_status'];
                                $period = date('F Y', mktime(0, 0, 0, $p['month'], 1, $p['year']));
                            ?>
                                <tr>
                                    <td><?= $period ?></td>
                                    <td><?= $dueDateObj->format('d M Y') ?></td>
                                    <td>৳<?= number_format($actualDue, 2) ?></td>
                                    <td>৳<?= number_format($p['late_fee'], 2) ?></td>
                                    <td>৳<?= number_format($p['amount_paid'], 2) ?></td>
                                    <td>৳<?= number_format($balance, 2) ?></td>
                                    <td><span class="payment-status <?= $statusClass ?>">
                                            <?= ucfirst($p['payment_status']) ?>
                                            <?= $lateFeeApplies ? ' (Late)' : '' ?>
                                        </span></td>
                                    <td>
                                        <a href="view.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-primary" title="View Details">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <button class="btn btn-sm btn-warning edit-payment" title="Edit Payment" data-id="<?= $p['id'] ?>">
                                            <i class="bi bi-pencil"></i>
                                        </button>
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
            <form id="editPaymentForm">
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
                        <label for="edit_late_fee" class="form-label">Late Fee</label>
                        <input type="number" class="form-control" id="edit_late_fee" name="late_fee" step="0.01" min="0">
                    </div>

                    <!-- ✅ Month Input Field -->
                    <div class="mb-3">
                        <label for="edit_month" class="form-label">Month</label>
                        <select id="edit_month" name="month" class="form-select">
                            <option value="1">January</option>
                            <option value="2">February</option>
                            <option value="3">March</option>
                            <option value="4">April</option>
                            <option value="5">May</option>
                            <option value="6">June</option>
                            <option value="7">July</option>
                            <option value="8">August</option>
                            <option value="9">September</option>
                            <option value="10">October</option>
                            <option value="11">November</option>
                            <option value="12">December</option>
                        </select>
                    </div>



                    <div class="mb-3">
                        <label for="edit_due_date" class="form-label">Due Date</label>
                        <input type="date" class="form-control" id="edit_due_date" name="due_date" required>
                    </div>

                    <div class="mb-3">
                        <label for="edit_late_fee_applied_date" class="form-label">Late Fee Apply Date</label>
                        <input type="date" class="form-control" id="edit_late_fee_applied_date" name="late_fee_applied_date">
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
                        // Populate the modal with payment data
                        $('#edit_payment_id').val(response.data.id);
                        $('#edit_amount_due').val(response.data.amount_due);
                        $('#edit_amount_paid').val(response.data.amount_paid);
                        $('#edit_late_fee').val(response.data.late_fee);
                        $('#edit_month').val(response.data.month);
                        $('#edit_due_date').val(response.data.due_date);
                        $('#edit_late_fee_applied_date').val(response.data.late_fee_applied_date);
                        $('#edit_payment_status').val(response.data.payment_status);

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
    });
</script>



<?php require_once BASE_PATH . '/admin/includes/footer_admin.php'; ?>