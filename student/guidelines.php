<?php
require_once __DIR__ . '/../config/config.php';
require_once BASE_PATH . '/config/auth.php';
include BASE_PATH . '/includes/header.php';

// Redirect if logged in
if (is_student_logged_in()) {
    header("Location: " . BASE_URL . "/student/dashboard.php");
    exit;
}

if (is_admin_logged_in()) {
    header("Location: " . BASE_URL . "/admin/dashboard.php");
    exit;
}

$page_title = "Hostel Guidelines";
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

<div class="container mt-4">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <h1 class="display-4"><i class="fas fa-book"></i> Hostel Guidelines & Rules</h1>
        <p class="lead">Essential information for all hostel residents</p>
    </div>

    <!-- Important Notice -->
    <div class="alert alert-info mb-4">
        <h5><i class="fas fa-exclamation-circle"></i> Important Notice</h5>
        <p class="mb-0">All students must adhere to these guidelines. Violations may result in disciplinary action.</p>
    </div>

    <!-- Main Guidelines Section -->
    <section class="mb-5">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h2 class="h4 mb-0"><i class="fas fa-list-ol"></i> General Hostel Rules</h2>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <h3 class="h5 card-title text-primary">Check-in/Check-out</h3>
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item">Gates close at 11:00 PM daily</li>
                                    <li class="list-group-item">Late entries require prior permission</li>
                                    <li class="list-group-item">Overnight absences must be reported</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <h3 class="h5 card-title text-primary">Room Maintenance</h3>
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item">Keep your room clean and tidy</li>
                                    <li class="list-group-item">Report damages immediately</li>
                                    <li class="list-group-item">No room modifications allowed</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <h3 class="h5 card-title text-primary">Visitors Policy</h3>
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item">Day visitors allowed only in common areas</li>
                                    <li class="list-group-item">No overnight guests without permission</li>
                                    <li class="list-group-item">All visitors must register at reception</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <h3 class="h5 card-title text-primary">Noise Regulations</h3>
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item">Quiet hours: 10:00 PM to 7:00 AM</li>
                                    <li class="list-group-item">Respect others' study time</li>
                                    <li class="list-group-item">No loud music at any time</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Facilities Section -->
    <section class="mb-5">
        <div class="card shadow-sm">
            <div class="card-header bg-success text-white">
                <h2 class="h4 mb-0"><i class="fas fa-home"></i> Facilities Usage Guidelines</h2>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="card h-100">
                            <div class="card-header bg-light">
                                <h3 class="h5 mb-0"><i class="fas fa-utensils"></i> Dining Hall</h3>
                            </div>
                            <div class="card-body">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item">Meal times: 7-9 AM, 12-2 PM, 6-8 PM</li>
                                    <li class="list-group-item">Maintain queue discipline</li>
                                    <li class="list-group-item">Return trays after meals</li>
                                    <li class="list-group-item">No outside food in dining area</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <div class="card h-100">
                            <div class="card-header bg-light">
                                <h3 class="h5 mb-0"><i class="fas fa-dumbbell"></i> Common Areas</h3>
                            </div>
                            <div class="card-body">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item">TV lounge closes at 11:00 PM</li>
                                    <li class="list-group-item">Clean up after using common areas</li>
                                    <li class="list-group-item">Sports equipment must be returned</li>
                                    <li class="list-group-item">No sleeping in common areas</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <div class="card h-100">
                            <div class="card-header bg-light">
                                <h3 class="h5 mb-0"><i class="fas fa-laptop"></i> Study Room</h3>
                            </div>
                            <div class="card-body">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item">Open 24 hours during exams</li>
                                    <li class="list-group-item">Maintain complete silence</li>
                                    <li class="list-group-item">No food or drinks allowed</li>
                                    <li class="list-group-item">Personal belongings not secured</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <div class="card h-100">
                            <div class="card-header bg-light">
                                <h3 class="h5 mb-0"><i class="fas fa-tint"></i> Laundry</h3>
                            </div>
                            <div class="card-body">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item">Laundry days: Mon, Wed, Fri</li>
                                    <li class="list-group-item">Mark all personal clothing</li>
                                    <li class="list-group-item">Remove clothes promptly</li>
                                    <li class="list-group-item">Report missing items immediately</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Safety & Security Section -->
    <section class="mb-5">
        <div class="card shadow-sm">
            <div class="card-header bg-warning text-dark">
                <h2 class="h4 mb-0"><i class="fas fa-shield-alt"></i> Safety & Security</h2>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h3 class="h5 mb-0">Emergency Procedures</h3>
                            </div>
                            <div class="card-body">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item"><i class="fas fa-phone-alt text-danger me-2"></i> Emergency numbers posted on each floor</li>
                                    <li class="list-group-item"><i class="fas fa-fire-extinguisher text-danger me-2"></i> Know fire exits and assembly points</li>
                                    <li class="list-group-item"><i class="fas fa-first-aid text-danger me-2"></i> First aid kits available at reception</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-light">
                                <h3 class="h5 mb-0">Security Measures</h3>
                            </div>
                            <div class="card-body">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item"><i class="fas fa-id-card text-primary me-2"></i> Always carry your student ID</li>
                                    <li class="list-group-item"><i class="fas fa-lock text-primary me-2"></i> Lock rooms when unattended</li>
                                    <li class="list-group-item"><i class="fas fa-eye text-primary me-2"></i> Report suspicious activity immediately</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Disciplinary Actions Section -->
    <section class="mb-5">
        <div class="card shadow-sm">
            <div class="card-header bg-danger text-white">
                <h2 class="h4 mb-0"><i class="fas fa-gavel"></i> Disciplinary Actions</h2>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th width="40%">Violation</th>
                                <th>Possible Consequences</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Unauthorized overnight guests</td>
                                <td>Written warning, possible fine</td>
                            </tr>
                            <tr>
                                <td>Noise violations</td>
                                <td>Warning, relocation, or suspension of privileges</td>
                            </tr>
                            <tr>
                                <td>Damage to property</td>
                                <td>Financial responsibility, possible suspension</td>
                            </tr>
                            <tr>
                                <td>Possession of prohibited items</td>
                                <td>Immediate confiscation, disciplinary hearing</td>
                            </tr>
                            <tr>
                                <td>Repeated violations</td>
                                <td>Hostel suspension or expulsion</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="alert alert-warning mt-3">
                    <i class="fas fa-info-circle"></i> <strong>Note:</strong> Serious violations may lead to university-level disciplinary action.
                </div>
            </div>
        </div>
    </section>

    <!-- Prohibited Items Section -->
    <section class="mb-5">
        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white">
                <h2 class="h4 mb-0"><i class="fas fa-ban"></i> Prohibited Items</h2>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <div class="card border-danger h-100">
                            <div class="card-header bg-danger text-white">
                                <h3 class="h5 mb-0">Substances</h3>
                            </div>
                            <div class="card-body">
                                <ul class="mb-0">
                                    <li>Alcohol</li>
                                    <li>Drugs</li>
                                    <li>Tobacco products</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card border-danger h-100">
                            <div class="card-header bg-danger text-white">
                                <h3 class="h5 mb-0">Appliances</h3>
                            </div>
                            <div class="card-body">
                                <ul class="mb-0">
                                    <li>Hot plates</li>
                                    <li>Rice cookers</li>
                                    <li>Electric kettles</li>
                                    <li>Heaters</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card border-danger h-100">
                            <div class="card-header bg-danger text-white">
                                <h3 class="h5 mb-0">Other Items</h3>
                            </div>
                            <div class="card-body">
                                <ul class="mb-0">
                                    <li>Weapons of any kind</li>
                                    <li>Fireworks</li>
                                    <li>Pets</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Acknowledgment Section -->
    <section>
        <div class="card border-primary shadow-sm">
            <div class="card-body text-center">
                <h2 class="h4 card-title text-primary"><i class="fas fa-check-circle me-2"></i>Acknowledgment</h2>
                <p class="card-text">By residing in the hostel, you agree to abide by these rules and guidelines.</p>
                <div class="d-grid gap-2 d-sm-flex justify-content-sm-center mt-3">
                    <a href="<?= BASE_URL ?>/student/register.php" class="btn btn-primary btn-lg px-4 gap-3">
                        <i class="fas fa-user-plus me-2"></i>Register for Hostel
                    </a>
                    <a href="<?= BASE_URL ?>/student/login.php" class="btn btn-outline-secondary btn-lg px-4">
                        <i class="fas fa-sign-in-alt me-2"></i>Student Login
                    </a>
                </div>
                <p class="text-muted mt-3 mb-0">Last updated: <?= date('F j, Y') ?></p>
            </div>
        </div>
    </section>
</div>

<?php 
include BASE_PATH . '/includes/footer.php'; 
?>