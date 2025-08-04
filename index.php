<?php
include __DIR__ . '/config/config.php';

require_once BASE_PATH . '/config/auth.php';
include BASE_PATH . '/includes/slide_message.php';
// Redirect if logged in
if (is_student_logged_in()) {
    header("Location: " . BASE_URL . "/student/dashboard.php");
    exit;
}

if (is_admin_logged_in()) {
    header("Location: " . BASE_URL . "/admin/dashboard.php");
    exit;
}

include BASE_PATH . '/includes/header.php';
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


        

        .bg-blur {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(rgba(194, 241, 243, 0), rgba(7, 51, 51, 0.5)),
                url('assets/images/university-campus_2.jpg') no-repeat center center;
            background-size: cover;
            filter: blur(3px);
            z-index: 0;
        }

    </style>
</head>
<!-- Hero Section with University Branding -->
<section class="hero-section text-white py-5 position-relative ">
    <div class="bg-blur"></div>
    <div class="container py-5 position-relative">
        <div class="row align-items-center">
            <div class="col-lg-7 mb-4">
                <h1 class="display-4 fw-bold mb-4 lead">Comfortable Living for Academic Success</h1>
                <p class="lead mb-4">Secure your place in our university hostels — safe, affordable, and convenient accommodation for students.</p>

                <!-- Button Group with Mobile Margin -->
                <div class="d-flex flex-wrap gap-3 mb-4 mb-sm-0 justify-content-center justify-content-sm-start">
                    <a href="<?= BASE_URL ?>/student/login.php" class="btn btn-light btn-lg px-4 mb-2 mb-sm-0">
                        Student Login
                    </a>
                    <a href="<?= BASE_URL ?>/student/register.php" class="btn btn-outline-light btn-lg px-4">
                        Apply Now
                    </a>
                </div>
            </div>

            <div class="col-lg-5" >
                <div class="card shadow-lg">
                    <div class="card-body p-4">
                        <h3 class="fw-bold mb-3 text-center" style="color: rgba(20, 17, 17, 0.9);">Application Process</h3>
                        <div class="process-steps">
                            <div class="step">
                                <div class="step-number bg-primary text-white">1</div>
                                <div class="step-content">
                                    <h5>Create Account</h5>
                                    <p class="text-muted small">Register with your university credentials</p>
                                </div>
                            </div>
                            <div class="step">
                                <div class="step-number bg-primary text-white">2</div>
                                <div class="step-content">
                                    <h5>Submit Application</h5>
                                    <p class="text-muted small">Fill hostel preference form</p>
                                </div>
                            </div>
                            <div class="step">
                                <div class="step-number bg-primary text-white">3</div>
                                <div class="step-content">
                                    <h5>Admin Approval</h5>
                                    <p class="text-muted small">Verification by hostel administration</p>
                                </div>
                            </div>
                            <div class="step">
                                <div class="step-number bg-primary text-white">4</div>
                                <div class="step-content">
                                    <h5>Confirmation</h5>
                                    <p class="text-muted small">Receive room allocation via email</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Hostels Showcase Section -->
