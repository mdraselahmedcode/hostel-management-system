<?php
require_once __DIR__ . '/../../../config/config.php';
require_once BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/config/auth.php';
require_once BASE_PATH . '/includes/slide_message.php';

require_admin();

$payment_id = isset($_GET['payment_id']) ? intval($_GET['payment_id']) : 0;

// Fetch payment record
$stmt = $conn->prepare("SELECT sp.*, s.first_name, s.last_name FROM student_payments sp
                        JOIN students s ON sp.student_id = s.id
                        WHERE sp.id = ?");
$stmt->bind_param("i", $payment_id);
$stmt->execute();
$payment = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$payment) {
    die("Payment record not found.");
}

// Handle form submission
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = $_POST['amount'];
    $payment_date = $_POST['payment_date'];
    $payment_method_id = $_POST['payment_method_id'];
    $reference_code = $_POST['reference_code'];
    $transaction_id = $_POST['transaction_id'];
    $receipt_number = $_POST['receipt_number'];
    $sender_mobile = $_POST['sender_mobile'];
    $sender_name = $_POST['sender_name'];
    $notes = $_POST['notes'];

    if ($amount <= 0) $errors[] = "Amount must be greater than zero.";
    if (empty($payment_date)) $errors[] = "Payment date is required.";

    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO payment_transactions 
            (payment_id, amount, payment_date, payment_method_id, reference_code, transaction_id, receipt_number,
             sender_mobile, sender_name, notes)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param(
            "idisssssss",
            $payment_id,
            $amount,
            $payment_date,
            $payment_method_id,
            $reference_code,
            $transaction_id,
            $receipt_number,
            $sender_mobile,
            $sender_name,
            $notes
        );

        if ($stmt->execute()) {
            header("Location: view_payment.php?id=" . $payment_id . "&success=1");
            exit;
        } else {
            $errors[] = "Failed to add transaction.";
        }

        $stmt->close();
    }
}

// Fetch payment methods
$methods = $conn->query("SELECT id, display_name FROM payment_methods WHERE active = 1")->fetch_all(MYSQLI_ASSOC);

require_once BASE_PATH . '/admin/includes/header_admin.php';
?>

<div class="content container-fluid mt-5">
    <div class="row full-height">
        <?php require_once BASE_PATH . '/admin/includes/sidebar_admin.php'; ?>

        <main class="col-md-10 ms-sm-auto px-md-4 py-4" style="overflow-y: auto; max-height: calc(100vh - 95px)">
            <a href="javascript:history.back()" class="btn btn-secondary mb-3">Back</a>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h3 class="mb-4">
                        Add Payment for <?= htmlspecialchars($payment['first_name'] . ' ' . $payment['last_name']) ?> —
                        <?= date("F Y", mktime(0, 0, 0, $payment['month'], 1, $payment['year'])) ?>
                    </h3>

                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($errors as $e): ?>
                                    <li><?= htmlspecialchars($e) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form method="POST" class="card shadow p-4">
                        <div class="mb-3">
                            <label for="amount" class="form-label">Amount Paid (TK)</label>
                            <input type="number" step="0.01" name="amount" id="amount" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="payment_date" class="form-label">Payment Date</label>
                            <input type="datetime-local" name="payment_date" id="payment_date" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="payment_method_id" class="form-label">Payment Method</label>
                            <select name="payment_method_id" id="payment_method_id" class="form-select" required>
                                <option value="">-- Select Method --</option>
                                <?php foreach ($methods as $method): ?>
                                    <option value="<?= $method['id'] ?>"><?= htmlspecialchars($method['display_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="reference_code" class="form-label">Reference Code</label>
                            <input type="text" name="reference_code" id="reference_code" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label for="transaction_id" class="form-label">Transaction ID</label>
                            <input type="text" name="transaction_id" id="transaction_id" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label for="receipt_number" class="form-label">Receipt Number</label>
                            <input type="text" name="receipt_number" id="receipt_number" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label for="sender_mobile" class="form-label">Sender Mobile</label>
                            <input type="text" name="sender_mobile" id="sender_mobile" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label for="sender_name" class="form-label">Sender Name</label>
                            <input type="text" name="sender_name" id="sender_name" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea name="notes" id="notes" rows="3" class="form-control"></textarea>
                        </div>

                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-circle"></i> Submit Payment
                        </button>
                        <a href="view_payment.php?id=<?= $payment_id ?>" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </main>
    </div>
</div>

<?php require_once BASE_PATH . '/admin/includes/footer_admin.php'; ?>
