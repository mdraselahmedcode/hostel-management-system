<?php
require_once __DIR__ . '/../config/config.php';
require_once BASE_PATH . '/config/db.php';
// require_once BASE_PATH . '/student/includes/header_student.php';

require_once BASE_PATH . '/includes/header.php';

// Fetch the country list for dropdowns
$countryQuery = $conn->query("SELECT id, country_name FROM countries");
$countries = $countryQuery->fetch_all(MYSQLI_ASSOC);

// Fetch the hostels list for dropdowns
$hostelQuery = $conn->query("SELECT id, hostel_name FROM hostels");
$hostels = $hostelQuery->fetch_all(MYSQLI_ASSOC);
?>

<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>

<div class="content container mt-4">
    <a href="<?= BASE_URL . '/student/login_student.php' ?>" class="btn btn-secondary mb-3">Back to Login</a>
    <div class="card-header bg-primary text-white mb-2 shadow-sm">
        <h2 class="mb-0">Student Registration Application</h2>
        <small class="d-block mt-1">Your application will be reviewed by administration before approval</small>
    </div>



    <form id="register-student-form" enctype="multipart/form-data">
        <!-- Profile -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-light">Profile</div>
            <div class="card-body">
                <input type="text" name="first_name" placeholder="First Name" class="form-control mb-2" required>
                <input type="text" name="last_name" placeholder="Last Name" class="form-control mb-2" required>
                <input type="email" name="email" placeholder="Email" class="form-control mb-2" required>

                <!-- Password Field -->
                <div class="position-relative mb-3">
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-control"
                        placeholder="Password"
                        required>
                    <button
                        type="button"
                        class="btn btn-link position-absolute end-0 top-50 translate-middle-y toggle-password"
                        data-target="#password">
                        <i class="bi bi-eye text-dark"></i>
                    </button>
                </div>
                <!-- Confirm Password Field -->
                <div class="position-relative mb-3">
                    <input
                        type="password"
                        id="confirm_password"
                        name="confirm_password"
                        class="form-control"
                        placeholder="Confirm Password"
                        required>
                    <button
                        type="button"
                        class="btn btn-link position-absolute end-0 top-50 translate-middle-y toggle-password"
                        data-target="#confirm_password">
                        <i class="bi bi-eye text-dark"></i>
                    </button>
                </div>
                <input type="text" name="varsity_id" placeholder="Varsity ID" class="form-control mb-2" required>
                <input type="text" name="department" placeholder="Department" class="form-control mb-2" required>
                <input type="text" name="batch_year" placeholder="Batch Year" class="form-control mb-2" required>
                <select name="blood_group" class="form-control mb-2" required>
                    <option value="">-- Select Blood Group --</option>
                    <option value="A+">A+</option>
                    <option value="A-">A-</option>
                    <option value="B+">B+</option>
                    <option value="B-">B-</option>
                    <option value="AB+">AB+</option>
                    <option value="AB-">AB-</option>
                    <option value="O+">O+</option>
                    <option value="O-">O-</option>
                    <option value="Unknown">Unknown</option>
                </select>
                <select name="gender" class="form-control mb-2" required>
                    <option value="">-- Select Gender --</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="other">Other</option>
                </select>
                <input type="text" name="contact_number" placeholder="Contact Number" class="form-control mb-2" required>
                <input type="text" name="emergency_contact" placeholder="Emergency Contact" class="form-control mb-2" required>
            </div>
        </div>

        <!-- Parent Details -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-light">Parent Details</div>
            <div class="card-body">
                <input type="text" name="father_name" placeholder="Father's Name" class="form-control mb-2" required>
                <input type="text" name="father_contact" placeholder="Father's Contact" class="form-control mb-2" required>
                <input type="text" name="mother_name" placeholder="Mother's Name" class="form-control mb-2">
                <input type="text" name="mother_contact" placeholder="Mother's Contact" class="form-control mb-2">
            </div>
        </div>

        <!-- Permanent address -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-light">Permanent Address</div>
            <div class="card-body">
                <select name="perm_country_id" class="form-control mb-2" required>
                    <option value="">Select Country</option>
                    <?php foreach ($countries as $country): ?>
                        <option value="<?= $country['id'] ?>"> <?= htmlspecialchars($country['country_name']) ?> </option>
                    <?php endforeach; ?>
                </select>
                <input type="text" name="perm_state" placeholder="State" class="form-control mb-2">
                <input type="text" name="perm_division" placeholder="Division" class="form-control mb-2" required>
                <input type="text" name="perm_district" placeholder="District" class="form-control mb-2" required>
                <input type="text" name="perm_sub_district" placeholder="Sub-district" class="form-control mb-2" required>
                <input type="text" name="perm_village" placeholder="Village" class="form-control mb-2" required>
                <input type="text" name="perm_postalcode" placeholder="Postal Code" class="form-control mb-2">
                <input type="text" name="perm_street" placeholder="Street" class="form-control mb-2">
                <input type="text" name="perm_house_no" placeholder="House No" class="form-control mb-2">
                <textarea name="perm_detail" placeholder="Additional Detail" class="form-control mb-2"></textarea>
            </div>
        </div>

        <!-- Temporary Address -->
        <div class="form-check mb-2">
            <input type="checkbox" id="same-address" class="form-check-input">
            <label for="same-address" class="form-check-label">Same as Permanent Address</label>
        </div>
        <div class="card mb-4">
            <div class="card-header bg-primary text-light">Temporary Address</div>
            <div class="card-body">
                <select name="temp_country_id" class="form-control mb-2" required>
                    <option value="">-- Select Country --</option>
                    <?php foreach ($countries as $country): ?>
                        <option value="<?= $country['id'] ?>"> <?= htmlspecialchars($country['country_name']) ?> </option>
                    <?php endforeach; ?>
                </select>
                <input type="text" name="temp_state" placeholder="State" class="form-control mb-2">
                <input type="text" name="temp_division" placeholder="Division" class="form-control mb-2" required>
                <input type="text" name="temp_district" placeholder="District" class="form-control mb-2" required>
                <input type="text" name="temp_sub_district" placeholder="Sub-district" class="form-control mb-2" required>
                <input type="text" name="temp_village" placeholder="Village" class="form-control mb-2" required>
                <input type="text" name="temp_postalcode" placeholder="Postal Code" class="form-control mb-2">
                <input type="text" name="temp_street" placeholder="Street" class="form-control mb-2">
                <input type="text" name="temp_house_no" placeholder="House No" class="form-control mb-2">
                <textarea name="temp_detail" placeholder="Additional Detail" class="form-control mb-2"></textarea>
            </div>
        </div>

        <div class="d-grid gap-2 mt-4">
            <button type="submit" class="btn btn-success btn-lg">
                <i class="fas fa-check-circle me-2"></i>
                Submit Application
            </button>
            <a href="<?= BASE_URL . '/student/sections/checkStatus.php' ?>" class="btn btn-outline-secondary">
                Already applied? Check Status
            </a>
        </div>
        <div class="text-center mt-3">
            <p class="text-muted">
                Need help? <a href="contact.php">Contact support</a> or
                <a href="faq.php#application-process">read our FAQ</a>
            </p>
        </div>
    </form>
    <div id="showMessage" class="alert d-none mt-3"></div>
