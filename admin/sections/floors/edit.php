<?php
require_once __DIR__ . '/../../../config/config.php';
require_once BASE_PATH . '/config/db.php';
include BASE_PATH . '/includes/slide_message.php';
require_once BASE_PATH . '/config/auth.php';

require_admin();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<div class='alert alert-danger'>Invalid floor ID</div>";
    require_once BASE_PATH . '/admin/includes/footer_admin.php';
    exit;
}

$floorId = intval($_GET['id']);

// Fetch floor details
$sql = "
    SELECT floors.*, hostels.hostel_name
    FROM floors
    LEFT JOIN hostels ON floors.hostel_id = hostels.id
    WHERE floors.id = $floorId
    LIMIT 1
";
$result = $conn->query($sql);
$floor = $result ? $result->fetch_assoc() : null;
if (!$floor) {
    echo "<div class='alert alert-danger'>Floor not found</div>";
    require_once BASE_PATH . '/admin/includes/footer_admin.php';
    exit;
}

// Fetch all hostels for dropdown
$hostels = [];
$hostelSql = "SELECT id, hostel_name FROM hostels ORDER BY hostel_name ASC";
$hostelResult = $conn->query($hostelSql);
if ($hostelResult && $hostelResult->num_rows > 0) {
    $hostels = $hostelResult->fetch_all(MYSQLI_ASSOC);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $hostel_id = intval($_POST['hostel_id']);
    $floor_number = intval($_POST['floor_number']);
    $floor_name = trim($_POST['floor_name']);

    $updateSql = "UPDATE floors SET hostel_id=?, floor_number=?, floor_name=? WHERE id=?";
    $stmt = $conn->prepare($updateSql);
    $stmt->bind_param('iisi', $hostel_id, $floor_number, $floor_name, $floorId);
    if ($stmt->execute()) {
        header('Location: index.php?msg=updated');
        exit;
    } else {
        $error = 'Failed to update floor.';
    }
}
// CSRF Token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

require_once BASE_PATH . '/admin/includes/header_admin.php';

?>

<head>
    <style>
        :root {
            --primary-color: #394e63ff;
            --primary-hover: #1c2935ff;
            --primary-text: #ffffff;
        }

        .bg-primary {
            background-color: var(--primary-color) !important;
        }

        .text-primary {
            color: var(--primary-color) !important;
        }

        .btn-primary {
            background-color: var(--primary-color) !important;
            border-color: var(--primary-color) !important;
            color: var(--primary-text) !important;
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }

        .btn-primary:hover {
            background-color: var(--primary-hover) !important;
            border-color: var(--primary-hover) !important;
        }

        .btn-primary:focus,
        .btn-primary:active {
            box-shadow: 0 0 0 0.2rem rgba(44, 62, 80, 0.5) !important;
        }

        a.text-primary:hover,
        a.text-primary:focus {
            color: var(--primary-hover) !important;
            text-decoration: underline;
        }

        .card-header.bg-primary {
            background-color: var(--primary-color) !important;
            color: var(--primary-text) !important;
        }

        .btn-outline-primary {
            color: var(--primary-color) !important;
            border-color: var(--primary-color) !important;
            background-color: transparent !important;
            transition: color 0.3s ease, background-color 0.3s ease, border-color 0.3s ease;
        }

        .btn-outline-primary:hover,
        .btn-outline-primary:focus,
        .btn-outline-primary:active {
            color: var(--primary-text) !important;
            background-color: var(--primary-color) !important;
            border-color: var(--primary-hover) !important;
            box-shadow: 0 0 0 0.2rem rgba(44, 62, 80, 0.25) !important;
        }

        .card.bg-primary {
            background-color: var(--primary-color) !important;
            color: var(--primary-text) !important;
        }
    </style>
</head>

<!-- Edit Floors Form -->
<div class="content container py-4 mt-5">
    <a href="javascript:history.back()" class="btn btn-outline-secondary mb-4">← Back</a>
    <div class="card shadow ">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Edit Floor</h4>
        </div>
        <div class="card-body">
            <form id="editFloorForm">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                <input type="hidden" name="id" value="<?= htmlspecialchars($floor['id']) ?>">

                <div class="mb-3">
                    <label for="hostel_id" class="form-label">Hostel</label>
                    <select name="hostel_id" id="hostel_id" class="form-select" required>
                        <option value="">Select Hostel</option>
                        <?php foreach ($hostels as $hostel): ?>
                            <option value="<?= $hostel['id'] ?>" <?= $hostel['id'] == $floor['hostel_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($hostel['hostel_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="floor_number" class="form-label">Floor Number</label>
                    <input type="number" name="floor_number" id="floor_number" class="form-control" value="<?= htmlspecialchars($floor['floor_number']) ?>" required>
                </div>
                <div class="mb-3">
                    <label for="floor_name" class="form-label">Floor Name</label>
                    <input type="text" name="floor_name" id="floor_name" class="form-control" value="<?= htmlspecialchars($floor['floor_name']) ?>">
                </div>
                <button type="submit" class="btn btn-primary">Update Floor</button>
                <div id="formMessage" class="mt-3"></div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#editFloorForm').on('submit', function(e) {
            e.preventDefault();

            const submitBtn = $(this).find('button[type="submit"]');
            submitBtn.prop('disabled', true).text('Updating...');

            const formData = $(this).serialize();
            $.ajax({
                type: 'POST',
                url: '<?= BASE_URL ?>/admin/php_files/sections/floors/edit.php',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        showSlideMessage(response.message, 'success');
                        setTimeout(function() {
                            window.location.href = '<?= BASE_URL . "/admin/sections/floors/index.php" ?>';
                        }, 2000);
                    } else {
                        showSlideMessage(response.message, 'danger');
                    }
                    submitBtn.prop('disabled', false).text('Update Floor');
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                    showSlideMessage('An error occurred. Please try again.', 'danger');
                    submitBtn.prop('disabled', false).text('Update Floor');
                }
            });
        });
    });
</script>

<?php require_once BASE_PATH . '/admin/includes/footer_admin.php'; ?>