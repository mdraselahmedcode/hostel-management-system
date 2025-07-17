<?php
require_once __DIR__ . '/../../../config/config.php';
require_once BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/config/auth.php'; 



require_student(); 

require_once BASE_PATH . '/student/includes/header_student.php';

$student = $_SESSION['student'];

// Fetch student details with joins
$stmt = $conn->prepare("
    SELECT s.*, r.room_number, h.hostel_name, f.floor_number, 
           a.country_id, a.state, a.division, a.district, a.sub_district, 
           a.village, a.postalcode, a.street, a.house_no, c.country_name
    FROM students s
    LEFT JOIN rooms r ON s.room_id = r.id
    LEFT JOIN hostels h ON s.hostel_id = h.id
    LEFT JOIN floors f ON r.floor_id = f.id
    LEFT JOIN addresses a ON s.permanent_address_id = a.id
    LEFT JOIN countries c ON a.country_id = c.id
    WHERE s.id = ?
");
$stmt->bind_param("i", $student['id']);
$stmt->execute();
$profile = $stmt->get_result()->fetch_assoc();
$stmt->close();
?>


<div class="content container-fluid" >
    
    <div class="row full-height">
        <!-- sidebar -->
        <?php require_once BASE_PATH . '/student/includes/sidebar_student.php'; ?>
        
        <!-- main content -->
         <main class="col-md-10 ms-sm-auto px-md-4" style="overflow-y: auto; max-height: calc(100vh - 20vh);">

            <div class="mb-3 mt-3">
                <a href="javascript:history.back()" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back
                </a>
            </div>

            <div class="row">

                <div class="col-lg-4">
                    <!-- Profile Card -->
                    <div class="card profile-card mb-4">
                        <div class="card-body text-center">
                            <div class="profile-avatar-container">
                                <img src="<?= htmlspecialchars($profile['profile_image_url'] ?? ('https://ui-avatars.com/api/?name=' . urlencode(($profile['first_name'] ?? '') . '+' . ($profile['last_name'] ?? '')) . '&background=random')) ?>"
                                    class="rounded-circle shadow"
                                    alt="Profile Image"
                                    width="150" height="150">
                                <button class="btn btn-sm btn-primary avatar-edit-btn" data-bs-toggle="modal" data-bs-target="#avatarModal">
                                    <i class="bi bi-camera-fill"></i>
                                </button>
                            </div>
                            <h4 class="mt-3 mb-1"><?= htmlspecialchars($profile['first_name'] . ' ' . $profile['last_name']) ?></h4>
                            <p class="text-muted mb-3"><?= htmlspecialchars($profile['varsity_id']) ?></p>

                            <div class="d-flex justify-content-center mb-3">
                                <button class="btn btn-outline-primary me-2" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                                    <i class="bi bi-pencil-fill"></i> Edit Profile
                                </button>
                            </div>

                            <hr>

                            <div class="profile-meta">
                                <div class="meta-item">
                                    <i class="bi bi-envelope-fill"></i>
                                    <span><?= htmlspecialchars($profile['email']) ?></span>
                                </div>
                                <div class="meta-item">
                                    <i class="bi bi-telephone-fill"></i>
                                    <span><?= htmlspecialchars($profile['contact_number'] ?? 'Not provided') ?></span>
                                </div>
                                <div class="meta-item">
                                    <i class="bi bi-gender-ambiguous"></i>
                                    <span><?= htmlspecialchars(ucfirst($profile['gender'] ?? '')) ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Links Card -->
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0">Quick Links</h6>
                        </div>
                        <div class="list-group list-group-flush">
                            <a href="payment_history.php" class="list-group-item list-group-item-action">
                                <i class="bi bi-credit-card-fill me-2"></i>Payment History
                            </a>
                            <a href="room_change.php" class="list-group-item list-group-item-action">
                                <i class="bi bi-door-open-fill me-2"></i>Room Change Request
                            </a>
                            <a href="complaints.php" class="list-group-item list-group-item-action">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>Submit Complaint
                            </a>
                            <a href="change_password.php" class="list-group-item list-group-item-action">
                                <i class="bi bi-key-fill me-2"></i>Change Password
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <!-- Main Profile Details -->
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Profile Information</h6>
                            <?php if (!empty($profile['is_checked_in'])): ?>
                                <span class="badge bg-success">Checked In</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Not Checked In</span>
                            <?php endif; ?>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <!-- Hostel Information -->
                                <div class="col-md-6">
                                    <div class="info-card">
                                        <h6 class="info-title">
                                            <i class="bi bi-building me-2"></i>Hostel Details
                                        </h6>
                                        <div class="info-content">
                                            <p><strong>Hostel:</strong> <?= htmlspecialchars($profile['hostel_name'] ?? 'Not assigned') ?></p>
                                            <p><strong>Room No:</strong> <?= htmlspecialchars($profile['room_number'] ?? 'Not assigned') ?></p>
                                            <p><strong>Floor:</strong> <?= htmlspecialchars($profile['floor_number'] ?? 'N/A') ?></p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Academic Information -->
                                <div class="col-md-6">
                                    <div class="info-card">
                                        <h6 class="info-title">
                                            <i class="bi bi-book me-2"></i>Academic Info
                                        </h6>
                                        <div class="info-content">
                                            <p><strong>Department:</strong> <?= htmlspecialchars($profile['department'] ?? 'Not provided') ?></p>
                                            <p><strong>Year:</strong> <?= htmlspecialchars($profile['batch_year'] ?? 'N/A') ?></p>
                                            <p><strong>Student Since:</strong> <?= date('d M Y', strtotime($profile['created_at'] ?? 'now')) ?></p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Permanent Address -->
                                <div class="col-12">
                                    <div class="info-card">
                                        <h6 class="info-title">
                                            <i class="bi bi-house-door-fill me-2"></i>Permanent Address
                                        </h6>
                                        <div class="info-content">
                                            <address>
                                                <?php if (!empty($profile['house_no'])): ?>
                                                    <?= htmlspecialchars($profile['house_no']) ?>,
                                                <?php endif; ?>
                                                <?= htmlspecialchars($profile['street'] ?? '') ?><br>
                                                <?= htmlspecialchars($profile['village'] ?? '') ?>,
                                                <?= htmlspecialchars($profile['sub_district'] ?? '') ?><br>
                                                <?= htmlspecialchars($profile['district'] ?? '') ?>,
                                                <?= htmlspecialchars($profile['division'] ?? '') ?><br>
                                                <?= htmlspecialchars($profile['state'] ?? '') ?>,
                                                <?= htmlspecialchars($profile['postalcode'] ?? '') ?><br>
                                                <?= htmlspecialchars($profile['country_name'] ?? '') ?>
                                            </address>
                                        </div>
                                    </div>
                                </div>

                                <!-- Emergency Contact -->
                                <div class="col-md-6">
                                    <div class="info-card">
                                        <h6 class="info-title">
                                            <i class="bi bi-person-lines-fill me-2"></i>Emergency Contact
                                        </h6>
                                        <div class="info-content">
                                            <p><strong>Father:</strong> <?= htmlspecialchars($profile['father_name'] ?? 'Not provided') ?></p>
                                            <p><strong>Contact:</strong> <?= htmlspecialchars($profile['father_contact'] ?? 'N/A') ?></p>
                                            <p><strong>Contact:</strong> <?= htmlspecialchars($profile['emergency_contact'] ?? 'N/A') ?></p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Additional Info -->
                                <div class="col-md-6">
                                    <div class="info-card">
                                        <h6 class="info-title">
                                            <i class="bi bi-info-circle-fill me-2"></i>Additional Info
                                        </h6>
                                        <div class="info-content">
                                            <p><strong>Blood Group:</strong> <?= htmlspecialchars($profile['blood_group'] ?? 'Not specified') ?></p>
                                            <!-- <p><strong>Medical Info:</strong> <?= htmlspecialchars($profile['medical_info'] ?? 'None') ?></p> -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Status Cards -->
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="card status-card bg-success bg-opacity-10">
                                <div class="card-body text-center">
                                    <h6 class="card-title">Payment Status</h6>
                                    <div class="status-icon">
                                        <i class="bi bi-check-circle-fill text-success"></i>
                                    </div>
                                    <p class="mb-0">Up to date</p>
                                    <small class="text-light">Last paid: 15 Oct 2023</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card status-card bg-info bg-opacity-10">
                                <div class="card-body text-center">
                                    <h6 class="card-title">Room Status</h6>
                                    <div class="status-icon">
                                        <i class="bi bi-house-check-fill text-info"></i>
                                    </div>
                                    <p class="mb-0">
                                        <?= ($profile['is_checked_in'] ?? 0) ? 'Active' : 'Not Checked In' ?>
                                    </p>
                                    <small class="text-light">
                                        Checked in:
                                        <?= !empty($profile['check_in_at']) && $profile['check_in_at'] !== '0000-00-00 00:00:00'
                                            ? date('d M Y', strtotime($profile['check_in_at']))
                                            : 'N/A' ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card status-card bg-warning bg-opacity-10">
                                <div class="card-body text-center">
                                    <h6 class="card-title">Complaints</h6>
                                    <div class="status-icon">
                                        <i class="bi bi-exclamation-triangle-fill text-warning"></i>
                                    </div>
                                    <p class="mb-0">1 Pending</p>
                                    <small class="text-light">Last update: 2 days ago</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </main>
    </div>
</div>

<!-- Avatar Update Modal -->
<div class="modal fade" id="avatarModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Profile Picture</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="avatarForm" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="profileImage" class="form-label">Select new image</label>
                        <input class="form-control" type="file" id="profileImage" name="profile_image" accept="image/*">
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Profile Modal -->
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Profile Information</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editProfileForm">
                    <!-- Form content would go here -->
                    <div class="text-end">
                        <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once BASE_PATH . '/student/includes/footer_student.php'; ?>

<style>
    .profile-card {
        border-radius: 15px;
        overflow: hidden;
        border: none;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
    }

    .profile-avatar-container {
        position: relative;
        display: inline-block;
    }

    .avatar-edit-btn {
        position: absolute;
        bottom: 10px;
        right: 10px;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        padding: 0;
    }

    .profile-meta .meta-item {
        display: flex;
        align-items: center;
        margin-bottom: 10px;
    }

    .profile-meta .meta-item i {
        width: 24px;
        text-align: center;
        margin-right: 10px;
        color: #0d6efd;
    }

    .info-card {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 15px;
        height: 100%;
    }

    .info-title {
        color: #0d6efd;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 15px;
        padding-bottom: 8px;
        border-bottom: 1px solid #eee;
    }

    .info-content p {
        margin-bottom: 8px;
    }

    .status-card {
        border-radius: 10px;
        border: none;
        transition: all 0.3s;
    }

    .status-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .status-icon {
        font-size: 2rem;
        margin: 10px 0;
    }
</style>

<script>
    $(document).ready(function() {
        // Avatar form submission
        $('#avatarForm').on('submit', function(e) {
            e.preventDefault();
            // AJAX upload logic here
        });

        // Edit profile form submission
        $('#editProfileForm').on('submit', function(e) {
            e.preventDefault();
            // AJAX update logic here
        });
    });
</script>