<?php
session_start();
require_once __DIR__ . '/../../../config/config.php';
require_once BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/admin/php_files/auth_check_admin.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Invalid Hostel ID.');
}

$hostelId = intval($_GET['id']);

// Fetch single hostel details
$sql = "
    SELECT 
        hostels.*, 
        admins.firstname AS incharge_firstname,
        admins.lastname AS incharge_lastname,
        addresses.*,
        countries.country_name,
        COUNT(floors.id) AS number_of_floors,
        (
            SELECT COUNT(*) 
            FROM rooms 
            WHERE rooms.floor_id IN (
                SELECT id FROM floors WHERE hostel_id = hostels.id
            )
        ) AS number_of_rooms
    FROM hostels
    LEFT JOIN admins ON hostels.hostel_incharge_id = admins.id
    LEFT JOIN addresses ON hostels.address_id = addresses.id
    LEFT JOIN countries ON addresses.country_id = countries.id
    LEFT JOIN floors ON hostels.id = floors.hostel_id
    WHERE hostels.id = $hostelId
    GROUP BY hostels.id
    LIMIT 1;
";

$result = $conn->query($sql);
$hostel = $result->fetch_assoc();

if (!$hostel) {
    die("Hostel not found.");
}

require_once BASE_PATH . '/admin/includes/header_admin.php';
?>



<?php
// [Your existing PHP code...]
?>

<style>
    .hostel-detail-card {
        border-radius: 10px;
        overflow: hidden;
        border: none;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    }
    
    .card-header {
        padding: 1.25rem 1.5rem;
        background: linear-gradient(135deg, #2c3e50 0%, #1a2530 100%);
    }
    
    .detail-section {
        margin-bottom: 1.5rem;
    }
    
    .detail-section h5 {
        color: #3c8dbc;
        border-bottom: 2px solid #f1f1f1;
        padding-bottom: 0.5rem;
        margin-bottom: 1rem;
        font-weight: 600;
    }
    
    .detail-item {
        margin-bottom: 0.75rem;
    }
    
    .detail-item strong {
        color: #495057;
        min-width: 120px;
        display: inline-block;
    }
    
    .amenities-list {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }
    
    .amenity-badge {
        background-color: #e9f7fe;
        color: #3c8dbc;
        padding: 0.35rem 0.75rem;
        border-radius: 50px;
        font-size: 0.85rem;
    }
    
    .address-block {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 1.25rem;
        line-height: 1.6;
    }
    
    .back-btn {
        transition: all 0.3s ease;
    }
    
    .back-btn:hover {
        transform: translateX(-3px);
    }
</style>

<div class="container py-4 mt-4">
    <div class="row">
        <div class="col-lg-10 mx-auto">
            <a href="<?= BASE_URL ?>/admin/sections/hostels/index.php" class="btn btn-outline-secondary back-btn mb-4 mt-4">
                <i class="bi bi-arrow-left me-2"></i>Back to Hostel List
            </a>

            <div class="card hostel-detail-card">
                <div class="card-header text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="bi bi-building me-2"></i><?= htmlspecialchars($hostel['hostel_name']) ?>
                        </h4>
                        <span class="badge bg-<?= $hostel['hostel_type'] === 'male' ? 'primary' : 'pink' ?>">
                            <?= ucfirst($hostel['hostel_type']) ?> Hostel
                        </span>
                    </div>
                </div>
                
                <div class="card-body">
                    <div class="row">
                        <!-- Left Column -->
                        <div class="col-md-6">
                            <div class="detail-section">
                                <h5><i class="bi bi-info-circle me-2"></i>Basic Information</h5>
                                
                                <div class="detail-item">
                                    <strong>Capacity:</strong>
                                    <span class="badge bg-info"><?= $hostel['capacity'] ?></span>
                                </div>
                                
                                <div class="detail-item">
                                    <strong>Floors:</strong>
                                    <span class="badge bg-secondary"><?= $hostel['number_of_floors'] ?></span>
                                </div>
                                
                                <div class="detail-item">
                                    <strong>Rooms:</strong>
                                    <span class="badge bg-secondary"><?= $hostel['number_of_rooms'] ?></span>
                                </div>
                                
                                <div class="detail-item">
                                    <strong>Contact:</strong>
                                    <a href="tel:<?= $hostel['contact_number'] ?>" class="text-decoration-none">
                                        <?= $hostel['contact_number'] ?>
                                    </a>
                                </div>
                                
                                <div class="detail-item">
                                    <strong>Amenities:</strong>
                                    <div class="amenities-list mt-2">
                                        <?php 
                                        $amenities = array_filter(array_map('trim', explode(',', $hostel['amenities'])));
                                        if (!empty($amenities)) {
                                            foreach ($amenities as $amenity) {
                                                echo '<span class="amenity-badge">' . htmlspecialchars($amenity) . '</span>';
                                            }
                                        } else {
                                            echo '<span class="text-muted">N/A</span>';
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="col-md-6">
                            <div class="detail-section">
                                <h5><i class="bi bi-person-badge me-2"></i>Hostel Incharge</h5>
                                
                                <div class="detail-item">
                                    <strong>Name:</strong>
                                    <?= htmlspecialchars($hostel['incharge_firstname'] . ' ' . $hostel['incharge_lastname']) ?>
                                </div>
                                
                                <div class="detail-item">
                                    <strong>Mail:</strong>
                                    <a href="mailto:<?= $hostel['email'] ?>" class="text-decoration-none">
                                        <?= $hostel['email'] ?>
                                    </a>
                                </div>
                            </div>
                            
                            <div class="detail-section mt-4">
                                <h5><i class="bi bi-geo-alt me-2"></i>Address</h5>
                                <div class="address-block">
                                    <?= htmlspecialchars(
                                        implode(', ', array_filter([
                                            $hostel['house_no'] ?? '',
                                            $hostel['street'] ?? '',
                                            $hostel['village'] ?? '',
                                            $hostel['sub_district'] ?? '',
                                            $hostel['district'] ?? '',
                                            $hostel['division'] ?? '',
                                            $hostel['state'] ?? '',
                                            $hostel['postalcode'] ?? '',
                                            $hostel['country_name'] ?? ''
                                        ]))
                                    ) ?>
                                    
                                    <?php if (!empty($hostel['detail'])): ?>
                                        <div class="mt-2 text-muted">
                                            <small><?= htmlspecialchars($hostel['detail']) ?></small>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card-footer bg-light">
                    <small class="text-muted">
                        Last updated: <?= date('M j, Y g:i a', strtotime($hostel['updated_at'])) ?>
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once BASE_PATH . '/admin/includes/footer_admin.php';
?>