<section class="py-5">
    <div class="container ">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Our Hostels</h2>
            <p class="lead text-muted">Modern facilities across multiple locations</p>
        </div>

        <div class="row g-4">
            <div class="col-md-6">
                <div class="card h-100 border-0 shadow-sm overflow-hidden">
                    <!-- <img src="assets/images/hostel_building.jpg" class="card-img-top" alt="Boys Hostel"> -->
                    <img src="assets/images/hostel_building.jpg" class="card-img-top" alt="Boys Hostel">
                    <div class="card-body">
                        <h4 class="fw-bold">Boys Hostel</h4>
                        <p class="text-muted">Capacity: 200 students</p>
                        <ul class="list-unstyled">
                            <li class="mb-2"><i class="fas fa-wifi text-primary me-2"></i> High-speed WiFi</li>
                            <li class="mb-2"><i class="fas fa-utensils text-primary me-2"></i> Dining Hall</li>
                            <li class="mb-2"><i class="fas fa-dumbbell text-primary me-2"></i> Gym Facilities</li>
                        </ul>
                    </div>
                    <div class="card-footer bg-transparent">
                        <a href="<?= BASE_URL . '/about.php' ?>" class="btn btn-outline-primary">View Details</a>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card h-100 border-0 shadow-sm overflow-hidden">
                    <img src="assets/images/girls_building.jpg" class="card-img-top" alt="Girls Hostel">
                    <div class="card-body">
                        <h4 class="fw-bold">Girls Hostel</h4>
                        <p class="text-muted">Capacity: 180 students</p>
                        <ul class="list-unstyled">
                            <li class="mb-2"><i class="fas fa-wifi text-primary me-2"></i> High-speed WiFi</li>
                            <li class="mb-2"><i class="fas fa-utensils text-primary me-2"></i> Dining Hall</li>
                            <li class="mb-2"><i class="fas fa-lock text-primary me-2"></i> 24/7 Security</li>
                        </ul>
                    </div>
                    <div class="card-footer bg-transparent">
                        <a href="<?= BASE_URL . '/about.php' ?>" class="btn btn-outline-primary">View Details</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Why Choose Our Hostels -->
<section class="py-4 bg-light">
    <div class="container py-3">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <img src="assets/images/hostel_life_w_girl.jpg" alt="Hostel Life" class="img-fluid rounded shadow">
            </div>
            <div class="col-lg-6">
                <h2 class="fw-bold mb-4 mt-4">Why Choose University Hostels?</h2>
                <div class="d-flex mb-3">
                    <div class="me-4">
                        <div class="bg-primary text-white rounded-circle p-3 d-inline-flex mb-2">
                            <i class="fas fa-map-marker-alt fa-2x"></i>
                        </div>
                    </div>
                    <div>
                        <h4 class="fw-bold">Prime Location</h4>
                        <p class="text-muted">Just minutes away from lecture halls and university facilities</p>
                    </div>
                </div>
                <div class="d-flex mb-3">
                    <div class="me-4">
                        <div class="bg-primary text-white rounded-circle p-3 d-inline-flex mb-2">
                            <i class="fas fa-shield-alt fa-2x"></i>
                        </div>
                    </div>
                    <div>
                        <h4 class="fw-bold">Safe Environment</h4>
                        <p class="text-muted">24/7 security with CCTV surveillance and controlled access</p>
                    </div>
                </div>
                <div class="d-flex mb-3">
                    <div class="me-4">
                        <div class="bg-primary text-white rounded-circle p-3 d-inline-flex mb-2">
                            <i class="fas fa-hand-holding-usd fa-2x"></i>
                        </div>
                    </div>
                    <div>
                        <h4 class="fw-bold">Affordable Rates</h4>
                        <p class="text-muted">Budget-friendly options with flexible payment plans</p>
                    </div>
                </div>
                <a href="<?= BASE_URL ?>/student/register.php" class="btn btn-primary btn-lg mt-3">Apply Now</a>
            </div>
        </div>
    </div>
</section>

