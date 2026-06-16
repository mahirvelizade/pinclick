<?php require_once 'config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$demo = get_demo($id);
if (!$demo) { http_response_code(404); die('Demo not found'); }
$data = $demo['data'];
$json_data = json_encode($data, JSON_UNESCAPED_UNICODE);
$title = htmlspecialchars($data['title'] ?? 'Untitled Demo');
$accent = $data['accent_color'] ?? '#6D5DFB';
$accent_r = hexdec(substr($accent, 1, 2));
$accent_g = hexdec(substr($accent, 3, 2));
$accent_b = hexdec(substr($accent, 5, 2));
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $title ?> - PinClick</title>
<style>
:root {
    --primary: #6D5DFB;
    --secondary: #8B5CF6;
    --bg: #0B1020;
    --bg-card: rgba(15, 23, 42, 0.8);
    --border: rgba(109, 93, 251, 0.15);
    --glass-border: rgba(255,255,255,0.06);
    --text: #E2E8F0;
    --text-muted: #64748B;
    --text-dim: #475569;
    --radius: 8px;
    --radius-lg: 12px;
    --accent: <?= $accent ?>;
}
* { margin: 0; padding: 0; box-sizing: border-box; }
body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
    background: var(--bg); color: var(--text); min-height: 100vh;
}
.btn {
    display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px;
    border-radius: var(--radius); border: 1px solid var(--glass-border);
    font-size: 13px; font-weight: 600; cursor: pointer; text-decoration: none;
    background: transparent; color: var(--text); transition: all 0.15s ease;
}
.btn:hover { background: rgba(<?= $accent_r ?>,<?= $accent_g ?>,<?= $accent_b ?>,0.1); border-color: var(--accent); }
.btn-primary { background: var(--accent); color: #fff; border-color: var(--accent); }
.btn-primary:hover { filter: brightness(1.15); }
.btn-ghost:hover { background: rgba(255,255,255,0.05); }
.btn-sm { padding: 6px 12px; font-size: 12px; }
.text-muted { color: var(--text-muted); }

/* Viewer layout */
.viewer-page { display: flex; align-items: center; justify-content: center; padding: 20px; }
.viewer-container { width: 100%; max-width: 1200px; margin: 0 auto; }

.viewer-header {
    display: flex; align-items: center; justify-content: space-between; padding: 16px 0; gap: 16px;
}
.viewer-header h2 { font-size: 20px; font-weight: 700; color: var(--accent); }
.viewer-controls { display: flex; gap: 8px; }

.viewer-stage {
    position: relative; background: var(--bg-card);
    border-radius: var(--radius-lg); border: 1px solid var(--glass-border); overflow: hidden;
}
.viewer-canvas {
    display: flex; align-items: center; justify-content: center; min-height: 400px; padding: 0;
}

.viewer-image-wrapper {
    position: relative; display: inline-block; line-height: 0; max-width: 100%;
}
.viewer-image {
    display: block; max-width: 100%; max-height: 70vh; object-fit: contain; user-select: none;
}

/* Pins */
.viewer-image-wrapper .pins-container {
    position: absolute; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none;
}
.pin-marker {
    position: absolute; transform: translate(-50%, -50%);
    cursor: pointer; pointer-events: all; z-index: 10;
    display: flex; flex-direction: column; align-items: center; gap: 2px;
    transition: transform 0.15s ease;
}
.pin-marker:hover { transform: translate(-50%, -50%) scale(1.15); z-index: 11; }
.pin-dot {
    width: 24px; height: 24px; border-radius: 50%;
    background: var(--accent); border: 3px solid #fff;
    box-shadow: 0 2px 12px rgba(<?= $accent_r ?>,<?= $accent_g ?>,<?= $accent_b ?>,0.5);
    animation: pinPulse 2s ease-in-out infinite;
}
@keyframes pinPulse {
    0%, 100% { box-shadow: 0 2px 12px rgba(<?= $accent_r ?>,<?= $accent_g ?>,<?= $accent_b ?>,0.5); }
    50% { box-shadow: 0 2px 20px rgba(<?= $accent_r ?>,<?= $accent_g ?>,<?= $accent_b ?>,0.8); }
}

/* Tooltip */
.tooltip-popover {
    position: absolute; z-index: 100; pointer-events: all;
    animation: tooltipFadeIn 0.15s ease;
}
@keyframes tooltipFadeIn {
    from { opacity: 0; transform: scale(0.9) translateY(-4px); }
    to { opacity: 1; transform: scale(1) translateY(0); }
}
.tooltip-card {
    background: var(--bg-card); border: 1px solid var(--border);
    border-radius: var(--radius); padding: 16px 20px;
    min-width: 200px; max-width: 300px;
    box-shadow: 0 8px 32px rgba(0,0,0,0.5); position: relative;
}

.tooltip-title {
    font-size: 13px; font-weight: 700; color: var(--accent);
    margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.5px;
}
.tooltip-card p { font-size: 14px; line-height: 1.5; margin-bottom: 12px; color: var(--text); word-break: break-word; }
.tooltip-card .btn { width: 100%; justify-content: center; font-size: 13px; padding: 8px 12px; }

.viewer-footer {
    display: flex; align-items: center; justify-content: space-between; padding: 16px 0; gap: 16px;
    opacity: 0; transition: opacity 0.4s ease;
}
.step-indicators { display: flex; gap: 6px; align-items: center; }
.step-dot {
    width: 10px; height: 10px; border-radius: 50%;
    background: rgba(255,255,255,0.15); cursor: pointer; transition: all 0.2s ease;
}
.step-dot.active { background: var(--accent); transform: scale(1.3); }
.step-dot:hover { background: rgba(255,255,255,0.3); }

/* Play overlay */
.viewer-overlay {
    position: absolute; inset: 0; z-index: 50;
    display: flex; flex-direction: column; align-items: center; justify-content: center;
    background: rgba(11, 16, 32, 0.55);
    backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px);
    cursor: pointer; transition: opacity 0.5s ease;
}
.viewer-overlay.hidden { opacity: 0; pointer-events: none; }
.play-btn {
    width: 88px; height: 88px; border-radius: 50%;
    background: var(--accent); border: 3px solid rgba(255,255,255,0.25);
    color: #fff; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    box-shadow: 0 0 50px rgba(<?= $accent_r ?>,<?= $accent_g ?>,<?= $accent_b ?>,0.45);
    transition: box-shadow 0.2s ease;
    padding: 0; line-height: 1;
}
.play-btn:hover { box-shadow: 0 0 70px rgba(<?= $accent_r ?>,<?= $accent_g ?>,<?= $accent_b ?>,0.7); }
.play-btn svg { animation: playPulse 2s ease-in-out infinite; }
@keyframes playPulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.18); }
}
.play-label { margin-top: 20px; color: var(--text-muted); font-size: 14px; letter-spacing: 0.5px; }

