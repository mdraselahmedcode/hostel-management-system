<?php
require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../../config/db.php';
require_once BASE_PATH . '/student/includes/header_student.php';

require_once BASE_PATH . '/config/auth.php'; 

require_student(); 

$student_id = $_SESSION['student']['id'];

// Fetch student room details (NO room_facilities join)
$room_details = null;
$roommates = [];

$sql = "SELECT 
            s.id AS student_id, s.first_name, s.last_name, s.email, s.profile_image_url, s.varsity_id, s.department,
            r.id AS room_id, r.room_number, r.max_capacity, r.hostel_id AS room_hostel_id, r.room_type_id,
            f.id AS floor_id, f.floor_number, f.floor_name,
            h.id AS hostel_id, h.hostel_name, h.hostel_type, h.contact_number,
            rt.type_name AS room_type, rt.description AS room_type_desc
        FROM students s
        LEFT JOIN rooms r ON s.room_id = r.id
        LEFT JOIN floors f ON r.floor_id = f.id
        LEFT JOIN hostels h ON r.hostel_id = h.id
        LEFT JOIN room_types rt ON r.room_type_id = rt.id
        WHERE s.id = ?
        GROUP BY s.id";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $student_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result && $result->num_rows > 0) {
    $room_details = $result->fetch_assoc();
}
$stmt->close();

// Calculate current occupancy
$current_occupancy = 0;
if ($room_details && $room_details['room_id']) {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM students WHERE room_id = ?");
    $stmt->bind_param('i', $room_details['room_id']);
    $stmt->execute();
    $stmt->bind_result($current_occupancy);
    $stmt->fetch();
    $stmt->close();
}

