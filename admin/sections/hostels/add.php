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



<style>
    .form-section {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 15px rgba(0, 0, 0, 0.05);
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .form-section h5 {
        color: #2c3e50;
        border-bottom: 2px solid #f1f1f1;
        padding-bottom: 0.5rem;
        margin-bottom: 1.5rem;
    }

    .form-label {
        font-weight: 500;
        color: #495057;
        margin-bottom: 0.5rem;
    }

    .form-control,
    .form-select {
        border-radius: 6px;
        padding: 0.5rem 0.75rem;
        border: 1px solid #ced4da;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #3c8dbc;
        box-shadow: 0 0 0 0.25rem rgba(60, 141, 188, 0.25);
    }

    .btn-submit {
        background-color: #3c8dbc;
        border: none;
        padding: 0.5rem 1.5rem;
        font-weight: 500;
        letter-spacing: 0.5px;
    }

    .btn-submit:hover {
        background-color: #367fa9;
    }

    .required:after {
        content: " *";
        color: #dc3545;
    }

    textarea.form-control {
        min-height: 100px;
    }

    #formMessage {
        position: fixed;
        bottom: 20px;
        right: 20px;
        max-width: 400px;
        z-index: 1000;
    }
</style>

<div class="container-fluid py-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <!-- Back Button -->
            <a href="<?= BASE_URL . '/admin/sections/hostels/index.php' ?>" class="btn btn-outline-secondary mb-4 mt-4">
                <i class="bi bi-arrow-left me-2"></i>Back to Hostels
            </a>

            <!-- Form Card -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="bi bi-building me-2"></i>Add New Hostel</h4>
                </div>

                <div class="card-body">
                    <!-- Message Container - Added at the top  -->
                    <div id="formMessage" class="mb-4 "></div>
                    <form id="addHostelForm">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

                        <!-- Hostel Information Section -->
                        <div class="form-section">
                            <h5><i class="bi bi-info-circle me-2"></i>Hostel Information</h5>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label required">Hostel Name</label>
                                    <input type="text" name="hostel_name" class="form-control" required>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label required">Type</label>
                                    <select name="hostel_type" class="form-select" required>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">Floors</label>
                                    <input type="number" name="number_of_floors" class="form-control" min="1">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label required">Contact Number</label>
                                    <input type="text" name="contact_number" class="form-control" required>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label required">Capacity</label>
                                    <input type="number" name="capacity" class="form-control" min="1" required>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label required">Hostel Incharge</label>
                                    <select name="hostel_incharge_id" class="form-select" required>
                                        <option value="">-- Select Incharge --</option>
                                        <?php foreach ($admins as $admin): ?>
                                            <option value="<?= $admin['id'] ?>">
                                                <?= htmlspecialchars($admin['firstname'] . ' ' . $admin['lastname']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Amenities</label>
                                    <textarea name="amenities" class="form-control" placeholder="WiFi, Laundry, Gym, etc. (comma separated)"></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Address Information Section -->
                        <div class="form-section">
                            <h5><i class="bi bi-geo-alt me-2"></i>Address Information</h5>

                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label required">Country</label>
                                    <select name="country_id" class="form-select" required>
                                        <option value="">-- Select Country --</option>
                                        <?php foreach ($countries as $country): ?>
                                            <option value="<?= $country['id'] ?>"><?= htmlspecialchars($country['country_name']) ?></option>
                                        <?php endforeach ?>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">State</label>
                                    <input type="text" name="state" class="form-control">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label required">Division</label>
                                    <input type="text" name="division" class="form-control" required>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label required">District</label>
                                    <input type="text" name="district" class="form-control" required>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label required">Sub District</label>
                                    <input type="text" name="sub_district" class="form-control" required>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label required">Village</label>
                                    <input type="text" name="village" class="form-control" required>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label required">Postal Code</label>
                                    <input type="text" name="postalcode" class="form-control" required>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Street</label>
                                    <input type="text" name="street" class="form-control">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">House No</label>
                                    <input type="text" name="house_no" class="form-control">
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Additional Details</label>
                                    <textarea name="detail" class="form-control" placeholder="Any additional address information"></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="text-end mt-4 mb-4">
                            <button type="submit" class="btn btn-submit text-white">
                                <i class="bi bi-save me-2"></i>Add Hostel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#addHostelForm').on('submit', function(e) {
            e.preventDefault();

            const submitBtn = $(this).find('button[type="submit"]');
            const originalText = submitBtn.html();
            submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...');

            $.ajax({
                type: 'POST',
                url: '<?= BASE_URL ?>/admin/php_files/sections/hostels/add.php',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        showAlert('success', response.message);
                        $('#addHostelForm')[0].reset();
                    } else {
                        showAlert('danger', response.message);
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
                    showAlert('danger', errorMsg);
                },
                complete: function() {
                    submitBtn.prop('disabled', false).html(originalText);
                }
            });
        });

        function showAlert(type, message) {
            const alertHtml = `
                <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                    <div class="d-flex">
                        <div class="flex-grow-1">${message}</div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            `;
            
            $('#formMessage').html(alertHtml);
            
            // Auto-dismiss after 5 seconds
            setTimeout(() => {
                $('#formMessage .alert').alert('close');
            }, 5000);
        }
    });
</script>

<?php
require_once BASE_PATH . '/admin/includes/footer_admin.php';
?>