/* Embed Modal */
.embed-modal-overlay {
    position: fixed; inset: 0; z-index: 200;
    background: rgba(0,0,0,0.6); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px);
    display: none; align-items: center; justify-content: center; padding: 20px;
}
.embed-modal-overlay.open { display: flex; }
.embed-modal-box {
    background: var(--bg-card); border: 1px solid var(--glass-border);
    border-radius: var(--radius-lg); padding: 28px; width: 100%; max-width: 600px;
    position: relative; animation: slideUp 0.2s ease;
}
@keyframes slideUp { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: translateY(0); } }
.embed-modal-close {
    position: absolute; top: 12px; right: 12px;
    background: none; border: none; color: var(--text-muted); font-size: 20px;
    cursor: pointer; padding: 4px 8px; border-radius: var(--radius); transition: 0.15s;
}
.embed-modal-close:hover { color: var(--text); background: rgba(255,255,255,0.05); }
.embed-modal-box h3 { font-size: 16px; font-weight: 700; margin-bottom: 4px; }
.embed-modal-box .sub { color: var(--text-muted); font-size: 13px; margin-bottom: 16px; }
.embed-textarea {
    width: 100%; min-height: 70px; padding: 10px 12px;
    background: rgba(0,0,0,0.3); border: 1px solid var(--glass-border);
    border-radius: var(--radius); color: var(--accent);
    font-family: 'SF Mono', 'Fira Code', monospace; font-size: 12px;
    resize: none; outline: none;
}
.embed-textarea:focus { border-color: var(--primary); }
.embed-modal-row { display: flex; gap: 8px; align-items: center; margin-top: 8px; }
.embed-modal-row label { font-size: 12px; color: var(--text-muted); }
.embed-modal-row select {
    padding: 5px 8px; background: rgba(255,255,255,0.05);
    border: 1px solid var(--glass-border); border-radius: var(--radius);
    color: var(--text); font-size: 12px; font-family: inherit; outline: none;
}
.embed-copy-ok { font-size: 12px; color: var(--success); opacity: 0; transition: 0.2s; }
.embed-copy-ok.show { opacity: 1; }
</style>
</head>
<body class="viewer-page">
<div class="viewer-container">
    <div class="viewer-header">
        <h2 id="demoTitle"><?= $title ?></h2>
        <div class="viewer-controls">
            <button class="btn btn-ghost btn-sm" onclick="closeViewer()">✕ Close</button>
            <button class="btn btn-ghost btn-sm" onclick="openEmbedModal()">🔗 Embed</button>
            <button class="btn btn-ghost btn-sm" onclick="restartDemo()">🔄 Restart</button>
        </div>
    </div>
    <div class="viewer-stage">
        <div class="viewer-canvas" id="viewerCanvas">
            <div class="viewer-image-wrapper" id="imageWrapper">
                <img id="viewerImage" class="viewer-image" style="display:none" alt="Demo step">
                <div class="pins-container" id="pinsContainer"></div>
                <div class="tooltip-popover" id="tooltipPopover" style="display:none">
                    <div class="tooltip-card">
                        <h4 class="tooltip-title" id="tooltipTitle"></h4>
                        <p id="tooltipText"></p>
                        <button class="btn btn-primary btn-sm" id="tooltipAction" onclick="handleTooltipAction()"></button>
                    </div>
                </div>
            </div>
            <div class="viewer-overlay" id="viewerOverlay" onclick="startDemo()">
                <button class="play-btn"><svg width="40" height="40" viewBox="0 0 24 24" fill="none"><path d="M8 5v14l11-7z" fill="currentColor"/></svg></button>
                <p class="play-label">Click to explore</p>
            </div>
        </div>
    </div>
    <div class="viewer-footer" id="viewerFooter">
        <button class="btn btn-ghost" onclick="prevStep()" id="prevBtn">⬅️ Previous</button>
        <div class="step-indicators" id="stepIndicators"></div>
        <button class="btn btn-primary" onclick="nextStep()" id="nextBtn">Next ➡️</button>
    </div>
