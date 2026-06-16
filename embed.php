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
    <style>
        .modal-overlay {
            position: fixed; inset: 0; z-index: 200;
            background: rgba(0,0,0,0.6); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px);
            display: flex; align-items: center; justify-content: center; padding: 20px;
            animation: fadeIn 0.2s ease;
        }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        .modal-box {
            background: var(--bg-card); border: 1px solid var(--glass-border);
            border-radius: var(--radius-lg); padding: 32px; width: 100%; max-width: 640px;
            position: relative; animation: slideUp 0.25s ease;
        }
        @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .modal-close {
            position: absolute; top: 16px; right: 16px;
            background: none; border: none; color: var(--text-muted); font-size: 24px;
            cursor: pointer; padding: 4px 8px; border-radius: var(--radius-sm); transition: var(--transition);
        }
        .modal-close:hover { color: var(--text); background: var(--glass-bg); }
        .modal-box h2 { font-size: 20px; font-weight: 700; margin-bottom: 4px; }
        .modal-box .subtitle { color: var(--text-muted); font-size: 14px; margin-bottom: 24px; }
        .modal-section { margin-bottom: 20px; }
        .modal-section h3 { font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-dim); margin-bottom: 8px; }
        .embed-textarea {
            width: 100%; min-height: 80px; padding: 12px;
            background: rgba(0,0,0,0.3); border: 1px solid var(--glass-border);
            border-radius: var(--radius-sm); color: var(--accent);
            font-family: 'SF Mono', 'Fira Code', monospace; font-size: 12px;
            resize: none; outline: none;
        }
        .embed-textarea:focus { border-color: var(--primary); }
        .embed-preview-frame {
            width: 100%; height: 400px; border: none;
            border-radius: var(--radius); background: var(--bg);
        }
        .modal-actions { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; }
        .modal-actions label { font-size: 13px; color: var(--text-muted); font-weight: 500; }
        .modal-actions select {
            padding: 6px 10px; background: rgba(255,255,255,0.05);
            border: 1px solid var(--glass-border); border-radius: var(--radius-sm);
            color: var(--text); font-size: 13px; font-family: inherit; outline: none;
        }
        .copy-feedback { font-size: 12px; color: var(--success); opacity: 0; transition: opacity 0.2s; }
        .copy-feedback.show { opacity: 1; }
    </style>
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
            <div class="glass-card" style="text-align:center;padding:80px 32px;">
                <h2>Embed "<?= $title ?>"</h2>
                <p class="text-muted" style="margin-bottom:24px;">Embed this demo on your website or share it</p>
                <a href="viewer.php?id=<?= $id ?>" class="btn btn-ghost" target="_blank">👁️ Preview Demo</a>
                <button class="btn btn-primary" onclick="openEmbedModal()">🔗 Get Embed Code</button>
            </div>
        </div>
    </main>

    <div class="modal-overlay" id="embedModal" onclick="if(event.target===this)closeModal()">
        <div class="modal-box">
            <button class="modal-close" onclick="closeModal()">✕</button>
            <h2>Embed Code</h2>
            <p class="subtitle">Copy and paste this code into your website's HTML</p>

            <div class="modal-section">
                <h3>HTML Code</h3>
                <textarea class="embed-textarea" id="embedCode" readonly onclick="this.select()"></textarea>
                <div style="margin-top:8px;display:flex;align-items:center;gap:12px;">
                    <button class="btn btn-primary btn-sm" onclick="copyCode()">📋 Copy Code</button>
                    <span class="copy-feedback" id="copyFeedback">Copied!</span>
                </div>
            </div>

            <div class="modal-section">
                <h3>Options</h3>
                <div class="modal-actions">
                    <label>Width</label>
                    <select id="optWidth" onchange="updateCode()">
                        <option value="100%">100% (Responsive)</option>
                        <option value="1200px">1200px</option>
                        <option value="900px">900px</option>
                        <option value="600px">600px</option>
                    </select>
                    <label>Height</label>
                    <select id="optHeight" onchange="updateCode()">
                        <option value="600">600px</option>
                        <option value="500">500px</option>
                        <option value="400">400px</option>
                        <option value="800">800px</option>
                    </select>
                </div>
            </div>

            <div class="modal-section">
                <h3>Preview</h3>
                <iframe id="previewFrame" class="embed-preview-frame" src="viewer.php?id=<?= $id ?>"></iframe>
            </div>
        </div>
    </div>

    <script>
    var siteUrl = '<?= $site_url ?>';
    var demoId = <?= $id ?>;

    function openEmbedModal() {
        document.getElementById('embedModal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
        updateCode();
    }

    function closeModal() {
        document.getElementById('embedModal').style.display = 'none';
        document.body.style.overflow = '';
    }

    function updateCode() {
        var w = document.getElementById('optWidth').value;
        var h = document.getElementById('optHeight').value;
        var code = '<iframe src="' + siteUrl + '/viewer.php?id=' + demoId + '" width="' + w + '" height="' + h + '" frameborder="0" style="border-radius:12px;box-shadow:0 4px 24px rgba(0,0,0,0.3)"></iframe>';
        document.getElementById('embedCode').value = code;
    }

    function copyCode() {
        var el = document.getElementById('embedCode');
        el.select();
        document.execCommand('copy');
        var fb = document.getElementById('copyFeedback');
        fb.classList.add('show');
        setTimeout(function() { fb.classList.remove('show'); }, 2000);
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeModal();
    });

    openEmbedModal();
    </script>
</body>
</html>
