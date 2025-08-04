<?php
require_once __DIR__ . '/../../../config/config.php';
require_once BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/config/auth.php';

require_admin();


// Handle export requests
if (isset($_GET['export'])) {
    $export_type = $_GET['export'];

    if ($export_type === 'csv') {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="payments_' . date('Y-m-d') . '.csv"');

        $output = fopen('php://output', 'w');

        // CSV header
    fputcsv($output, [
            'Student Name',
            'Varsity ID',
            'Hostel',
            'Room',
            'Amount Due',
            'Amount Paid',
            'Late Fee',
            'Status',
            'Due Date',
            'Payment Date',
            'Collected By'
        ]);

        // Sample query to fetch payments, customize as needed
        $payments = [];

        $stmt = $conn->query("
        SELECT 
            sp.amount_due,
            sp.amount_paid,
            sp.late_fee,
            sp.is_late,
            sp.payment_status,
            sp.due_date,
            sp.created_at,
            s.first_name,
            s.last_name,
            s.varsity_id,
            h.hostel_name,
            r.room_number,
            rt.type_name AS room_type,
            a.firstname AS collected_by_firstname,
            a.lastname AS collected_by_lastname
        FROM student_payments sp
        JOIN students s ON sp.student_id = s.id
        JOIN hostels h ON sp.hostel_id = h.id
        JOIN rooms r ON sp.room_id = r.id
        JOIN room_types rt ON sp.room_type_id = rt.id
        LEFT JOIN admins a ON sp.created_by = a.id
        WHERE YEAR(sp.created_at) = YEAR(CURDATE())
        ORDER BY sp.due_date DESC
    ");

        if ($stmt && $stmt->num_rows > 0) {
        while ($row = $stmt->fetch_assoc()) {
            $payments[] = $row;
        }
    }

    // Output CSV rows
    foreach ($payments as $payment) {
        $studentName = $payment['first_name'] . ' ' . $payment['last_name'];
        $roomDisplay = $payment['room_number'] . ' (' . $payment['room_type'] . ')';
        $collectedBy = trim($payment['collected_by_firstname'] . ' ' . $payment['collected_by_lastname']);

        fputcsv($output, [
        $studentName,
        '="' . $payment['varsity_id'] . '"',  
        $payment['hostel_name'],
        $roomDisplay,
        $payment['amount_due'],
        $payment['amount_paid'],
        ($payment['late_fee'] > 0 && $payment['is_late']) ? $payment['late_fee'] : '',
        ucfirst($payment['payment_status']),
        date('d M Y', strtotime($payment['due_date'])),
        date('d M Y', strtotime($payment['created_at'])),
        $collectedBy ?: '-'
    ]);
        }

    fclose($output);
    exit();
    } 
}



// Set default timezone
date_default_timezone_set('Asia/Dhaka');

// Get filter parameters
$hostel_id = $_GET['hostel_id'] ?? null;
$payment_status = $_GET['payment_status'] ?? null;
$month = $_GET['month'] ?? date('n');
$year = $_GET['year'] ?? date('Y');
$student_name = $_GET['student_name'] ?? null;
$varsity_id = $_GET['varsity_id'] ?? null;

// Build base query
$query = "SELECT 
            sp.id, 
            sp.amount_due, 
            sp.amount_paid, 
            sp.payment_status,
            sp.due_date,
            sp.late_fee,
            sp.is_late,
            sp.late_fee_applied_date,
            sp.created_at,
            CONCAT(s.first_name, ' ', s.last_name) AS student_name,
            s.varsity_id,
            h.hostel_name,
            r.room_number,
            rt.type_name AS room_type,
            CONCAT(a.firstname, ' ', a.lastname) AS collected_by
          FROM student_payments sp
          JOIN students s ON sp.student_id = s.id
          JOIN hostels h ON sp.hostel_id = h.id
          JOIN rooms r ON sp.room_id = r.id
          JOIN room_types rt ON sp.room_type_id = rt.id
          LEFT JOIN admins a ON sp.created_by = a.id
          WHERE sp.month = ? AND sp.year = ?";

$params = [$month, $year];
$param_types = "ii";

// Add filters
if ($hostel_id && is_numeric($hostel_id)) {
    $query .= " AND sp.hostel_id = ?";
    $params[] = $hostel_id;
    $param_types .= "i";
}

if ($payment_status && in_array($payment_status, ['paid', 'unpaid', 'partial', 'late'])) {
    $query .= " AND sp.payment_status = ?";
    $params[] = $payment_status;
    $param_types .= "s";
}

if ($student_name) {
    $query .= " AND (s.first_name LIKE ? OR s.last_name LIKE ?)";
    $params[] = "%$student_name%";
    $params[] = "%$student_name%";
    $param_types .= "ss";
}

if ($varsity_id) {
    $query .= " AND s.varsity_id LIKE ?";
    $params[] = "%$varsity_id%";
    $param_types .= "s";
}

// Add sorting
$query .= " ORDER BY sp.created_at DESC";

// Prepare and execute query
$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Database error: " . $conn->error);
}