</div>

<div class="embed-modal-overlay" id="embedModal" onclick="if(event.target===this)closeEmbedModal()">
    <div class="embed-modal-box">
        <button class="embed-modal-close" onclick="closeEmbedModal()">✕</button>
        <h3>Embed Code</h3>
        <p class="sub">Copy and paste this code into your website's HTML</p>
        <textarea class="embed-textarea" id="embedCode" readonly onclick="this.select()"></textarea>
        <div class="embed-modal-row">
            <button class="btn btn-primary btn-sm" onclick="copyEmbedCode()">📋 Copy</button>
            <span class="embed-copy-ok" id="embedCopyOk">Copied!</span>
            <label>Width</label>
            <select id="embW" onchange="updateEmbedCode()">
                <option value="100%">100%</option>
                <option value="900px">900px</option>
                <option value="600px">600px</option>
            </select>
            <label>Height</label>
            <select id="embH" onchange="updateEmbedCode()">
                <option value="600">600</option>
                <option value="400">400</option>
                <option value="800">800</option>
            </select>
        </div>
    </div>
</div>
var PINCLICK_DATA = <?= $json_data ?>;
var PINCLICK_ID = <?= $id ?>;
var curStep = 0, demoData = null, demoId = <?= $id ?>, started = false;

function init() {
    demoData = PINCLICK_DATA;
    document.getElementById('demoTitle').textContent = demoData.title || 'Untitled Demo';
    if (!demoData.steps || !demoData.steps.length) {
        document.querySelector('.viewer-canvas').innerHTML = '<p class="text-muted" style="padding:80px">No steps</p>';
        return;
    }
    renderDots();
    loadStep(0);
    fetch('api/track_view.php', { method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({demo_id:demoId}) }).catch(function(){});
}

