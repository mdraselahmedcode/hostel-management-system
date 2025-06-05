<?php
session_start();
require_once __DIR__ . '/../../../config/config.php';
require_once BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/admin/php_files/auth_check_admin.php';

// accepting and validating room fee id
$feeId = isset($_GET['roomFees_id']) ? (int)$_GET['roomFees_id'] : 0;
if ($feeId <= 0) {
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


// Fetch hostels
$hostels = [];
$hostelStmt = $conn->query("SELECT id, hostel_name FROM hostels ORDER BY hostel_name ASC");
if ($hostelStmt && $hostelStmt->num_rows > 0) {
    $hostels = $hostelStmt->fetch_all(MYSQLI_ASSOC);
}


$selectedHostelId = $hostel_id; 
$selectedRoomTypeId = $roomType_id; 
$selectedRoomFeesId = $feeId; 

// fetching room types based on the selected hostel id
$roomTypes = [];
if($selectedRoomTypeId > 0) {
    $roomTypeStmt = $conn->prepare("
        SELECT id, type_name
        FROM room_types
        WHERE hostel_id = ?
    ");
    $roomTypeStmt->bind_param("i", $selectedHostelId);
    $roomTypeStmt->execute();
    $result = $roomTypeStmt->get_result(); 
    
    if($result->num_rows === 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid room type'
        ]);
        exit; 
    }
    $roomTypes = $result->fetch_all(MYSQLI_ASSOC);
    $roomTypeStmt->close(); 
    header('Content-Type: application/json');
    echo json_encode($roomTypes, JSON_PRETTY_PRINT);
    exit;
}


// fetching room fees based on the selected hostel id and room type id
$roomFees = [];
if($selectedRoomFeesId > 0) {
    $roomFeeStmt = $conn->prepare("
        SELECT * 
        From room_fees 
        WHERE hostel_id = ? AND room_type_id = ?
    ");
    $roomFeeStmt->bind_param("ii", $selectedHostelId, $selectedRoomTypeId);
    $roomFeeStmt->execute(); 
    $result = $roomFeeStmt->get_result(); 
    if($result->num_rows === 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid Room Fees Id'
        ]);
    }
    $roomFees = $result->fetch_all(MYSQLI_ASSOC); 
    $roomFeeStmt->close();

    
}





$billingCycles = ['monthly', 'quarterly', 'yearly'];

require_once BASE_PATH . '/admin/includes/header_admin.php';
?>

<div class="content container mt-5">
    <a href="<?= BASE_URL . '/admin/sections/roomFees/index.php' ?>" class="btn btn-secondary mb-3">← Back</a>

    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h4>Edit Room Fee</h4>
        </div>
        <div class="card-body">
            <form id="editFeesForm">
                <input type="hidden" name="id" value="<?= $roomFees['id'] ?>">

                <div class="mb-3">
                    <label for="hostel_id" class="form-label">Hostel</label>
                    <select name="hostel_id" id="hostel_id" class="form-select" required>
                        <option value="">-- Select Hostel --</option>
                        <?php foreach ($hostels as $hostel): ?>
                            <option value="<?= $hostel['id'] ?>" <?= $hostel['id'] == $roomFees['hostel_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($hostel['hostel_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="room_type_id" class="form-label">Room Type</label>
                    <select name="room_type_id" id="room_type_id" class="form-select" required>
                        <option value="">-- Select Room Type --</option>
                        <?php foreach ($roomTypes as $type): ?>
                            <option value="<?= $type['id'] ?>" <?= $type['id'] == $roomFees['room_type_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($type['type_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="price" class="form-label">Price</label>
                    <input type="text" name="price" id="price" class="form-control" value="<?= htmlspecialchars($roomFees['price']) ?>" required>
                </div>

                <div class="mb-3">
                    <label for="billing_cycle" class="form-label">Billing Cycle</label>
                    <select name="billing_cycle" id="billing_cycle" class="form-select" required>
                        <option value="">-- Select Billing Cycle --</option>
                        <?php foreach ($billingCycles as $cycle): ?>
                            <option value="<?= $cycle ?>" <?= $cycle === $roomFees['billing_cycle'] ? 'selected' : '' ?>>
                                <?= ucfirst($cycle) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="effective_from" class="form-label">Effective From</label>
                    <input type="date" name="effective_from" id="effective_from" class="form-control" value="<?= htmlspecialchars($roomFees['effective_from']) ?>" required>
                </div>

                <button type="submit" class="btn btn-primary">Update Fee</button>
            </form>
            <div id="formMessage" class="mt-3"></div>
        </div>
    </div>
</div>

<script>
    $('#editFeesForm').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const formData = form.serialize();

        $.ajax({
            type: 'POST',
            url: '<?= BASE_URL ?>/admin/php_files/sections/roomFees/update.php',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#formMessage').html('<div class="alert alert-success">' + response.message + '</div>');
                    setTimeout(() => {
                        window.location.href = 'index.php';
                    }, 2000);
                } else {
                    $('#formMessage').html('<div class="alert alert-danger">' + response.message + '</div>');
                }
            },
            error: function() {
                $('#formMessage').html('<div class="alert alert-danger">An error occurred. Please try again.</div>');
            }
        });
    });



    // $('#hostel_id').on('change', function() {
    //     const hostelId = $(this).val();

    //     $('#room_type_id').html('<option value="">Loading...</option>');

    //     $.ajax({
    //         type: 'POST',
    //         url: '<?= BASE_URL ?>/admin/php_files/sections/roomFees/get_room_types_by_hostel_id.php',
    //         data: {
    //             hostel_id: hostelId
    //         },
    //         dataType: 'json',
    //         success: function(response) {
    //             if (response.success) {
    //                 let options = '<option value="">-- Select Room Type --</option>';
    //                 response.roomTypes.forEach(type => {
    //                     options += `<option value="${type.id}">${type.type_name}</option>`;
    //                 });
    //                 $('#room_type_id').html(options);
    //             } else {
    //                 $('#room_type_id').html('<option value="">No room types found</option>');
    //             }
    //         },
    //         error: function() {
    //             $('#room_type_id').html('<option value="">Error fetching room types</option>');
    //         }
    //     });
    // });
</script>

<?php require_once BASE_PATH . '/admin/includes/footer_admin.php'; ?>