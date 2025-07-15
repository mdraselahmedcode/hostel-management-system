<?php
session_start();
require_once __DIR__ . '/../../../config/config.php';
require_once BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/admin/php_files/auth_check_admin.php';

// Check if admin ID is provided and valid
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$adminId = (int) $_GET['id'];

// Fetch all admin types for dropdown
$adminTypes = [];
$typeSql = "SELECT id, type_name FROM admin_types ORDER BY type_name ASC";
$typeResult = $conn->query($typeSql);
if ($typeResult && $typeResult->num_rows > 0) {
    $adminTypes = $typeResult->fetch_all(MYSQLI_ASSOC);
}

// Fetch existing admin data
$stmt = $conn->prepare("SELECT id, firstname, lastname, email, admin_type_id FROM admins WHERE id = ?");
$stmt->bind_param('i', $adminId);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    header("Location: index.php");
    exit;
}
$admin = $result->fetch_assoc();
$stmt->close();

require_once BASE_PATH . '/admin/includes/header_admin.php';
?>

<div class="content container-fluid mt-5">
    <div class="row full-height">
        <?php require_once BASE_PATH . '/admin/includes/sidebar_admin.php'; ?>

        <main class="col-md-10 ms-sm-auto px-md-4 py-4">
            <a href="index.php" class="btn btn-secondary mb-3">Back</a>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="mb-4">Edit Admin</h2>


                    <form id="editAdminForm" novalidate>
                        <input type="hidden" name="id" id="adminId" value="<?= $adminId ?>">

                        <div class="mb-3">
                            <label for="firstname" class="form-label">First Name</label>
                            <input type="text" name="firstname" id="firstname" class="form-control" value="<?= htmlspecialchars($admin['firstname']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="lastname" class="form-label">Last Name</label>
                            <input type="text" name="lastname" id="lastname" class="form-control" value="<?= htmlspecialchars($admin['lastname']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" id="email" class="form-control" value="<?= htmlspecialchars($admin['email']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="admin_type_id" class="form-label">Admin Type</label>
                            <select name="admin_type_id" id="admin_type_id" class="form-select" required>
                                <option value="">-- Select Admin Type --</option>
                                <?php foreach ($adminTypes as $type): ?>
                                    <option value="<?= $type['id'] ?>" <?= $type['id'] == $admin['admin_type_id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($type['type_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary mb-2">Update Admin</button>
                    </form>
                    <div id="showMessage"></div>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
    document.getElementById('editAdminForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const form = e.target;
        const messageDiv = document.getElementById('showMessage');
        messageDiv.innerHTML = '';

        const formData = new FormData(form);

        fetch('<?= BASE_URL . '/admin/php_files/sections/admins/edit.php' ?>', {
                method: 'POST',
                body: formData,
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    messageDiv.innerHTML = `<div class="alert alert-success">${data.message}</div>`;

                    // Wait 2 seconds, fade out the message, then redirect
                    setTimeout(() => {
                        messageDiv.querySelector('.alert').classList.add('fade');
                        setTimeout(() => {
                            window.location.href = 'index.php';
                        }, 500); // wait for fade animation
                    }, 2000);

                } else if (data.errors) {
                    const errorList = data.errors.map(err => `<li>${err}</li>`).join('');
                    messageDiv.innerHTML = `<div class="alert alert-danger"><ul>${errorList}</ul></div>`;
                } else {
                    messageDiv.innerHTML = `<div class="alert alert-danger">An unknown error occurred.</div>`;
                }
            })
            .catch(error => {
                messageDiv.innerHTML = `<div class="alert alert-danger">Network error. Please try again.</div>`;
                console.error('Error:', error);
            });
    });
</script>


<?php require_once BASE_PATH . '/admin/includes/footer_admin.php'; ?>