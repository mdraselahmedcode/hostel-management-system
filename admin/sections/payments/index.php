<?php
require_once __DIR__ . '/../../../config/config.php';
include BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/config/auth.php';
include BASE_PATH . '/includes/slide_message.php';
require_once BASE_PATH . '/admin/php_files/sections/payments/updateLateStatus.php';


date_default_timezone_set('Asia/Dhaka'); // Set your local timezone

require_admin();

//  update is late status
updateLateStatus();

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
$query = "SELECT sp.*, s.first_name, s.last_name, s.varsity_id, s.id AS student_id, h.hostel_name, r.room_number 
          FROM student_payments sp
          JOIN students s ON sp.student_id = s.id
          JOIN hostels h ON sp.hostel_id = h.id
          JOIN rooms r ON sp.room_id = r.id";



$conditions = [];
$params = [];
$types = '';

// For use in multiple queries
$whereClause = '';
$bindParams = function ($stmt, $types, $params) {
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
};

// Search
if (!empty($search)) {
    $conditions[] = "s.varsity_id LIKE ?";
    $params[] = '%' . $search . '%';
    $types .= 's';
}

// Status
if (!empty($status)) {
    if ($status === 'late') {
        $conditions[] = "sp.is_late = 1";
    } else {
        $conditions[] = "sp.payment_status = ?";
        $params[] = $status;
        $types .= 's';
    }
}

// Hostel
if (!empty($hostel)) {
    $conditions[] = "sp.hostel_id = ?";
    $params[] = $hostel;
    $types .= 'i';
}

// Month
if (!empty($month)) {
    $conditions[] = "sp.month = ?";
    $params[] = $month;
    $types .= 'i';
}

// Year
if (!empty($year)) {
    $conditions[] = "sp.year = ?";
    $params[] = $year;
    $types .= 'i';
}

if (!empty($conditions)) {
    $whereClause = ' WHERE ' . implode(' AND ', $conditions);
}


