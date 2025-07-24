<?php
require_once __DIR__ . '/../../../config/config.php';
require_once BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/config/auth.php';
require_once BASE_PATH . '/includes/slide_message.php';

require_admin();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    // showSlideMessage('Invalid transaction ID.', 'danger');
    header('Location: ' . BASE_URL . '/admin/sections/payments/index.php');
    exit();
}

$txn_id = (int)$_GET['id'];

// Fetch transaction details with improved query
$query = "
    SELECT pt.*, 
           pm.display_name AS payment_method, 
           CONCAT(a.firstname, ' ', a.lastname) AS verifier_fullname
    FROM payment_transactions pt
    JOIN payment_methods pm ON pt.payment_method_id = pm.id
    LEFT JOIN admins a ON pt.verified_by = a.id
    WHERE pt.id = ?
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $txn_id);
$stmt->execute();
$result = $stmt->get_result();
$transaction = $result->fetch_assoc();
$stmt->close();

if (!$transaction) {
    header('Location: ' . BASE_URL . '/admin/sections/payments/index.php');
    exit();
}


require_once BASE_PATH . '/admin/includes/header_admin.php';
?>

<div class="content container-fluid mt-5">
    <div class="row g-4">
        <?php require_once BASE_PATH . '/admin/includes/sidebar_admin.php'; ?>

        <main class="col-md-10 ms-sm-auto px-md-4 ">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
                <h1 class="h2">Transaction Details</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="javascript:history.back()" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Back to Transactions
                    </a>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Transaction #<?= $transaction['id'] ?></h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="table-responsive">
                                <table class="table table-borderless">
                                    <tr>
                                        <th class="w-25">Payment ID</th>
                                        <td><?= htmlspecialchars($transaction['payment_id']) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Amount</th>
                                        <td class="fw-bold">৳<?= number_format($transaction['amount'], 2) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Payment Date</th>
                                        <td><?= date('F j, Y, g:i a', strtotime($transaction['payment_date'])) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Payment Method</th>
                                        <td><?= htmlspecialchars($transaction['payment_method']) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Reference Code</th>
                                        <td><?= !empty($transaction['reference_code']) ? htmlspecialchars($transaction['reference_code']) : '<span class="text-muted">—</span>' ?></td>
                                    </tr>
                                    <tr>
                                        <th>Transaction ID</th>
                                        <td><?= !empty($transaction['transaction_id']) ? htmlspecialchars($transaction['transaction_id']) : '<span class="text-muted">—</span>' ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="table-responsive">
                                <table class="table table-borderless">
                                    <tr>
                                        <th class="w-25">Receipt Number</th>
                                        <td><?= !empty($transaction['receipt_number']) ? htmlspecialchars($transaction['receipt_number']) : '<span class="text-muted">—</span>' ?></td>
                                    </tr>
                                    <tr>
                                        <th>Sender</th>
                                        <td>
                                            <?= !empty($transaction['sender_name']) ? htmlspecialchars($transaction['sender_name']) : '<span class="text-muted">—</span>' ?>
                                            <?php if (!empty($transaction['sender_mobile'])): ?>
                                                <br><small class="text-muted"><?= htmlspecialchars($transaction['sender_mobile']) ?></small>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Status</th>
                                        <td>
                                            <?php
                                            $statusClass = [
                                                'verified' => 'success',
                                                'pending' => 'warning',
                                                'rejected' => 'danger'
                                            ][$transaction['verification_status'] ?? 'secondary'];
                                            ?>
                                            <span class="badge bg-<?= $statusClass ?>">
                                                <?= ucfirst($transaction['verification_status']) ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Verified By</th>
                                        <td><?= !empty($transaction['verifier_fullname']) ? htmlspecialchars($transaction['verifier_fullname']) : '<span class="text-muted">—</span>' ?></td>
                                    </tr>
                                    <tr>
                                        <th>Created At</th>
                                        <td><?= date('F j, Y, g:i a', strtotime($transaction['created_at'])) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Last Updated</th>
                                        <td><?= date('F j, Y, g:i a', strtotime($transaction['updated_at'])) ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="card border-0 bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">Notes</h6>
                                    <div class="bg-white p-3 rounded border">
                                        <?= !empty($transaction['notes']) ? nl2br(htmlspecialchars($transaction['notes'])) : '<span class="text-muted">No notes available</span>' ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if (!empty($transaction['screenshot_path'])): ?>
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="card border-0 bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">Screenshot</h6>
                                        <div class="text-center">
                                            <a href="<?= BASE_URL ?>/uploads/<?= $transaction['screenshot_path'] ?>" data-fancybox="screenshot" data-caption="Transaction Screenshot">
                                                <img src="<?= BASE_URL ?>/uploads/<?= $transaction['screenshot_path'] ?>"
                                                    alt="Transaction Screenshot" class="img-fluid rounded border" style="max-height: 400px;">
                                            </a>
                                            <p class="text-muted mt-2">Click to enlarge</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if ($transaction['verification_status'] === 'pending'): ?>
                    <div class="card-footer bg-light d-flex justify-content-end gap-2">
                        <a href="verify_transaction.php?id=<?= $txn_id ?>&action=verify" class="btn btn-success">
                            <i class="bi bi-check-circle me-1"></i> Verify
                        </a>
                        <a href="verify_transaction.php?id=<?= $txn_id ?>&action=reject" class="btn btn-danger">
                            <i class="bi bi-x-circle me-1"></i> Reject
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</div>

<?php
require_once BASE_PATH . '/admin/includes/footer_admin.php';
?>