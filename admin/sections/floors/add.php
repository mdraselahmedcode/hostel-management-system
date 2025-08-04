<?php
require_once __DIR__ . '/../../../config/config.php';
require_once BASE_PATH . '/config/db.php';
include BASE_PATH . '/includes/slide_message.php';
require_once BASE_PATH . '/config/auth.php';

require_admin();

// Fetch hostels to populate the dropdown
$hostels = [];
$query = "SELECT id, hostel_name FROM hostels ORDER BY hostel_name ASC";
$result = $conn->query($query);
if ($result && $result->num_rows > 0) {
    $hostels = $result->fetch_all(MYSQLI_ASSOC);
}

require_once BASE_PATH . '/admin/includes/header_admin.php';
?>

<div class="content container-fluid mt-5">
    <div class="row full-height">
        <?php require_once BASE_PATH . '/admin/includes/sidebar_admin.php'; ?>

        <main class="col-md-10 ms-sm-auto px-md-4 py-4">
            <a href="<?= BASE_URL . '/admin/sections/floors/index.php' ?>" class="btn btn-secondary mb-3">← Back</a>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="mb-4">Add New Floor</h2>

                    <form id="addFloorForm">
                        <div class="mb-3">
                            <label for="floor_name" class="form-label">Floor Name</label>
                            <input type="text" class="form-control" id="floor_name" name="floor_name" required>
                        </div>

                        <div class="mb-3">
                            <label for="floor_number" class="form-label">Floor Number</label>
                            <input type="number" class="form-control" id="floor_number" name="floor_number" required>
                        </div>

                        <div class="mb-3">
                            <label for="hostel_id" class="form-label">Select Hostel</label>
                            <select class="form-select" id="hostel_id" name="hostel_id" required>
                                <option value="">-- Select Hostel --</option>
                                <?php foreach ($hostels as $hostel): ?>
                                    <option value="<?= $hostel['id'] ?>"><?= htmlspecialchars($hostel['hostel_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-success">Add Floor</button>
                    </form>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- jQuery AJAX script -->
<script>
    $(document).ready(function() {
        $('#addFloorForm').on('submit', function(e) {
            e.preventDefault();

            $.ajax({
                type: 'POST',
                url: '<?= BASE_URL ?>/admin/php_files/sections/floors/add.php',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        showSlideMessage(response.message, 'success');
                        $('#addFloorForm')[0].reset();

                        setTimeout(function() {
                            window.location.href = '<?= BASE_URL . "/admin/sections/floors/index.php" ?>';
                        }, 2000);
                    } else {
                        showSlideMessage(response.message, 'danger');
                    }
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                    showSlideMessage('An error occurred while adding the floor.', 'danger');
                }
            });
        });
    });
</script>


<?php require_once BASE_PATH . '/admin/includes/footer_admin.php'; ?>