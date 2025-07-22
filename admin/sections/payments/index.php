<?php
require_once __DIR__ . '/../../../config/config.php';
include BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/config/auth.php';
include BASE_PATH . '/includes/slide_message.php';

require_admin();

// Get hostels for filter - MUST BE BEFORE FILTER VALIDATION
$hostels_result = $conn->query("SELECT id, hostel_name FROM hostels");
$hostels = $hostels_result->fetch_all(MYSQLI_ASSOC);
$hostels_result->free();

// Validate search input
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Validate filter inputs
$allowed_statuses = ['paid', 'unpaid', 'partial', 'late', ''];
$status = isset($_GET['status']) && in_array($_GET['status'], $allowed_statuses) ? $_GET['status'] : '';

$hostel = isset($_GET['hostel']) ? filter_var($_GET['hostel'], FILTER_VALIDATE_INT) : null;
$month = isset($_GET['month']) ? filter_var($_GET['month'], FILTER_VALIDATE_INT, [
    'options' => ['min_range' => 1, 'max_range' => 12]
]) : null;
$year = isset($_GET['year']) ? filter_var($_GET['year'], FILTER_VALIDATE_INT, [
    'options' => ['min_range' => 2020, 'max_range' => date('Y') + 1]
]) : null;

// Build the query with filters
$query = "SELECT sp.*, s.first_name, s.last_name, s.varsity_id, h.hostel_name, r.room_number 
          FROM student_payments sp
          JOIN students s ON sp.student_id = s.id
          JOIN hostels h ON sp.hostel_id = h.id
          JOIN rooms r ON sp.room_id = r.id";

$conditions = [];
$params = [];
$types = '';


// Add search condition
if (!empty($search)) {
    $conditions[] = "s.varsity_id LIKE ?";
    $params[] = '%' . $search . '%';
    $types .= 's';
}

// Add filter conditions based on validated parameters
if (!empty($status)) {
    $conditions[] = "sp.payment_status = ?";
    $params[] = $status;
    $types .= 's';
}

if (!empty($hostel)) {
    $conditions[] = "sp.hostel_id = ?";
    $params[] = $hostel;
    $types .= 'i';
}

if (!empty($month)) {
    $conditions[] = "sp.month = ?";
    $params[] = $month;
    $types .= 'i';
}

if (!empty($year)) {
    $conditions[] = "sp.year = ?";
    $params[] = $year;
    $types .= 'i';
}

// Combine conditions
if (!empty($conditions)) {
    $query .= " WHERE " . implode(" AND ", $conditions);
}

$query .= " ORDER BY sp.year DESC, sp.month DESC";

// Prepare and execute the query with filters
$stmt = $conn->prepare($query);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$payments_result = $stmt->get_result();
$payments = $payments_result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

require_once BASE_PATH . '/admin/includes/header_admin.php';
?>


<head>
    <link rel="stylesheet" href="<?= BASE_URL ?>/admin/assets/css/payment_index.css">
</head>

