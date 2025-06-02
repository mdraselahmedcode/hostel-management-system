<?php
session_start();
require_once __DIR__ . '/../../../config/config.php';
require_once BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/admin/php_files/auth_check_admin.php';

// Get Room ID
$roomId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($roomId <= 0) die("Invalid room ID.");


// Fetch Room
$stmt = $conn->prepare("
    SELECT rooms.*, room_types.default_capacity 
    FROM rooms 
    JOIN room_types ON rooms.room_type_id = room_types.id 
    WHERE rooms.id = ?
");
$stmt->bind_param("i", $roomId);
$stmt->execute();
$result = $stmt->get_result();
$room = $result->fetch_assoc();
if (!$room) die("Room not found.");
$stmt->close();

// Fetch Floors
$floors = [];
$floorSql = "SELECT id, floor_number FROM floors ORDER BY floor_number ASC";
$floorResult = $conn->query($floorSql);
if ($floorResult && $floorResult->num_rows > 0) {
    $floors = $floorResult->fetch_all(MYSQLI_ASSOC);
}

// Fetch Room Types
$roomTypes = [];
$typeSql = "SELECT id, type_name, default_capacity FROM room_types ORDER BY type_name ASC";
$typeResult = $conn->query($typeSql);
if ($typeResult && $typeResult->num_rows > 0) {
    $roomTypes = $typeResult->fetch_all(MYSQLI_ASSOC);
}

require_once BASE_PATH . '/admin/includes/header_admin.php';
?>

<div class="content container-fluid">
    <div class="row full-height">
        <?php require_once BASE_PATH . '/admin/includes/sidebar_admin.php'; ?>

        <main class="col-md-10 ms-sm-auto px-md-4 py-4">
            <a href="<?= BASE_URL . '/admin/sections/rooms/index.php' ?>" class="btn btn-secondary mb-3">Back to Room List</a>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="mb-4">Edit Room</h2>

                    <form id="editRoomForm" method="POST">
                        <input type="hidden" name="room_id" value="<?= $roomId ?>">

                        <div class="mb-3">
                            <label for="room_number" class="form-label">Room Number</label>
                            <input type="text" name="room_number" id="room_number" class="form-control"
                                value="<?= htmlspecialchars($room['room_number']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="max_capacity" class="form-label">Max Capacity</label>
                            <input type="number" name="max_capacity" id="max_capacity" class="form-control"
                                value="<?= $room['max_capacity'] ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="room_type_id" class="form-label">Room Type</label>
                            <select name="room_type_id" id="room_type_id" class="form-select" required>
                                <option value="">-- Select Room Type --</option>
                                <?php foreach ($roomTypes as $type): ?>
                                    <option value="<?= $type['id'] ?>" <?= $room['room_type_id'] == $type['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($type['type_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="floor_id" class="form-label">Floor</label>
                            <select name="floor_id" id="floor_id" class="form-select" required>
                                <option value="">-- Select Floor --</option>
                                <?php foreach ($floors as $floor): ?>
                                    <option value="<?= $floor['id'] ?>" <?= $room['floor_id'] == $floor['id'] ? 'selected' : '' ?>>
                                        Floor <?= $floor['floor_number'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">Update Room</button>
                    </form>

                    <div id="showMessage" class="mt-3"></div>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('#editRoomForm').on('submit', function (e) {
            e.preventDefault();

            const submitBtn = $(this).find('button[type="submit"]');
            submitBtn.prop('disabled', true).text('Updating...');

            $.ajax({
                url: '<?= BASE_URL . '/admin/php_files/sections/rooms/edit.php' ?>',
                method: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function (response) {
                    const msgClass = response.success ? 'success' : 'danger';
                    $('#showMessage').html(`<div class="alert alert-${msgClass}">${response.message}</div>`);

                    if (response.success) {
                        setTimeout(() => {
                            window.location.href = '<?= BASE_URL . '/admin/sections/rooms/index.php' ?>';
                        }, 2000);
                    }

                    submitBtn.prop('disabled', false).text('Update Room');
                },
                error: function () {
                    $('#showMessage').html('<div class="alert alert-danger">An error occurred. Please try again.</div>');
                    submitBtn.prop('disabled', false).text('Update Room');
                }
            });
        });
    });
</script>

<?php require_once BASE_PATH . '/admin/includes/footer_admin.php'; ?>
