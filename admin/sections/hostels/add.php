<?php
session_start();
require_once __DIR__ . '/../../../config/config.php';
require_once BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/admin/includes/header_admin.php';
require_once BASE_PATH . '/admin/php_files/auth_check_admin.php';

// Fetch admins for the incharge dropdown
$sql = "SELECT id, firstname, lastname FROM admins ORDER BY firstname ASC";
$result = $conn->query($sql);
$admins = [];

if ($result && $result->num_rows > 0) {
    $admins = $result->fetch_all(MYSQLI_ASSOC);
}

// Fetch countries for the country dropdown
$countryQuery = "SELECT id, country_name FROM countries ORDER BY country_name ASC";
$countryResult = $conn->query($countryQuery);
$countries = [];
if ($countryResult->num_rows > 0) {
    $countries = $countryResult->fetch_all(MYSQLI_ASSOC);
}

// Validating CSRF Token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<!-- Add Hostel Form -->
<div class="main-content container mt-5">
    <!-- back button -->
    <a href="<?= BASE_URL . '/admin/sections/hostels/index.php' ?>" class="btn btn-secondary mb-3">Back</a>
    
    <div>
        <h4 class="mt-3">Add New Hostel</h4>

        <form id="addHostelForm">
            <!-- CSRF token -->
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Hostel Name</label>
                    <input type="text" name="hostel_name" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Type</label>
                    <select name="hostel_type" class="form-select" required>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Floors</label>
                    <input type="number" name="number_of_floors" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Contact Number</label>
                    <input type="text" name="contact_number" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Capacity</label>
                    <input type="number" name="capacity" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Hostel Incharge</label>
                    <select name="hostel_incharge_id" class="form-select" required>
                        <option value="">-- Select Incharge --</option>
                        <?php foreach ($admins as $admin): ?>
                            <option value="<?= $admin['id'] ?>">
                                <?= htmlspecialchars($admin['firstname'] . ' ' . $admin['lastname']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-12">
                    <label class="form-label">Amenities (comma separated)</label>
                    <textarea name="amenities" class="form-control"></textarea>
                </div>
                <!-- Address Section -->
                <div class="col-md-12 mt-4">
                    <h5>Adress Information</h5>
                </div>
                <div class="col-md-4">
                    <label for="" class="form-label">Country</label>
                    <select name="country_id" id="" class="form-select" required>
                        <option value="">-- Select Country --</option>
                        <?php foreach ($countries as $country): ?>
                            <option value="<?= $country['id'] ?>"><?= htmlspecialchars($country['country_name']) ?></option>

                        <?php endforeach ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="" class="form-label">State</label>
                    <input type="text" name="state" class="form-control">
                </div>
                <div class="col-md-4">
                    <label for="" class="form-label">Division</label>
                    <input type="text" name="division" class="form-control required">
                </div>
                <div class="col-md-4">
                    <label for="" class="form-label">District</label>
                    <input type="text" name="district" class="form-control required">
                </div>
                <div class="col-md-4">
                    <label for="" class="form-label">Sub District</label>
                    <input type="text" name="sub_district" class="form-control required">
                </div>
                <div class="col-md-4">
                    <label for="" class="form-label">Village</label>
                    <input type="text" name="village" class="form-control required">
                </div>
                <div class="col-md-4">
                    <label for="" class="form-label">Postal Code</label>
                    <input type="text" name="postalcode" class="form-control required">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Street</label>
                    <input type="text" name="street" class="form-control">
                </div>

                <div class="col-md-4">
                    <label class="form-label">House No</label>
                    <input type="text" name="house_no" class="form-control">
                </div>
                <div class="col-md-12">
                    <label class="form-label">Additional Details</label>
                    <textarea name="detail" class="form-control"></textarea>
                </div>
                <div class="col-md-12">
                    <button type="submit" class="btn btn-success mt-2">Add Hostel</button>
                </div>
            </div>
        </form>
        <div id="formMessage" class="mt-3"></div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#addHostelForm').on('submit', function(e) {
            e.preventDefault();

            const submitBtn = $(this).find('button[type = "submit"]');
            submitBtn.prop('disabled', true).text('Submitting...'); // Disable & change button text

            const formData = $(this).serialize();
            $.ajax({
                type: 'POST',
                url: '<?= BASE_URL ?>/admin/php_files/sections/hostels/add.php',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('#formMessage').html('<div class="alert alert-success">' + response.message + '</div>');
                        $('#addHostelForm')[0].reset(); // Reset the form
                    } else {
                        $('#formMessage').html('<div class="alert alert-danger">' + response.message + '</div>');
                    }
                    submitBtn.prop('disabled', false).text('Add Hostel'); // Re-enable button
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                    $('#formMessage').html('<div class="alert alert-danger">An error occurred. Please try again.</div>');
                    submitBtn.prop('disabled', false).text('Add Hostel'); // Re-enable button
                }
            })
        })
    })
</script>

<?php
require_once BASE_PATH . '/admin/includes/footer_admin.php';
?>