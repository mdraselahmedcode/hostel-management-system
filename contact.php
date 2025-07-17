<?php
require_once __DIR__ . '/config/config.php';
include BASE_PATH . '/includes/header.php';
$currentPage = 'contact.php'; // For navigation highlighting

?>


<head>

    <title>Contact Us - City University Hostel Management System</title>

    <style>
        .contact-hero {
            background: linear-gradient(rgba(0, 0, 0, 0), rgba(0, 0, 0, 0.5)),
                url('<?= BASE_URL ?>/assets/images/university-campus_3.jpg') no-repeat center center;
            background-size: cover;
            color: white;
            padding: 100px 0;
        }

        .contact-card {
            transition: all 0.3s ease;
            border: none;
            border-radius: 10px;
        }

        .contact-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .contact-icon {
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            margin-bottom: 20px;
        }

        .form-control:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }

        .map-container {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>



<!-- Hero Section -->
<section class="contact-hero">
    <div class="container text-center">
        <h1 class="display-4 fw-bold mb-4">Contact Us</h1>
        <p class="lead">We're here to help with any questions about hostel accommodation</p>
    </div>
</section>

<!-- Contact Information -->
<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="contact-card p-4 h-100 bg-white shadow-sm text-center">
                    <div class="contact-icon bg-primary bg-opacity-10 text-primary mx-auto">
                        <i class="fas fa-map-marker-alt fa-2x"></i>
                    </div>
                    <h4>Our Location</h4>
                    <p class="text-muted">Hostel Administration Office<br>
                        City University Campus<br>
                        Block B, Ground Floor<br>
                        Dhaka 1212, Bangladesh</p>
                    <a href="https://maps.app.goo.gl/HhqxdaV6Pna4Z7uM9" class="btn btn-sm btn-outline-primary" target="_blank">
                        View on Map
                    </a>

                </div>
            </div>

            <div class="col-md-4">
                <div class="contact-card p-4 h-100 bg-white shadow-sm text-center">
                    <div class="contact-icon bg-primary bg-opacity-10 text-primary mx-auto">
                        <i class="fas fa-phone-alt fa-2x"></i>
                    </div>
                    <h4>Phone Numbers</h4>
                    <p class="text-muted">
                        <strong>General Enquiries:</strong> +880 2 55667788<br>
                        <strong>Admissions:</strong> +880 2 55667789<br>
                        <strong>Emergency:</strong> +880 1929 951023
                    </p>
                    <a href="tel:+880255667788" class="btn btn-sm btn-outline-primary">Call Now</a>
                </div>
            </div>

            <div class="col-md-4">
                <div class="contact-card p-4 h-100 bg-white shadow-sm text-center">
                    <div class="contact-icon bg-primary bg-opacity-10 text-primary mx-auto">
                        <i class="fas fa-envelope fa-2x"></i>
                    </div>
                    <h4>Email Addresses</h4>
                    <p class="text-muted">
                        <strong>General:</strong> hostel@cityuniv.edu<br>
                        <strong>Admissions:</strong> hostel-admissions@cityuniv.edu<br>
                        <strong>Support:</strong> hostel-support@cityuniv.edu
                    </p>
                    <a href="mailto:hostel@cityuniv.edu" class="btn btn-sm btn-outline-primary">Email Us</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contact Form and Map -->
<section class="py-5 bg-light" id="contact-section">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Send Us a Message</h4>
                    </div>
                    <div class="card-body p-4">
                        <form id="contactForm" method="POST">
                            <div class="mb-3">
                                <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="tel" class="form-control" id="phone" name="phone">
                            </div>
                            <div class="mb-3">
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
                            <div class="mb-3">
                                <label for="message" class="form-label">Message <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 py-2">
                                <i class="fas fa-paper-plane me-2"></i> Send Message
                            </button>
                        </form>
                        <div id="formMessage" class="mt-3"></div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Our Location</h4>
                    </div>
                    <div class="card-body p-0">
                        <div class="map-container ratio ratio-16x9">
                            <!-- Google Map Embed -->
                            <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d912.1185424070097!2d90.3102887!3d23.872799!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3755c2102dc1cd51%3A0x6f95e92193fc8978!2sCity%20University%20Bangladesh!5e0!3m2!1sen!2sbd!4v1752697102390!5m2!1sen!2sbd" 
                                width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </div>
                        <div class="p-4">
                            <h5>Hostel Administration Office Hours</h5>
                            <ul class="list-unstyled">
                                <li><strong>Monday-Friday:</strong> 9:00 AM - 5:00 PM</li>
                                <li><strong>Saturday:</strong> 10:00 AM - 2:00 PM</li>
                                <li><strong>Sunday:</strong> Closed</li>
                            </ul>
                            <p class="mb-0"><strong>Note:</strong> For emergencies outside office hours, please contact the hostel warden on duty.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="py-5" id="faq">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Frequently Asked Questions</h2>
            <p class="lead text-muted">Quick answers to common questions</p>
        </div>

        <div class="accordion" id="faqAccordion">
            <div class="accordion-item border-0 shadow-sm mb-3">
                <h3 class="accordion-header" id="headingOne">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne">
                        How can I check my hostel application status?
                    </button>
                </h3>
                <div id="collapseOne" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        You can check your application status by logging into the student portal or visiting the "Check Status" page. If you need further assistance, please contact the admissions office.
                    </div>
                </div>
            </div>

            <div class="accordion-item border-0 shadow-sm mb-3">
                <h3 class="accordion-header" id="headingTwo">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo">
                        What documents do I need for hostel admission?
                    </button>
                </h3>
                <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        You'll need your university admission letter, a copy of your national ID or passport, two passport-sized photographs, and a completed hostel application form. International students may need additional documents.
                    </div>
                </div>
            </div>

            <div class="accordion-item border-0 shadow-sm mb-3">
                <h3 class="accordion-header" id="headingThree">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree">
                        How do I pay my hostel fees?
                    </button>
                </h3>
                <div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        Hostel fees can be paid through our online payment system via bKash, Nagad, or bank transfer. Detailed instructions are available in the payment section of your student portal.
                    </div>
                </div>
            </div>

            <div class="accordion-item border-0 shadow-sm">
                <h3 class="accordion-header" id="headingFour">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour">
                        Who should I contact in case of an emergency?
                    </button>
                </h3>
                <div id="collapseFour" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        For emergencies, please contact the hostel warden on duty at +880 1929 951023 or the university security hotline at +880 2 55667700. For medical emergencies, the campus medical center is available 24/7.
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>



<!-- <script src="<?= BASE_URL ?>/vendor/jquery/jquery.min.js"></script>
    <script src="<?= BASE_URL ?>/vendor/bootstrap/js/bootstrap.bundle.min.js"></script> -->



<script>
    $(document).ready(function() {
        // Form submission handling
        $('#contactForm').on('submit', function(e) {
            e.preventDefault();
            const form = $(this);
            const submitBtn = form.find('button[type="submit"]');
            const originalBtnText = submitBtn.html();

            // Show loading state
            submitBtn.prop('disabled', true).html(
                '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Sending...'
            );

            $.ajax({
                url: '<?= BASE_URL ?>/includes/contact_handler.php',
                type: 'POST',
                data: form.serialize(),
                dataType: 'json',
                success: function(response) {
                    const messageDiv = $('#formMessage');
                    messageDiv.empty();

                    if (response.success) {
                        messageDiv.html(
                            '<div class="alert alert-success alert-dismissible fade show">' +
                            '<i class="fas fa-check-circle me-2"></i>' + response.message +
                            '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                            '</div>'
                        );
                        form[0].reset();
                    } else {
                        messageDiv.html(
                            '<div class="alert alert-danger alert-dismissible fade show">' +
                            '<i class="fas fa-exclamation-circle me-2"></i>' + response.message +
                            '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                            '</div>'
                        );
                    }
                },
                error: function(xhr) {
                    $('#formMessage').html(
                        '<div class="alert alert-danger alert-dismissible fade show">' +
                        '<i class="fas fa-times-circle me-2"></i> An error occurred. Please try again later.' +
                        '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                        '</div>'
                    );
                    console.error(xhr.responseText);
                },
                complete: function() {
                    submitBtn.prop('disabled', false).html(originalBtnText);
                }
            });
        });
    });
</script>


<!-- Include Footer -->
<?php include __DIR__ . '/includes/footer.php'; ?>