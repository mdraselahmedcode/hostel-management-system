<?php
require_once __DIR__ . '/../../../config/config.php';
require_once BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/config/auth.php';
include BASE_PATH . '/includes/slide_message.php';

require_admin();

// Varsity ID Search
$varsityIdFilter = $_GET['varsity_id'] ?? '';

// Handle verification actions
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['verify'])) {
    $txn_id = (int)$_POST['txn_id'];
    $action = $_POST['action'];

    $stmt = $conn->prepare("
        UPDATE payment_transactions 
        SET verification_status = ?, 
            verified_by = ?,
            updated_at = NOW()
        WHERE id = ?
    ");

    $verified_status = $action === 'verify' ? 'verified' : 'rejected';
    $stmt->bind_param("sii", $verified_status, $_SESSION['admin']['id'], $txn_id);
    $stmt->execute();

    if ($action == 'verify') {
        $txn = $conn->query("
            SELECT payment_id, amount 
            FROM payment_transactions 
            WHERE id = $txn_id
        ")->fetch_assoc();

        $update_payment = $conn->prepare("
            UPDATE student_payments 
            SET amount_paid = amount_paid + ?,
                payment_status = CASE 
                    WHEN amount_paid + ? >= amount_due THEN 'paid'
                    ELSE 'partial'
                END,
                updated_at = NOW()
            WHERE id = ?
        ");
        $update_payment->bind_param("ddi", $txn['amount'], $txn['amount'], $txn['payment_id']);
        $update_payment->execute();
    }

    $_SESSION['message'] = "Payment $verified_status successfully";
    header("Location: payment_requests.php");
    exit();
}

// Build SQL for pending requests
$sql = "
    SELECT pt.*, 
           pm.display_name AS payment_method,
           s.first_name, s.last_name, s.varsity_id, s.contact_number AS mobile,
           sp.amount_due, sp.amount_paid, sp.payment_status, sp.month, sp.year
    FROM payment_transactions pt
    JOIN payment_methods pm ON pt.payment_method_id = pm.id
    JOIN student_payments sp ON pt.payment_id = sp.id
    JOIN students s ON sp.student_id = s.id
    WHERE pt.verification_status = 'pending'
";

if (!empty($varsityIdFilter)) {
    $escaped = $conn->real_escape_string($varsityIdFilter);
    $sql .= " AND s.varsity_id LIKE '%$escaped%'";
}

$sql .= " ORDER BY pt.payment_date DESC";
$requests = $conn->query($sql)->fetch_all(MYSQLI_ASSOC);

require_once BASE_PATH . '/admin/includes/header_admin.php';
?>

<div class="content container-fluid mt-5">
    <div class="row full-height">
        <?php require_once BASE_PATH . '/admin/includes/sidebar_admin.php'; ?>

        <main class="col-md-10 ms-sm-auto px-md-4 py-4">
            <a href="<?= BASE_URL . '/admin/sections/payments/index.php' ?>" class="btn btn-secondary mb-3">
                <i class="bi bi-arrow-left"></i> Back to Payments
            </a>

            <div class="card shadow-sm">
                <div class="card-body" style="max-height: 700px; overflow-y: auto;">
                    <h2 class="mb-4"><i class="bi bi-cash-coin"></i> Payment Verification Requests</h2>

                    <!-- Search Form -->
                    <div class="mb-4">
                        <form method="GET">
                            <div class="row g-2 align-items-center">
                                <div class="col-sm-12 col-md-5 col-lg-4">
                                    <input type="text"
                                        name="varsity_id"
                                        class="form-control"
                                        placeholder="Search by Varsity ID"
                                        value="<?= htmlspecialchars($varsityIdFilter) ?>">
                                </div>
                                <div class="col-auto">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="bi bi-search"></i> Search
                                    </button>
                                </div>
                                <div class="col-auto">
                                    <a href="payment_requests.php" class="btn btn-secondary w-100">
                                        <i class="bi bi-arrow-counterclockwise"></i> Reset
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>


                    <?php if (empty($requests)): ?>
                        <div class="alert alert-info">No pending payment verification requests</div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Period</th>
                                        <th>Payment Date</th>
                                        <th>Student</th>
                                        <th>Varsity ID</th>
                                        <th>Mobile</th>
                                        <th>Payment Method</th>
                                        <th>Amount</th>
                                        <th>Reference</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($requests as $index => $request): ?>
                                        <tr>
                                            <td><?= $index + 1 ?></td>
                                            <td><?= date('F Y', mktime(0, 0, 0, $request['month'], 1, $request['year'])) ?></td>
                                            <td><?= date('d M Y', strtotime($request['payment_date'])) ?></td>
                                            <td><?= htmlspecialchars($request['first_name'] . ' ' . $request['last_name']) ?></td>
                                            <td><?= htmlspecialchars($request['varsity_id']) ?></td>
                                            <td><?= htmlspecialchars($request['sender_mobile'] ?? $request['mobile'] ?? 'N/A') ?></td>
                                            <td><?= htmlspecialchars($request['payment_method']) ?></td>
                                            <td>৳<?= number_format($request['amount'], 2) ?></td>
                                            <td><?= htmlspecialchars($request['reference_code'] ?? 'N/A') ?></td>
                                            <td><span class="badge bg-warning text-dark">Pending</span></td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <button
                                                        class="btn btn-sm btn-success verify-btn"
                                                        data-id="<?= $request['id'] ?>"
                                                        data-action="verify"
                                                        title="Verify Payment Request">
                                                        <i class="bi bi-check-circle"></i> Verify
                                                    </button>

                                                    <button
                                                        class="btn btn-sm btn-danger verify-btn"
                                                        data-id="<?= $request['id'] ?>"
                                                        data-action="reject"
                                                        title="Reject Payment Request">
                                                        <i class="bi bi-x-circle"></i> Reject
                                                    </button>

                                                    <?php if (!empty($request['screenshot_path'])): ?>
                                                        <button class="btn btn-sm btn-info " data-bs-toggle="modal" data-bs-target="#screenshotModal<?= $index ?>">
                                                            <i class="bi bi-image"></i> View
                                                        </button>
                                                    <?php endif; ?>

                                                    <a href="<?= BASE_URL ?>/admin/sections/payments/view.php?id=<?= $request['payment_id'] ?>"
                                                        class="btn btn-sm btn-info d-inline-flex align-items-center gap-1 small"
                                                        title="View Payments">
                                                        <i class="bi bi-eye"></i>
                                                        <span>View Payments</span>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>

                                        <?php if (!empty($request['screenshot_path'])): ?>
                                            <!-- Screenshot Modal -->
                                            <div class="modal fade" id="screenshotModal<?= $index ?>" tabindex="-1">
                                                <div class="modal-dialog modal-lg">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Payment Evidence</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body text-center">
                                                            <img src="<?= BASE_URL ?>/uploads/<?= $request['screenshot_path'] ?>" class="img-fluid" alt="Payment screenshot">
                                                            <?php if (!empty($request['notes'])): ?>
                                                                <div class="mt-3 text-start">
                                                                    <h6>Notes:</h6>
                                                                    <p><?= nl2br(htmlspecialchars($request['notes'])) ?></p>
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('.verify-btn').on('click', function() {
            const txnId = $(this).data('id');
            const action = $(this).data('action');
            const $row = $(this).closest('tr');

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
                        $row.fadeOut();
                    } else {
                        showSlideMessage(response.message, 'error');
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