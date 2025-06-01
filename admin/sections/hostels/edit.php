<?php
session_start();
require_once __DIR__ . '/../../../config/config.php';
require_once BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/admin/includes/header_admin.php';
require_once BASE_PATH . '/admin/php_files/auth_check_admin.php';

// Check if hostel_id is passed
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<div class='alert alert-danger'>Invalid hostel ID</div>";
    require_once BASE_PATH . '/admin/includes/footer_admin.php';
    exit;
}

$hostel_id = intval($_GET['id']);

// Fetch hostel details
$hostelQuery = "
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
        addresses.detail
    FROM hostels
    LEFT JOIN admins ON hostels.hostel_incharge_id = admins.id
    LEFT JOIN addresses ON hostels.address_id = addresses.id
    LEFT JOIN countries ON addresses.country_id = countries.id
    WHERE hostels.id = ?
    LIMIT 1
";
$stmt = $conn->prepare($hostelQuery);
$stmt->bind_param("i", $hostel_id);
$stmt->execute();
$result = $stmt->get_result();




if ($result->num_rows === 0) {
    echo "<div class='alert alert-danger'>Hostel not found</div>";
    require_once BASE_PATH . '/admin/includes/footer_admin.php';
    exit;
}

$hostel = $result->fetch_assoc();
// echo print_r($hostel, true); // Uncomment for debugging if needed
// exit;

// Fetch admins
$adminResult = $conn->query("SELECT id, firstname, lastname FROM admins ORDER BY firstname ASC");
$admins = $adminResult->fetch_all(MYSQLI_ASSOC);

// Fetch countries
$countryResult = $conn->query("SELECT id, country_name FROM countries ORDER BY country_name ASC");
$countries = $countryResult->fetch_all(MYSQLI_ASSOC);

// CSRF Token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<!-- Edit Hostel Form -->
<div class="main-content container mt-5">
    <a href="<?= BASE_URL . '/admin/sections/hostels/index.php' ?>" class="btn btn-secondary mb-3">Back</a>
    <div>
        <h4 class="mt-3">Edit Hostel</h4>

        <form id="editHostelForm">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <input type="hidden" name="id" value="<?= $hostel['id'] ?>">

            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Hostel Name</label>
                    <input type="text" name="hostel_name" class="form-control" required value="<?= htmlspecialchars($hostel['hostel_name']) ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Type</label>
                    <select name="hostel_type" class="form-select" required>
                        <option value="male" <?= $hostel['hostel_type'] === 'male' ? 'selected' : '' ?>>Male</option>
                        <option value="female" <?= $hostel['hostel_type'] === 'female' ? 'selected' : '' ?>>Female</option>
                    </select>
                </div>
                <!-- <div class="col-md-3">
                    <label class="form-label">Floors</label>
                    <input type="number" name="number_of_floors" class="form-control" value="<?= htmlspecialchars($hostel['number_of_floors']) ?>">
                </div> -->
                <div class="col-md-3">
                    <label class="form-label">Contact Number</label>
                    <input type="text" name="contact_number" class="form-control" required value="<?= htmlspecialchars($hostel['contact_number']) ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Capacity</label>
                    <input type="number" name="capacity" class="form-control" required value="<?= htmlspecialchars($hostel['capacity']) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Hostel Incharge</label>
                    <select name="hostel_incharge_id" class="form-select" required>
                        <option value="">-- Select Incharge --</option>
                        <?php foreach ($admins as $admin): ?>
                            <option value="<?= $admin['id'] ?>" <?= $hostel['hostel_incharge_id'] == $admin['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($admin['firstname'] . ' ' . $admin['lastname']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-12">
                    <label class="form-label">Amenities (comma separated)</label>
                    <textarea name="amenities" class="form-control"><?= htmlspecialchars($hostel['amenities']) ?></textarea>
                </div>

                <!-- Address Section -->
                <div class="col-md-12 mt-4">
                    <h5>Address Information</h5>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Country</label>
                    <select name="country_id" class="form-select" required>
                        <option value="">-- Select Country --</option>
                        <?php foreach ($countries as $country): ?>
                            <option value="<?= $country['id'] ?>" <?= $hostel['country_id'] == $country['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($country['country_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">State</label>
                    <input type="text" name="state" class="form-control" value="<?= htmlspecialchars($hostel['state']) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Division</label>
                    <input type="text" name="division" class="form-control" value="<?= htmlspecialchars($hostel['division']) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">District</label>
                    <input type="text" name="district" class="form-control" value="<?= htmlspecialchars($hostel['district']) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Sub District</label>
                    <input type="text" name="sub_district" class="form-control" value="<?= htmlspecialchars($hostel['sub_district']) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Village</label>
                    <input type="text" name="village" class="form-control" value="<?= htmlspecialchars($hostel['village']) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Postal Code</label>
                    <input type="text" name="postalcode" class="form-control" value="<?= htmlspecialchars($hostel['postalcode']) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Street</label>
                    <input type="text" name="street" class="form-control" value="<?= htmlspecialchars($hostel['street']) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">House No</label>
                    <input type="text" name="house_no" class="form-control" value="<?= htmlspecialchars($hostel['house_no']) ?>">
                </div>
                <div class="col-md-12">
                    <label class="form-label">Additional Details</label>
                    <textarea name="detail" class="form-control"><?= htmlspecialchars($hostel['detail']) ?></textarea>
                </div>

                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary mt-2">Update Hostel</button>
                </div>
            </div>
        </form>

        <div id="showMessage" class="mt-3"></div>
    </div>
</div>

<!-- AJAX submission -->
<script>
    $(document).ready(function() {
        $('#editHostelForm').on('submit', function(e) {
            e.preventDefault();

            const submitBtn = $(this).find('button[type="submit"]');
            submitBtn.prop('disabled', true).text('Updating...');

            const formData = $(this).serialize();
            $.ajax({
                type: 'POST',
                url: '<?= BASE_URL ?>/admin/php_files/sections/hostels/edit.php',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('#showMessage').html('<div class="alert alert-success">' + response.message + '</div>');
                        setTimeout(function(){
                            window.location.href = "<?= BASE_URL . "/admin/sections/hostels/index.php"?>"; 
                        }, 2000)
                    } else {
                        $('#showMessage').html('<div class="alert alert-danger">' + response.message + '</div>');
                    }
                    submitBtn.prop('disabled', false).text('Update Hostel');
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                    $('#showMessage').html('<div class="alert alert-danger">An error occurred. Please try again.</div>');
                    submitBtn.prop('disabled', false).text('Update Hostel');
                }
            });
        });
    });
</script>

<?php
require_once BASE_PATH . '/admin/includes/footer_admin.php';
?>
