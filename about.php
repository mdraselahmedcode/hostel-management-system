<?php
require_once __DIR__ . '/config/config.php';

$currentPage = 'about.php'; 

require_once BASE_PATH . '/includes/header.php'; 
?>


<head>
    <title>About Us - City University Hostel Management System</title>

    <style>
        .hero-section {
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), 
                        url('<?= BASE_URL ?>/assets/images/hostel-building.jpg') no-repeat center center;
            background-size: cover;
            color: white;
            padding: 100px 0;
        }
        .mission-card {
            border-left: 4px solid #0d6efd;
            transition: all 0.3s ease;
        }
        .mission-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .team-member {
            transition: all 0.3s ease;
        }
        .team-member:hover {
            transform: translateY(-5px);
        }
        .stat-item {
            border-radius: 10px;
            transition: all 0.3s ease;
        }
        .stat-item:hover {
            transform: scale(1.05);
        }
    </style>
</head>

    

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container text-center">
            <h1 class="display-4 fw-bold mb-4">About City University Hostels</h1>
            <p class="lead">Providing comfortable and secure accommodation for our students since 1995</p>
        </div>
    </section>

    <!-- About Section -->
    <section class="py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <img src="<?= BASE_URL ?>/assets/images/hostel_life_w_girl.jpg" alt="Hostel Life" class="img-fluid rounded shadow">
                </div>
                <div class="col-lg-6">
                    <h2 class="fw-bold mb-4">Our Story</h2>
                    <p>City University Hostel Management System was established in 1995 with a vision to provide safe, affordable, and comfortable accommodation for students pursuing higher education at City University.</p>
                    <p>Over the years, we have grown from a single hostel building to a network of 5 modern hostels accommodating over 2,000 students annually. Our hostels are designed to create a home away from home, fostering academic excellence and personal growth.</p>
                    <p>With our online management system, we've streamlined the hostel allocation process, making it easier for students to apply, track applications, and manage their hostel stay.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Mission and Values -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">Our Mission & Values</h2>
                <p class="lead text-muted">What drives us every day</p>
            </div>
            
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="mission-card p-4 h-100 bg-white rounded shadow-sm">
                        <div class="icon-box bg-primary bg-opacity-10 text-primary rounded-circle p-3 mb-3 d-inline-flex">
                            <i class="fas fa-home fa-2x"></i>
                        </div>
                        <h4>Comfortable Living</h4>
                        <p>We provide well-maintained rooms with modern amenities to ensure our students have a comfortable living environment conducive to studying.</p>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mission-card p-4 h-100 bg-white rounded shadow-sm">
                        <div class="icon-box bg-primary bg-opacity-10 text-primary rounded-circle p-3 mb-3 d-inline-flex">
                            <i class="fas fa-shield-alt fa-2x"></i>
                        </div>
                        <h4>Safety First</h4>
                        <p>24/7 security, CCTV surveillance, and strict access control ensure our students' safety is never compromised.</p>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mission-card p-4 h-100 bg-white rounded shadow-sm">
                        <div class="icon-box bg-primary bg-opacity-10 text-primary rounded-circle p-3 mb-3 d-inline-flex">
                            <i class="fas fa-hand-holding-heart fa-2x"></i>
                        </div>
                        <h4>Student Support</h4>
                        <p>Our dedicated staff provides round-the-clock support to address any concerns and ensure a smooth hostel experience.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="py-5 bg-primary text-white">
        <div class="container">
            <div class="row text-center">
                <div class="col-md-3 mb-4 mb-md-0">
                    <div class="stat-item p-3">
                        <h2 class="display-4 fw-bold">5</h2>
                        <p class="mb-0">Hostel Buildings</p>
                    </div>
                </div>
                <div class="col-md-3 mb-4 mb-md-0">
                    <div class="stat-item p-3">
                        <h2 class="display-4 fw-bold">2,000+</h2>
                        <p class="mb-0">Students Accommodated</p>
                    </div>
                </div>
                <div class="col-md-3 mb-4 mb-md-0">
                    <div class="stat-item p-3">
                        <h2 class="display-4 fw-bold">28</h2>
                        <p class="mb-0">Dedicated Staff</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-item p-3">
                        <h2 class="display-4 fw-bold">25+</h2>
                        <p class="mb-0">Years of Experience</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Team Section -->
    <section class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">Our Hostel Administration</h2>
                <p class="lead text-muted">The team that keeps everything running smoothly</p>
            </div>
            
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="team-member text-center p-4 bg-white rounded shadow-sm">
                        <img src="<?= BASE_URL ?>/assets/images/team1.jpg" alt="Hostel Warden" class="img-fluid rounded-circle mb-3" width="150">
                        <h4>Dr. Sarah Johnson</h4>
                        <p class="text-primary fw-bold">Chief Warden</p>
                        <p>With 15 years of experience in student accommodation management, Dr. Johnson oversees all hostel operations.</p>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="team-member text-center p-4 bg-white rounded shadow-sm">
                        <img src="<?= BASE_URL ?>/assets/images/team2.jpg" alt="Deputy Warden" class="img-fluid rounded-circle mb-3" width="150">
                        <h4>Mr. Robert Chen</h4>
                        <p class="text-primary fw-bold">Deputy Warden</p>
                        <p>Specializing in student welfare, Mr. Chen ensures all student concerns are addressed promptly.</p>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="team-member text-center p-4 bg-white rounded shadow-sm">
                        <img src="<?= BASE_URL ?>/assets/images/team3.jpg" alt="Systems Manager" class="img-fluid rounded-circle mb-3" width="150">
                        <h4>Ms. Aisha Rahman</h4>
                        <p class="text-primary fw-bold">Systems Manager</p>
                        <p>Our tech expert who manages the online hostel management system and digital infrastructure.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">What Our Students Say</h2>
                <p class="lead text-muted">Experiences from our hostel residents</p>
            </div>
            
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="p-4 bg-white rounded shadow-sm h-100">
                        <div class="d-flex align-items-center mb-3">
                            <img src="<?= BASE_URL ?>/assets/images/student1.jpg" alt="Student" class="rounded-circle me-3" width="60">
                            <div>
                                <h5 class="mb-0">Michael Tan</h5>
                                <small class="text-muted">Computer Science, 3rd Year</small>
                            </div>
                        </div>
                        <p>"The hostel facilities are excellent with high-speed WiFi and 24/7 study rooms. It's been a great environment for my studies."</p>
                        <div class="text-warning">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="p-4 bg-white rounded shadow-sm h-100">
                        <div class="d-flex align-items-center mb-3">
                            <img src="<?= BASE_URL ?>/assets/images/student2.jpg" alt="Student" class="rounded-circle me-3" width="60">
                            <div>
                                <h5 class="mb-0">Priya Patel</h5>
                                <small class="text-muted">Business Administration, 2nd Year</small>
                            </div>
                        </div>
                        <p>"I love the sense of community here. The hostel organizes regular events that helped me make friends quickly."</p>
                        <div class="text-warning">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star-half-alt"></i>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="p-4 bg-white rounded shadow-sm h-100">
                        <div class="d-flex align-items-center mb-3">
                            <img src="<?= BASE_URL ?>/assets/images/student3.jpg" alt="Student" class="rounded-circle me-3" width="60">
                            <div>
                                <h5 class="mb-0">David Kim</h5>
                                <small class="text-muted">Engineering, 4th Year</small>
                            </div>
                        </div>
                        <p>"The online management system makes everything so easy - from applying to paying fees. The staff are always helpful when needed."</p>
                        <div class="text-warning">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="py-5 bg-primary text-white">
        <div class="container text-center">
            <h2 class="fw-bold mb-4">Ready to Join Our Hostel Community?</h2>
            <a href="<?= BASE_URL ?>/student/register.php" class="btn btn-light btn-lg px-5 me-3">Apply Now</a>
            <a href="<?= BASE_URL ?>/contact.php" class="btn btn-outline-light btn-lg px-5">Contact Us</a>
        </div>
    </section>

    <!-- Include Footer -->
    <?php include __DIR__ . '/includes/footer.php'; ?>

 