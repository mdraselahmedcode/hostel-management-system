<?php
require_once __DIR__ . '/../../../config/config.php';
require_once BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/config/auth.php';
require_once BASE_PATH . '/student/includes/header_student.php';

require_student();
?>

<div class="content container-fluid">
  <div class="row full-height">
    <!-- Sidebar (from sidebar_student.php) -->
    <?php require_once BASE_PATH . '/student/includes/sidebar_student.php'; ?>

    <!-- Main content -->
    <main class="col-12 col-md-10 col-lg-10 py-5 pt-3">
      <!-- Back Button -->
      <div class="mb-4">
        <a href="javascript:history.back()" class="btn btn-outline-secondary">
          <i class="bi bi-arrow-left"></i> Back
        </a>
      </div>
      
      <div class="card mx-auto" style="max-width: 600px;">
        <div class="card-body text-center">
          <h3 class="card-title text-muted">
            <i class="bi bi-exclamation-circle text-warning"></i> Payment History
          </h3>
          <p class="fs-5 mt-3">
            This feature is not available yet.
          </p>
          <p class="text-muted">
            Please check back for future updates.
          </p>
        </div>
      </div>
    </main>
  </div>
</div>

<?php require_once BASE_PATH . '/student/includes/footer_student.php'; ?>
