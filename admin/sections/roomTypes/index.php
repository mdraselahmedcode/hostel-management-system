<?php
session_start();
require_once __DIR__ . '/../../../config/config.php';
require_once BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/admin/php_files/auth_check_admin.php';

// Flash message
$successMessage = $_GET['success'] ?? null;

// Fetch all room types
$roomTypes = [];
$sql = "SELECT id, type_name, description, default_capacity, buffer_limit, created_at, updated_at 
        FROM room_types ORDER BY id DESC";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    $roomTypes = $result->fetch_all(MYSQLI_ASSOC);
}

require_once BASE_PATH . '/admin/includes/header_admin.php';
?>

<div class="content container-fluid">
    <div class="row full-height">
        <?php require_once BASE_PATH . '/admin/includes/sidebar_admin.php'; ?>

        <main class="col-md-10 ms-sm-auto px-md-4 py-4">
            <a href="<?= BASE_URL ?>/admin/dashboard_admin.php" class="btn btn-secondary mb-3">Back</a>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="mb-4">Manage Room Types</h2>

                    <?php if ($successMessage): ?>
                        <div class="alert alert-success"><?= htmlspecialchars($successMessage) ?></div>
                    <?php endif; ?>

                    <a href="<?= BASE_URL ?>/admin/sections/roomTypes/add.php" class="btn btn-success mb-3">+ Add New Room Type</a>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>ID</th>
                                    <th>Type Name</th>
                                    <th>Default Capacity</th>
                                    <th>Buffer Limit</th>
                                    <th>Edit</th>
                                    <th>Delete</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($roomTypes)): ?>
                                    <tr>
                                        <td colspan="11" class="text-center">No room types found.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php $serial = 1; ?>
                                    <?php foreach ($roomTypes as $type): ?>
                                        <tr>
                                            <td><?= $serial++ ?></td>
                                            <td><?= $type['id'] ?></td>
                                            <td><?= htmlspecialchars($type['type_name']) ?></td>
                                            <td><?= (int)$type['default_capacity'] ?></td>
                                            <td><?= (int)$type['buffer_limit'] ?></td>
                                            <td>
                                                <a href="<?= BASE_URL ?>/admin/sections/roomTypes/edit.php?id=<?= $type['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                                            </td>
                                            <td>
                                                <a href="javascript:void(0);" class="btn btn-sm btn-danger delete-type" data-id="<?= $type['id'] ?>">Delete</a>
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
    $('.delete-type').on('click', function () {
        const button = $(this);
        const typeId = button.data('id');

        if (confirm('Are you sure you want to delete this room type?')) {
            button.prop('disabled', true);

            $.ajax({
                type: 'POST',
                url: '<?= BASE_URL ?>/admin/php_files/sections/roomTypes/delete.php',
                data: { id: typeId },
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        button.closest('tr').remove();
                        $("#showMessage").html('<div class="alert alert-success">' + response.message + '</div>').fadeIn();

                        setTimeout(() => {
                            $("#showMessage").fadeOut('slow', () => $(this).html('').show());
                        }, 3000);
                    } else {
                        $("#showMessage").html('<div class="alert alert-danger">' + response.message + '</div>');
                    }
                },
                error: function (xhr) {
                    console.error(xhr.responseText);
                    $("#showMessage").html('<div class="alert alert-danger">An error occurred. Please try again.</div>');
                },
                complete: function () {
                    button.prop('disabled', false);
                }
            });
        }
    });
</script>

<?php require_once BASE_PATH . '/admin/includes/footer_admin.php'; ?>
