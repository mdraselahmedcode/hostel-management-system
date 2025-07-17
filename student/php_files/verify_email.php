<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/db.php';

$token = $_GET['token'] ?? '';

function renderPage($title, $message, $alertClass = 'alert-info') {
    // Output a minimal HTML page with Bootstrap CSS from CDN
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title><?= htmlspecialchars($title) ?></title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    </head>
    <body class="bg-light d-flex align-items-center justify-content-center" style="height:100vh;">
        <div class="card shadow p-4" style="max-width: 500px; width: 100%;">
            <h2 class="mb-3"><?= htmlspecialchars($title) ?></h2>
            <div class="alert <?= htmlspecialchars($alertClass) ?>" role="alert">
                <?= $message ?>
            </div>
            <a href="<?= BASE_URL ?? '/' ?>" class="btn btn-primary">Go to Home</a>
        </div>
    </body>
    </html>
    <?php
    exit;
}

if (!$token) {
    renderPage("Verification Error", "Missing verification token.", "alert-danger");
}

$stmt = $conn->prepare("SELECT id, is_verified FROM students WHERE verification_token = ?");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();

if (!$student) {
    renderPage("Verification Error", "Invalid or expired verification token.", "alert-danger");
}

if ($student['is_verified']) {
    renderPage("Already Verified", "Your email is already verified.", "alert-success");
}

// Update verification status
$update = $conn->prepare("UPDATE students SET is_verified = 1, verification_token = NULL WHERE id = ?");
$update->bind_param("i", $student['id']);
if ($update->execute()) {
    renderPage("Verification Successful", "Email successfully verified! Please wait for admin approval.", "alert-success");
} else {
    renderPage("Verification Failed", "An error occurred during verification. Please try again later.", "alert-danger");
}
?>
