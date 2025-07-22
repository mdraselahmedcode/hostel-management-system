<?php
require_once __DIR__ . '/../config/config.php';
include BASE_PATH . '/config/auth.php';

if (is_student_logged_in()) {
    header("Location: " . BASE_URL . "/student/dashboard.php");
    exit;
}

if (is_admin_logged_in()) {
    header("Location: " . BASE_URL . "/admin/dashboard.php");
    exit;
}

$page_title = "Admin Help Center";
require_once BASE_PATH . '/includes/header.php';
?>

<div class="container mt-4">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <h1 class="display-4"><i class="fas fa-life-ring"></i> Admin Help Center</h1>
        <p class="lead">Resources and guidance for hostel administrators</p>
    </div>

    <!-- Quick Help Cards -->
    <section class="mb-5">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card text-white bg-primary h-100 shadow-sm">
                    <div class="card-body">
                        <h2 class="h4 card-title"><i class="fas fa-book me-2"></i>Documentation</h2>
                        <p class="card-text">Access the complete system documentation and user manuals.</p>
                        <a href="#" class="btn btn-light stretched-link">View Docs</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-success h-100 shadow-sm">
                    <div class="card-body">
                        <h2 class="h4 card-title"><i class="fas fa-headset me-2"></i>Support</h2>
                        <p class="card-text">Contact technical support for immediate assistance.</p>
                        <a href="<?= BASE_URL ?>/contact.php" class="btn btn-light stretched-link">Contact Now</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-warning h-100 shadow-sm">
                    <div class="card-body">
                        <h2 class="h4 card-title"><i class="fas fa-video me-2"></i>Tutorials</h2>
                        <p class="card-text">Watch video tutorials for common administrative tasks.</p>
                        <a href="#" class="btn btn-light stretched-link">Watch Videos</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="mb-5">
        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white">
                <h2 class="h4 mb-0"><i class="fas fa-question-circle me-2"></i>Frequently Asked Questions</h2>
            </div>
            <div class="card-body">
                <div class="accordion" id="faqAccordion">
                    <!-- FAQ Item 1 -->
                    <div class="accordion-item border mb-2">
                        <h3 class="accordion-header" id="headingOne">
                            <button class="accordion-button collapsed py-3" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne">
                                <i class="fas fa-user-graduate me-2"></i> How do I add a new student to the system?
                            </button>
                        </h3>
                        <div id="collapseOne" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                <ol class="mb-0">
                                    <li>Navigate to <strong>Students > Add New Student</strong></li>
                                    <li>Fill in all required student information</li>
                                    <li>Upload required documents (ID, admission letter)</li>
                                    <li>Assign to a hostel and room</li>
                                    <li>Click "Save" to complete the process</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                    
                    <!-- FAQ Item 2 -->
                    <div class="accordion-item border mb-2">
                        <h3 class="accordion-header" id="headingTwo">
                            <button class="accordion-button collapsed py-3" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo">
                                <i class="fas fa-chart-pie me-2"></i> How can I generate occupancy reports?
                            </button>
                        </h3>
                        <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                <p>To generate hostel occupancy reports:</p>
                                <ol class="mb-0">
                                    <li>Go to <strong>Reports > Occupancy Reports</strong></li>
                                    <li>Select the hostel block or specific hostel</li>
                                    <li>Choose date range (optional)</li>
                                    <li>Click "Generate Report"</li>
                                    <li>You can export the report as PDF, Excel, or CSV</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                    
                    <!-- FAQ Item 3 -->
                    <div class="accordion-item border">
                        <h3 class="accordion-header" id="headingThree">
                            <button class="accordion-button collapsed py-3" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree">
                                <i class="fas fa-money-bill-wave me-2"></i> What if a student's payment isn't showing?
                            </button>
                        </h3>
                        <div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                <p>If a payment isn't reflecting in the system:</p>
                                <ol class="mb-0">
                                    <li>Verify the payment receipt with the student</li>
                                    <li>Check the "Pending Payments" section</li>
                                    <li>If still not found, go to <strong>Payments > Manual Payment Entry</strong></li>
                                    <li>Enter the payment details manually</li>
                                    <li>Contact the finance department if the issue persists</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Common Tasks Section -->
    <section class="mb-5">
        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white">
                <h2 class="h4 mb-0"><i class="fas fa-tasks me-2"></i>Common Administrative Tasks</h2>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-header bg-light">
                                <h3 class="h5 mb-0"><i class="fas fa-user-graduate me-2"></i>Student Management</h3>
                            </div>
                            <div class="list-group list-group-flush">
                                <a href="<?= BASE_URL ?>/admin/students/add.php" class="list-group-item list-group-item-action">
                                    <i class="fas fa-user-plus me-2"></i> Add New Student
                                </a>
                                <a href="<?= BASE_URL ?>/admin/students/" class="list-group-item list-group-item-action">
                                    <i class="fas fa-users me-2"></i> View All Students
                                </a>
                                <a href="<?= BASE_URL ?>/admin/students/transfer.php" class="list-group-item list-group-item-action">
                                    <i class="fas fa-exchange-alt me-2"></i> Transfer Student
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-header bg-light">
                                <h3 class="h5 mb-0"><i class="fas fa-home me-2"></i>Hostel Management</h3>
                            </div>
                            <div class="list-group list-group-flush">
                                <a href="<?= BASE_URL ?>/admin/hostels/" class="list-group-item list-group-item-action">
                                    <i class="fas fa-home me-2"></i> Manage Hostels
                                </a>
                                <a href="<?= BASE_URL ?>/admin/rooms/" class="list-group-item list-group-item-action">
                                    <i class="fas fa-door-open me-2"></i> Manage Rooms
                                </a>
                                <a href="<?= BASE_URL ?>/admin/maintenance/" class="list-group-item list-group-item-action">
                                    <i class="fas fa-tools me-2"></i> Maintenance Requests
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Troubleshooting Section -->
    <section class="mb-5">
        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white">
                <h2 class="h4 mb-0"><i class="fas fa-bug me-2"></i>Troubleshooting Guide</h2>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th width="30%">Issue</th>
                                <th>Solution</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Can't login to admin panel</td>
                                <td>
                                    <ul class="mb-0">
                                        <li>Ensure you're using correct credentials</li>
                                        <li>Reset password if needed</li>
                                        <li>Contact system administrator if locked out</li>
                                    </ul>
                                </td>
                            </tr>
                            <tr>
                                <td>System running slowly</td>
                                <td>
                                    <ul class="mb-0">
                                        <li>Clear your browser cache</li>
                                        <li>Try a different browser</li>
                                        <li>Report to IT if issue persists</li>
                                    </ul>
                                </td>
                            </tr>
                            <tr>
                                <td>Error messages appearing</td>
                                <td>
                                    <ul class="mb-0">
                                        <li>Note the exact error message</li>
                                        <li>Try the action again</li>
                                        <li>Take screenshot and report to support</li>
                                    </ul>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <!-- Quick Links -->
    <section>
        <div class="card border-primary shadow-sm">
            <div class="card-body text-center">
                <h2 class="h4 card-title text-primary"><i class="fas fa-link me-2"></i>Quick Links</h2>
                <div class="d-flex flex-wrap justify-content-center gap-3 mt-3">
                    <a href="<?= BASE_URL ?>/admin/dashboard.php" class="btn btn-primary">
                        <i class="fas fa-tachometer-alt me-2"></i>Admin Dashboard
                    </a>
                    <a href="<?= BASE_URL ?>/admin/settings.php" class="btn btn-outline-primary">
                        <i class="fas fa-cog me-2"></i>System Settings
                    </a>
                    <a href="<?= BASE_URL ?>/contact.php" class="btn btn-outline-success">
                        <i class="fas fa-headset me-2"></i>Contact Support
                    </a>
                </div>
            </div>
        </div>
    </section>
</div>

<?php
require_once BASE_PATH . '/admin/includes/footer_admin.php';
?>