</div>

<script>
    // Load floor when hostel is selected
    $('#hostel-select').on('change', function() {
        var hostelId = $(this).val();
        var $floorSelect = $('#floor-select');

        $floorSelect.html('<option value="">Loading...</option>');

        $.ajax({
            url: "<?= BASE_URL . '/admin/sections/students/get_floors.php' ?>",
            method: 'GET',
            data: {
                hostel_id: hostelId
            },
            dataType: 'json',
            success: function(data) {
                if (data.length === 0) {
                    $floorSelect.html('<option value="">No floors available</option>');
                } else {
                    $floorSelect.html('<option value="">-- Select Floor --</option>');
                }
                $.each(data, function(index, floor) {
                    var floorName = floor.floor_name ? floor.floor_name + ' (Floor ' + floor.floor_number + ')' : 'Floor ' + floor.floor_number;
                    $floorSelect.append(
                        $('<option>').val(floor.id).text(floorName)
                    );
                });
            },
            error: function() {
                $floorSelect.html('<option value="">Error loading floors</option>');
            }
        });
    });

    // Load room when floor is selected
    $('#floor-select').on('change', function() {
        var floorId = $(this).val();
        var $roomSelect = $('#room-select');

        $roomSelect.html('<option value="">Loading...</option>');
        $.ajax({
            url: "<?= BASE_URL . '/admin/sections/students/get_rooms.php' ?>",
            method: 'GET',
            data: {
                floor_id: floorId
            },
            dataType: 'json',
            success: function(data) {
                if (data.length === 0) {
                    $roomSelect.html('<option value="">No rooms available</option>');
                } else {
                    $roomSelect.html('<option value="">-- Select Room --</option>');
                }
                $.each(data, function(index, room) {
                    $roomSelect.append(
                        $('<option>').val(room.id).text(room.room_number)
                    );
                });
            },
            error: function() {
                $roomSelect.html('<option value="">Error loading rooms</option>');
            }
        });
    });

    // Copy permanent address to temporary address if same check box is checked
    $('#same-address').on('change', function() {
        if (this.checked) {
            $('select[name="temp_country_id"]').val($('select[name="perm_country_id"]').val());
            $('input[name="temp_state"]').val($('input[name="perm_state"]').val());
            $('input[name="temp_division"]').val($('input[name="perm_division"]').val());
            $('input[name="temp_district"]').val($('input[name="perm_district"]').val());
            $('input[name="temp_sub_district"]').val($('input[name="perm_sub_district"]').val());
            $('input[name="temp_village"]').val($('input[name="perm_village"]').val());
            $('input[name="temp_postalcode"]').val($('input[name="perm_postalcode"]').val());
            $('input[name="temp_street"]').val($('input[name="perm_street"]').val());
            $('input[name="temp_house_no"]').val($('input[name="perm_house_no"]').val());
            $('textarea[name="temp_detail"]').val($('textarea[name="perm_detail"]').val());
        } else {
            $('select[name="temp_country_id"]').val('');
            $('input[name="temp_division"], input[name="temp_state"], input[name="temp_district"], input[name="temp_sub_district"], input[name="temp_village"], input[name="temp_postalcode"], input[name="temp_street"], input[name="temp_house_no"]').val('');
            $('textarea[name="temp_detail"]').val('');
        }
    });

    // Toggle password visibility
    $('.toggle-password').click(function() {
        const target = $(this).data('target');
        const icon = $(this).find('i');

        if ($(target).attr('type') === 'password') {
            $(target).attr('type', 'text');
            icon.removeClass('bi-eye').addClass('bi-eye-slash');
        } else {
            $(target).attr('type', 'password');
            icon.removeClass('bi-eye-slash').addClass('bi-eye');
        }
    });

    // Handle form submit
    $(document).ready(function() {
        $('#register-student-form').on('submit', function(e) {
            e.preventDefault();
            var form = this;
            var formData = new FormData(this);

            $('#showMessage').html('')
                .removeClass('alert-danger alert-success')
                .addClass('alert-info')
                .html('<div class="spinner-border spinner-border-sm " role="status"></div> Processing... ')
                .removeClass('d-none');

            $.ajax({
                url: '<?= BASE_URL . '/student/php_files/register_student_handler.php' ?>',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    var messageDiv = $('#showMessage')
                        .removeClass('alert-info alert-danger')
                        .addClass(response.success ? 'alert-success' : 'alert-danger')
                        .html(response.message);

                    if (response.success) {
                        form.reset();
                        setTimeout(function() {
                            $('#showMessage').fadeOut();
                        }, 3000)
                    } else {
                        $('#showMessage').removeClass('d-none').html((response.message || 'Unknown error'));
                    }
                },
                error: function(xhr, status, error) {
                    $('#showMessage')
                        .removeClass('alert-info alert-success')
                        .addClass('alert-danger')
                        .html('Server error: ' + error);
                }
            })
        })
    })
</script>

<?php require_once BASE_PATH . '/student/includes/footer_student.php'; ?>