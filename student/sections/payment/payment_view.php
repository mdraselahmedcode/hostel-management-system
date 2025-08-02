<?php
require_once __DIR__ . '/../../../config/config.php';
include BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/config/auth.php';
require_once BASE_PATH . '/student/sections/payment/helpers.php';
include BASE_PATH . '/includes/slide_message.php';
require_once BASE_PATH . '/admin/php_files/sections/payments/updateLateStatus.php';

if (!is_student_logged_in()) {
    header('Location: ' . BASE_URL . '/student/login.php');
    exit;
}




$student_id = $_SESSION['student']['id'];
$current_year = date('Y');
$selected_year = isset($_GET['year']) ? (int)$_GET['year'] : $current_year;

//  update is late status
updateLateStatus($student_id);

// Get last verified payment transaction for the student
$stmt = $conn->prepare("
    SELECT pt.amount, pt.payment_date
    FROM payment_transactions pt
    JOIN student_payments sp ON pt.payment_id = sp.id
    WHERE sp.student_id = ? AND pt.verification_status = 'verified'
    ORDER BY pt.payment_date DESC
    LIMIT 1
");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$last_payment_result = $stmt->get_result();
$last_payment = $last_payment_result->fetch_assoc();
$stmt->close();



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


$stmt = $conn->prepare("
    SELECT 
        sp.*, 
        DATE_FORMAT(sp.due_date, '%M %Y') AS billing_period,
        sp.amount_paid,
        rf.price AS room_fee,
        rf.billing_cycle,
        s.id AS student_id,
        s.first_name AS first_name,
        s.last_name AS last_name,
        s.contact_number
    FROM student_payments sp
    JOIN room_fees rf ON sp.room_fee_id = rf.id
    JOIN students s ON sp.student_id = s.id
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
$late_fee_paid = 0;



while ($payment = $payment_result->fetch_assoc()) {
    $amount_paid = (float) $payment['amount_paid'];
    $payments[] = $payment;

    $student_name = $payment['first_name'] . ' ' . $payment['last_name'];
    $student_mobile = $payment['contact_number'];

    $total_due += $payment['amount_due'];
    $total_paid += $amount_paid;
    // $total_outstanding += ($payment['amount_due'] - $amount_paid);
    $total_outstanding += $payment['balance'];
    // $total_late_fee += $payment['late_fee'];
    $total_late_fee += ($payment['late_fee'] > 0 && $payment['is_late'] == 1) ? $payment['late_fee'] : 0;
    $late_fee_paid += ($payment['late_fee'] > 0 && $payment['is_late'] == 1 && $payment['is_late_fee_taken']) ? $payment['late_fee'] : 0;
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
                                    <?php if ($last_payment): ?>
                                        <?= date('M j, Y', strtotime($last_payment['payment_date'])) ?>
                                        <span class="badge bg-success"><?= formatCurrency($last_payment['amount']) ?> paid</span>
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
                            <div class="d-flex align-items-center justify-content-between">
                                <h3 class="card-text"><?= formatCurrency($total_late_fee) ?></h3>
                                <muted>Paid: <?= number_format((float)$late_fee_paid, 2) ?></muted>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Table -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive" style="max-height: 250px; overflow-y: auto;">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Billing Period</th>
                                    <th>Due Date</th>
                                    <th>L.F. Applied Date</th>
                                    <th>Room Fee</th>
                                    <th>Late Fee</th>
                                    <th>Amount Due</th>
                                    <th>Amount Paid</th>
                                    <th>Balance</th>
                                    <th>Carried Over</th>
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
                                        $balance = $payment['balance'];
                                        $is_overdue = $payment['payment_status'] === 'unpaid' && strtotime($payment['due_date']) < time();

                                        // Row color logic
                                        $row_class = 'table-default';
                                        if ($payment['payment_status'] === 'paid') {
                                            $row_class = 'table-success';
                                        } elseif ($payment['payment_status'] === 'partial') {
                                            $row_class = 'table-info';
                                        } elseif ($payment['payment_status'] === 'unpaid' && $is_overdue) {
                                            $row_class = 'table-danger';
                                        }
                                    ?>

                                        <tr class="<?= $row_class ?>">
                                            <td><?= $payment['billing_period'] ?></td>
                                            <td><?= date('M j, Y', strtotime($payment['due_date'])) ?></td>
                                            <td><?= date('M j, Y', strtotime($payment['late_fee_applied_date'])) ?></td>
                                            <td><?= number_format((float)$payment['room_fee'], 2) ?></td>
                                            <td>
                                                <?php
                                                if ($payment['late_fee'] > 0 && $payment['is_late'] == 1) {
                                                    echo $payment['late_fee'];
                                                } else {
                                                    echo '—';
                                                }
                                                ?>
                                            </td>
                                            <td><?= formatCurrency($payment['amount_due']) ?></td>
                                            <td><?= formatCurrency($payment['amount_paid']) ?></td>
                                            <td><?= formatCurrency($balance) ?></td>
                                            <td><?= formatCurrency($payment['o_p_balance_added']) ?></td>
                                            <td>
                                                <span class="badge bg-<?= getStatusBadge($payment['payment_status']) ?>">
                                                    <?= ucfirst($payment['payment_status']) ?>
                                                </span>
                                                <?php if ($payment['late_fee'] > 0): ?>
                                                    <?php
                                                    $text_color = $payment['is_late_fee_taken'] ? 'success shadow-sm' : 'danger';
                                                    ?>
                                                    <small class="text-<?= $text_color ?> ">
                                                        <?= $payment['is_late'] ? formatCurrency($payment['late_fee']) . ' late fee' : '' ?>
                                                    </small>
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
                                                <!-- View Receipt -->
                                                <a href="<?= BASE_URL ?>/admin/sections/payments/monthly_payment_report.php?id=<?= $payment['id'] ?>"
                                                    class="btn btn-sm btn-outline-primary" title="View Monthly Report">
                                                    <i class="bi bi-receipt"></i>
                                                </a>
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
        <div class="modal-content" style="max-height: 700px; overflow-y: auto;">
            <div class="modal-header">
                <h5 class="modal-title">Make Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="paymentForm" method="post" enctype="multipart/form-data">
                <input type="hidden" name="payment_id" id="payment_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="amount" class="form-label">Amount to Pay</label>
                        <div class="input-group">
                            <span class="input-group-text"><?= CURRENCY_SYMBOL ?></span>
                            <input type="number" class="form-control" id="amount" name="amount" min="0" step="0.01">
                        </div>
                        <small class="text-muted">Outstanding balance: <span id="outstanding_balance"></span></small>
                    </div>

                    <div class="mb-3">
                        <label for="payment_method" class="form-label">Payment Method</label>
                        <select class="form-select" id="payment_method" name="payment_method_id" required onchange="updateAccountHint(this)">
                            <option value="" data-account="">Select method</option>
                            <?php
                            $methods = $conn->query("SELECT id, display_name, account_number FROM payment_methods WHERE active = 1 AND name != 'cash'");
                            while ($method = $methods->fetch_assoc()):
                            ?>
                                <option value="<?= $method['id'] ?>" data-account="<?= htmlspecialchars($method['account_number']) ?>">
                                    <?= htmlspecialchars($method['display_name']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                        <small id="accountHint" class="text-muted d-block mt-1"></small>
                    </div>

                    <div class="mb-3">
                        <label for="reference_code" class="form-label">Reference Code</label>
                        <input type="text" class="form-control" id="reference_code" name="reference_code" required>
                    </div>

                    <div class="mb-3">
                        <label for="transaction_id" class="form-label">Transaction ID (if available)</label>
                        <input type="text" class="form-control" id="transaction_id" name="transaction_id">
                    </div>

                    <div class="mb-3">
                        <label for="sender_name" class="form-label">Sender Name</label>
                        <input type="text" class="form-control" id="sender_name" name="sender_name" value="<?= htmlspecialchars($student_name) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="sender_mobile" class="form-label">Sender Mobile Number</label>
                        <input type="text" class="form-control" id="sender_mobile" name="sender_mobile" value="<?= $student_mobile ?>" required pattern="\d{10,15}">
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
    function updateAccountHint(selectEl) {
        const selectedOption = selectEl.options[selectEl.selectedIndex];
        const accountInfo = selectedOption.getAttribute('data-account');
        const hint = document.getElementById('accountHint');
        hint.textContent = accountInfo ? `Send to: ${accountInfo}` : '';
    }
</script>


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
            const balance = parseFloat($(this).data('balance')) || 0;

            // // Reset form
            $('#paymentForm')[0].reset();

            $('#payment_id').val(paymentId);
            $('#amount').val(balance.toFixed(2));
            $('#amount').attr('max', balance.toFixed(2));
            $('#outstanding_balance').text('<?= CURRENCY_SYMBOL ?>' + balance);

            $('#makePaymentModal').modal('show');
        });

        // Payment form validation and AJAX submission
        $('#paymentForm').submit(function(e) {
            e.preventDefault();

            const formData = {
                payment_id: $('#payment_id').val(),
                amount: $('#amount').val(),
                payment_method_id: $('#payment_method').val(),
                reference_code: $('#reference_code').val(),
                transaction_id: $('#transaction_id').val(),
                payment_notes: $('#payment_notes').val(),
                sender_mobile: $('#sender_mobile').val(),
                sender_name: $('#sender_name').val()
            };

            const amount = parseFloat(formData.amount);
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
                url: '<?= BASE_URL ?>/student/php_files/sections/payment/process_payment.php',
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