function renderDots() {
    var c = document.getElementById('stepIndicators');
    c.innerHTML = '';
    demoData.steps.forEach(function(_, i) {
        var d = document.createElement('div');
        d.className = 'step-dot' + (i === curStep ? ' active' : '');
        d.onclick = function() { hideTip(); loadStep(i); };
        c.appendChild(d);
    });
}

function loadStep(i) {
    var step = demoData.steps[i];
    if (!step) return;
    curStep = i;
    var img = document.getElementById('viewerImage');
    img.style.display = 'none';
    hideTip();

    function ready() {
        img.style.display = 'block';
        renderPins();
        if (started) autoShowFirstTip();
    }

    var url = step.image;
    if (url.indexOf('://') === -1) {
        url = window.location.protocol + '//' + window.location.host + '/' + url.replace(/^\//, '');
    }
    img.onload = ready;
    img.onerror = function() {
        img.onerror = function() { img.alt = 'Failed to load image'; img.style.display = 'block'; };
        img.src = step.image;
    };
    img.src = url;

    document.getElementById('prevBtn').disabled = i === 0;
    document.getElementById('prevBtn').style.opacity = i === 0 ? '0.4' : '1';
    var nb = document.getElementById('nextBtn');
    nb.disabled = i === demoData.steps.length - 1;
    nb.style.opacity = i === demoData.steps.length - 1 ? '0.4' : '1';
    nb.textContent = i === demoData.steps.length - 1 ? '✅ Finish' : 'Next ➡️';

    document.querySelectorAll('.step-dot').forEach(function(d, idx) {
        d.classList.toggle('active', idx === i);
    });
}

function renderPins() {
    var c = document.getElementById('pinsContainer');
    c.innerHTML = '';
    var step = demoData.steps[curStep];
    if (!step || !step.pins) return;
    step.pins.forEach(function(pin, idx) {
        var el = document.createElement('div');
        el.className = 'pin-marker';
        el.style.left = pin.x + '%';
        el.style.top = pin.y + '%';
        el.innerHTML = '<span class="pin-dot"></span>';
        el.onclick = function(e) {
            e.stopPropagation();
            showTip(e, pin, idx);
        };
        c.appendChild(el);
    });
}

function showTip(e, pin, idx) {
    var pop = document.getElementById('tooltipPopover');
    document.getElementById('tooltipTitle').textContent = pin.title || '';
    document.getElementById('tooltipTitle').style.display = pin.title ? 'block' : 'none';
    document.getElementById('tooltipText').textContent = pin.text || 'Continue';
    var btn = document.getElementById('tooltipAction');
    if (pin.action === 'url') {
        btn.textContent = 'Open Link'; btn.dataset.action = 'url'; btn.dataset.url = pin.url || '#';
    } else if (pin.action === 'previous') {
        btn.textContent = 'Previous'; btn.dataset.action = 'previous';
    } else {
        btn.textContent = 'Next'; btn.dataset.action = 'next';
    }

    var wrap = document.getElementById('imageWrapper');
    var wr = wrap.getBoundingClientRect();
    var pr = e.currentTarget.getBoundingClientRect();
    var cx = pr.left - wr.left + pr.width / 2;
    var cy = pr.top - wr.top + pr.height / 2;

    var gap = 16;
    var dirH = pin.x < 50 ? 'right' : 'left';
    var dirV = pin.y < 50 ? 'down' : 'up';
    var fx, fy;

    if (dirH === 'right') { fx = cx + gap; pop.style.transform = 'none'; }
    else { fx = cx - gap; pop.style.transform = 'translateX(-100%)'; }

    if (dirV === 'down') { fy = cy + gap; }
    else { fy = cy - gap; }

    pop.style.left = Math.round(fx) + 'px';
    pop.style.top = Math.round(fy) + 'px';
    pop.style.display = 'block';

    var por = pop.getBoundingClientRect();

    if (por.right > wr.right) { fx = cx - gap; pop.style.transform = 'translateX(-100%)'; }
    else if (por.left < wr.left) { fx = cx + gap; pop.style.transform = 'none'; }

    if (por.bottom > wr.height - 5) { fy = cy - gap; }
    else if (por.top < 5) { fy = cy + gap; }

    pop.style.left = Math.round(fx) + 'px';
    pop.style.top = Math.round(fy) + 'px';
    pop.className = 'tooltip-popover';
    pop.style.display = 'block';

    fetch('api/track_click.php', { method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({demo_id:demoId,step_index:curStep,pin_index:idx}) }).catch(function(){});
}

function autoShowFirstTip() {
    var firstEl = document.querySelector('.pin-marker');
    if (!firstEl) return;
    var step = demoData.steps[curStep];
    if (!step || !step.pins || !step.pins.length) return;
    showTip({ currentTarget: firstEl }, step.pins[0], 0);
}
function hideTip() { document.getElementById('tooltipPopover').style.display = 'none'; }
function nextStep() { if (curStep < demoData.steps.length - 1) loadStep(curStep + 1); }
function prevStep() { if (curStep > 0) loadStep(curStep - 1); }
function restartDemo() { hideTip(); loadStep(0); }
function startDemo() {
    started = true;
    document.getElementById('viewerOverlay').classList.add('hidden');
    document.getElementById('viewerFooter').style.opacity = '1';
    autoShowFirstTip();
}
function handleTooltipAction() {
    var btn = document.getElementById('tooltipAction');
    hideTip();
    if (btn.dataset.action === 'next') nextStep();
    else if (btn.dataset.action === 'previous') prevStep();
    else if (btn.dataset.action === 'url') window.open(btn.dataset.url, '_blank');
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'ArrowRight' || e.key === ' ') { e.preventDefault(); nextStep(); }
    else if (e.key === 'ArrowLeft') { e.preventDefault(); prevStep(); }
    else if (e.key === 'Escape') {
        if (document.getElementById('embedModal').classList.contains('open')) closeEmbedModal();
        else hideTip();
    }
});

