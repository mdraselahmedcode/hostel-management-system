<?php
require_once __DIR__ . '/../../../config/config.php';
include BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/config/auth.php';
require_once BASE_PATH . '/student/sections/payment/helpers.php';
include BASE_PATH . '/includes/slide_message.php';

if (!is_student_logged_in()) {
    header('Location: ' . BASE_URL . '/student/login.php');
    exit;
}

$student_id = $_SESSION['student']['id'];
$current_year = date('Y');
$selected_year = isset($_GET['year']) ? (int)$_GET['year'] : $current_year;

// Get student info with hostel and room details
$stmt = $conn->prepare("
    SELECT s.*, h.hostel_name, r.room_number, rt.type_name AS room_type
    FROM students s
    LEFT JOIN hostels h ON s.hostel_id = h.id
    LEFT JOIN rooms r ON s.room_id = r.id
    LEFT JOIN room_types rt ON r.room_type_id = rt.id
    WHERE s.id = ?
");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();
$stmt->close();

if (!$student || !$student['is_checked_in']) {
    die("No active hostel assignment found");
}

// Get current room fee based on room type and hostel
$stmt = $conn->prepare("
    SELECT rf.* 
    FROM room_fees rf
    JOIN rooms r ON rf.room_type_id = r.room_type_id AND rf.hostel_id = r.hostel_id
    WHERE r.id = ?
    AND rf.effective_from <= ?
    ORDER BY rf.effective_from DESC
    LIMIT 1
");
$today = date('Y-m-d');
$stmt->bind_param("is", $student['room_id'], $today);
$stmt->execute();
$room_fee_result = $stmt->get_result();
$room_fee = $room_fee_result->fetch_assoc();
$stmt->close();

// Get payment history with transaction sums
$stmt = $conn->prepare("
    SELECT sp.*, 
        DATE_FORMAT(sp.due_date, '%M %Y') AS billing_period,
        (SELECT IFNULL(SUM(pt.amount), 0) 
         FROM payment_transactions pt 
         WHERE pt.payment_id = sp.id AND pt.verification_status = 'verified') AS amount_paid
    FROM student_payments sp
    WHERE sp.student_id = ? AND sp.year = ?
    ORDER BY sp.year DESC, sp.month DESC
");
$stmt->bind_param("ii", $student_id, $selected_year);
$stmt->execute();
$payment_result = $stmt->get_result();

$payments = [];
$total_due = 0;
$total_paid = 0;
$total_outstanding = 0;
$total_late_fee = 0;

while ($payment = $payment_result->fetch_assoc()) {
    $amount_paid = (float) $payment['amount_paid'];
    $payments[] = $payment;

    $total_due += $payment['amount_due'];
    $total_paid += $amount_paid;
    $total_outstanding += ($payment['amount_due'] - $amount_paid);
    $total_late_fee += $payment['late_fee'];
}
$stmt->close();

$page_title = "Payment History";

include BASE_PATH . '/student/includes/header_student.php';
?>





<div class="content container-fluid">
    <div class="row full-height">
        <?php include BASE_PATH . '/student/includes/sidebar_student.php' ?>

        <!-- Main Content Column -->
        <div class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <!-- Back Button -->
            <div class="mb-4 mt-3">
                <button class="btn btn-outline-secondary d-md-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu">
                    <i class="bi bi-list"></i> Menu
                </button>
                <a href="javascript:history.back()" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back
                </a>
            </div>

            <div class="row mb-4">
                <div class="col-md-8">
                    <h2 class="mb-0"><i class="fas fa-money-bill-wave me-2"></i>Payment History</h2>
                    <p class="mb-0 text-muted">
                        <?php if ($student['hostel_name']): ?>
                            Hostel: <?= htmlspecialchars($student['hostel_name']) ?> |
                            Room: <?= htmlspecialchars($student['room_number']) ?> (<?= htmlspecialchars($student['room_type']) ?>)
                        <?php else: ?>
                            No current hostel assignment
                        <?php endif; ?>
                    </p>
                </div>
                <div class="col-md-4 text-end">
                    <form method="get" class="d-inline-block">
                        <div class="input-group">
                            <select name="year" class="form-select" onchange="this.form.submit()">
                                <?php for ($year = $current_year; $year >= ($current_year - 5); $year--): ?>
                                    <option value="<?= $year ?>" <?= $year == $selected_year ? 'selected' : '' ?>>
                                        <?= $year ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Current Fee Information -->
            <?php if ($room_fee): ?>
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <h5>Current Fee Rate</h5>
                                <h3><?= formatCurrency($room_fee['price']) ?></h3>
                                <small class="text-muted"><?= ucfirst($room_fee['billing_cycle']) ?> billing</small>
                            </div>
                            <div class="col-md-4">
                                <h5>Check-In Date</h5>
                                <p><?= $student['check_in_at'] ? date('M j, Y', strtotime($student['check_in_at'])) : 'Not checked in' ?></p>
                            </div>
                            <div class="col-md-4">
                                <h5>Last Payment</h5>
                                <p>
                                    <?php if ($total_paid > 0): ?>
                                        <?= date('M j, Y') ?>
                                        <span class="badge bg-success"><?= formatCurrency($total_paid) ?> paid</span>
                                    <?php else: ?>
                                        No payments yet
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Summary Cards -->
            <div class="row mb-4">
                <div class="col-md-3 mb-3">
                    <div class="card bg-primary text-white h-100">
                        <div class="card-body">
                            <h5 class="card-title">Total Due</h5>
                            <h3 class="card-text"><?= formatCurrency($total_due) ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card bg-success text-white h-100">
                        <div class="card-body">
                            <h5 class="card-title">Total Paid</h5>
                            <h3 class="card-text"><?= formatCurrency($total_paid) ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card bg-danger text-white h-100">
                        <div class="card-body">
                            <h5 class="card-title">Outstanding</h5>
                            <h3 class="card-text"><?= formatCurrency($total_outstanding) ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card bg-warning text-dark h-100">
                        <div class="card-body">
                            <h5 class="card-title">Late Fees</h5>
                            <h3 class="card-text"><?= formatCurrency($total_late_fee) ?></h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Table -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Billing Period</th>
                                    <th>Due Date</th>
                                    <th>Amount Due</th>
                                    <th>Amount Paid</th>
                                    <th>Balance</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($payments)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-4">No payment records found for selected year</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($payments as $payment):
                                        $balance = $payment['amount_due'] - $payment['amount_paid'];
                                        $is_overdue = $payment['payment_status'] == 'late' ||
                                            ($payment['payment_status'] == 'unpaid' && strtotime($payment['due_date']) < time());
                                    ?>
                                        <tr class="<?= $is_overdue ? 'table-danger' : '' ?>">
                                            <td><?= $payment['billing_period'] ?></td>
                                            <td><?= date('M j, Y', strtotime($payment['due_date'])) ?></td>
                                            <td><?= formatCurrency($payment['amount_due']) ?></td>
                                            <td><?= formatCurrency($payment['amount_paid']) ?></td>
                                            <td><?= formatCurrency($balance) ?></td>
                                            <td>
                                                <span class="badge bg-<?= getStatusBadge($payment['payment_status']) ?>">
                                                    <?= ucfirst($payment['payment_status']) ?>
                                                </span>
                                                <?php if ($payment['late_fee'] > 0): ?>
                                                    <small class="text-danger">(+<?= formatCurrency($payment['late_fee']) ?> late fee)</small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($balance > 0): ?>
                                                    <button class="btn btn-sm btn-primary make-payment"
                                                        data-payment-id="<?= $payment['id'] ?>"
                                                        data-balance="<?= $balance ?>">
                                                        <i class="fas fa-credit-card me-1"></i> Pay Now
                                                    </button>
                                                <?php endif; ?>
                                                <button class="btn btn-sm btn-outline-secondary view-details"
                                                    data-payment-id="<?= $payment['id'] ?>">
                                                    <i class="fas fa-eye me-1"></i> Details
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payment History Modal -->
<div class="modal fade" id="paymentHistoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Payment Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="paymentDetailsContent">
                <!-- Content loaded via AJAX -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Make Payment Modal -->
<div class="modal fade" id="makePaymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Make Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="paymentForm" method="post">
                <input type="hidden" name="payment_id" id="payment_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="payment_amount" class="form-label">Amount to Pay</label>
                        <div class="input-group">
                            <span class="input-group-text"><?= CURRENCY_SYMBOL ?></span>
                            <input type="number" class="form-control" id="payment_amount" name="payment_amount"
                                min="0" step="0.01" required>
                        </div>
                        <small class="text-muted">Outstanding balance: <span id="outstanding_balance"></span></small>
                    </div>
                    <div class="mb-3">
                        <label for="payment_method" class="form-label">Payment Method</label>
                        <select class="form-select" id="payment_method" name="payment_method" required>
                            <option value="">Select method</option>
                            <?php
                            // Fetch active payment methods from database
                            $methods = $conn->query("SELECT id, display_name FROM payment_methods WHERE active = 1");
                            while ($method = $methods->fetch_assoc()): ?>
                                <option value="<?= $method['id'] ?>"><?= htmlspecialchars($method['display_name']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="transaction_id" class="form-label">Transaction ID (if any)</label>
                        <input type="text" class="form-control" id="transaction_id" name="transaction_id">
                    </div>
                    <div class="mb-3">
                        <label for="payment_notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="payment_notes" name="payment_notes" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Submit Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // View payment details - updated to show transaction history
        $('.view-details').click(function() {
            const paymentId = $(this).data('payment-id');
            $.ajax({
                url: '<?= BASE_URL ?>/student/sections/payment/get_payment_details.php',
                type: 'GET',
                data: {
                    payment_id: paymentId
                },
                success: function(response) {
                    $('#paymentDetailsContent').html(response);
                    $('#paymentHistoryModal').modal('show');
                },
                error: function() {
                    showSlideMessage('Error loading payment details', 'danger');
                }
            });
        });

        // Make payment - updated to include payment method validation
        $('.make-payment').click(function() {
            const paymentId = $(this).data('payment-id');
            const balance = $(this).data('balance');

            $('#payment_id').val(paymentId);
            $('#payment_amount').val(balance.toFixed(2));
            $('#payment_amount').attr('max', balance);
            $('#outstanding_balance').text('<?= CURRENCY_SYMBOL ?>' + balance.toFixed(2));

            // Reset form
            $('#paymentForm')[0].reset();
            $('#makePaymentModal').modal('show');
        });

        // Payment form validation and AJAX submission
        $('#paymentForm').submit(function(e) {
            e.preventDefault();

            const formData = {
                payment_id: $('#payment_id').val(),
                payment_amount: $('#payment_amount').val(),
                payment_method_id: $('#payment_method').val(),
                transaction_id: $('#transaction_id').val(),
                payment_notes: $('#payment_notes').val(),
                sender_mobile: $('#sender_mobile').val(),
                sender_name: $('#sender_name').val()
            };

            const amount = parseFloat(formData.payment_amount);
            const balance = parseFloat($('#outstanding_balance').text().replace(/[^0-9.-]+/g, ""));

            if (amount > balance) {
                showSlideMessage('Payment amount cannot exceed outstanding balance', 'danger');
                return false;
            }

            if (amount <= 0) {
                showSlideMessage('Payment amount must be greater than zero', 'danger');
                return false;
            }

            if (!formData.payment_method_id) {
                showSlideMessage('Please select a payment method', 'danger');
                return false;
            }

            // For mobile payments, validate sender info
            const paymentMethod = $('#payment_method option:selected').text().toLowerCase();
            if ((paymentMethod.includes('bkash') || paymentMethod.includes('nagad') || paymentMethod.includes('rocket')) && 
                (!formData.sender_mobile || !formData.sender_name)) {
                showSlideMessage('Please provide sender mobile number and name for mobile payments', 'danger');
                return false;
            }

            $.ajax({
                url: '<?= BASE_URL ?>/student/sections/payment/process_payment.php',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('#makePaymentModal').modal('hide');
                        showSlideMessage(response.message || 'Payment submitted successfully!', 'success');
                        setTimeout(() => location.reload(), 2000);
                    } else {
                        showSlideMessage(response.message || 'An error occurred while processing your payment.', 'danger');
                    }
                },
                error: function(xhr) {
                    let errorMsg = 'Something went wrong.';
                    try {
                        const res = JSON.parse(xhr.responseText);
                        if (res.message) errorMsg = res.message;
                    } catch (e) {}
                    showSlideMessage(errorMsg, 'danger');
                }
            });
        });

        // Show/hide mobile payment fields based on selection
        $('#payment_method').change(function() {
            const method = $(this).find('option:selected').text().toLowerCase();
            if (method.includes('bkash') || method.includes('nagad') || method.includes('rocket')) {
                $('#mobilePaymentFields').show();
            } else {
                $('#mobilePaymentFields').hide();
            }
        });
    });
</script>

<?php require_once BASE_PATH . '/student/includes/footer_student.php'; ?>