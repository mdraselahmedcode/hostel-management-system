<?php
require_once __DIR__ . '/../../../config/config.php';
require_once BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/config/auth.php';

require_admin();

// Fetch all hostels
$hostels = [];
$sql = "
        SELECT 
    hostels.*, 
    admins.firstname AS incharge_firstname,
    admins.lastname AS incharge_lastname,
    addresses.country_id,
    countries.country_name,
    addresses.state,
    addresses.division,
    addresses.district,
    addresses.sub_district,
    addresses.village,
    addresses.postalcode,
    addresses.street,
    addresses.house_no,
    addresses.detail,
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
GROUP BY hostels.id
ORDER BY hostels.created_at DESC;
";

$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    $hostels = $result->fetch_all(MYSQLI_ASSOC);
}

require_once BASE_PATH . '/admin/includes/header_admin.php';
?>

<div class="content container-fluid mt-5">
    <div class="row full-height">
        <!-- Sidebar -->
        <?php
        require_once BASE_PATH . '/admin/includes/sidebar_admin.php';
        ?>

        <!-- Main Content -->
        <main class="col-md-10 ms-sm-auto px-md-4 py-4">
            <!-- back button -->
            <a href="javascript:history.back()" class="btn btn-secondary mb-3">Back</a>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="mb-4">Hostel Management</h2>

                    <a href="<?= BASE_URL . '/admin/sections/hostels/add.php' ?>" class="btn btn-success mb-3">+ Add New Hostel</a>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Hostel Name</th>
                                    <th>Incharge</th>
                                    <th>Type</th>
                                    <th>Floors</th>
                                    <th>Rooms</th>
                                    <th>Capacity</th>
                                    <th>Contact</th>
                                    <!-- <th>Amenities</th> -->
                                    <th>Country</th>
                                    <!-- <th>Full Address</th> -->
                                    <th>View</th>
                                    <th>Edit</th>
                                    <th>Delete</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($hostels)): ?>
                                    <tr>
                                        <td colspan="12" class="text-center">No hostels found.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php $serial = 1; ?>
                                    <?php foreach ($hostels as $hostel): ?>
                                        <tr>
                                            <td><?= $serial++ ?></td>
                                            <td><?= htmlspecialchars($hostel['hostel_name']) ?></td>
                                            <td><?= htmlspecialchars($hostel['incharge_firstname'] . ' ' . $hostel['incharge_lastname']) ?></td>
                                            <td><?= ucfirst($hostel['hostel_type']) ?></td>
                                            <td><?= $hostel['number_of_floors'] ?? 'N/A' ?></td>
                                            <td><?= $hostel['number_of_rooms'] ?? 'N/A' ?></td>
                                            <td><?= $hostel['capacity'] ?></td>
                                            <td><?= $hostel['contact_number'] ?></td>
                                            <!-- <td>  -->
                                            <!-- htmlspecialchars($hostel['amenities'])  -->
                                            <!-- </td> -->
                                            <td><?= htmlspecialchars($hostel['country_name'] ?? 'N/A') ?></td>
                                            <!-- <td> -->
                                            <!-- htmlspecialchars(
                                                    implode(', ', array_filter([
                                                        $hostel['house_no'] ?? '',
                                                        $hostel['street'] ?? '',
                                                        $hostel['village'] ?? '',
                                                        $hostel['sub_district'] ?? '',
                                                        $hostel['district'] ?? '',
                                                        $hostel['division'] ?? '',
                                                        $hostel['state'] ?? '',
                                                        $hostel['postalcode'] ?? '',
                                                        $hostel['detail'] ?? ''
                                                    ]))
                                                )  -->
                                            <!-- </td> -->
                                            <td>
                                                <a href="<?= BASE_URL ?>/admin/sections/hostels/view.php?id=<?= $hostel['id'] ?>" class="btn btn-sm btn-info text-light">View</a>
                                            </td>
                                            <td>
                                                <a href="<?= BASE_URL ?>/admin/sections/hostels/edit.php?id=<?= $hostel['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                                            </td>
                                            <td>
                                                <a href="<?= BASE_URL ?>/admin/php_files/sections/hostels/delete.php?id=<?= $hostel['id'] ?>"
                                                    class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Are you sure you want to delete this hostel?')">
                                                    Delete
                                                </a>
                                            </td>

                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<?php
require_once BASE_PATH . '/admin/includes/footer_admin.php';
?>