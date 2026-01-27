<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SimpleRest - For Reasoning Architecture</title>

    <?= base() ?>

    <link rel="stylesheet" href="<?= asset('third_party/bootstrap/5.x/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --primary-color: #0066cc;
            --secondary-color: #004080;
            --accent-color: #00aaff;
            --light-bg: #f8f9fa;
            --dark-text: #212529;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }

        /* Navbar */
        .navbar {
            padding: 1rem 0;
            box-shadow: 0 2px 4px rgba(0,0,0,.08);
            background: white !important;
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--primary-color) !important;
        }

        .nav-link {
            font-weight: 500;
            margin: 0 0.5rem;
            transition: color 0.3s;
        }

        .nav-link:hover {
            color: var(--primary-color) !important;
        }

        .btn-download {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 0.5rem 1.5rem;
            border-radius: 5px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-download:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 102, 204, 0.3);
        }

        /* Hero Section */
        .hero {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 100px 0 80px;
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="rgba(255,255,255,0.05)" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,122.7C672,117,768,139,864,138.7C960,139,1056,117,1152,101.3C1248,85,1344,75,1392,69.3L1440,64L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>');
            background-size: cover;
            background-position: bottom;
        }

        .hero h1 {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            position: relative;
        }

        .hero .lead {
            font-size: 1.25rem;
            margin-bottom: 2rem;
            opacity: 0.95;
        }

        .hero .btn-hero {
            padding: 0.75rem 2rem;
            font-size: 1.1rem;
            border-radius: 5px;
            font-weight: 600;
            margin: 0.5rem;
        }

        .btn-primary-hero {
            background: white;
            color: var(--primary-color);
            border: 2px solid white;
        }

        .btn-primary-hero:hover {
            background: transparent;
            color: white;
            border-color: white;
        }

        .btn-outline-hero {
            background: transparent;
            color: white;
            border: 2px solid white;
        }

        .btn-outline-hero:hover {
            background: white;
            color: var(--primary-color);
        }

        /* Section Titles */
        .section-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: var(--dark-text);
        }

        .section-subtitle {
            font-size: 1.1rem;
            color: #6c757d;
            margin-bottom: 3rem;
        }

        .section-divider {
            width: 60px;
            height: 4px;
            background: var(--primary-color);
            margin: 0 auto 3rem;
        }

        /* Feature Cards */
        .feature-card {
            padding: 2rem;
            border-radius: 10px;
            background: white;
            border: 1px solid #e9ecef;
            transition: all 0.3s;
            height: 100%;
            box-shadow: 0 2px 8px rgba(0,0,0,.05);
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 12px 24px rgba(0,0,0,.12);
            border-color: var(--primary-color);
        }

        .feature-icon {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin: 0 auto 1.5rem;
        }

        .feature-card h3 {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: var(--dark-text);
        }

        .feature-card p {
            color: #6c757d;
            line-height: 1.6;
        }

        /* Doc Cards */
        .doc-card {
            background: white;
            border-radius: 10px;
            padding: 2rem;
            border-left: 4px solid var(--primary-color);
            box-shadow: 0 4px 12px rgba(0,0,0,.08);
            transition: all 0.3s;
            height: 100%;
        }

        .doc-card:hover {
            transform: translateX(5px);
            box-shadow: 0 6px 16px rgba(0,0,0,.12);
        }

        .doc-card h4 {
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }

        .doc-card ul {
            list-style: none;
            padding: 0;
        }

        .doc-card li {
            padding: 0.5rem 0;
            color: #6c757d;
        }

        .doc-card li::before {
            content: "→";
            color: var(--primary-color);
            font-weight: bold;
            margin-right: 0.5rem;
        }

        /* Success Stories */
        .success-card {
            background: white;
            border-radius: 10px;
            padding: 2rem;
            box-shadow: 0 4px 12px rgba(0,0,0,.08);
            height: 100%;
        }

        .success-icon {
            font-size: 3rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }

        .success-card h4 {
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .success-card .stats {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-color);
            margin: 1rem 0;
        }

        /* Footer */
        footer {
            background: #212529;
            color: white;
            padding: 3rem 0 1rem;
            margin-top: 5rem;
        }

        footer h5 {
            font-weight: 600;
            margin-bottom: 1.5rem;
        }

        footer a {
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            transition: color 0.3s;
        }

        footer a:hover {
            color: white;
        }

        .footer-bottom {
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid rgba(255,255,255,0.1);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2rem;
            }

            .section-title {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
        <div class="container">
            <a class="navbar-brand" href="/">
                <i class="bi bi-code-square"></i> SimpleRest
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#home">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#about">About Us</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#services">Services</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#docs">Documentation</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-download ms-3" href="#download">
                            <i class="bi bi-download"></i> Download
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="hero">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8 mx-auto text-center">
                    <h1>Build Intelligent Systems with SimpleRest</h1>
                    <p class="lead">
                        A production-grade backend framework designed for multi-LLM integrations, automatic API endpoints,
                        powerful access control and CLI tools for reproducible experimentation.
                    </p>
                    <p class="lead">
                        Powered by <strong>4thinking.com</strong> - designed from the ground up to be safe in AI-driven environments.
                    </p>
                    <div class="mt-4">
                        <a href="#download" class="btn btn-primary-hero btn-hero">
                            <i class="bi bi-download"></i> Download Now
                        </a>
                        <a href="#docs" class="btn btn-outline-hero btn-hero">
                            <i class="bi bi-book"></i> Documentation
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title">For Reasoning Architecture</h2>
                <div class="section-divider"></div>
                <p class="section-subtitle">
                    An architectural paradigm focused on reducing inference complexity, minimizing implicit behavior,
                    and enforcing deterministic execution paths.
                </p>
            </div>
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-lightning-charge"></i>
                        </div>
                        <h3>Explicit Over Convenience</h3>
                        <p>
                            Every runtime decision is traceable to an explicit rule or schema. No hidden behavior based on annotations or reflection.
                        </p>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-diagram-3"></i>
                        </div>
                        <h3>Schema-Driven Execution</h3>
                        <p>
                            Behavior is driven by declarative schemas that are interpretable both by humans and machines,
                            reducing cognitive overhead.
                        </p>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <h3>Deterministic Runtime</h3>
                        <p>
                            Identical inputs produce identical outputs. No environment-dependent side effects.
                            Perfect for regulated environments and high-availability services.
                        </p>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-graph-up"></i>
                        </div>
                        <h3>Graceful Degradation</h3>
                        <p>
                            Partial system failure does not cascade. Reduced functionality is preferable to total outage.
                            Supports degraded views at API and UI level.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="services" class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title">Core Features</h2>
                <div class="section-divider"></div>
                <p class="section-subtitle">
                    Everything you need to build production-ready backend systems
                </p>
            </div>
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="feature-card text-center">
                        <div class="feature-icon">
                            <i class="bi bi-robot"></i>
                        </div>
                        <h3>AI-Friendly Architecture</h3>
                        <p>
                            Designed to work seamlessly with LLM agents. AI is treated as an advisory component,
                            never as a mandatory dependency.
                        </p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="feature-card text-center">
                        <div class="feature-icon">
                            <i class="bi bi-terminal"></i>
                        </div>
                        <h3>Powerful CLI Tools</h3>
                        <p>
                            Activable execution profiles selectable at runtime. Control enabled modules, validation strictness,
                            and security enforcement level.
                        </p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="feature-card text-center">
                        <div class="feature-icon">
                            <i class="bi bi-plug"></i>
                        </div>
                        <h3>Automatic API Endpoints</h3>
                        <p>
                            Backend systems work out-of-the-box without writing Models or Controllers.
                            RAD (Rapid Application Development) at its finest.
                        </p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="feature-card text-center">
                        <div class="feature-icon">
                            <i class="bi bi-lock"></i>
                        </div>
                        <h3>Robust Access Control</h3>
                        <p>
                            Fine-grained permissions system with role-based access control.
                            Perfect for enterprise applications and multi-tenant systems.
                        </p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="feature-card text-center">
                        <div class="feature-icon">
                            <i class="bi bi-gear"></i>
                        </div>
                        <h3>Rules Engine</h3>
                        <p>
                            Deterministic rules engine with no probabilistic branching.
                            All rules are versioned and auditable.
                        </p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="feature-card text-center">
                        <div class="feature-icon">
                            <i class="bi bi-layers"></i>
                        </div>
                        <h3>Multi-Profile Support</h3>
                        <p>
                            Run in production, degraded, offline, or AI-assisted modes.
                            Runtime behavior is selected explicitly via CLI or configuration.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Documentation Section -->
    <section id="docs" class="py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title">Quick Start Guide</h2>
                <div class="section-divider"></div>
                <p class="section-subtitle">
                    Get started with SimpleRest in minutes
                </p>
            </div>
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <div class="doc-card">
                        <h4><i class="bi bi-1-circle-fill"></i> Installation</h4>
                        <ul>
                            <li>Download the latest release</li>
                            <li>Extract to your web directory</li>
                            <li>Run composer install</li>
                            <li>Configure your database</li>
                            <li>Run migrations</li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="doc-card">
                        <h4><i class="bi bi-2-circle-fill"></i> Configuration</h4>
                        <ul>
                            <li>Set up your .env file</li>
                            <li>Configure ACL permissions</li>
                            <li>Define your schemas</li>
                            <li>Select runtime profile</li>
                            <li>Enable desired modules</li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="doc-card">
                        <h4><i class="bi bi-3-circle-fill"></i> Development</h4>
                        <ul>
                            <li>Use CLI commands for scaffolding</li>
                            <li>Define declarative schemas</li>
                            <li>API endpoints auto-generated</li>
                            <li>Test with built-in tools</li>
                            <li>Deploy to production</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Success Stories -->
    <section id="success" class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title">Why Choose SimpleRest</h2>
                <div class="section-divider"></div>
                <p class="section-subtitle">
                    Doing less, explicitly, and correctly
                </p>
            </div>
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <div class="success-card text-center">
                        <div class="success-icon">
                            <i class="bi bi-speedometer2"></i>
                        </div>
                        <h4>Rapid Development</h4>
                        <div class="stats">10x</div>
                        <p>Faster API development with automatic endpoint generation and declarative schemas.</p>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="success-card text-center">
                        <div class="success-icon">
                            <i class="bi bi-people"></i>
                        </div>
                        <h4>Built for Teams</h4>
                        <div class="stats">100%</div>
                        <p>Explicit behavior means easier onboarding and reduced cognitive load for your entire team.</p>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="success-card text-center">
                        <div class="success-icon">
                            <i class="bi bi-shield-fill-check"></i>
                        </div>
                        <h4>Production Ready</h4>
                        <div class="stats">99.9%</div>
                        <p>Deterministic runtime and graceful degradation ensure high availability in production.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Download Section -->
    <section id="download" class="py-5 bg-primary text-white">
        <div class="container text-center">
            <h2 class="mb-4">Ready to Get Started?</h2>
            <p class="lead mb-4">
                Download SimpleRest today and start building intelligent backend systems
            </p>
            <div class="d-flex justify-content-center gap-3">
                <a href="https://github.com/boctulus/simplerest" class="btn btn-light btn-lg" target="_blank">
                    <i class="bi bi-github"></i> View on GitHub
                </a>
                <a href="https://4thinking.com" class="btn btn-outline-light btn-lg" target="_blank">
                    <i class="bi bi-globe"></i> Visit 4thinking.com
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <h5><i class="bi bi-code-square"></i> SimpleRest</h5>
                    <p>
                        A production-grade backend framework implementing the For Reasoning Architecture paradigm.
                    </p>
                    <p class="mt-3">
                        <strong>Powered by 4thinking.com</strong>
                    </p>
                </div>
                <div class="col-lg-4 mb-4">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#home">Home</a></li>
                        <li class="mb-2"><a href="#about">About Us</a></li>
                        <li class="mb-2"><a href="#services">Services</a></li>
                        <li class="mb-2"><a href="#docs">Documentation</a></li>
                        <li class="mb-2"><a href="#download">Download</a></li>
                    </ul>
                </div>
                <div class="col-lg-4 mb-4">
                    <h5>Resources</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="https://github.com/boctulus/simplerest">GitHub Repository</a></li>
                        <li class="mb-2"><a href="https://4thinking.com">4thinking.com</a></li>
                        <li class="mb-2"><a href="#">Community Forum</a></li>
                        <li class="mb-2"><a href="#">API Reference</a></li>
                        <li class="mb-2"><a href="#">Contributing Guide</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom text-center">
                <p class="mb-0">
                    &copy; <?= date('Y') ?> SimpleRest Framework. Built with the For Reasoning Architecture.
                    All rights reserved.
                </p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="<?= asset('third_party/bootstrap/5.x/bootstrap.bundle.min.js') ?>"></script>

    <script>
        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    const offsetTop = target.offsetTop - 70;
                    window.scrollTo({
                        top: offsetTop,
                        behavior: 'smooth'
                    });
                }
            });
        });

        // Active nav link on scroll
        window.addEventListener('scroll', function() {
            let scrollPosition = window.scrollY + 100;

            document.querySelectorAll('section[id]').forEach(section => {
                const sectionTop = section.offsetTop;
                const sectionHeight = section.offsetHeight;
                const sectionId = section.getAttribute('id');

                if (scrollPosition >= sectionTop && scrollPosition < sectionTop + sectionHeight) {
                    document.querySelectorAll('.nav-link').forEach(link => {
                        link.classList.remove('active');
                        if (link.getAttribute('href') === `#${sectionId}`) {
                            link.classList.add('active');
                        }
                    });
                }
            });
        });
    </script>

    <?= footer() ?>
</body>
</html>
