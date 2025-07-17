    <?php
    require_once __DIR__ . '/../../../config/config.php';
    require_once BASE_PATH . '/config/db.php';
    require_once BASE_PATH . '/config/auth.php'; 
    include BASE_PATH . '/includes/slide_message.php';


    require_admin(); 
    require_once BASE_PATH . '/admin/includes/response_helper.php';
    require_once BASE_PATH . '/admin/includes/csrf.php';

    // accepting and validating room fee id
    $roomFee_Id = isset($_GET['roomFees_id']) ? (int)$_GET['roomFees_id'] : 0;
    if ($roomFee_Id <= 0) {
        header("Location: index.php");
        exit;
    }

    // accepting and validating hostel id
    $hostel_id = isset($_GET['hostel_id']) ? (int)$_GET['hostel_id'] : 0;
    if ($hostel_id <= 0) {
        header("Location: index.php");
        exit;
    }

    // accepting and validating room type id
    $roomType_id = isset($_GET['roomType_id']) ? (int)$_GET['roomType_id'] : 0;
    if ($roomType_id <= 0) {
        header("Location: index.php");
        exit;
    }


    // Fetching hostels
    $hostels = [];
    if ($hostel_id > 0) {
        $hostelStmt = $conn->prepare("
                SELECT id, hostel_name
                FROM hostels
            ");
        $hostelStmt->execute();
        $result = $hostelStmt->get_result();
        $hostels = $result->fetch_all(MYSQLI_ASSOC);
        // json_response($roomTypes['id ']);
    }


    // Fetching room types
    $roomTypes = [];
    $roomTypeStmt = $conn->prepare("
            SELECT 
                room_types.id, 
                room_types.type_name
            FROM room_types 
            WHERE hostel_id = ?
        ");
    $roomTypeStmt->bind_param("i", $hostel_id);
    $roomTypeStmt->execute();
    $result = $roomTypeStmt->get_result();
    $roomTypes = $result->fetch_all(MYSQLI_ASSOC);

    // json_response($roomTypes);


    // Fetching room fees
    $roomFees = [];
    if ($roomFee_Id > 0) {
        $roomFeeStmt = $conn->prepare("
                SELECT id, room_type_id, hostel_id, price, billing_cycle, effective_from
                FROM room_fees
                WHERE id = ?
            ");
        $roomFeeStmt->bind_param("i", $roomFee_Id);
        $roomFeeStmt->execute();
        $result = $roomFeeStmt->get_result();
        $roomFees = $result->fetch_all(MYSQLI_ASSOC);
        // json_response($roomFees);
    }


    $csrf_token = generate_csrf_token();

    require_once BASE_PATH . '/admin/includes/header_admin.php';

    ?>


    <div class="content container mt-5">
        <a href="<?= BASE_URL . '/admin/sections/roomFees/index.php' ?>" class="btn btn-secondary mb-3 mt-4">← Back</a>
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h4>Edit Room Fee</h4>
            </div>
            <div class="card-body">
                <form id="editFeesForm">
                    <!-- passing generated csrf token -->
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                    <!-- Passing room fee id -->
                    <input type="hidden" name="id" value="<?= $roomFee_Id ?>">

                    <!-- select hostel option -->
                    <div class="mb-3">
                        <label for="hostel_id" class="form-label">Hostel</label>
                        <select name="hostel_id" id="hostel_id" class="form-select" required="required">
                            <option value="">-- Select Hostel --</option>
                            <?php foreach ($hostels as $hostel): ?>
                                <option value="<?= $hostel['id'] ?>" <?= $hostel['id'] == $roomFees[0]['hostel_id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($hostel['hostel_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- select room types option -->
                    <div class="mb-3">
                        <label for="room_type_id" class="form-label">Room Type</label>
                        <select name="room_type_id" id="room_type_id" class="form-select" required>
                            <option value="">-- Select Room Type --</option>
                            <?php if (!empty($roomTypes)): ?>
                                <?php foreach ($roomTypes as $roomType): ?>
                                    <option value="<?= $roomType['id'] ?>"
                                        <?= $roomType['id'] == $roomFees[0]['room_type_id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($roomType['type_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>

                            <?php if (empty($roomTypes)): ?>
                                <div class="text-danger mt-2">No room types found for this hostel</div>
                            <?php endif; ?>
                        </select>
                    </div>


                    <!-- Price by setting it as default from passed room fees id -->
                    <div class="mb-3">
                        <label for="price" class="form-label">price</label>
                        <input type="text" name="price" id="price" class="form-control" value="<?= htmlspecialchars($roomFees[0]['price']) ?>" required>
                    </div>

                    <!-- Billing Cycle by setting it as default from passed room fees id -->
                    <div class="mb-3">
                        <label for="billing_cycle" class="form-label">Billing Cycle</label>
                        <select name="billing_cycle" id="billing_cycle" class="form-select" required>
                            <option value="">-- Select Billing Cycle --</option>
                            <?php
                            // Ensure $billingCycles is defined
                            $billingCycles = ['monthly', 'quarterly', 'yearly'];
                            // Use null coalescing operator in case $roomFees is not set
                            $selectedCycle = strtolower($roomFees[0]['billing_cycle'] ?? '');
                            foreach ($billingCycles as $cycle):
                                $isSelected = strtolower($cycle) === $selectedCycle ? 'selected' : '';
                            ?>
                                <option value="<?php echo htmlspecialchars($cycle); ?>" <?php echo $isSelected; ?>>
                                    <?php echo ucfirst($cycle); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>



                    <!-- effective from by setting it as default from passed room fees id -->
                    <div class="mb-3">
                        <label for="effective_from" class="form-label">Effective From</label>
                        <input type="date" name="effective_from" id="effective_from" class="form-control" value="<?= htmlspecialchars($roomFees[0]['effective_from']) ?>" required>
                    </div>

                    <button type="submit" class="btn btn-primary">Update Fee</button>
                </form>
                <div id="showMessage" class="mt-3"></div>
            </div>
        </div>
    </div>


    <script>
        $(document).ready(function() {
            $('#hostel_id').on('change', function() {
                const hostelId = $(this).val(); // storing the value of value attribute from hostel options
                const $roomTypeSelect = $('#room_type_id');

                // Clear existing options
                $roomTypeSelect.html('<option value="">-- Select Room Type --</option>');

                if (hostelId) {
                    $.ajax({
                        url: '<?= BASE_URL . '/admin/php_files/sections/roomTypes/get_room_types_by_hostel_id.php' ?>',
                        method: 'GET',
                        data: {
                            hostel_id: hostelId
                        },
                        dataType: 'json',
                        success: function(response) {
                            const messageContainer = $('<div class="text-danger mt-2"> No room types found for this hostel. </div> ');
                            $('#room_type_id').next('.text-danger').remove(); // remove old message if 

                            if (response.success && Array.isArray(response.data) && response.data.length > 0) {
                                response.data.forEach(function(roomType) {
                                    $roomTypeSelect.append(
                                        $('<option>', { // creating new optins based on fetched room types
                                            value: roomType.id,
                                            text: roomType.type_name
                                        })
                                    );
                                })
                            } else {
                                // show the no-room-types message
                                $roomTypeSelect.after(messageContainer);
                                console.warn(response.message || 'No room types found.');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('AJAX error: ', status, error);
                        }
                    })
                }

            })
        })

        // handle on submit
        $('#editFeesForm').on('submit', function (e) {
            e.preventDefault();

            const submitBtn = $(this).find('button[type="submit"]');
            submitBtn.prop('disabled', true).text('Updating...');

            $.ajax({
                url: '<?= BASE_URL . '/admin/php_files/sections/roomFees/edit.php' ?>',
                method: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        showSlideMessage(response.message, 'success');

                        setTimeout(function () {
                            window.location.href = '<?= BASE_URL . '/admin/sections/roomFees/index.php' ?>';
                        }, 2000);
                    } else {
                        showSlideMessage(response.message || 'No fees record found.', 'danger');
                        submitBtn.prop('disabled', false).text('Update Fee');
                    }
                },
                error: function (xhr, status, error) {
                    showSlideMessage('An error occurred. Please try again.', 'danger');
                    submitBtn.prop('disabled', false).text('Update Fee');
                    console.error('AJAX error: ', status, error);
                }
            });
        });

    </script>
    <?php require_once BASE_PATH . '/admin/includes/footer_admin.php' ?>