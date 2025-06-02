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

// Filters
$selectedHostelId = isset($_GET['hostel_id']) ? (int) $_GET['hostel_id'] : 0;
$selectedFloorId = isset($_GET['floor_id']) ? (int) $_GET['floor_id'] : 0;

// Fetch floors if hostel is selected
$floors = [];
if ($selectedHostelId > 0) {
    $floorSql = "SELECT id, floor_number FROM floors WHERE hostel_id = $selectedHostelId ORDER BY floor_number ASC";
    $floorResult = $conn->query($floorSql);
    if ($floorResult && $floorResult->num_rows > 0) {
        $floors = $floorResult->fetch_all(MYSQLI_ASSOC);
    }
}

// Fetch rooms
$rooms = [];
$sql = "
    SELECT 
        rooms.id,
        rooms.room_number,
        rooms.max_capacity,
        room_types.type_name,
        room_types.default_capacity,
        floors.floor_number,
        floors.floor_name,
        hostels.hostel_name,
        (
            SELECT COUNT(*) FROM students WHERE students.room_id = rooms.id
        ) AS current_occupants
    FROM rooms
    LEFT JOIN room_types ON rooms.room_type_id = room_types.id
    LEFT JOIN floors ON rooms.floor_id = floors.id
    LEFT JOIN hostels ON floors.hostel_id = hostels.id
    WHERE 1
";

if ($selectedHostelId > 0) {
    $sql .= " AND hostels.id = $selectedHostelId";
}
if ($selectedFloorId > 0) {
    $sql .= " AND floors.id = $selectedFloorId";
}

$sql .= " ORDER BY rooms.id DESC";

$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    $rooms = $result->fetch_all(MYSQLI_ASSOC);
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
                    <h2 class="mb-4">Manage Rooms</h2>

                    <a href="<?= BASE_URL . '/admin/sections/rooms/add.php' ?>" class="btn btn-success mb-3">+ Add New Room</a>

                    <!-- Filter Form -->
                    <form method="get" class="mb-3">
                        <div class="row">
                            <!-- Hostel Dropdown -->
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

                            <!-- Floor Dropdown (only if hostel is selected) -->
                            <?php if (!empty($floors)): ?>
                                <div class="col-md-4">
                                    <select name="floor_id" class="form-select" onchange="this.form.submit()">
                                        <option value="0">-- Filter by Floor --</option>
                                        <?php foreach ($floors as $floor): ?>
                                            <option value="<?= $floor['id'] ?>" <?= $selectedFloorId == $floor['id'] ? 'selected' : '' ?>>
                                                Floor <?= htmlspecialchars($floor['floor_number']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            <?php endif; ?>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th> <!-- Serial Number Column -->
                                    <th>ID</th>
                                    <th>Room Number</th>
                                    <th>Default Capacity</th>
                                    <th>Max Capacity</th>
                                    <th>Current Occupants</th>
                                    <th>Available Seat</th>
                                    <th>Room Type</th>
                                    <th>Floor</th>
                                    <th>Floor Name</th>
                                    <th>Hostel Name</th>
                                    <th>Edit</th>
                                    <th>Delete</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($rooms)): ?>
                                    <tr>
                                        <td colspan="13" class="text-center">No rooms found.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php $serial = 1; ?>
                                    <?php foreach ($rooms as $room): ?>
                                        <tr>
                                            <td><?= $serial++ ?></td>
                                            <td><?= $room['id'] ?></td>
                                            <td><?= htmlspecialchars($room['room_number']) ?></td>
                                            <td><?= $room['default_capacity'] ?? '0' ?></td>
                                            <td><?= $room['max_capacity'] ?></td>
                                            <td><?= $room['current_occupants'] ?? '0' ?></td>
                                            <?php
                                            $capacity = isset($room['default_capacity']) ? (int)$room['default_capacity'] : 0;
                                            $occupants = isset($room['current_occupants']) ? (int)$room['current_occupants'] : 0;
                                            ?>
                                            <td><?= $capacity - $occupants ?></td>
                                            <td><?= $room['type_name'] ?? 'N/A' ?></td>
                                            <td><?= $room['floor_number'] ?? 'N/A' ?></td>
                                            <td><?= $room['floor_name'] ?? 'N/A' ?></td>
                                            <td><?= $room['hostel_name'] ?? 'N/A' ?></td>
                                            <td>
                                                <a href="<?= BASE_URL ?>/admin/sections/rooms/edit.php?id=<?= $room['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                                            </td>
                                            <td>
                                                <a href="javascript:void(0);" class="delete-room btn btn-sm btn-danger" data-id="<?= $room['id'] ?>">Delete</a>
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
    $('.delete-room').on('click', function() {
        const button = $(this);
        const roomId = button.data('id');
        console.log("Room ID:", roomId); // Debug

        if (confirm('Are you sure you want to delete this room?')) {
            button.prop('disabled', true);

            $.ajax({
                type: 'POST',
                url: '<?= BASE_URL ?>/admin/php_files/sections/rooms/delete.php',
                data: {
                    id: roomId
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        button.closest('tr').remove();
                        setTimeout(function() {
                            $("#showMessage").html('<div class="alert alert-success">' + response.message + '</div>');

                            // Hide the message after 3 seconds
                            setTimeout(function() {
                                $("#showMessage").fadeOut('slow', function() {
                                    $(this).html('').show(); // Clear and reset visibility
                                });
                            }, 3000);

                        }, 300); // Initial delay before showing the message
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