<?php
require_once __DIR__ . '/config/config.php';
require_once BASE_PATH . '/config/auth.php';
include BASE_PATH . '/includes/header.php';
?>

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
    
    .developer-card img {
        transition: transform 0.3s ease;
    }

    .developer-card img:hover {
        transform: scale(1.05);
    }

    .list-group-item i {
        color: var(--bs-primary);
    }

    .card-footer i {
        vertical-align: middle;
    }

    .badge {
        font-size: 0.8rem;
    }

    .card-header i {
        color: var(--bs-dark);
    }
</style>

<div class="content container my-4">
    <div class="row">
        <main class="col-md-12 px-md-4">
            <div class="d-flex justify-content-between align-items-center pb-2 mb-4 border-bottom">
                <h1 class="h3"><i class="bi bi-person-workspace me-2 text-primary"></i>Developer Information</h1>
            </div>

            <div class="card shadow-sm developer-card">
                <div class="card-header bg-light fw-semibold">
                    <i class="bi bi-code-square me-2"></i> About the Developer
                </div>

                <div class="card-body">
                    <div class="row">
                        <!-- Developer Photo -->
                        <div class="col-md-4 text-center mb-4">
                            <img src="https://avatars.githubusercontent.com/u/195084452?s=400&u=c5af70cb5b3afd0e77d13d406c28b7c93b45c335&v=4"
                                class="rounded-circle shadow" width="150" height="150" alt="Developer Photo">
                            <h4 class="mt-3">Md Rasel Ahmed</h4>
                            <p class="text-muted">Full Stack Developer</p>
                        </div>

                        <!-- Details -->
                        <div class="col-md-8">
                            <!-- Summary -->
                            <h5 class="mb-3"><i class="bi bi-person-badge me-2"></i>Professional Summary</h5>
                            <p>
                                Web and mobile app developer with 3 years of experience in building scalable and robust solutions.
                                Proficient in MERN stack, PHP, React Native, Expo, jQuery, MySQL, MongoDB, Tailwind CSS, TypeScript, WebSocket, and more.
                            </p>

                            <!-- Skills -->
                            <h5 class="mb-3 mt-4"><i class="bi bi-gear-wide-connected me-2"></i>Technical Skills</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            MERN Stack
                                            <span class="badge bg-primary">Expert</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            PHP
                                            <span class="badge bg-primary">Expert</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            React Native
                                            <span class="badge bg-primary">Expert</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            Expo
                                            <span class="badge bg-info text-dark">Advanced</span>
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            jQuery
                                            <span class="badge bg-success">Advanced</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            MySQL/MongoDB
                                            <span class="badge bg-success">Expert</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            Tailwind CSS
                                            <span class="badge bg-success">Advanced</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            TypeScript
                                            <span class="badge bg-secondary">Intermediate</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <!-- Contact -->
                            <h5 class="mb-3 mt-4">
                                <i class="bi bi-envelope me-2"></i>Contact Information
                            </h5>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">
                                    <i class="bi bi-envelope-fill me-2 "></i>
                                    <a href="mailto:mdraselahmed.code@gmail.com">mdraselahmed.code@gmail.com</a>
                                </li>
                                <li class="list-group-item">
                                    <i class="bi bi-phone-fill me-2"></i>
                                    <a href="tel:+8801929951023">+880 1929 951023</a>
                                </li>
                                <li class="list-group-item">
                                    <i class="bi bi-github me-2"></i>
                                    <a href="https://github.com/mdraselahmedcode" target="_blank">GitHub Profile</a>
                                </li>
                                <li class="list-group-item">
                                    <i class="bi bi-linkedin me-2"></i>
                                    <a href="#" title="having trouble">LinkedIn Profile</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="card-footer text-muted text-center">
                    System developed with <i class="bi bi-heart-fill text-danger"></i> by Md Rasel Ahmed
                </div>
            </div>
        </main>
    </div>
</div>

<?php include BASE_PATH . '/includes/footer.php'; ?>