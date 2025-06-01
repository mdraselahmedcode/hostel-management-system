<?php
session_start();
require_once __DIR__ . '/../../../config/config.php';
require_once BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/admin/php_files/auth_check_admin.php';

// Fetch all hostels for dropdown
$hostels = [];
$hostelSql = "SELECT id, hostel_name FROM hostels ORDER BY hostel_name ASC";
$hostelResult = $conn->query($hostelSql);
if ($hostelResult && $hostelResult->num_rows > 0) {
    $hostels = $hostelResult->fetch_all(MYSQLI_ASSOC);
}

// Check for selected hostel filter
$selectedHostelId = isset($_GET['hostel_id']) ? (int) $_GET['hostel_id'] : 0;

// Fetch floors with optional filtering
$floors = [];
$sql = "
    SELECT 
        floors.id,
        floors.floor_number,
        floors.floor_name,
        hostels.hostel_name,
        (
            SELECT COUNT(*) FROM rooms WHERE rooms.floor_id = floors.id
        ) AS number_of_rooms
    FROM floors
    LEFT JOIN hostels ON floors.hostel_id = hostels.id
";

if ($selectedHostelId > 0) {
    $sql .= " WHERE floors.hostel_id = $selectedHostelId";
}

$sql .= " ORDER BY floors.id DESC";

$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    $floors = $result->fetch_all(MYSQLI_ASSOC);
}

require_once BASE_PATH . '/admin/includes/header_admin.php';
?>

<div class="content container-fluid">
    <div class="row full-height">
        <?php require_once BASE_PATH . '/admin/includes/sidebar_admin.php'; ?>

        <main class="col-md-10 ms-sm-auto px-md-4 py-4">
            <a href="<?= BASE_URL . '/admin/dashboard_admin.php' ?>" class="btn btn-secondary mb-3">Back</a>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="mb-4">Manage Floors</h2>

                    <a href="<?= BASE_URL . '/admin/sections/floors/add.php' ?>" class="btn btn-success mb-3">+ Add New Floor</a>

                    <!-- Hostel filter -->
                    <form method="get" class="mb-3">
                        <div class="row">
                            <div class="col-md-4">
                                <select name="hostel_id" class="form-select" onchange="this.form.submit()">
                                    <option value="0">-- Filter by Hostel --</option>
                                    <?php foreach ($hostels as $hostel): ?>
                                        <option value="<?= $hostel['id'] ?>" <?= $selectedHostelId == $hostel['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($hostel['hostel_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Floor name</th>
                                    <th>Floor number</th>
                                    <th>Hostel name</th>
                                    <th>Rooms</th>
                                    <th>View rooms</th>
                                    <th>Edit</th>
                                    <th>Delete</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($floors)): ?>
                                    <tr>
                                        <td colspan="12" class="text-center">No floors found.</td>
                                    </tr>
                                <?php else: ?>  
                                    <?php foreach ($floors as $floor): ?>
                                        <tr>
                                            <td><?= $floor['id'] ?></td>
                                            <td><?= htmlspecialchars($floor['floor_name']) ?></td>
                                            <td><?= $floor['floor_number'] ?? 'N/A' ?></td>
                                            <td><?= $floor['hostel_name'] ?? 'N/A' ?></td>
                                            <td><?= $floor['number_of_rooms'] ?? 'N/A' ?></td>
                                            <td>
                                                <a href="<?= BASE_URL ?>/admin/sections/floors/edit.php?id=<?= $floor['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-danger delete-floor" data-id="<?= $floor['id'] ?>">Delete</button>
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
$(document).ready(function() {
    $('.delete-floor').on('click', function() {
        const button = $(this);
        const floorId = button.data('id');

        if (confirm('Are you sure you want to delete this floor?')) {
            button.prop('disabled', true);

            $.ajax({
                type: 'POST',
                url: '<?= BASE_URL ?>/admin/php_files/sections/floors/delete.php',
                data: { id: floorId },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        button.closest('tr').remove();
                        $("#showMessage").html('<div class="alert alert-success">' + response.message + '</div>');
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
});
</script>

<?php require_once BASE_PATH . '/admin/includes/footer_admin.php'; ?>
