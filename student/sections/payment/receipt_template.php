<!-- <?php
// student/sections/payment/receipt_template.php

require_once __DIR__ . '/../../../config/config.php'; 
include BASE_PATH . '/config/db.php'; 

$transaction_id = $transaction_id ?? null;

if (!$transaction_id) {
    echo "<h3>Invalid transaction ID</h3>";
    return;
}

// Escape transaction_id to prevent SQL injection (you can also use prepared statements)
$transaction_id = (int) $transaction_id;

// Run the query
$sql = "
    SELECT pt.*, s.name AS student_name
    FROM payment_transactions pt
    JOIN students s ON pt.student_id = s.id
    WHERE pt.id = $transaction_id
";

$result = $conn->query($sql);

if (!$result || $result->num_rows === 0) {
    echo "<h3>No data found</h3>";
    return;
}

$data = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payment Receipt</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        .receipt { border: 1px solid #000; padding: 20px; width: 100%; }
        h2 { text-align: center; }
    </style>
</head>
<body>
    <div class="receipt">
        <h2>Payment Receipt</h2>
        <p><strong>Student Name:</strong> <?= htmlspecialchars($data['student_name']) ?></p>
        <p><strong>Transaction ID:</strong> <?= $data['id'] ?></p>
        <p><strong>Amount:</strong> <?= $data['amount'] ?></p>
        <p><strong>Date:</strong> <?= $data['created_at'] ?></p>
    </div>
</body>
</html> -->