<!-- Application Timeline -->
<section class="py-4">
    <div class="container py-3">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Application Timeline</h2>
            <p class="lead text-muted">Important dates for hostel applications</p>
        </div>

        <div class="timeline">
            <div class="timeline-item">
                <div class="timeline-date">July 1</div>
                <div class="timeline-content">
                    <h4>Applications Open</h4>
                    <p>Online registration begins for new students</p>
                </div>
            </div>
            <div class="timeline-item">
                <div class="timeline-date">August 15</div>
                <div class="timeline-content">
                    <h4>Priority Deadline</h4>
                    <p>Early applicants get preference in room allocation</p>
                </div>
            </div>
            <div class="timeline-item">
                <div class="timeline-date">September 1</div>
                <div class="timeline-content">
                    <h4>Allocation Notifications</h4>
                    <p>First round of room assignments sent via email</p>
                </div>
            </div>
            <div class="timeline-item">
                <div class="timeline-date">September 15</div>
                <div class="timeline-content">
                    <h4>Move-in Day</h4>
                    <p>Hostel check-in begins for new residents</p>
                </div>
            </div>
        </div>

        <div class="text-center mt-5">
            <a href="<?= BASE_URL ?>/student/register.php" class="btn btn-primary btn-lg px-5">Apply Now</a>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="py-4 bg-light">
    <div class="container py-3">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Frequently Asked Questions</h2>
            <p class="lead text-muted">Find answers to common questions about hostel accommodation</p>
        </div>

        <div class="row">
            <div class="col-lg-6">
                <div class="accordion mb-4" id="faqAccordion1">
                    <div class="accordion-item border-0 shadow-sm mb-3">
                        <h3 class="accordion-header" id="headingOne">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne">
                                Who is eligible for university hostel accommodation?
                            </button>
                        </h3>
                        <div id="collapseOne" class="accordion-collapse collapse" data-bs-parent="#faqAccordion1">
                            <div class="accordion-body">
                                All registered full-time students of the university are eligible to apply. Priority is given to first-year students and those coming from distant locations.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item border-0 shadow-sm mb-3">
                        <h3 class="accordion-header" id="headingTwo">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo">
                                How long does the approval process take?
                            </button>
                        </h3>
                        <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#faqAccordion1">
                            <div class="accordion-body">
                                The approval process typically takes 5-7 working days after submission of complete documents. You'll receive an email notification once your application is processed.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="accordion" id="faqAccordion2">
                    <div class="accordion-item border-0 shadow-sm mb-3">
                        <h3 class="accordion-header" id="headingThree">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree">
                                What documents do I need to submit?
                            </button>
                        </h3>
                        <div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#faqAccordion2">
                            <div class="accordion-body">
                                You'll need your university admission letter, ID proof, and passport-sized photographs. International students may need additional documents.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item border-0 shadow-sm mb-3">
                        <h3 class="accordion-header" id="headingFour">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour">
                                Can I choose my room or roommate?
                            </button>
                        </h3>
                        <div id="collapseFour" class="accordion-collapse collapse" data-bs-parent="#faqAccordion2">
                            <div class="accordion-body">
                                You can indicate preferences during application, but final assignments are made by the hostel administration based on availability and other factors.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contact Section -->

<section class="py-4">
    <div class="container py-3">
        <div class="row align-items-stretch">
            <!-- Left Column -->
            <div class="col-lg-5 d-flex">
                <div class="w-100">
                    <h2 class="fw-bold mb-4">Hostel Administration Office</h2>
                    <div class="d-flex mb-4">
                        <div class="me-4 text-primary">
                            <i class="fas fa-map-marker-alt fa-2x"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold">Location</h4>
                            <p class="text-muted">University Campus, Hostel Block A<br>Ground Floor, Room 101</p>
                        </div>
                    </div>
                    <div class="d-flex mb-4">
                        <div class="me-4 text-primary">
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold">Office Hours</h4>
                            <p class="text-muted">Monday - Friday: 9:00 AM - 5:00 PM<br>Saturday: 10:00 AM - 2:00 PM</p>
                        </div>
                    </div>
                    <div class="d-flex mb-4">
                        <div class="me-4 text-primary">
                            <i class="fas fa-phone-alt fa-2x"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold">Contact</h4>
                            <p class="text-muted">+8801929951023<br>cityhostel@university.edu</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column (Contact Form) -->
            <div class="col-lg-6 d-flex">
                <div class="card shadow-sm border-0 w-100">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Send Us a Message</h4>
                    </div>
                    <div class="card-body p-4">
                        <form id="contactForm">
                            <div class="row">
                                <!-- Name and Email Side-by-side -->
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>

                                <!-- Phone and Subject Side-by-side -->
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label">Phone Number</label>
                                    <input type="tel" class="form-control" id="phone" name="phone">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="subject" class="form-label">Subject <span class="text-danger">*</span></label>
                                    <select class="form-select" id="subject" name="subject" required>
                                        <option value="" selected disabled>Select a subject</option>
                                        <option value="Admission Query">Admission Query</option>
                                        <option value="Room Allocation">Room Allocation</option>
                                        <option value="Payment Issue">Payment Issue</option>
                                        <option value="Complaint">Complaint</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>

                                <!-- Message Full Width -->
                                <div class="col-12 mb-3">
                                    <label for="message" class="form-label">Message <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="message" name="message" rows="4" required></textarea>
                                </div>

                                <!-- Submit Button Full Width -->
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary w-100 py-2">
                                        <i class="fas fa-paper-plane me-2"></i> Send Message
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>



