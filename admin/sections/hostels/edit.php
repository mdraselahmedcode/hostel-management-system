<?php
require_once __DIR__ . '/../../../config/config.php';
require_once BASE_PATH . '/config/db.php';
include BASE_PATH . '/includes/slide_message.php';
require_once BASE_PATH . '/config/auth.php';

require_admin();

require_once BASE_PATH . '/admin/includes/header_admin.php';
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





<?php
// [Your existing PHP code...]
?>

<style>
    .edit-hostel-form {
        background: white;
        border-radius: 10px;
        box-shadow: 0 5px 25px rgba(0, 0, 0, 0.08);
        padding: 2rem;
    }

    .form-section {
        margin-bottom: 2rem;
        padding-bottom: 1.5rem;
        border-bottom: 1px solid #eee;
    }

    .form-section h5 {
        color: #2c3e50;
        font-weight: 600;
        margin-bottom: 1.5rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #f1f1f1;
    }

    .form-section h5 i {
        margin-right: 10px;
        color: #3c8dbc;
    }

    .form-label {
        font-weight: 500;
        color: #495057;
        margin-bottom: 0.5rem;
    }

    .form-control,
    .form-select {
        border-radius: 6px;
        padding: 0.6rem 0.75rem;
        border: 1px solid #ddd;
        transition: all 0.3s ease;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #3c8dbc;
        box-shadow: 0 0 0 0.25rem rgba(60, 141, 188, 0.15);
    }

    .btn-submit {
        background: linear-gradient(135deg, #3c8dbc 0%, #367fa9 100%);
        border: none;
        padding: 0.7rem 2rem;
        font-weight: 500;
        letter-spacing: 0.5px;
        transition: all 0.3s ease;
    }

    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(60, 141, 188, 0.2);
    }

    .back-btn {
        transition: all 0.3s ease;
    }

    .back-btn:hover {
        transform: translateX(-3px);
    }

    #showMessage {
        position: fixed;
        top: 20px;
        right: 20px;
        max-width: 400px;
        z-index: 1000;
    }

    @media (max-width: 768px) {
        .edit-hostel-form {
            padding: 1.5rem;
        }
    }
</style>

<div class="container py-4 mt-4">
    <div class="row">
        <div class="col-lg-10 mx-auto">
            <a href="javascript:history.back()" class="btn btn-outline-secondary back-btn mb-4 mt-4">
                <i class="bi bi-arrow-left me-2"></i>Back
            </a>

            <div class="edit-hostel-form  mb-5">
                <h3 class="mb-4 text-primary">
                    <i class="bi bi-building"></i> Edit Hostel: <?= htmlspecialchars($hostel['hostel_name']) ?>
                </h3>

                <form id="editHostelForm" class="mb-2">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    <input type="hidden" name="id" value="<?= $hostel['id'] ?>">

                    <!-- Hostel Information Section -->
                    <div class="form-section">
                        <h5><i class="bi bi-info-circle"></i>Hostel Information</h5>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Hostel Name</label>
                                <input type="text" name="hostel_name" class="form-control" required
                                    value="<?= htmlspecialchars($hostel['hostel_name']) ?>">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Type</label>
                                <select name="hostel_type" class="form-select" required>
                                    <option value="male" <?= $hostel['hostel_type'] === 'male' ? 'selected' : '' ?>>Male</option>
                                    <option value="female" <?= $hostel['hostel_type'] === 'female' ? 'selected' : '' ?>>Female</option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Contact Number</label>
                                <input type="text" name="contact_number" class="form-control" required
                                    value="<?= htmlspecialchars($hostel['contact_number']) ?>">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Capacity</label>
                                <input type="number" name="capacity" class="form-control" required min="1"
                                    value="<?= htmlspecialchars($hostel['capacity']) ?>">
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

                            <div class="col-12">
                                <label class="form-label">Amenities</label>
                                <textarea name="amenities" class="form-control"
                                    placeholder="WiFi, Laundry, Gym, etc. (comma separated)"><?= htmlspecialchars($hostel['amenities']) ?></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Address Information Section -->
                    <div class="form-section">
                        <h5><i class="bi bi-geo-alt"></i>Address Information</h5>

                        <div class="row g-3">
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
                                <input type="text" name="state" class="form-control"
                                    value="<?= htmlspecialchars($hostel['state']) ?>">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Division</label>
                                <input type="text" name="division" class="form-control"
                                    value="<?= htmlspecialchars($hostel['division']) ?>">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">District</label>
                                <input type="text" name="district" class="form-control"
                                    value="<?= htmlspecialchars($hostel['district']) ?>">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Sub District</label>
                                <input type="text" name="sub_district" class="form-control"
                                    value="<?= htmlspecialchars($hostel['sub_district']) ?>">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Village</label>
                                <input type="text" name="village" class="form-control"
                                    value="<?= htmlspecialchars($hostel['village']) ?>">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Postal Code</label>
                                <input type="text" name="postalcode" class="form-control"
                                    value="<?= htmlspecialchars($hostel['postalcode']) ?>">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Street</label>
                                <input type="text" name="street" class="form-control"
                                    value="<?= htmlspecialchars($hostel['street']) ?>">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">House No</label>
                                <input type="text" name="house_no" class="form-control"
                                    value="<?= htmlspecialchars($hostel['house_no']) ?>">
                            </div>

                            <div class="col-12">
                                <label class="form-label">Additional Details</label>
                                <textarea name="detail" class="form-control"><?= htmlspecialchars($hostel['detail']) ?></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="text-end mt-4">
                        <button type="submit" class="btn btn-submit text-white">
                            <i class="bi bi-save me-2 mb-2"></i>Update Hostel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#editHostelForm').on('submit', function(e) {
            e.preventDefault();

            const submitBtn = $(this).find('button[type="submit"]');
            const originalText = submitBtn.html();
            submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Updating...');

            $.ajax({
                type: 'POST',
                url: '<?= BASE_URL ?>/admin/php_files/sections/hostels/edit.php',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        showSlideMessage(response.message, 'success');
                        setTimeout(function() {
                            window.location.href = "<?= BASE_URL ?>/admin/sections/hostels/index.php";
                        }, 1500);
                    } else {
                        showSlideMessage(response.message, 'danger');
                    }
                },
                error: function(xhr) {
                    let errorMsg = 'An error occurred. Please try again.';
                    try {
                        const response = JSON.parse(xhr.responseText);
                        errorMsg = response.message || errorMsg;
                    } catch (e) {
                        console.error('Error parsing response:', e);
                    }
                    showSlideMessage(errorMsg, 'danger');
                },
                complete: function() {
                    submitBtn.prop('disabled', false).html(originalText);
                }
            });
        });
    });
</script>


<?php
require_once BASE_PATH . '/admin/includes/footer_admin.php';
?>