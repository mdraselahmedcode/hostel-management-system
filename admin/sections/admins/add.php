<?php
session_start();
require_once __DIR__ . '/../../../config/config.php';
require_once BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/admin/php_files/auth_check_admin.php';

// Fetch admin types for dropdown
$adminTypes = [];
$typeSql = "SELECT id, type_name FROM admin_types ORDER BY type_name ASC";
$typeResult = $conn->query($typeSql);
if ($typeResult && $typeResult->num_rows > 0) {
    $adminTypes = $typeResult->fetch_all(MYSQLI_ASSOC);
}

$errors = [];
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstname = trim($_POST['firstname']);
    $lastname = trim($_POST['lastname']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $adminTypeId = (int) $_POST['admin_type_id'];

    // Validation
    if (empty($firstname)) $errors[] = "First name is required.";
    if (empty($lastname)) $errors[] = "Last name is required.";
    if (empty($email)) $errors[] = "Email is required.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email format.";
    if (empty($password)) $errors[] = "Password is required.";
    if (strlen($password) < 6) $errors[] = "Password must be at least 6 characters.";
    if ($adminTypeId <= 0) $errors[] = "Please select a valid admin type.";

    // Check for existing email
    $stmt = $conn->prepare("SELECT id FROM admins WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $errors[] = "Email already exists.";
    }
    $stmt->close();

    if (empty($errors)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO admins (firstname, lastname, email, password, admin_type_id, created_at, updated_at) VALUES (?, ?, ?, ?, ?, NOW(), NOW())");
        $stmt->bind_param("ssssi", $firstname, $lastname, $email, $hashedPassword, $adminTypeId);

        if ($stmt->execute()) {
            $success = "Admin added successfully.";
        } else {
            $errors[] = "Error adding admin. Please try again.";
        }
        $stmt->close();
    }
}

require_once BASE_PATH . '/admin/includes/header_admin.php';
?>

<div class="content container-fluid mt-5">
    <div class="row full-height">
        <?php require_once BASE_PATH . '/admin/includes/sidebar_admin.php'; ?>

        <main class="col-md-10 ms-sm-auto px-md-4 py-4">
            <a href="<?= BASE_URL . '/admin/sections/admins/index.php' ?>" class="btn btn-secondary mb-3 mt-2">Back to Admin List</a>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="mb-4">Add New Admin</h2>

                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                    <li><?= htmlspecialchars($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <?php if ($success): ?>
                        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                    <?php endif; ?>

                    <form id="addAdminForm">
                        <div class="mb-3">
                            <label for="firstname" class="form-label">First Name</label>
                            <input type="text" name="firstname" id="firstname" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="lastname" class="form-label">Last Name</label>
                            <input type="text" name="lastname" id="lastname" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" id="email" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" name="password" id="password" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="admin_type_id" class="form-label">Admin Type</label>
                            <select name="admin_type_id" id="admin_type_id" class="form-select" required>
                                <option value="">-- Select Admin Type --</option>
                                <?php foreach ($adminTypes as $type): ?>
                                    <option value="<?= $type['id'] ?>">
                                        <?= htmlspecialchars($type['type_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success mb-3 mb-2">Add Admin</button>
                    </form>
                    <div class="showMessage"></div>
                </div>
            </div>
        </main>
    </div>
</div>


<script>
    $(document).ready(function() {
        $('#addAdminForm').on('submit', function(e) {
            e.preventDefault();

            // Clear previous messages
            $('.showMessage').html('');

            const formData = $(this).serialize();

            $.ajax({
                url: '<?= BASE_URL . '/admin/php_files/sections/admins/add.php' ?>',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('.showMessage').html(
                            `<div class="alert alert-success">${response.message}</div>`
                        );
                        $('#addAdminForm')[0].reset();

                        // Disappear message and redirect after 2 seconds
                        setTimeout(() => {
                            $('.showMessage').fadeOut('slow', function() {
                                window.location.href = 'index.php';
                            });
                        }, 2000);
                    } else if (response.errors && response.errors.length > 0) {
                        const errorHtml = response.errors.map(err => `<li>${err}</li>`).join('');
                        $('.showMessage').html(
                            `<div class="alert alert-danger"><ul>${errorHtml}</ul></div>`
                        );
                    } else {
                        $('.showMessage').html(
                            `<div class="alert alert-danger">An unexpected error occurred.</div>`
                        );
                    }
                },
                error: function() {
                    $('.showMessage').html(
                        `<div class="alert alert-danger">AJAX request failed. Please try again.</div>`
                    );
                }
            });
        });
    });
</script>



<?php require_once BASE_PATH . '/admin/includes/footer_admin.php'; ?>