<?php require_once 'config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$demo = get_demo($id);

if (!$demo) {
    $not_found = true;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Embed Demo - PinClick</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <nav class="navbar">
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

    <main class="main-content">
        <div class="container">
            <?php if (isset($not_found)): ?>
            <div class="glass-card">
                <h2>Demo not found</h2>
                <a href="dashboard.php" class="btn btn-primary">Back to Dashboard</a>
            </div>
            <?php else: ?>
            <div class="page-header">
                <h1>Embed Demo</h1>
                <p class="text-muted">Embed "<?= htmlspecialchars($demo['title']) ?>" on your website</p>
            </div>

            <div class="glass-card">
                <h3>Embed Code</h3>
                <p class="text-muted">Copy and paste this code into your website's HTML:</p>
                <div class="embed-code-wrapper">
                    <textarea class="embed-code" id="embedCode" readonly onclick="this.select()"><iframe src="<?= SITE_URL ?>/viewer.php?id=<?= $id ?>" width="100%" height="600" frameborder="0" style="border-radius:12px;box-shadow:0 4px 24px rgba(0,0,0,0.3)"></iframe></textarea>
                </div>
                <button class="btn btn-primary" onclick="copyEmbed()">Copy Code</button>
            </div>

            <div class="glass-card">
                <h3>Preview</h3>
                <div class="embed-preview">
                    <iframe src="viewer.php?id=<?= $id ?>" width="100%" height="600" frameborder="0" style="border-radius:12px;box-shadow:0 4px 24px rgba(0,0,0,0.3)"></iframe>
                </div>
            </div>

            <div class="glass-card">
                <h3>Options</h3>
                <div class="embed-options">
                    <div class="option-row">
                        <label>Width</label>
                        <select id="embedWidth" onchange="updateEmbed()">
                            <option value="100%">100% (Responsive)</option>
                            <option value="1200px">1200px</option>
                            <option value="900px">900px</option>
                            <option value="600px">600px</option>
                        </select>
                    </div>
                    <div class="option-row">
                        <label>Height</label>
                        <select id="embedHeight" onchange="updateEmbed()">
                            <option value="600">600px</option>
                            <option value="500">500px</option>
                            <option value="400">400px</option>
                            <option value="700">700px</option>
                            <option value="800">800px</option>
                        </select>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </main>

    <script>
    function copyEmbed() {
        const el = document.getElementById('embedCode');
        el.select();
        document.execCommand('copy');
        showToast('Embed code copied!', 'success');
    }

    function updateEmbed() {
        const w = document.getElementById('embedWidth').value;
        const h = document.getElementById('embedHeight').value;
        const code = `<iframe src="<?= SITE_URL ?>/viewer.php?id=<?= $id ?>" width="${w}" height="${h}" frameborder="0" style="border-radius:12px;box-shadow:0 4px 24px rgba(0,0,0,0.3)"></iframe>`;
        document.getElementById('embedCode').value = code;
    }

    function showToast(msg, type) {
        const t = document.createElement('div');
        t.className = 'toast toast-' + type;
        t.textContent = msg;
        document.body.appendChild(t);
        setTimeout(() => t.classList.add('show'), 10);
        setTimeout(() => { t.classList.remove('show'); setTimeout(() => t.remove(), 300); }, 3000);
    }
    </script>
</body>
</html>
