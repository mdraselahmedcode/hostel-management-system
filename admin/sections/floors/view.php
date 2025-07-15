<?php
session_start();
require_once __DIR__ . '/../../../config/config.php';
require_once BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/admin/php_files/auth_check_admin.php';

// Validate floor ID
$floorId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($floorId <= 0) {
    header('Location: index.php');
    exit;
}

// Fetch floor info
$floor = null;
$sqlFloor = "
    SELECT 
        floors.id,
        floors.floor_number,
        floors.floor_name,
        hostels.hostel_name
    FROM floors
    LEFT JOIN hostels ON floors.hostel_id = hostels.id
    WHERE floors.id = $floorId
";
$resultFloor = $conn->query($sqlFloor);
if ($resultFloor && $resultFloor->num_rows > 0) {
    $floor = $resultFloor->fetch_assoc();
} else {
    header('Location: index.php');
    exit;
}

// Fetch rooms on this floor
$rooms = [];
$sqlRooms = "
    SELECT 
        rooms.id,
        rooms.room_number,
        rooms.max_capacity,
        room_types.type_name AS room_type
    FROM rooms
    LEFT JOIN room_types ON rooms.room_type_id = room_types.id
    WHERE rooms.floor_id = $floorId
    ORDER BY rooms.room_number ASC
";
$resultRooms = $conn->query($sqlRooms);
if ($resultRooms && $resultRooms->num_rows > 0) {
    $rooms = $resultRooms->fetch_all(MYSQLI_ASSOC);
}

require_once BASE_PATH . '/admin/includes/header_admin.php';
?>

<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>

<div class="content container-fluid mt-5">
    <div class="row">
        <?php require_once BASE_PATH . '/admin/includes/sidebar_admin.php'; ?>

        <main class="col-md-10 ms-sm-auto px-md-4 py-4">

            <!-- Back Button -->
            <a href="index.php" class="btn btn-outline-secondary mb-4">
                <i class="bi bi-arrow-left"></i> Back to Floors
            </a>

            <!-- Floor Info Card -->
            <div class="card shadow-sm mb-4 border-primary">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">🧱 Floor Information</h4>
                </div>
                <div class="card-body">
                    <div class="row gy-3">
                        <div class="col-md-6">
                            <strong>Floor Name:</strong> <?= htmlspecialchars($floor['floor_name']) ?>
                        </div>
                        <div class="col-md-6">
                            <strong>Floor Number:</strong> <?= $floor['floor_number'] ?>
                        </div>
                        <div class="col-md-6">
                            <strong>Hostel:</strong> <?= htmlspecialchars($floor['hostel_name']) ?>
                        </div>
                        <div class="col-md-6">
                            <strong>Floor ID:</strong> <?= $floor['id'] ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Rooms Table -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">🛏️ Rooms on this Floor</h5>
                    <span class="badge bg-light text-dark">Total Rooms: <?= count($rooms) ?></span>
                </div>
                <div class="card-body">

                    <?php if (empty($rooms)): ?>
                        <div class="alert alert-info">
                            No rooms found for this floor.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                            <table class="table table-bordered table-hover align-middle mb-0">
                                <thead class="table-dark text-center">
                                    <tr>
                                        <th>Sl No</th>
                                        <th>Room Number</th>
                                        <th>Room Type</th>
                                        <th>Max Capacity</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($rooms as $index => $room): ?>
                                        <tr class="text-center">
                                            <td><?= $index + 1 ?></td>
                                            <td><?= htmlspecialchars($room['room_number']) ?></td>
                                            <td><?= htmlspecialchars($room['room_type'] ?? 'N/A') ?></td>
                                            <td><?= $room['max_capacity'] ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>

                </div>
            </div>
        </main>
    </div>
</div>

<?php require_once BASE_PATH . '/admin/includes/footer_admin.php'; ?>
