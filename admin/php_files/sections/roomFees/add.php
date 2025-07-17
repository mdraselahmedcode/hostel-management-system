    <?php
    header('Content-Type: application/json');

    require_once __DIR__ . '/../../../../config/config.php';
    require_once BASE_PATH . '/config/db.php';
    // only admin will get access
    require_once BASE_PATH . '/config/auth.php';

    require_admin();

    // Validate and sanitize input 
    $hostel_id       = isset($_POST['hostel_id']) ? intval($_POST['hostel_id']) : 0;
    $room_type_id    = isset($_POST['room_type_id']) ? intval($_POST['room_type_id']) : 0;
    $price           = isset($_POST['price']) ? trim($_POST['price']) : '';
    $billing_cycle   = isset($_POST['billing_cycle']) ? trim($_POST['billing_cycle']) : '';
    $effective_from  = isset($_POST['effective_from']) ? trim($_POST['effective_from']) : '';

    // Validate required fields
    if (!$hostel_id || !$room_type_id || !$price || !$billing_cycle || !$effective_from) {
        echo json_encode(['success' => false, 'message' => 'All fields are required.']);
        exit;
    }

    // Validate that the room_type belongs to the given hostel
    $roomTypeCheck = $conn->prepare("
        SELECT id FROM room_types WHERE id = ? AND hostel_id = ?
    ");
    $roomTypeCheck->bind_param("ii", $room_type_id, $hostel_id);
    $roomTypeCheck->execute();
    $result = $roomTypeCheck->get_result();
    $roomTypeCheck->close();

    if ($result->num_rows !== 1) {
        echo json_encode(['success' => false, 'message' => 'Invalid room type or hostel.']);
        exit;
    }

    // Validate date
    $date = DateTime::createFromFormat('Y-m-d', $effective_from);
    if (!$date || $date->format('Y-m-d') !== $effective_from) {
        echo json_encode(['success' => false, 'message' => 'Invalid date format.']);
        exit;
    }

    // Check for duplicate fee entry for same hostel + room type + effective date
    $duplicateCheck = $conn->prepare("
        SELECT id FROM room_fees 
        WHERE hostel_id = ? AND room_type_id = ?
    ");
    $duplicateCheck->bind_param("ii", $hostel_id, $room_type_id);
    $duplicateCheck->execute();
    $duplicateCheck->store_result();

    if ($duplicateCheck->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Fee already exists for this room type in this hostel']);
        $duplicateCheck->close();
        exit;
    }
    $duplicateCheck->close();

    // Validate price
    if (!is_numeric($price) || floatval($price) < 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid price.']);
        exit;
    }

    // Validate billing cycle
    $valid_cycles = ['monthly', 'quarterly', 'yearly'];
    if (!in_array($billing_cycle, $valid_cycles)) {
        echo json_encode(['success' => false, 'message' => 'Invalid billing cycle.']);
        exit;
    }

    // Insert new fee record
    $insertStmt = $conn->prepare("
        INSERT INTO room_fees (hostel_id, room_type_id, price, billing_cycle, effective_from)
        VALUES (?, ?, ?, ?, ?)
    ");

    if (!$insertStmt) {
        echo json_encode(['success' => false, 'message' => 'Insert preparation failed.']);
        exit;
    }

    $insertStmt->bind_param("iisss", $hostel_id, $room_type_id, $price, $billing_cycle, $effective_from);

    if ($insertStmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Room fee added successfully.']);
    } else {
        error_log("Insert Error: " . $insertStmt->error);
        echo json_encode(['success' => false, 'message' => 'Failed to insert room fee.']);
    }

    $insertStmt->close();
    $conn->close();
