<?php
require_once __DIR__ . '/../../../config/config.php';
require_once BASE_PATH . '/config/db.php';
include BASE_PATH . '/includes/slide_message.php';
require_once BASE_PATH . '/config/auth.php';

require_admin();

// Fetch all admin types for filter dropdown
$adminTypes = [];
$typeSql = "SELECT id, type_name FROM admin_types ORDER BY type_name ASC";
$typeResult = $conn->query($typeSql);
if ($typeResult && $typeResult->num_rows > 0) {
    $adminTypes = $typeResult->fetch_all(MYSQLI_ASSOC);
}

// Filter
$selectedTypeId = isset($_GET['type_id']) ? (int) $_GET['type_id'] : 0;

// Fetch admins
$admins = [];
$sql = "
    SELECT 
        admins.id,
        admins.firstname,
        admins.lastname,
        admins.email,
        admin_types.type_name,
        admins.created_at,
        admins.updated_at
    FROM admins
    LEFT JOIN admin_types ON admins.admin_type_id = admin_types.id
    WHERE 1
";

if ($selectedTypeId > 0) {
    $sql .= " AND admins.admin_type_id = $selectedTypeId";
}

$sql .= " ORDER BY admins.id DESC";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    $admins = $result->fetch_all(MYSQLI_ASSOC);
}

require_once BASE_PATH . '/admin/includes/header_admin.php';
?>

<div class="content container-fluid mt-5">
    <div class="row full-height">
        <?php require_once BASE_PATH . '/admin/includes/sidebar_admin.php'; ?>

        <main class="col-md-10 ms-sm-auto px-md-4 py-4">
            <a href="<?= BASE_URL . '/admin/dashboard.php' ?>" class="btn btn-secondary mb-3 ">Back</a>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="mb-4">Manage Admins</h2>

                    <a href="<?= BASE_URL . '/admin/sections/admins/add.php' ?>" class="btn btn-success mb-3">+ Add New Admin</a>

                    <!-- Filter Form -->
                    <form method="get" class="mb-3">
                        <div class="row">
                            <div class="col-md-4">
                                <select name="type_id" class="form-select" onchange="this.form.submit()">
                                    <option value="0">-- Filter by Admin Type --</option>
                                    <?php foreach ($adminTypes as $type): ?>
                                        <option value="<?= $type['id'] ?>" <?= $selectedTypeId == $type['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($type['type_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-bordered table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>First Name</th>
                                    <th>Last Name</th>
                                    <th>Email</th>
                                    <th>Admin Type</th>
                                    <th>Created At</th>
                                    <th>Updated At</th>
                                    <th>Edit</th>
                                    <th>Delete</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($admins)): ?>
                                    <tr>
                                        <td colspan="10" class="text-center">No admins found.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php $serial = 1; ?>
                                    <?php foreach ($admins as $admin): ?>
                                        <tr>
                                            <td><?= $serial++ ?></td>
                                            <td><?= htmlspecialchars($admin['firstname']) ?></td>
                                            <td><?= htmlspecialchars($admin['lastname']) ?></td>
                                            <td><?= htmlspecialchars($admin['email']) ?></td>
                                            <td><?= $admin['type_name'] ?? 'N/A' ?></td>
                                            <td><?= date('F j, Y, g:i A', strtotime($admin['created_at'])) ?></td>
                                            <td><?= date('F j, Y, g:i A', strtotime($admin['updated_at'])) ?></td>
                                            <td>
                                                <a href="<?= BASE_URL ?>/admin/sections/admins/edit.php?id=<?= $admin['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                                            </td>
                                            <td>
                                                <a href="javascript:void(0);" class="delete-admin btn btn-sm btn-danger" data-id="<?= $admin['id'] ?>">Delete</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                        <div id="showMessage" class="mt-3"></div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
    $('.delete-admin').on('click', function() {
        const button = $(this);
        const adminId = button.data('id');

        if (confirm('Are you sure you want to delete this admin?')) {
            button.prop('disabled', true);

            $.ajax({
                type: 'POST',
                url: '<?= BASE_URL ?>/admin/php_files/sections/admins/delete.php',
                data: {
                    id: adminId
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        button.closest('tr').remove();
                        showSlideMessage(response.message, 'success');
                    } else {
                        showSlideMessage(response.message, 'danger');
                    }
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                    showSlideMessage('An error occurred. Please try again.', 'danger');
                },
                complete: function() {
                    button.prop('disabled', false);
                }
            });
        }
    });
</script>


<?php require_once BASE_PATH . '/admin/includes/footer_admin.php'; ?>