<div class="content container-fluid mt-5">
    <div class="row full-height">
        <!-- Sidebar -->
        <?php require_once BASE_PATH . '/admin/includes/sidebar_admin.php'; ?>

        <!-- Main Content -->
        <main class="col-md-10 ms-sm-auto px-md-4 py-4" style="overflow-y: auto; max-height: calc(100vh - 95px)">
            <a href="javascript:history.back()" class="btn btn-secondary mb-3">Back</a>

            <!-- Page Header -->
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Payment Management</h1>
                <!-- Add this below your page header -->
                <?php if (isset($_GET['status']) || isset($_GET['hostel']) || isset($_GET['month']) || isset($_GET['year']) || !empty($search)): ?>
                    <div class="alert alert-info mb-3">
                        <i class="bi bi-funnel"></i> Active Filters:
                        <?php
                        $activeFilters = [];
                        if (!empty($_GET['status'])) $activeFilters[] = "Status: " . ucfirst($_GET['status']);
                        if (!empty($_GET['hostel'])) {
                            $hostelName = array_column($hostels, 'hostel_name', 'id')[$_GET['hostel']] ?? 'Unknown';
                            $activeFilters[] = "Hostel: " . htmlspecialchars($hostelName);
                        }
                        if (!empty($_GET['month'])) $activeFilters[] = "Month: " . date('F', mktime(0, 0, 0, $_GET['month'], 1));
                        if (!empty($_GET['year'])) $activeFilters[] = "Year: " . $_GET['year'];
                        if (!empty($search)) $activeFilters[] = "Student ID: " . htmlspecialchars($search);
                        echo implode(', ', $activeFilters);
                        ?>
                    </div>
                <?php endif; ?>
                <!-- Add this near your filter button -->
                <div class="btn-toolbar mb-2 mb-md-0">
                    <!-- Add this search form -->

                    <div class="btn-group me-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#filterModal">
                            <i class="bi bi-funnel"></i> Filter
                        </button>
                        <?php if (isset($_GET['status']) || isset($_GET['hostel']) || isset($_GET['month']) || isset($_GET['year']) || !empty($search)): ?>
                            <a href="?" class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-x-circle"></i> Clear Filters
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>



            <!-- Filter Modal -->
            <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="filterModalLabel">Filter Payments</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form method="GET" action="">
                            <!-- In your filter modal form, modify the select/input elements to preserve selections -->
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Payment Status</label>
                                    <select class="form-select" id="status" name="status">
                                        <option value="">All Statuses</option>
                                        <option value="paid" <?= isset($_GET['status']) && $_GET['status'] === 'paid' ? 'selected' : '' ?>>Paid</option>
                                        <option value="unpaid" <?= isset($_GET['status']) && $_GET['status'] === 'unpaid' ? 'selected' : '' ?>>Unpaid</option>
                                        <option value="partial" <?= isset($_GET['status']) && $_GET['status'] === 'partial' ? 'selected' : '' ?>>Partial</option>
                                        <option value="late" <?= isset($_GET['status']) && $_GET['status'] === 'late' ? 'selected' : '' ?>>Late</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="hostel" class="form-label">Hostel</label>
                                    <select class="form-select" id="hostel" name="hostel">
                                        <option value="">All Hostels</option>
                                        <?php foreach ($hostels as $hostel): ?>
                                            <option value="<?= $hostel['id'] ?>"
                                                <?= isset($_GET['hostel']) && $_GET['hostel'] == $hostel['id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($hostel['hostel_name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="month" class="form-label">Month</label>
                                    <select class="form-select" id="month" name="month">
                                        <option value="">All Months</option>
                                        <?php for ($i = 1; $i <= 12; $i++): ?>
                                            <option value="<?= $i ?>"
                                                <?= isset($_GET['month']) && $_GET['month'] == $i ? 'selected' : '' ?>>
                                                <?= date('F', mktime(0, 0, 0, $i, 1)) ?>
                                            </option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="year" class="form-label">Year</label>
                                    <input type="number" class="form-control" id="year" name="year"
                                        min="2020" max="<?= date('Y') + 1 ?>"
                                        value="<?= $_GET['year'] ?? '' ?>">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Apply Filters</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Payment Summary Cards -->
            <!-- Update your summary cards to use the filtered $payments array -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card payment-card">
                        <div class="card-body">
                            <h5 class="card-title">Total Due</h5>
                            <p class="card-text fs-4 fw-bold">৳<?=
                                                                number_format(array_reduce($payments, function ($carry, $item) {
                                                                    return $carry + $item['amount_due'];
                                                                }, 0), 2)
                                                                ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card payment-card">
                        <div class="card-body">
                            <h5 class="card-title">Total Paid</h5>
                            <p class="card-text fs-4 fw-bold">৳<?=
                                                                number_format(array_reduce($payments, function ($carry, $item) {
                                                                    return $carry + $item['amount_paid'];
                                                                }, 0), 2)
                                                                ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card payment-card">
                        <div class="card-body">
                            <h5 class="card-title">Pending Payments</h5>
                            <p class="card-text fs-4 fw-bold"><?=
                                                                count(array_filter($payments, function ($p) {
                                                                    return $p['payment_status'] === 'unpaid';
                                                                }))
                                                                ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card payment-card">
                        <div class="card-body">
                            <h5 class="card-title">Late Payments</h5>
                            <p class="card-text fs-4 fw-bold"><?=
                                                                count(array_filter($payments, function ($p) {
                                                                    return $p['payment_status'] === 'late';
                                                                }))
                                                                ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search by student id -->
            <!-- Search by student ID -->
            <div class="row mb-3">
                <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                    <form class="d-flex" method="GET" action="">
                        <!-- Hidden filters -->
                        <input type="hidden" name="status" value="<?= htmlspecialchars($_GET['status'] ?? '') ?>">
                        <input type="hidden" name="hostel" value="<?= htmlspecialchars($_GET['hostel'] ?? '') ?>">
                        <input type="hidden" name="month" value="<?= htmlspecialchars($_GET['month'] ?? '') ?>">
                        <input type="hidden" name="year" value="<?= htmlspecialchars($_GET['year'] ?? '') ?>">

                        <!-- Input Group -->
                        <div class="input-group input-group-sm shadow-sm rounded">
                            <input
                                type="text"
                                class="form-control border-end-0"
                                name="search"
                                placeholder="Student ID"
                                value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                            <button class="btn btn-outline-secondary" type="submit">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>


            <!-- Payments Table -->
            <div class="card payment-card">
                <div class="card-header">
                    <i class="bi bi-credit-card me-2"></i>Payment Records
                    <?php if (is_admin_logged_in()): ?>
                        <div class="mt-2 mt-md-0 d-flex flex-column flex-md-row gap-2 justify-content-md-end">
                            <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#updateGeneratePaymentsModal">
                                Update Payments
                            </button>
                            <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#generatePaymentsModal">
                                <i class="bi bi-plus-circle"></i> Generate Payments
                            </button>
                        </div>
                    <?php endif; ?>


                    <!-- Update Generated Payment Modal -->
                    <div class="modal fade" id="updateGeneratePaymentsModal" tabindex="-1" aria-labelledby="updateGeneratePaymentsModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <form id="updatePaymentGenerateForm">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Update Generated Payments</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label for="due_date" class="form-label">New Due Date</label>
                                            <input type="date" class="form-control" name="due_date" id="update_due_date" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="late_fee" class="form-label">Late Fee (Optional)</label>
                                            <input type="number" class="form-control" name="late_fee" id="update_late_fee" min="0" step="1">
                                        </div>
                                        <div class="mb-3">
                                            <label for="late_fee_applied_date" class="form-label">Late Fee Applied Date</label>
                                            <input type="date" class="form-control" name="late_fee_applied_date" id="update_late_fee_applied_date">
                                        </div>
                                        <div class="mb-3">
                                            <label for="month" class="form-label">Month</label>
                                            <select name="month" id="update_month" class="form-select" required>
                                                <?php for ($m = 1; $m <= 12; $m++): ?>
                                                    <option value="<?= $m ?>"><?= date('F', mktime(0, 0, 0, $m, 1)) ?></option>
                                                <?php endfor; ?>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="year" class="form-label">Year</label>
                                            <input type="number" name="year" id="update_year" class="form-control" value="<?= date('Y') ?>" required>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-warning">Update Payments</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>


                    <!-- Payment Generation Modal -->
                    <div class="modal fade" id="generatePaymentsModal" tabindex="-1" aria-labelledby="generatePaymentsModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <form id="paymentGenerateForm">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Generate Monthly Payments</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label for="due_date" class="form-label">Due Date</label>
                                            <input type="date" class="form-control" name="due_date" id="due_date" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="late_fee" class="form-label">Late Fee (Optional)</label>
                                            <input type="number" class="form-control" name="late_fee" id="late_fee" min="0" step="1">
                                        </div>
                                        <div class="mb-3">
                                            <label for="late_fee_applied_date" class="form-label">Late Fee Applied Date</label>
                                            <input type="date" class="form-control" name="late_fee_applied_date" id="late_fee_applied_date">
                                        </div>
                                        <div class="mb-3">
                                            <label for="month" class="form-label">Month</label>
                                            <select name="month" id="month" class="form-select" required>
                                                <?php for ($m = 1; $m <= 12; $m++): ?>
                                                    <option value="<?= $m ?>"><?= date('F', mktime(0, 0, 0, $m, 1)) ?></option>
                                                <?php endfor; ?>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="year" class="form-label">Year</label>
                                            <input type="number" name="year" id="year" class="form-control" value="<?= date('Y') ?>" required>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-primary">Generate</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive" style="max-height: 300px; overflow-y: auto">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>Student ID</th>
                                    <th>Hostel</th>
                                    <th>Room</th>
                                    <th>Period</th>
                                    <th>Due Date</th>
                                    <th>Amount Due</th>
                                    <th>Amount Paid</th>
                                    <th>Late Fee</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($payments as $payment):
                                    $statusClass = 'status-' . $payment['payment_status'];
                                    $monthYear = date('F Y', mktime(0, 0, 0, $payment['month'], 1, $payment['year']));
                                    $dueDate = date('d M Y', strtotime($payment['due_date']));

                                    $amountDue = $payment['amount_due'];
                                    $isLate = false;

                                    $currentDate = new DateTime();
                                    $dueDateObj = new DateTime($payment['due_date']);
                                    // Create DateTime for late_fee_applied_date, if exists; else default to a far future date so late fee won't apply
                                    $lateFeeAppliedDateObj = isset($payment['late_fee_applied_date']) && $payment['late_fee_applied_date'] !== null
                                        ? new DateTime($payment['late_fee_applied_date'])
                                        : new DateTime('9999-12-31');

                                    if (
                                        in_array($payment['payment_status'], ['unpaid', 'partial']) &&
                                        $currentDate > $dueDateObj &&
                                        $currentDate >= $lateFeeAppliedDateObj
                                    ) {
                                        $amountDue += $payment['late_fee'];
                                        $isLate = true;
                                    }
                                ?>

                                    <tr>
                                        <td><?= htmlspecialchars($payment['first_name'] . ' ' . $payment['last_name']) ?></td>
                                        <td><?= htmlspecialchars($payment['varsity_id']) ?></td>
                                        <td><?= htmlspecialchars($payment['hostel_name']) ?></td>
                                        <td><?= htmlspecialchars($payment['room_number']) ?></td>
                                        <td><?= $monthYear ?></td>
                                        <td><?= $dueDate ?></td>
                                        <td>৳<?= number_format($amountDue, 2) ?></td>
                                        <td>৳<?= number_format($payment['amount_paid'], 2) ?></td>
                                        <td><?= htmlspecialchars($payment['late_fee']) ?></td>
                                        <td>
                                            <span class="payment-status <?= $statusClass ?>">
                                                <?= ucfirst($payment['payment_status']) ?>
                                                <?= $isLate ? ' (Late)' : '' ?>
                                            </span>
                                        </td>
                                        <td class="payment-actions">
                                            <a href="<?= BASE_URL ?>/admin/sections/payments/view.php?id=<?= $payment['id'] ?>" class="btn btn-sm btn-primary" title="View Details">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <?php if ($payment['payment_status'] !== 'paid'): ?>
                                                <a href="verify.php?id=<?= $payment['id'] ?>" class="btn btn-sm btn-success" title="Verify Payment">
                                                    <i class="bi bi-check-circle"></i>
                                                </a>
                                            <?php endif; ?>
                                            <a href="receipt.php?id=<?= $payment['id'] ?>" class="btn btn-sm btn-info" title="Generate Receipt">
                                                <i class="bi bi-receipt"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination would go here if implemented -->
                </div>
            </div>
        </main>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Payment Generation Form
        $('#paymentGenerateForm').submit(function(e) {
            e.preventDefault();

            const formData = $(this).serialize();
            const $modal = $('#generatePaymentsModal');
            const $submitBtn = $(this).find('button[type="submit"]');
            $submitBtn.prop('disabled', true).text('Generating...');
            $.ajax({
                url: '<?= BASE_URL . '/admin/php_files/sections/payments/generate_payments.php' ?>',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    $modal.modal('hide');
                    console.log(response);
                    if (response.generated > 0) {
                        showSlideMessage(response.message, 'success');
                    } else {
                        showSlideMessage(response.message, 'info');
                    }
                    setTimeout(() => location.reload(), 3000);
                },
                error: function() {
                    showSlideMessage('Failed to generate payments. Please try again.', 'danger');
                },
                complete: function() {
                    $submitBtn.prop('disabled', false).text('Generate');
                }
            });
        });

        // Update Payment Form
        $('#updatePaymentGenerateForm').submit(function(e) {
            e.preventDefault();

            const formData = $(this).serialize();
            const $modal = $('#updateGeneratePaymentsModal');
            const $submitBtn = $(this).find('button[type="submit"]');
            $submitBtn.prop('disabled', true).text('Updating...');

            $.ajax({
                url: '<?= BASE_URL . '/admin/php_files/sections/payments/update_generated_payments.php' ?>',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    $modal.modal('hide');
                    if (response.updated > 0) {
                        showSlideMessage(response.message, 'success');
                    } else {
                        showSlideMessage(response.message, 'info');
                    }
                    setTimeout(() => location.reload(), 3000);
                },
                error: function() {
                    showSlideMessage('Failed to update payments. Please try again.', 'danger');
                },
                complete: function() {
                    $submitBtn.prop('disabled', false).text('Update Payments');
                }
            });
        });
    });
</script>



<?php require_once BASE_PATH . '/admin/includes/footer_admin.php'; ?>