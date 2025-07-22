<?php
require_once __DIR__ . '/../../../../config/config.php';
require_once BASE_PATH . '/config/db.php';

$status = $_POST['status'] ?? '';
$hostel = $_POST['hostel'] ?? '';
$month = $_POST['month'] ?? '';
$year = $_POST['year'] ?? '';

// Build base query
$query = "
SELECT sp.*, s.first_name, s.last_name, s.varsity_id, h.hostel_name, r.room_number
FROM student_payments sp
JOIN students s ON sp.student_id = s.id
JOIN hostels h ON sp.hostel_id = h.id
JOIN rooms r ON sp.room_id = r.id
WHERE 1=1
";

$params = [];
$types = '';

if ($status !== '') {
    $query .= " AND sp.payment_status = ?";
    $params[] = $status;
    $types .= 's';
}
if ($hostel !== '') {
    $query .= " AND sp.hostel_id = ?";
    $params[] = $hostel;
    $types .= 'i';
}
if ($month !== '') {
    $query .= " AND sp.month = ?";
    $params[] = $month;
    $types .= 'i';
}
if ($year !== '') {
    $query .= " AND sp.year = ?";
    $params[] = $year;
    $types .= 'i';
}

$query .= " ORDER BY sp.due_date DESC";
$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

$currentDate = new DateTime();

while ($payment = $result->fetch_assoc()):
    $statusClass = 'status-' . $payment['payment_status'];
    $monthYear = date('F Y', mktime(0, 0, 0, $payment['month'], 1, $payment['year']));
    $dueDate = date('d M Y', strtotime($payment['due_date']));

    $amountDue = $payment['amount_due'];
    $isLate = false;

    $dueDateObj = new DateTime($payment['due_date']);
    $lateFeeAppliedDateObj = isset($payment['late_fee_applied_date']) && $payment['late_fee_applied_date']
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
        <a href="<?= BASE_URL ?>/admin/sections/payments/view.php?id=<?= $payment['id'] ?>" class="btn btn-sm btn-primary">
            <i class="bi bi-eye"></i>
        </a>
        <?php if ($payment['payment_status'] !== 'paid'): ?>
            <a href="verify.php?id=<?= $payment['id'] ?>" class="btn btn-sm btn-success">
                <i class="bi bi-check-circle"></i>
            </a>
        <?php endif; ?>
        <a href="receipt.php?id=<?= $payment['id'] ?>" class="btn btn-sm btn-info">
            <i class="bi bi-receipt"></i>
        </a>
    </td>
</tr>

<?php endwhile; ?>

<?php if ($result->num_rows === 0): ?>
<tr><td colspan="11" class="text-center">No records found.</td></tr>
<?php endif; ?>
