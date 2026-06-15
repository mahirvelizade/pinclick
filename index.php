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
                <span class="brand-icon">◆</span>
                <span>PinClick</span>
            </a>
            <div class="nav-links">
                <a href="dashboard.php" class="nav-link">Dashboard</a>
            </div>
        </div>
    </nav>

    <section class="hero">
        <div class="hero-bg"></div>
        <div class="container">
            <div class="hero-content">
                <div class="hero-badge">✨ Interactive Demo Builder</div>
                <h1 class="hero-title">Create <span class="gradient-text">Interactive</span> Product Demos<br>in Minutes</h1>
                <p class="hero-subtitle">Upload screenshots, add clickable hotspots, and share beautiful interactive walkthroughs with your users. No coding required.</p>
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
                    <span class="brand-icon">◆</span>
                    <span>PinClick</span>
                </div>
                <p class="text-muted small">Built with PHP & Vanilla JS. No frameworks, no dependencies.</p>
            </div>
        </div>
    </footer>
</body>
</html>
