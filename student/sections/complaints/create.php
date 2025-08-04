<?php
require_once __DIR__ . '/../../../config/config.php';
require_once BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/config/auth.php';
require_once BASE_PATH . '/student/includes/header_student.php';

require_student(); 

$student_id = $_SESSION['student']['id'];

// Get complaint categories
$stmt = $conn->prepare("SELECT * FROM complaint_categories ORDER BY name");
$stmt->execute();
$categories = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Get student's hostel and room
$stmt = $conn->prepare("SELECT hostel_id, room_id FROM students WHERE id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $category_id = (int) $_POST['category_id'];
    $priority = $_POST['priority'];

    // Insert complaint
    $stmt = $conn->prepare("
        INSERT INTO complaints (student_id, hostel_id, room_id, category_id, title, description, priority)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("iiiisss", $student_id, $student['hostel_id'], $student['room_id'], $category_id, $title, $description, $priority);
    
    if ($stmt->execute()) {
        $complaint_id = $stmt->insert_id;
        $stmt->close();

        // File upload handling
        if (!empty($_FILES['attachments']['name'][0])) {
            $upload_dir = BASE_PATH . "/uploads/complaints/{$complaint_id}/";
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            foreach ($_FILES['attachments']['name'] as $index => $filename) {
                if ($_FILES['attachments']['error'][$index] === UPLOAD_ERR_OK) {
                    $tmp_path = $_FILES['attachments']['tmp_name'][$index];
                    $safe_name = basename($filename);
                    $target_path = $upload_dir . $safe_name;

                    if (move_uploaded_file($tmp_path, $target_path)) {
                        $file_path = "/uploads/complaints/{$complaint_id}/{$safe_name}";
                        $file_type = $_FILES['attachments']['type'][$index];
                        $file_size = (int) $_FILES['attachments']['size'][$index];

                        $stmt = $conn->prepare("
                            INSERT INTO complaint_attachments (complaint_id, file_path, file_name, file_type, file_size)
                            VALUES (?, ?, ?, ?, ?)
                        ");
                        $stmt->bind_param("isssi", $complaint_id, $file_path, $safe_name, $file_type, $file_size);
                        $stmt->execute();
                        $stmt->close();
                    }
                }
            }
        }

        $_SESSION['success'] = "Complaint submitted successfully.";
        header("Location: view.php?id=$complaint_id");
        exit;
    } else {
        $error = "Failed to submit complaint: " . $stmt->error;
        $stmt->close();
    }
}
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
    </style>
</head>

<div class="content container-fluid">
  <div class="row full-height">
    <!-- Sidebar -->
    <?php include BASE_PATH . '/student/includes/sidebar_student.php'; ?>

    <!-- Main content -->
    <main class="col-12 col-md-10 col-lg-10 py-5 pt-3" style="max-height: calc(100vh - 142.75px);">
      <div class="mb-4 d-flex justify-content-end">
        <a href="<?= BASE_URL . '/student/sections/complaints/index.php' ?>" class="btn btn-outline-secondary">
          <i class="bi bi-arrow-left"></i> Back to Complaints
        </a>
      </div>

      <div class="card mx-auto" style="max-width: 700px;">
        <div class="card-body">
          <h4 class="mb-4">Submit New Complaint</h4>

          <?php if (!empty($error)): ?>
              <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
          <?php endif; ?>

          <form method="POST" enctype="multipart/form-data" >
              <div class="mb-2">
                  <label for="title" class="form-label">Title</label>
                  <input type="text" class="form-control" id="title" name="title" required>
              </div>

              <div class="mb-2">
                  <label for="category_id" class="form-label">Category</label>
                  <select class="form-select" id="category_id" name="category_id" required>
                      <option value="">Select a category</option>
                      <?php foreach ($categories as $category): ?>
                          <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></option>
                      <?php endforeach; ?>
                  </select>
              </div>

              <div class="mb-2">
                  <label for="priority" class="form-label">Priority</label>
                  <select class="form-select" id="priority" name="priority" required>
                      <option value="medium">Medium</option>
                      <option value="low">Low</option>
                      <option value="high">High</option>
                      <option value="urgent">Urgent</option>
                  </select>
              </div>

              <div class="mb-3">
                  <label for="description" class="form-label">Description</label>
                  <textarea class="form-control" style="resize: none;" id="description" name="description" rows="4"  required ></textarea>
              </div>

              <div class="mb-3">
                  <label for="attachments" class="form-label">Attachments (optional)</label>
                  <input type="file" class="form-control" id="attachments" name="attachments[]" multiple>
                  <div class="form-text">You can upload multiple files (images, documents, etc.)</div>
              </div>

              <button type="submit" class="btn btn-primary">Submit Complaint</button>
          </form>
        </div>
      </div>
    </main>
  </div>
</div>

<?php require_once BASE_PATH . '/student/includes/footer_student.php'; ?>