document.getElementById('imageWrapper').onclick = function(e) {
    if (!e.target.closest('.tooltip-popover') && !e.target.closest('.pin-marker')) hideTip();
};

function closeViewer() {
    if (window.self !== window.top) {
        parent.postMessage({ action: 'close' }, '*');
        try { parent.history.back(); } catch(e) {}
    } else {
        window.close();
        if (!window.closed) { window.location.href = 'dashboard.php'; }
    }
}

function openEmbedModal() {
    updateEmbedCode();
    document.getElementById('embedModal').classList.add('open');
}
function closeEmbedModal() {
    document.getElementById('embedModal').classList.remove('open');
}
function updateEmbedCode() {
    var w = document.getElementById('embW').value;
    var h = document.getElementById('embH').value;
    var url = window.location.origin + '/viewer.php?id=' + demoId;
    document.getElementById('embedCode').value = '<iframe src="' + url + '" width="' + w + '" height="' + h + '" frameborder="0" style="border-radius:12px;box-shadow:0 4px 24px rgba(0,0,0,0.3)"></iframe>';
}
function copyEmbedCode() {
    document.getElementById('embedCode').select();
    document.execCommand('copy');
    var fb = document.getElementById('embedCopyOk');
    fb.classList.add('show');
    setTimeout(function() { fb.classList.remove('show'); }, 2000);
}

init();
</script>
</body>
</html>