$stmt->bind_param($param_types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
$payments = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Get summary statistics
$summary_query = "SELECT 
                        COUNT(*) AS total_payments,
                        SUM(amount_paid) AS total_collected,
                        SUM(CASE WHEN payment_status = 'paid' THEN 1 ELSE 0 END) AS paid_count,
                        SUM(CASE WHEN payment_status = 'unpaid' THEN 1 ELSE 0 END) AS unpaid_count,
                        SUM(CASE WHEN payment_status = 'partial' THEN 1 ELSE 0 END) AS partial_count,
                        SUM(CASE WHEN payment_status = 'late' THEN 1 ELSE 0 END) AS late_count,
                        SUM(CASE WHEN is_late_fee_taken = 1 THEN late_fee ELSE 0 END) AS total_late_fees
                    FROM student_payments
                    WHERE month = ? AND year = ?";

if ($hostel_id && is_numeric($hostel_id)) {
    $summary_query .= " AND hostel_id = ?";
}

$stmt = $conn->prepare($summary_query);
if (!$stmt) {
    die("Database error: " . $conn->error);
}

if ($hostel_id && is_numeric($hostel_id)) {
    $stmt->bind_param("iii", $month, $year, $hostel_id);
} else {
    $stmt->bind_param("ii", $month, $year);
}

$stmt->execute();
$summary = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Get hostels for dropdown
$hostels = $conn->query("SELECT id, hostel_name FROM hostels ORDER BY hostel_name")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Reports - Hostel Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .report-header {
            background-color: #f8f9fa;
            padding: 20px;
            margin-bottom: 30px;
            border-radius: 5px;
        }

        .summary-card {
            border-left: 4px solid #0d6efd;
        }

        .table-responsive {
            overflow-x: auto;
        }

        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .status-paid {
            background-color: #d1fae5;
            color: #065f46;
        }

        .status-unpaid {
            background-color: #fee2e2;
            color: #b91c1c;
        }

        .status-partial {
            background-color: #fef3c7;
            color: #92400e;
        }

        .status-late {
            background-color: #e0e7ff;
            color: #4338ca;
        }


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

<body>
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col">
                <h2><i class="bi bi-file-earmark-text"></i> Payment Reports</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/admin/sections/payments/index.php">Payments</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Payment Reports</li>
                    </ol>
                </nav>
            </div>
        </div>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-header">
                <h5><i class="bi bi-funnel"></i> Filters</h5>
            </div>
            <div class="card-body">
                <form method="get" class="row g-3">
                    <div class="col-md-3">
                        <label for="month" class="form-label">Month</label>
                        <select name="month" id="month" class="form-select">
                            <?php for ($m = 1; $m <= 12; $m++): ?>
                                <option value="<?= $m ?>" <?= $m == $month ? 'selected' : '' ?>>
                                    <?= date('F', mktime(0, 0, 0, $m, 1)) ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="year" class="form-label">Year</label>
                        <select name="year" id="year" class="form-select">
                            <?php for ($y = date('Y') - 2; $y <= date('Y') + 1; $y++): ?>
                                <option value="<?= $y ?>" <?= $y == $year ? 'selected' : '' ?>><?= $y ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="hostel_id" class="form-label">Hostel</label>
                        <select name="hostel_id" id="hostel_id" class="form-select">
                            <option value="">All Hostels</option>
                            <?php foreach ($hostels as $hostel): ?>
                                <option value="<?= $hostel['id'] ?>" <?= $hostel_id == $hostel['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($hostel['hostel_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="payment_status" class="form-label">Payment Status</label>
                        <select name="payment_status" id="payment_status" class="form-select">
                            <option value="">All Statuses</option>
                            <option value="paid" <?= $payment_status == 'paid' ? 'selected' : '' ?>>Paid</option>
                            <option value="unpaid" <?= $payment_status == 'unpaid' ? 'selected' : '' ?>>Unpaid</option>
                            <option value="partial" <?= $payment_status == 'partial' ? 'selected' : '' ?>>Partial</option>
                            <option value="late" <?= $payment_status == 'late' ? 'selected' : '' ?>>Late</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="student_name" class="form-label">Student Name</label>
                        <input type="text" name="student_name" id="student_name" class="form-control"
                            value="<?= htmlspecialchars($student_name ?? '') ?>" placeholder="Search by name">
                    </div>
                    <div class="col-md-4">
                        <label for="varsity_id" class="form-label">Varsity ID</label>
                        <input type="text" name="varsity_id" id="varsity_id" class="form-control"
                            value="<?= htmlspecialchars($varsity_id ?? '') ?>" placeholder="Search by ID">
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2"><i class="bi bi-search"></i> Filter</button>
                        <a href="?" class="btn btn-outline-secondary"><i class="bi bi-arrow-counterclockwise"></i> Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card summary-card h-100">
                    <div class="card-body">
                        <h6 class="card-subtitle mb-2 text-muted">Total Payments</h6>
                        <h3 class="card-title"><?= $summary['total_payments'] ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card summary-card h-100">
                    <div class="card-body">
                        <h6 class="card-subtitle mb-2 text-muted">Total Collected</h6>
                        <h3 class="card-title">Tk <?= number_format($summary['total_collected'], 2) ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card summary-card h-100">
                    <div class="card-body">
                        <h6 class="card-subtitle mb-2 text-muted">Late Fees</h6>
                        <h3 class="card-title">Tk <?= number_format($summary['total_late_fees'], 2) ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card summary-card h-100">
                    <div class="card-body">
                        <h6 class="card-subtitle mb-2 text-muted">Collection Rate</h6>
                        <h3 class="card-title">
                            <?= $summary['total_payments'] > 0 ?
                                round(($summary['paid_count'] / $summary['total_payments']) * 100) : 0 ?>%
                        </h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Breakdown -->
        <div class="row mb-4">
            <div class="col">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 text-center">
                                <span class="status-badge status-paid">Paid: <?= $summary['paid_count'] ?></span>
                            </div>
                            <div class="col-md-3 text-center">
                                <span class="status-badge status-unpaid">Unpaid: <?= $summary['unpaid_count'] ?></span>
                            </div>
                            <div class="col-md-3 text-center">
                                <span class="status-badge status-partial">Partial: <?= $summary['partial_count'] ?></span>
                            </div>
                            <div class="col-md-3 text-center">
                                <span class="status-badge status-late">Late: <?= $summary['late_count'] ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Records -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5><i class="bi bi-list-check"></i> Payment Records</h5>
                <div>
                    <a href="<?= $_SERVER['REQUEST_URI'] ?>&export=csv" class="btn btn-sm btn-outline-success me-2">
                        <i class="bi bi-file-earmark-excel"></i> Export CSV
                    </a>
                    <a href="#" id="exportPdfBtn" class="btn btn-sm btn-outline-danger">
                        <i class="bi bi-file-earmark-pdf"></i> Export PDF
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Student</th>
                                <th>Varsity ID</th>
                                <th>Hostel</th>
                                <th>Room</th>
                                <th>Amount Due</th>
                                <th>Amount Paid</th>
                                <th>Late Fee</th>
                                <th>Status</th>
                                <th>Due Date</th>
                                <th>L.F. Applied Date</th>
                                <th>Payment Date</th>
                                <th>Collected By</th>
                                <!-- <th>Action</th> -->
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($payments)): ?>
                                <tr>
                                    <td colspan="13" class="text-center py-4">No payment records found</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($payments as $index => $payment): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td><?= htmlspecialchars($payment['student_name']) ?></td>
                                        <td><?= htmlspecialchars($payment['varsity_id']) ?></td>
                                        <td><?= htmlspecialchars($payment['hostel_name']) ?></td>
                                        <td><?= htmlspecialchars($payment['room_number']) ?> (<?= htmlspecialchars($payment['room_type']) ?>)</td>
                                        <td>Tk <?= number_format($payment['amount_due'], 2) ?></td>
                                        <td>Tk <?= number_format($payment['amount_paid'], 2) ?></td>
                                        <td>
                                            <?php 
                                                if ($payment['late_fee'] > 0 && $payment['is_late'] === 1) {
                                                   echo 'Tk ' . number_format($payment['late_fee'], 2);
                                                } else {
                                                    echo '—';
                                                }
                                            ?>
                                        </td>

                                        <td>
                                            <span class="status-badge status-<?= $payment['payment_status'] ?>">
                                                <?= ucfirst($payment['payment_status']) ?>
                                            </span>
                                        </td>
                                        <td><?= date('d M Y', strtotime($payment['due_date'])) ?></td>
                                        <td><?= date('d M Y', strtotime($payment['late_fee_applied_date'])) ?></td>
                                        <td><?= date('d M Y', strtotime($payment['created_at'])) ?></td>
                                        <td><?= $payment['collected_by'] ?? '—' ?></td>
                                        <!-- <td>
                                            <a href="<?= BASE_URL ?> /admin/sections/payments/receipt.php?id=<?= $payment['id'] ?>"
                                                class="btn btn-sm btn-outline-primary" title="View Receipt">
                                                <i class="bi bi-receipt"></i>
                                            </a>
                                        </td> -->
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jsPDF CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <!-- AutoTable Plugin -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>


    <script>
        document.getElementById("exportPdfBtn").addEventListener("click", function() {
            const {
                jsPDF
            } = window.jspdf;
            const doc = new jsPDF();

            doc.text("Payment Report", 14, 15);
            doc.setFontSize(10);

            // Select table
            const table = document.querySelector("table");

            // AutoTable
            doc.autoTable({
                html: table,
                startY: 20,
                theme: 'striped',
                headStyles: {
                    fillColor: [13, 110, 253]
                },
                styles: {
                    fontSize: 8
                },
                didDrawPage: function(data) {
                    doc.setFontSize(9);
                    doc.text('Generated on: ' + new Date().toLocaleDateString(), 14, doc.internal.pageSize.height - 10);
                }
            });

            doc.save("payment_report_<?= date('Y-m-d') ?>.pdf");
        });
    </script>


</body>

</html>