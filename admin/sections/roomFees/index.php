<?php
session_start();
require_once __DIR__ . '/../../../config/config.php';
require_once BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/admin/php_files/auth_check_admin.php';

// Fetch hostels for filter dropdown
$hostels = [];
$hostelSql = "SELECT id, hostel_name FROM hostels ORDER BY hostel_name ASC";
$hostelResult = $conn->query($hostelSql);
if ($hostelResult && $hostelResult->num_rows > 0) {
    $hostels = $hostelResult->fetch_all(MYSQLI_ASSOC);
}

$selectedHostelId = isset($_GET['hostel_id']) ? (int) $_GET['hostel_id'] : 0;

// Fetch room fees
$roomFees = [];
$sql = "
    SELECT 
        room_fees.id,
        room_fees.price,
        room_fees.billing_cycle,
        room_fees.effective_from,
        room_types.type_name,
        floors.floor_number,
        hostels.hostel_name
    FROM room_types
    LEFT JOIN room_fees ON room_types.id = room_fees.room_type_id
    LEFT JOIN floors ON room_fees.floor_id = floors.id
    LEFT JOIN hostels ON floors.hostel_id = hostels.id
    WHERE 1
";

if ($selectedHostelId > 0) {
    $sql .= " AND (hostels.id = $selectedHostelId OR hostels.id IS NULL)";
}

$sql .= " ORDER BY room_fees.effective_from DESC";

$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    $roomFees = $result->fetch_all(MYSQLI_ASSOC);
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
                    <h2 class="mb-4">Manage Room Fees</h2>

                    <a href="<?= BASE_URL . '/admin/sections/roomFees/add.php' ?>" class="btn btn-success mb-3">+ Add New Fee</a>

                    <!-- Filter Form -->
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

                    <!-- Room Fees Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Room Type</th>
                                    <th>Price (৳)</th>
                                    <th>Billing Cycle</th>
                                    <th>Effective From</th>
                                    <th>Hostel</th>
                                    <th>Edit</th>
                                    <th>Delete</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($roomFees)): ?>
                                    <tr>
                                        <td colspan="8" class="text-center">No room fee records found.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php $serial = 1; ?>
                                    <?php foreach ($roomFees as $fee): ?>
                                        <tr>
                                            <td><?= $serial++ ?></td>
                                            <td><?= htmlspecialchars($fee['type_name']) ?></td>
                                            <td><?= $fee['price'] !== null ? number_format($fee['price'], 2) : '<span class="text-muted">Not set</span>' ?></td>
                                            <td><?= !empty($fee['billing_cycle']) ? ucfirst($fee['billing_cycle']) : '<span class="text-muted">N/A</span>' ?></td>
                                            <td><?= !empty($fee['effective_from']) ? date('d M Y', strtotime($fee['effective_from'])) : '<span class="text-muted">N/A</span>' ?></td>
                                            <td><?= !empty($fee['hostel_name']) ? htmlspecialchars($fee['hostel_name']) : '<span class="text-muted">N/A</span>' ?></td>
                                            <td>
                                                <a href="<?= BASE_URL ?>/admin/sections/roomFees/edit.php?id=<?= $fee['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                                            </td>
                                            <td>
                                                <a href="javascript:void(0);" class="delete-fee btn btn-sm btn-danger" data-id="<?= $fee['id'] ?>">Delete</a>
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
    $('.delete-fee').on('click', function () {
        const button = $(this);
        const feeId = button.data('id');

        if (confirm('Are you sure you want to delete this fee record?')) {
            button.prop('disabled', true);

            $.ajax({
                type: 'POST',
                url: '<?= BASE_URL ?>/admin/php_files/sections/roomFees/delete.php',
                data: { id: feeId },
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        button.closest('tr').remove();
                        $("#showMessage").html('<div class="alert alert-success">' + response.message + '</div>').fadeIn();

                        setTimeout(function () {
                            $("#showMessage").fadeOut('slow', function () {
                                $(this).html('').show();
                            });
                        }, 3000);
                    } else {
                        $("#showMessage").html('<div class="alert alert-danger">' + response.message + '</div>');
                    }
                },
                error: function () {
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
