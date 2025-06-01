<?php
session_start();
require_once __DIR__ . '/../../../config/config.php';
require_once BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/admin/php_files/auth_check_admin.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Invalid Hostel ID.');
}

$hostelId = intval($_GET['id']);

// Fetch single hostel details
$sql = "
    SELECT 
        hostels.*, 
        admins.firstname AS incharge_firstname,
        admins.lastname AS incharge_lastname,
        addresses.*,
        countries.country_name,
        COUNT(floors.id) AS number_of_floors,
        (
            SELECT COUNT(*) 
            FROM rooms 
            WHERE rooms.floor_id IN (
                SELECT id FROM floors WHERE hostel_id = hostels.id
            )
        ) AS number_of_rooms
    FROM hostels
    LEFT JOIN admins ON hostels.hostel_incharge_id = admins.id
    LEFT JOIN addresses ON hostels.address_id = addresses.id
    LEFT JOIN countries ON addresses.country_id = countries.id
    LEFT JOIN floors ON hostels.id = floors.hostel_id
    WHERE hostels.id = $hostelId
    GROUP BY hostels.id
    LIMIT 1;
";

$result = $conn->query($sql);
$hostel = $result->fetch_assoc();

if (!$hostel) {
    die("Hostel not found.");
}

require_once BASE_PATH . '/admin/includes/header_admin.php';
?>



<div class="content container py-4">
    <a href="<?= BASE_URL ?>/admin/sections/hostels/index.php" class="btn btn-outline-secondary mb-4">
        ← Back to Hostel List
    </a>

    <div class="card shadow">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0"><?= htmlspecialchars($hostel['hostel_name']) ?> Details</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <!-- Left Column -->
                <div class="col-md-6">
                    <h5 class="text-muted">Basic Info</h5>
                    <p><strong>Type:</strong> <?= ucfirst($hostel['hostel_type']) ?></p>
                    <p><strong>Capacity:</strong> <?= $hostel['capacity'] ?></p>
                    <p><strong>Floors:</strong> <?= $hostel['number_of_floors'] ?> </p>
                    <p><strong>Contact:</strong> <?= $hostel['contact_number'] ?></p>
                    <p><strong>Amenities:</strong> <?= htmlspecialchars($hostel['amenities']) ?: 'N/A' ?></p>
                </div>

                <!-- Right Column -->
                <div class="col-md-6">
                    <h5 class="text-muted">Incharge Info</h5>
                    <p><strong>Name:</strong> <?= htmlspecialchars($hostel['incharge_firstname'] . ' ' . $hostel['incharge_lastname']) ?></p>
                </div>
            </div>

            <hr class="my-4">

            <h5 class="text-muted">Full Address</h5>
            <p>
                <?= htmlspecialchars(
                    implode(', ', array_filter([
                        $hostel['house_no'] ?? '',
                        $hostel['street'] ?? '',
                        $hostel['village'] ?? '',
                        $hostel['sub_district'] ?? '',
                        $hostel['district'] ?? '',
                        $hostel['division'] ?? '',
                        $hostel['state'] ?? '',
                        $hostel['postalcode'] ?? '',
                        $hostel['country_name'] ?? '',
                        $hostel['detail'] ?? ''
                    ]))
                ) ?>
            </p>
        </div>
    </div>
</div>


<?php
require_once BASE_PATH . '/admin/includes/footer_admin.php';
?>