$query .= $whereClause . " ORDER BY sp.year DESC, sp.month DESC";


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
        <!-- Sidebar -->
        <?php require_once BASE_PATH . '/admin/includes/sidebar_admin.php'; ?>

        <!-- Main Content -->
        <main class="col-md-10 ms-sm-auto px-md-4 py-4" style="overflow-y: auto; max-height: calc(100vh - 95px)">
            <a href="<?= BASE_URL . '/admin/dashboard.php' ?>" class="btn btn-secondary mb-3">Back</a>

            <!-- Page Header -->
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Payment Management</h1>

                <!-- filtering comment -->
                <?php if (isset($_GET['status']) || isset($_GET['hostel']) || isset($_GET['month']) || isset($_GET['year']) || !empty($search)): ?>
                    <div class="alert alert-info mb-3">
                        <i class="bi bi-funnel"></i> Active Filters:
                        <?php
                        $activeFilters = [];
                        // if (!empty($_GET['status'])) $activeFilters[] = "Status: " . ucfirst($_GET['status']);
                        if (!empty($_GET['status'])) {
                            $statusLabel = $_GET['status'] === 'late' ? 'Late (Overdue)' : ucfirst($_GET['status']);
                            $activeFilters[] = "Status: " . $statusLabel;
                        }

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

                <div class="d-flex flex-column flex-md-row gap-2 align-items-start align-items-md-center">


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
                <div class="col-md-3 mb-2">
                    <div class="card payment-card h-100">
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
                <div class="col-md-3 mb-2">
                    <div class="card payment-card h-100">
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
                <?php
                $pendingAmountQuery = "
                        SELECT COUNT(*) AS total_count, SUM(pt.amount) AS total_pending 
                        FROM payment_transactions pt
                        JOIN student_payments sp ON pt.payment_id = sp.id
                        JOIN students s ON sp.student_id = s.id
                        $whereClause AND pt.verification_status = 'pending'
                    ";

                $stmt = $conn->prepare($pendingAmountQuery);
                $bindParams($stmt, $types, $params);
                $stmt->execute();
                $result = $stmt->get_result();
                $totalPendingAmount = 0;
                $totalPendingCount = 0;
                if ($result && $row = $result->fetch_assoc()) {
                    $totalPendingAmount = $row['total_pending'] ?? 0;
                    $totalPendingCount = $row['total_count'] ?? 0;
                }
                $stmt->close();
                ?>
                <div class="col-md-3 mb-2">
                    <div class="card payment-card h-100 ">
                        <div class="card-body">
                            <h5 class="card-title">Pending Payments</h5>
                            <div class="d-flex align-items-center justify-content-between">
                                <span class="fs-4 fw-bold"><?= $totalPendingCount ?></span>
                                <strong class="text-muted ms-2">৳<?= number_format($totalPendingAmount, 2) ?></strong>
                            </div>
                        </div>
                    </div>
                </div>





                <?php
                // Get total late count, paid late count, paid late fee amount, and total late fee (paid + unpaid)
                $lateFeeQuery = "
                        SELECT 
                            SUM(CASE WHEN sp.is_late = 1 THEN 1 ELSE 0 END) AS total_late_count,
                            SUM(CASE WHEN sp.is_late = 1 AND sp.is_late_fee_taken = 1 THEN 1 ELSE 0 END) AS paid_late_count,
                            SUM(CASE WHEN sp.is_late = 1 AND sp.is_late_fee_taken = 1 THEN sp.late_fee ELSE 0 END) AS total_paid_late_fee,
                            SUM(CASE WHEN sp.is_late = 1 THEN sp.late_fee ELSE 0 END) AS total_late_fee
                        FROM student_payments sp
                        JOIN students s ON sp.student_id = s.id
                        $whereClause
                    ";

                $stmt = $conn->prepare($lateFeeQuery);
                $bindParams($stmt, $types, $params);
                $stmt->execute();
                $result = $stmt->get_result();
                $total_late_count = 0;
                $paid_late_count = 0;
                $total_paid_late_fee = 0.00;
                $total_late_fee = 0.00;

                if ($result && $row = $result->fetch_assoc()) {
                    $total_late_count = (int)$row['total_late_count'];
                    $paid_late_count = (int)$row['paid_late_count'];
                    $total_paid_late_fee = (float)($row['total_paid_late_fee'] ?? 0.00);
                    $total_late_fee = (float)($row['total_late_fee'] ?? 0.00);
                }
                $stmt->close();
                ?>

                <div class="col-md-3 mb-2">
                    <div class="card payment-card h-100">
                        <div class="card-body">
                            <h5 class="card-title">Late Payments</h5>
                            <div class="mb-1">
                                <strong>Total Late:</strong> <?= $total_late_count ?>
                                (Paid: <?= $paid_late_count ?>)
                            </div>
                            <div class="mb-1">
                                <strong>Late Fee:</strong> ৳<?= number_format($total_late_fee, 2) ?>
                                (Paid: ৳<?= number_format($total_paid_late_fee, 2) ?>)
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Search by student ID -->
            <!-- Search and Payment Request Header -->
            <div class="row mb-3 align-items-center" style="row-gap: 15px;">
                <!-- Search by student ID -->
                <div class="col-xl-4 col-lg-5 col-md-6 col-sm-12">
                    <form class="d-flex" method="GET" action="" style="width: 100%;">
                        <!-- Hidden filters -->
                        <input type="hidden" name="status" value="<?= htmlspecialchars($_GET['status'] ?? '') ?>">
                        <input type="hidden" name="hostel" value="<?= htmlspecialchars($_GET['hostel'] ?? '') ?>">
                        <input type="hidden" name="month" value="<?= htmlspecialchars($_GET['month'] ?? '') ?>">
                        <input type="hidden" name="year" value="<?= htmlspecialchars($_GET['year'] ?? '') ?>">

                        <!-- Stylish Input Group -->
                        <div class="input-group shadow-sm rounded w-100" style="flex-wrap: nowrap;">
                            <input
                                type="text"
                                class="form-control border-0 bg-light px-3 py-2"
                                name="search"
                                placeholder="Search by Student ID"
                                value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
                                style="font-size: 0.95rem; flex: 1 1 auto;">
                            <button class="btn btn-primary px-3" type="submit" style="flex-shrink: 0;">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Report + Requests buttons -->
                <div class="col-xl-8 col-lg-7 col-md-6 col-sm-12 d-flex justify-content-xl-end justify-content-lg-end justify-content-md-end justify-content-start align-items-center" style="gap: 10px; flex-wrap: wrap;">
                    <!-- Generate Report Button -->
                    <a href="<?= BASE_URL . '/admin/sections/payments/generate_payment_report.php' ?>" class="btn btn-sm btn-success" style="min-width: 160px;">
                        <i class="bi bi-file-earmark-text me-1"></i> Generate Report
                    </a>

                    <?php
                    // Count pending payment verification requests
                    $pending_count = $conn->query("
                        SELECT COUNT(*) AS count 
                        FROM payment_transactions 
                        WHERE verification_status = 'pending'
                    ")->fetch_assoc()['count'];
                    ?>

                    <!-- Payment Requests Button -->
                    <a href="payment_requests.php" class="btn btn-sm btn-warning position-relative" style="min-width: 180px;">
                        <i class="bi bi-cash-coin"></i> Payment Requests
                        <?php if ($pending_count > 0): ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.75rem;">
                                <?= $pending_count ?>
                                <span class="visually-hidden">pending requests</span>
                            </span>
                        <?php endif; ?>
                    </a>
                </div>
            </div>



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

            <!-- Payments Table -->
            <div class="card shadow-sm border-0 payment-card">
                <div class="card-header d-flex flex-wrap justify-content-between align-items-center text-white"
                    style="background: linear-gradient(to left, #6888a9ff 0%, #1a2530 100%);">

                    <div>
                        <i class="bi bi-credit-card me-2"></i>
                        <strong>Payment Records</strong>
                    </div>
                    <?php if (is_admin_logged_in()): ?>
                        <div class="d-flex flex-wrap gap-2 mt-2 mt-md-0">
                            <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#generatePaymentsModal">
                                <i class="bi bi-plus-circle me-1"></i> Generate Payment
                            </button>
                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#updateGeneratePaymentsModal">
                                <i class="bi bi-pencil-square me-1"></i> Update
                            </button>
                            <a class="btn btn-info btn-sm" href="<?= BASE_URL . '/admin/sections/payments/payment_method/index.php' ?>">
                                <i class="bi bi-gear me-1"></i> Manage Methods
                            </a>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="card-body">
                    <div class="table-responsive" style="max-height: 270px; overflow-y: auto">
                        <table class="table table-hover table-bordered align-middle mb-0">
                            <thead class="table-light text-center">
                                <tr>
                                    <th>Student</th>
                                    <th>ID</th>
                                    <th>Hostel</th>
                                    <th>Room</th>
                                    <th>Period</th>
                                    <th>Due Date</th>
                                    <th>Amount Due</th>
                                    <th>Paid</th>
                                    <th>Balance</th>
                                    <th>Is Late</th>
                                    <th>Late Fee</th>
                                    <th>L.F. Applied Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($payments as $payment):

                                    $statusClass = match ($payment['payment_status']) {
                                        'paid' => 'badge bg-success',
                                        'unpaid' => 'badge bg-danger',
                                        'partial' => 'badge bg-warning text-dark',
                                        'late' => 'badge bg-secondary',
                                        default => 'badge bg-light text-dark',
                                    };

                                    $monthYear = date('F Y', mktime(0, 0, 0, $payment['month'], 1, $payment['year']));
                                    $dueDate = date('d M Y', strtotime($payment['due_date']));
                                    $late_fee_applied_date = date('d M Y', strtotime($payment['late_fee_applied_date']));

                                    $amountDue = $payment['amount_due'];
                                    $isLate = false;

                                    $currentDate = new DateTime();
                                    $dueDateObj = new DateTime($payment['due_date']);
                                    $lateFeeAppliedDateObj = isset($payment['late_fee_applied_date']) && $payment['late_fee_applied_date'] !== null
                                        ? new DateTime($payment['late_fee_applied_date'])
                                        : new DateTime('9999-12-31');

                                    if (
                                        in_array($payment['payment_status'], ['unpaid', 'partial']) &&
                                        $currentDate >= $dueDateObj &&
                                        $currentDate >= $lateFeeAppliedDateObj
                                    ) {
                                        $isLate = true;
                                    }
                                ?>
                                    <tr class="text-center">
                                        <td><?= htmlspecialchars($payment['first_name'] . ' ' . $payment['last_name']) ?></td>
                                        <td><?= htmlspecialchars($payment['varsity_id']) ?></td>
                                        <td><?= htmlspecialchars($payment['hostel_name']) ?></td>
                                        <td><?= htmlspecialchars($payment['room_number']) ?></td>
                                        <td><?= $monthYear ?></td>
                                        <td><?= $dueDate ?></td>
                                        <td>৳<?= number_format($amountDue, 2) ?></td>
                                        <td>৳<?= number_format($payment['amount_paid'], 2) ?></td>
                                        <td>৳<?= number_format($payment['balance'], 2) ?></td>
                                        <td><?= $payment['is_late'] ? 'Yes' : '—' ?></td>
                                        <td>
                                            <?php if ($payment['late_fee'] > 0 && $payment['is_late']): ?>
                                                ৳<?= number_format($payment['late_fee'], 2) ?>
                                            <?php else: ?>
                                                —
                                            <?php endif; ?>
                                        </td>
                                        <td><?= $late_fee_applied_date ?></td>
                                        <td>
                                            <span class="<?= $statusClass ?>">
                                                <?= ucfirst($payment['payment_status']) ?>
                                                <?= $isLate ? '<small>(Late)</small>' : '' ?>
                                            </span>
                                        </td>
                                        <td class="align-middle">
                                            <div class="d-flex justify-content-center align-items-center gap-1">
                                                <a href="<?= BASE_URL ?>/admin/sections/payments/view.php?id=<?= $payment['id'] ?>" class="btn btn-sm btn-info" title="View">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="<?= BASE_URL ?>/admin/sections/payments/monthly_payment_report.php?id=<?= $payment['id'] ?>" class="btn btn-sm btn-outline-info" title="Receipt">
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