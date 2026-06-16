<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PinClick - Interactive Demo Builder</title>
    <meta name="description" content="PinClick - Create interactive product demos with screenshots and clickable hotspots. No coding required.">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body class="landing-page">
    <nav class="navbar landing-nav">
        <div class="nav-inner">
            <a href="index.php" class="nav-brand">
                <span class="brand-icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 64 64" fill="#6C5DFB" style="display:block"><path d="M58.583 29.15a5.797 5.797 0 0 1-7.71 1.49c-.68-.45-.68-.44-1.43.32-3.67 3.72-4.68 4.72-7.74 7.75l-1.37 1.37c-.217.2-.254.193-.09.42 3.9 5.87 1.42 12.45-2.21 16.65-1.845 1.983-3.76 2.015-5.7.23-3.371-3.382-5.858-5.793-9.52-9.56-.35-.304-.199-.36-.67 0q-4.32 3.54-8.66 7.04-2.61 2.115-5.24 4.24a3.172 3.172 0 0 1-1.99.88 2.182 2.182 0 0 1-1.55-.69c-1.4-1.37-.36-2.8.09-3.41 3.597-4.929 7.105-9.84 10.78-14.76.266-.371.223-.279-.1-.62-3.319-3.29-5.62-5.597-8.6-8.59-2.11-2.092-1.99-4.206.26-6.16 4.22-3.68 11.45-5.6 16.51-1.91.04.03.07.05.1.07a1223.59 1223.59 0 0 1 10.03-10.04 6.19 6.19 0 0 1 9.35-8.05c6.26 6.2 8.94 8.89 15.13 15.13a6.121 6.121 0 0 1 .33 8.2z"/></svg></span>
                <span>PinClick</span>
            </a>
            <div class="nav-links">
                <a href="dashboard.php" class="nav-link">Dashboard</a>
            </div>
        </div>
    </nav>

    <section class="hero">
        <div class="hero-bg"></div>
        <div class="hero-clover hero-clover-left">🍀</div>
        <div class="hero-clover hero-clover-right">🍀</div>
        <div class="container">
            <div class="hero-content">
                <div class="hero-badge"><svg class="badge-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 748 448" width="28" height="18"><style>.scraft{fill:#fcc35c}.heart{fill:#e9214b}</style><path class="scraft" d="m156.9 291.4c-30.6 18.6-47.5 57.7-50.5 77 92.5-91.9 122 25 227.5 19 11.2-0.6 94.3-12.6 134.5-19l-13.5-125c-15-0.5-46.5 6.2-66.5 20-40.5 28-165.5-12-231.5 28z"/><path class="heart" d="m542.6 153.2c3.3-6.3 8.3-11.9 15.4-13.8 16.4-4.3 28.6 2.2 34.1 17.8 3.6-8.4 5-12.6 14.1-16.8 14.5-6.8 36.2 2.6 37 19.8 3.2 26.4-27.7 37.8-43 53.3-4.1 4.1-6.2 8.5-8.4 13.8-3-6.9-4.9-10.9-10.7-16-14.6-12.8-45.5-28-40.2-51.9 0.2-2.3 0.9-4 1.7-6.2zm86 5.9c10.2-5.6 4.4-16.3-6-16-10.1 6-5.7 16.6 6 16z"/></svg> Interactive Demo Builder</div>
                <h1 class="hero-title">Create <span class="gradient-text">Interactive</span> Product<br>Demos in Minutes</h1>
                <p class="hero-subtitle"><span class="hero-clover-inline">🍀</span> Upload screenshots, add clickable hotspots,<br>and share beautiful interactive walkthroughs with your users.</p>
                <div class="hero-actions">
                    <a href="dashboard.php" class="btn btn-primary btn-lg">Get Started Free</a>
                    <a href="#features" class="btn btn-ghost btn-lg">Learn More</a>
                </div>
            </div>
        </div>
    </section>

    <section class="features-section" id="features">
        <div class="container">
            <div class="section-header">
                <h2>Everything you need</h2>
                <p class="text-muted">Build engaging product demos that convert</p>
            </div>
            <div class="features-grid">
                <div class="glass-card feature-card">
                    <div class="feature-icon">🖼</div>
                    <h3>Screenshot Uploads</h3>
                    <p class="text-muted">Upload PNG, JPG or WEBP screenshots. Drag & drop supported.</p>
                </div>
                <div class="glass-card feature-card">
                    <div class="feature-icon">📌</div>
                    <h3>Clickable Hotspots</h3>
                    <p class="text-muted">Place pins anywhere on your screenshots. Add tooltips and actions.</p>
                </div>
                <div class="glass-card feature-card">
                    <div class="feature-icon">🔗</div>
                    <h3>Share Publicly</h3>
                    <p class="text-muted">Get a shareable link for each demo. No login required for viewers.</p>
                </div>
                <div class="glass-card feature-card">
                    <div class="feature-icon">📱</div>
                    <h3>Responsive Viewer</h3>
                    <p class="text-muted">Works on all devices. Pins use percentage positioning.</p>
                </div>
                <div class="glass-card feature-card">
                    <div class="feature-icon">🔌</div>
                    <h3>Embed Anywhere</h3>
                    <p class="text-muted">Embed demos in your website or docs via iframe.</p>
                </div>
                <div class="glass-card feature-card">
                    <div class="feature-icon">📦</div>
                    <h3>ZIP Export</h3>
                    <p class="text-muted">Export demos as standalone HTML for self-hosting.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="how-it-works">
        <div class="container">
            <div class="section-header">
                <h2>How it works</h2>
                <p class="text-muted">Three simple steps to create your first demo</p>
            </div>
            <div class="steps-showcase">
                <div class="step-card">
                    <div class="step-number">1</div>
                    <h3>Upload Screenshots</h3>
                    <p class="text-muted">Upload screenshots of your product or app interface</p>
                </div>
                <div class="step-connector"></div>
                <div class="step-card">
                    <div class="step-number">2</div>
                    <h3>Add Hotspots</h3>
                    <p class="text-muted">Click on images to place pins. Add tooltips and actions.</p>
                </div>
                <div class="step-connector"></div>
                <div class="step-card">
                    <div class="step-number">3</div>
                    <h3>Share & Embed</h3>
                    <p class="text-muted">Share a link, embed in your site, or export as ZIP.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="cta-section">
        <div class="container">
            <div class="glass-card cta-card">
                <h2>Ready to create your first demo?</h2>
                <p class="text-muted">Start building interactive walkthroughs in seconds</p>
                <a href="dashboard.php" class="btn btn-primary btn-lg">Get Started →</a>
            </div>
        </div>
    </section>

    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-brand">
<span class="brand-icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 64 64" fill="#6C5DFB" style="display:block"><path d="M58.583 29.15a5.797 5.797 0 0 1-7.71 1.49c-.68-.45-.68-.44-1.43.32-3.67 3.72-4.68 4.72-7.74 7.75l-1.37 1.37c-.217.2-.254.193-.09.42 3.9 5.87 1.42 12.45-2.21 16.65-1.845 1.983-3.76 2.015-5.7.23-3.371-3.382-5.858-5.793-9.52-9.56-.35-.304-.199-.36-.67 0q-4.32 3.54-8.66 7.04-2.61 2.115-5.24 4.24a3.172 3.172 0 0 1-1.99.88 2.182 2.182 0 0 1-1.55-.69c-1.4-1.37-.36-2.8.09-3.41 3.597-4.929 7.105-9.84 10.78-14.76.266-.371.223-.279-.1-.62-3.319-3.29-5.62-5.597-8.6-8.59-2.11-2.092-1.99-4.206.26-6.16 4.22-3.68 11.45-5.6 16.51-1.91.04.03.07.05.1.07a1223.59 1223.59 0 0 1 10.03-10.04 6.19 6.19 0 0 1 9.35-8.05c6.26 6.2 8.94 8.89 15.13 15.13a6.121 6.121 0 0 1 .33 8.2z"/></svg></span>
                    <span>PinClick</span>
                </div>
                <p class="text-muted small">Built with PHP & Vanilla JS. No frameworks, no dependencies.</p>
            </div>
        </div>
    </footer>
</body>
</html>