// Fetch roommates
if ($room_details && $room_details['room_id']) {
    $sql_roommates = "SELECT 
                        s.id, s.first_name, s.last_name, s.email, s.profile_image_url, 
                        s.contact_number, s.varsity_id, s.department
                      FROM students s
                      WHERE room_id = ? AND id != ?
                      ORDER BY s.first_name";
    $stmt = $conn->prepare($sql_roommates);
    $stmt->bind_param('ii', $room_details['room_id'], $student_id);
    $stmt->execute();
    $roommates = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

// Fetch room fee details with history
$current_fee = null;
$fee_history = [];
if ($room_details && $room_details['room_hostel_id'] && $room_details['room_type_id']) {
    $sql_fee = "SELECT price, billing_cycle, effective_from
                FROM room_fees 
                WHERE hostel_id = ? AND room_type_id = ? 
                ORDER BY effective_from DESC";
    $stmt = $conn->prepare($sql_fee);
    $stmt->bind_param('ii', $room_details['room_hostel_id'], $room_details['room_type_id']);
    $stmt->execute();
    $fee_result = $stmt->get_result();
    if ($fee_result && $fee_result->num_rows > 0) {
        $first = true;
        while ($row = $fee_result->fetch_assoc()) {
            if ($first) {
                $current_fee = $row;
                $first = false;
            } else {
                $fee_history[] = $row;
            }
        }
    }
    $stmt->close();
}
?>



<div class="content container-fluid">
    <div class="row full-height">
        <?php require_once BASE_PATH . '/student/includes/sidebar_student.php'; ?>
        <main class="col-md-10 ms-sm-auto px-md-4" style="overflow-y: auto; max-height: calc(100vh - 119px);">
            <div class="mb-3 mt-3">
                <a href="javascript:history.back()" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back
                </a>
            </div>
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">My Accommodation Details</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#roomChangeModal">
                        <i class="bi bi-house-gear"></i> Request Room Change
                    </button>
                </div>
            </div>

            <?php if ($room_details && $room_details['room_id']): ?>
                <!-- Room Overview Card -->
                <div class="row mb-4">
                    <div class="col-md-8">
                        <div class="card shadow-sm h-100">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">
                                    <i class="bi bi-door-open"></i> Room <?= htmlspecialchars($room_details['room_number']) ?> 
                                    <span class="badge bg-light text-dark float-end"><?= htmlspecialchars($room_details['room_type']) ?></span>
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="room-info">
                                            <p><strong><i class="bi bi-building"></i> Hostel:</strong> 
                                                <?= htmlspecialchars($room_details['hostel_name']) ?>
                                                <span class="badge bg-info text-dark"><?= htmlspecialchars(ucfirst($room_details['hostel_type'])) ?></span>
                                            </p>
                                            <p><strong><i class="bi bi-layer-forward"></i> Floor:</strong> 
                                                <?= htmlspecialchars($room_details['floor_name']) ?> (Floor <?= htmlspecialchars($room_details['floor_number']) ?>)
                                            </p>
                                            <p><strong><i class="bi bi-people"></i> Occupancy:</strong> 
                                                <span class="text-success"><?= $current_occupancy ?></span> / 
                                                <?= htmlspecialchars($room_details['max_capacity']) ?> students
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="room-contact">
                                            <p><strong><i class="bi bi-telephone"></i> Hostel Contact:</strong> 
                                                <a href="tel:<?= htmlspecialchars($room_details['contact_number']) ?>">
                                                    <?= htmlspecialchars($room_details['contact_number']) ?>
                                                </a>
                                            </p>
                                            <!-- hostel address -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card shadow-sm h-100">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0"><i class="bi bi-cash-stack"></i> Current Room Fee</h5>
                            </div>
                            <div class="card-body text-center">
                                <?php if ($current_fee): ?>
                                    <h3 class="text-primary"><?= number_format($current_fee['price'], 2) ?> BDT</h3>
                                    <p class="mb-1"><strong>Billing Cycle:</strong> 
                                        <span class="badge bg-info"><?= htmlspecialchars(ucfirst($current_fee['billing_cycle'])) ?></span>
                                    </p>
                                    <p class="text-muted"><small>Effective from <?= date('M d, Y', strtotime($current_fee['effective_from'])) ?></small></p>
                                    <a href="<?= BASE_URL . '/student/sections/payment/payment_history.php' ?>" class="btn btn-sm btn-outline-primary mt-2">
                                        View Payment History
                                    </a>
                                <?php else: ?>
                                    <div class="alert alert-warning mb-0">No fee information available</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Roommates Section -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-people-fill"></i> Roommates</h5>
                    </div>
                    <div class="card-body">
                        <?php if (count($roommates) > 0): ?>
                            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                                <?php foreach ($roommates as $mate): ?>
                                    <div class="col">
                                        <div class="card h-100">
                                            <div class="card-body text-center">
                                                <div class="position-relative d-inline-block">
                                                    <?php if ($mate['profile_image_url']): ?>
                                                        <img src="<?= htmlspecialchars($mate['profile_image_url']) ?>" 
                                                             class="rounded-circle mb-3" 
                                                             width="100" height="100" 
                                                             alt="<?= htmlspecialchars($mate['first_name']) ?>">
                                                    <?php else: ?>
                                                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mb-3" 
                                                             style="width: 100px; height: 100px;">
                                                            <i class="bi bi-person-circle" style="font-size: 3rem;"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                                <h5><?= htmlspecialchars($mate['first_name'] . ' ' . $mate['last_name']) ?></h5>
                                                <p class="text-muted mb-1"><?= htmlspecialchars($mate['varsity_id']) ?></p>
                                                <p class="text-muted mb-2"><?= htmlspecialchars($mate['department']) ?></p>
                                                <div class="d-flex justify-content-center gap-2">
                                                    <a href="mailto:<?= htmlspecialchars($mate['email']) ?>" class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-envelope"></i>
                                                    </a>
                                                    <a href="tel:<?= htmlspecialchars($mate['contact_number']) ?>" class="btn btn-sm btn-outline-success">
                                                        <i class="bi bi-telephone"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <i class="bi bi-emoji-frown" style="font-size: 3rem;"></i>
                                <h5 class="mt-3">No roommates assigned</h5>
                                <p class="text-muted">You currently have the room to yourself</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Fee History (if available) -->
                <?php if (!empty($fee_history)): ?>
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="bi bi-clock-history"></i> Fee History</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Effective Date</th>
                                            <th>Amount (BDT)</th>
                                            <th>Billing Cycle</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($fee_history as $fee): ?>
                                            <tr>
                                                <td><?= date('M d, Y', strtotime($fee['effective_from'])) ?></td>
                                                <td><?= number_format($fee['price'], 2) ?></td>
                                                <td><?= htmlspecialchars(ucfirst($fee['billing_cycle'])) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                
            <?php else: ?>
                <div class="alert alert-warning">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-exclamation-triangle-fill me-2" style="font-size: 1.5rem;"></i>
                        <div>
                            <h5 class="mb-1">No Room Assigned</h5>
                            <p class="mb-0">You are not currently assigned to any room. Please contact the hostel administration.</p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </main>
    </div>
</div>

<!-- Room Change Modal -->
<div class="modal fade" id="roomChangeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Request Room Change</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="roomChangeForm">
                    <div class="mb-3">
                        <label for="reason" class="form-label">Reason for Change</label>
                        <select class="form-select" id="reason" name="reason" required>
                            <option value="">Select a reason</option>
                            <option value="roommate issues">Roommate Issues</option>
                            <option value="too noisy">Too Noisy</option>
                            <option value="prefer different location">Prefer Different Location</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="details" class="form-label">Additional Details</label>
                        <textarea class="form-control" id="details" name="details" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="preferredRoom" class="form-label">Preferred Room (Optional)</label>
                        <input type="text" class="form-control" id="preferredRoom" name="preferred_room">
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Submit Request</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .room-info p, .room-contact p {
        margin-bottom: 0.8rem;
    }
    .card {
        border-radius: 10px;
        border: none;
    }
    .card-header {
        border-radius: 10px 10px 0 0 !important;
    }
    .badge {
        font-weight: 500;
    }
</style>

<script>
$(document).ready(function() {
    // Room change form submission
    $('#roomChangeForm').on('submit', function(e) {
        e.preventDefault();
        // Add AJAX submission logic here
        alert('Room change request submitted!');
        $('#roomChangeModal').modal('hide');
    });
});
</script>

<?php require_once BASE_PATH . '/student/includes/footer_student.php'; ?>