<script>
    $(document).ready(function() {
        $('#contactForm').on('submit', function(e) {
            e.preventDefault();
            const form = $(this);
            const submitBtn = form.find('button[type="submit"]');
            const originalBtnText = submitBtn.html();

            submitBtn.prop('disabled', true).html(
                '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Sending...'
            );

            $.ajax({
                url: '<?= BASE_URL ?>/contact_send_message_handler.php',
                type: 'POST',
                data: form.serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        showSlideMessage(response.message + "<br/>We will contact you later.", 'success');
                        form[0].reset();
                    } else {
                        showSlideMessage(response.message, 'danger');
                    }
                },
                error: function(xhr) {
                    showSlideMessage('An error occurred. Please try again later.', 'danger');
                    console.error(xhr.responseText);
                },
                complete: function() {
                    submitBtn.prop('disabled', false).html(originalBtnText);
                }
            });
        });
    });
</script>


<?php
include BASE_PATH . '/includes/footer.php';
?>

<style>
    .btn-light:hover,
    .btn-outline-light:hover {
        background-color: #f8f9fa;
        color: #000;
        transform: scale(1.02);
        transition: all 0.3s ease-in-out;
    }


    .hero-section {
        position: relative;
    }

    .bg-university {
        background-color: #003366;
        /* University color theme */
    }

    .step-content h5 {
        color: rgba(20, 17, 17, 1);
    }

    .process-steps .step {
        display: flex;
        align-items: flex-start;
        margin-bottom: 1.5rem;
    }

    .process-steps .step-number {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
        flex-shrink: 0;
    }

    .timeline {
        position: relative;
        max-width: 800px;
        margin: 0 auto;
    }

    .timeline::before {
        content: '';
        position: absolute;
        width: 2px;
        background-color: #dee2e6;
        top: 0;
        bottom: 0;
        left: 50%;
        margin-left: -1px;
    }

    .timeline-item {
        padding: 10px 40px;
        position: relative;
        width: 50%;
        box-sizing: border-box;
    }

    .timeline-item:nth-child(odd) {
        left: 0;
    }

    .timeline-item:nth-child(even) {
        left: 50%;
    }

    .timeline-date {
        padding: 8px 15px;
        background-color: #0d6efd;
        color: white;
        border-radius: 20px;
        display: inline-block;
        margin-bottom: 10px;
        font-weight: bold;
    }

    .timeline-content {
        padding: 20px;
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }

    .timeline-item:nth-child(odd) .timeline-content {
        margin-right: 15px;
    }

    .timeline-item:nth-child(even) .timeline-content {
        margin-left: 15px;
    }

    .timeline-item::after {
        content: '';
        position: absolute;
        width: 20px;
        height: 20px;
        background-color: white;
        border: 4px solid #0d6efd;
        border-radius: 50%;
        top: 25px;
        z-index: 1;
    }

    .timeline-item:nth-child(odd)::after {
        right: -10px;
    }

    .timeline-item:nth-child(even)::after {
        left: -10px;
    }
</style>