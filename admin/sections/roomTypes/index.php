<?php
session_start();
require_once __DIR__ . '/../../../config/config.php';
require_once BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/admin/php_files/auth_check_admin.php';

// Flash message
$successMessage = $_GET['success'] ?? null;

// For Filter by selecting hostel id
$filterHostelId = $_GET['hostel_id'] ?? null;


$hostels = [];
$hostelResult = $conn->query("SELECT id, hostel_name FROM hostels ORDER BY hostel_name");
if ($hostelResult && $hostelResult->num_rows > 0) {
    $hostels = $hostelResult->fetch_all(MYSQLI_ASSOC);
}



// Fetch all room types
$roomTypes = [];
$sql = "SELECT 
    room_types.id, 
    room_types.type_name, 
    room_types.description,
    room_types.default_capacity,
    room_types.buffer_limit,
    room_types.hostel_id,
    hostels.hostel_name,
    room_types.created_at,
    room_types.updated_at
FROM room_types
LEFT JOIN hostels ON hostels.id = room_types.hostel_id
";
// filtering fetching data based on selected hostel id
if ($filterHostelId) {
    $sql .= " WHERE room_types.hostel_id = " . intval($filterHostelId);
}
$sql .= " ORDER BY created_at DESC";

$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    $roomTypes = $result->fetch_all(MYSQLI_ASSOC);
}

require_once BASE_PATH . '/admin/includes/header_admin.php';
?>

<div class="content container-fluid mt-5">
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
                        <!-- Filter by hostel -->
                        <form method="GET" class="mb-3 col-md-4">
                            <label for="hostel_id" class="form-label">Filter by Hostel:</label>
                            <select name="hostel_id" id="hostel_id" class="form-select" onchange="this.form.submit()">
                                <option value="">-- All Hostels --</option>
                                <?php foreach ($hostels as $hostel): ?>
                                    <option value="<?= $hostel['id'] ?>" <?= ($filterHostelId == $hostel['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($hostel['hostel_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </form>

                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                            <table class="table table-bordered table-striped mb-0">
                                <thead class="table-dark text-center">
                                    <tr>
                                        <th>#</th>
                                        <th>Type Name</th>
                                        <th>Default Capacity</th>
                                        <th>Buffer Limit</th>
                                        <th>Hostel Name</th>
                                        <th>Edit</th>
                                        <th>Delete</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($roomTypes)): ?>
                                        <tr>
                                            <td colspan="8" class="text-center">No room types found.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php $serial = 1; ?>
                                        <?php foreach ($roomTypes as $type): ?>
                                            <tr>
                                                <td><?= $serial++ ?></td>
                                                <td><?= htmlspecialchars(ucfirst(strtolower($type['type_name']))) ?></td>
                                                <td><?= (int)$type['default_capacity'] ?></td>
                                                <td><?= (int)$type['buffer_limit'] ?></td>
                                                <td><?= htmlspecialchars($type['hostel_name']) ?></td>
                                                <td>
                                                    <a href="<?= BASE_URL ?>/admin/sections/roomTypes/edit.php?roomTypeId=<?= $type['id'] ?>&hostel_id=<?= $type['hostel_id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                                                </td>
                                                <td>
                                                    <a href="javascript:void(0);" class="btn btn-sm btn-danger delete-type" data-id="<?= $type['id'] ?>" data-hostel-id="<?= $type['hostel_id'] ?>">Delete</a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <div id="showMessage" class="mt-3"></div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
    $('.delete-type').on('click', function() {
        const button = $(this);
        const typeId = button.data('id');
        const hostelId = button.data('hostel-id');

        if (confirm('Are you sure you want to delete this room type?')) {
            button.prop('disabled', true);

            $.ajax({
                type: 'POST',
                url: '<?= BASE_URL ?>/admin/php_files/sections/roomTypes/delete.php',
                data: {
                    id: typeId,
                    hostel_id: hostelId
                },
                dataType: 'json',
                success: function(response) {
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
                error: function(xhr) {
                    console.error(xhr.responseText);
                    $("#showMessage").html('<div class="alert alert-danger">An error occurred. Please try again.</div>');
                },
                complete: function() {
                    button.prop('disabled', false);
                }
            });
        }
    });
</script>

<?php require_once BASE_PATH . '/admin/includes/footer_admin.php'; ?>