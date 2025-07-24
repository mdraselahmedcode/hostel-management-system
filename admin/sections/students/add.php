<?php
require_once __DIR__ . '/../../../config/config.php';
require_once BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/admin/includes/response_helper.php';
require_once BASE_PATH . '/config/auth.php';
include BASE_PATH . '/includes/slide_message.php';


require_admin();

// Fetch the country list for dropdowns
$countryQuery = $conn->query("
        SELECT id, country_name FROM countries
    ");
$countries = $countryQuery->fetch_all(MYSQLI_ASSOC);
// json_response($countries); 


// Fetch the hostels list for dropdowns
$hostelQuery = $conn->query("
    SELECT id, hostel_name
    FROM hostels
");
$hostels = $hostelQuery->fetch_all(MYSQLI_ASSOC);
// json_response($hostels); 





require_once BASE_PATH . '/admin/includes/header_admin.php';
?>

<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<div class="content container my-5">
    <a href="javascript:history.back()" class="btn btn-secondary mb-3 mt-4 ">Back</a>
    <h2>Add New Student</h2>


    <form id="add-student-form" enctype="multipart/form-data">
        <!-- Profile -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-light">Profile</div>
            <div class="card-body">
                <input type="text" name="first_name" placeholder="First Name" class="form-control mb-2" required>
                <input type="text" name="last_name" placeholder="Last Name" class="form-control mb-2" required>
                <input type="email" name="email" placeholder="Email" class="form-control mb-2" required>
                <!-- First Password Field -->
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

                <!-- Second Password Field (Confirm) -->
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
                <select name="gender" class="form-control mb-2">
                    <option value="">-- Select Gender --</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="other">Other</option>
                </select>
                <input type="text" name="contact_number" placeholder="Contact Number" class="form-control mb-2">
                <input type="text" name="emergency_contact" placeholder="Emergency Contact" class="form-control mb-2">
            </div>
        </div>

        <!-- Parent Details -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-light">Parent Details</div>
            <div class="card-body">
                <input type="text" name="father_name" placeholder="Father's Name" class="form-control mb-2">
                <input type="text" name="father_contact" placeholder="Father's Contact" class="form-control mb-2">
                <input type="text" name="mother_name" placeholder="Mother's Name" class="form-control mb-2">
                <input type="text" name="mother_contact" placeholder="Mother's Contact" class="form-control mb-2">
            </div>
        </div>

        <!-- Hostel Info -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-light">Hostel & Room</div>
            <div class="card-body">
                <!-- select hostel -->
                <select name="hostel_id" id="hostel-select" class="form-select mb-2" required>
                    <option value="">-- Select Hostel --</option>
                    <?php foreach ($hostels as $hostel): ?>
                        <option value="<?= $hostel['id'] ?>"> <?= htmlspecialchars($hostel['hostel_name']) ?> </option>
                    <?php endforeach; ?>
                </select>
                <!-- select floor -->
                <select name="floor_id" id="floor-select" class="form-select mb-2">
                    <option value="">-- Select Floor --</option>
                </select>
                <!-- select room -->
                <select name="room_id" id="room-select" class="form-select mb-3">
                    <option value="">-- Select Room --</option>
                </select>

                <label><input type="checkbox" name="is_checked_in" value="1" class="mb-2"><span style="margin-left: 5px;">Checked In</span></label>
                <input type="datetime-local" name="check_in_at" class="form-control mb-2">
                <input type="datetime-local" name="check_out_at" class="form-control mb-2">
            </div>
        </div>

        <!-- Verification -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-light">Approval & Verification</div>
            <div class="card-body">
                <label><input type="checkbox" name="is_verified" value="1"> Verified</label><br>
                <label><input type="checkbox" name="is_approved" value="1"> Approved </label><br>
            </div>
        </div>

        <!-- Permanent address -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-light">Permanent Address</div>
            <div class="card-body">
                <select name="perm_country_id" class="form-control mb-2">
                    <option value="">Select Country</option>
                    <?php foreach ($countries as $country): ?>
                        <option value="<?= $country['id'] ?>"> <?= htmlspecialchars($country['country_name']) ?> </option>
                    <?php endforeach; ?>
                </select>
                <input type="text" name="perm_state" placeholder="State" class="form-control mb-2">
                <input type="text" name="perm_division" placeholder="Division" class="form-control mb-2">
                <input type="text" name="perm_district" placeholder="District" class="form-control mb-2">
                <input type="text" name="perm_sub_district" placeholder="Sub-district" class="form-control mb-2">
                <input type="text" name="perm_village" placeholder="Village" class="form-control mb-2">
                <input type="text" name="perm_postalcode" placeholder="Postal Code" class="form-control mb-2">
                <input type="text" name="perm_street" placeholder="Street" class="form-control mb-2">
                <input type="text" name="perm_house_no" placeholder="House No" class="form-control mb-2">
                <textarea name="perm_detail" placeholder="Additional Detail" class="form-control mb-2"></textarea>
            </div>
        </div>

        <!-- Temporary Address -->
        <!-- copy permanent address into temporary address on check -->
        <div class="form-check mb-2">
            <input type="checkbox" id="same-address" class="form-check-input">
            <label for="same-address" class="form-check-label">Same as Permanent Address</label>
        </div>
        <!-- Temporary Address -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-light">Temporary Address</div>
            <div class="card-body">
                <select name="temp_country_id" class="form-control mb-2">
                    <option value="">-- Select Country --</option>
                    <?php foreach ($countries as $country): ?>
                        <option value="<?= $country['id'] ?>"> <?= htmlspecialchars($country['country_name']) ?> </option>
                    <?php endforeach; ?>
                </select>
                <input type="text" name="temp_state" placeholder="State" class="form-control mb-2">
                <input type="text" name="temp_division" placeholder="Division" class="form-control mb-2">
                <input type="text" name="temp_district" placeholder="District" class="form-control mb-2">
                <input type="text" name="temp_sub_district" placeholder="Sub-district" class="form-control mb-2">
                <input type="text" name="temp_village" placeholder="Village" class="form-control mb-2">
                <input type="text" name="temp_postalcode" placeholder="Postal Code" class="form-control mb-2">
                <input type="text" name="temp_street" placeholder="Street" class="form-control mb-2">
                <input type="text" name="temp_house_no" placeholder="House No" class="form-control mb-2">
                <textarea name="temp_detail" placeholder="Additional Detail" class="form-control mb-2"></textarea>
            </div>
        </div>

        <button type="submit" class="btn btn-success mb-4">Add Student</button>
    </form>
</div>


<script>
    // load floor when hostel is selected
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


    // load room when floor is selected
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


    // copy permanent address to temporary address if same check box is checked
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





    $(document).ready(function() {
        $('#add-student-form').on('submit', function(e) {
            e.preventDefault();
            var form = this;
            var formData = new FormData(this);

            // Optional: show temporary loading message
            showSlideMessage('Processing student data...', 'info');

            $.ajax({
                url: '<?= BASE_URL . '/admin/php_files/sections/students/add.php' ?>',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    // console.log(response);

                    if (response.success) {
                        showSlideMessage(response.message || 'Student added successfully.', 'success');
                        form.reset();
                    } else {
                        showSlideMessage(response.message || 'Failed to add student.', 'danger');
                    }
                },
                error: function(xhr, status, error) {
                    showSlideMessage('Server error: ' + error, 'danger');
                }
            });
        });
    });
</script>


<?php require_once BASE_PATH . '/admin/includes/footer_admin.php' ?>