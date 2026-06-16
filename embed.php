<?php require_once 'config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$demo = get_demo($id);
if (!$demo) { http_response_code(404); die('Demo not found'); }
$title = htmlspecialchars($demo['title']);
$site_url = SITE_URL;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Embed - <?= $title ?></title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <nav class="navbar">
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

    <main class="main-content">
        <div class="container">
            <div class="page-header">
                <div>
                    <h1>Embed Demo</h1>
                    <p class="text-muted">Embed "<?= $title ?>" on your website</p>
                </div>
            </div>

            <div class="glass-card">
                <h3>Embed Code</h3>
                <p class="text-muted">Copy and paste this code into your website's HTML:</p>
                <div class="embed-code-wrapper">
                    <textarea class="embed-code" id="embedCode" readonly onclick="this.select()"><iframe src="<?= $site_url ?>/viewer.php?id=<?= $id ?>" width="100%" height="600" frameborder="0" style="border-radius:12px;box-shadow:0 4px 24px rgba(0,0,0,0.3)"></iframe></textarea>
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
        const code = `<iframe src="<?= $site_url ?>/viewer.php?id=<?= $id ?>" width="${w}" height="${h}" frameborder="0" style="border-radius:12px;box-shadow:0 4px 24px rgba(0,0,0,0.3)"></iframe>`;
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
