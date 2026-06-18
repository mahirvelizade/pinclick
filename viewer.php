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
    --spotlight-color: rgba(<?= $accent_r ?>,<?= $accent_g ?>,<?= $accent_b ?>,0.6);
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
.pin-ripple {
    position: absolute; inset: -8px; pointer-events: none;
    border: 1.5px solid var(--accent);
    border-radius: 50%;
    opacity: 0;
    animation: rippleExpand 2.5s ease-out infinite;
}

/* Spotlight Areas */
.spotlight-backdrop {
    position: absolute; inset: 0; pointer-events: none; z-index: 4;
    background: rgba(<?= $accent_r ?>,<?= $accent_g ?>,<?= $accent_b ?>,0.08);
    opacity: 0;
    animation: WidgetBackdropAnimation_fadeIn 0.6s ease forwards;
    animation-delay: 0.15s;
}
@keyframes WidgetBackdropAnimation_fadeIn {
    from { opacity: 0; }
    to { opacity: 0.35; }
}
.spotlight-area {
    position: absolute; pointer-events: none; z-index: 5;
    border-radius: 6px;
    opacity: 0;
    animation: spotlightAreaIn 0.5s ease forwards, WidgetSpotlight_spotlightBlink 2s ease infinite;
    animation-delay: 0.5s, 0.5s;
    background: rgba(<?= $accent_r ?>,<?= $accent_g ?>,<?= $accent_b ?>,0.04);
}
.spotlight-area::before {
    content: '';
    position: absolute; inset: 0;
    border: 1px solid var(--accent);
    border-radius: 6px;
    opacity: 1;
    pointer-events: none;
}
@keyframes WidgetSpotlight_spotlightBlink {
    0% { box-shadow: var(--spotlight-color) 0 0 0 0; opacity: .8; }
    80% { box-shadow: var(--spotlight-color) 0 0 0 20px; opacity: 0; }
    100% { box-shadow: var(--spotlight-color) 0 0 0 0; opacity: 0; }
}
@keyframes rippleExpand {
    0% { transform: scale(0.8); opacity: 0.7; }
    100% { transform: scale(1.3); opacity: 0; }
}

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
.play-label { margin-top: 20px; color: #fff; font-size: 14px; letter-spacing: 0.5px; }

.welcome-overlay {
    position: fixed; inset: 0; z-index: 100;
    background: rgba(11, 16, 32, 0.75);
    backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px);
    display: none; align-items: center; justify-content: center; padding: 20px;
}
.welcome-overlay.open { display: flex; }
.welcome-card {
    background: #fff; border-radius: 16px;
    padding: 40px; max-width: 560px; width: 100%;
    box-shadow: 0 20px 60px rgba(0,0,0,0.5);
}
.welcome-card h2 { font-size: 28px; font-weight: 700; margin-bottom: 20px; color: #111; text-align: center; }
.welcome-card p { font-size: .875em; min-height: 1.5em; line-height: 1.5; color: #444; margin-bottom: 16px; }
.welcome-card .btn-wrap { text-align: center; }
.welcome-card .btn { margin-top: 12px; min-width: 160px; padding: 12px 24px; font-size: 15px; justify-content: center; }
.welcome-card .btn-primary:hover { background: #fff; color: var(--accent); border-color: var(--accent); filter: none; }

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
.embed-modal-row { display: flex; gap: 8px; align-items: center; margin-top: 8px; flex-wrap: wrap; }
.embed-modal-row label { font-size: 12px; color: var(--text-muted); }
.embed-modal-row select {
    padding: 5px 8px; background: rgba(255,255,255,0.05);
    border: 1px solid var(--glass-border); border-radius: var(--radius);
    color: var(--text); font-size: 12px; font-family: inherit; outline: none;
}
.embed-copy-ok { font-size: 12px; color: #10B981; opacity: 0; transition: 0.2s; }
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
                <div class="areas-container" id="areasContainer" style="position:absolute;inset:0;pointer-events:none;z-index:4"></div>
                <div class="tooltip-popover" id="tooltipPopover" style="display:none">
                    <div class="tooltip-card">
                        <h4 class="tooltip-title" id="tooltipTitle"></h4>
                        <p id="tooltipText"></p>
                        <button class="btn btn-primary btn-sm" id="tooltipAction" onclick="handleTooltipAction()"></button>
                    </div>
                </div>
            </div>
            <div class="viewer-overlay" id="viewerOverlay" onclick="document.getElementById('welcomeOverlay').classList.add('open')">
                <button class="play-btn"><svg width="40" height="40" viewBox="0 0 24 24" fill="none"><path d="M8 5v14l11-7z" fill="currentColor"/></svg></button>
                <p class="play-label">Click to explore</p>
            </div>
            <div class="welcome-overlay" id="welcomeOverlay">
                <div class="welcome-card">
                    <h2>Welcome to Demo</h2>
                    <p>We're excited to introduce you to our platform and show how it can help you gain valuable insights, streamline your workflows, and strengthen your overall operations.</p>
                    <p>This short product tour takes just a few minutes and provides a high-level overview of the platform's key features, capabilities, and common use cases.</p>
                    <p>Once you've completed the tour, you can explore the platform further by signing up for a free trial. If you've already submitted the required information, our team will review your request and get in touch with you soon.</p>
                    <div class="btn-wrap"><button class="btn btn-primary" onclick="startDemo()">Continue</button></div>
                </div>
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

<script>
var PINCLICK_DATA = <?= $json_data ?>;
var curStep = 0, demoData = null, demoId = <?= $id ?>, started = false, currentPinIdx = 0;

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
    curStep = i; currentPinIdx = 0;
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

    updateNavButtons();

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
        if (idx > currentPinIdx) return;
        var el = document.createElement('div');
        el.className = 'pin-marker';
        el.style.left = pin.x + '%';
        el.style.top = pin.y + '%';
        el.innerHTML = '<span class="pin-dot"></span><span class="pin-ripple"></span>';
        el.onclick = function(e) {
            e.stopPropagation();
            showTip(e, pin, idx);
        };
        c.appendChild(el);
    });
}

function renderAreas(pinIdx) {
    var c = document.getElementById('areasContainer');
    c.innerHTML = '';
    var step = demoData.steps[curStep];
    if (!step) return;
    var areas = null;
    if (pinIdx !== undefined && step.pins && step.pins[pinIdx] && step.pins[pinIdx].areas) {
        areas = step.pins[pinIdx].areas;
    } else if (step.areas) {
        areas = step.areas;
    }
    if (!areas) return;
    areas.forEach(function(area, idx) {
        var wrap = document.createElement('div');
        wrap.className = 'spotlight-backdrop';

        var spot = document.createElement('div');
        spot.className = 'spotlight-area';
        spot.style.left = area.x + '%';
        spot.style.top = area.y + '%';
        spot.style.width = area.width + '%';
        spot.style.height = area.height + '%';

        wrap.appendChild(spot);
        c.appendChild(wrap);
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

    renderAreas(idx);

    var wrap = document.getElementById('imageWrapper');
    var wr = wrap.getBoundingClientRect();
    var pr = e.currentTarget.getBoundingClientRect();
    var cx = pr.left - wr.left + pr.width / 2;
    var cy = pr.top - wr.top + pr.height / 2;
    var gap = 16;

    function tryPlace(hSide, vSide) {
        var fx, fy;
        if (hSide === 'right') { fx = cx + gap; pop.style.transform = 'none'; }
        else if (hSide === 'left') { fx = cx - gap; pop.style.transform = 'translateX(-100%)'; }
        else { fx = cx - pop.offsetWidth / 2; pop.style.transform = 'none'; }
        if (vSide === 'down') { fy = cy + gap; }
        else if (vSide === 'up') { fy = cy - gap; }
        else { fy = cy - pop.offsetHeight / 2; }
        pop.style.left = Math.round(fx) + 'px';
        pop.style.top = Math.round(fy) + 'px';
        pop.style.display = 'block';
        var por = pop.getBoundingClientRect();
        if (por.right > wr.right || por.left < wr.left || por.bottom > wr.bottom || por.top < wr.top) {
            pop.style.display = 'none';
            return false;
        }
        var areaEls = document.querySelectorAll('.spotlight-area');
        for (var i = 0; i < areaEls.length; i++) {
            var ar = areaEls[i].getBoundingClientRect();
            if (!(por.right < ar.left || por.left > ar.right || por.bottom < ar.top || por.top > ar.bottom)) {
                pop.style.display = 'none';
                return false;
            }
        }
        return true;
    }

    var dirH = pin.x < 50 ? 'right' : 'left';
    var dirV = pin.y < 50 ? 'down' : 'up';
    var ordersH = [dirH, 'centerH', dirH === 'right' ? 'left' : 'right'];
    var ordersV = [dirV, 'centerV', dirV === 'down' ? 'up' : 'down'];
    var gapValues = [16, 32, 48];
    var placed = false;

    for (var gi = 0; gi < gapValues.length && !placed; gi++) {
        gap = gapValues[gi];
        for (var hi = 0; hi < 3 && !placed; hi++) {
            for (var vi = 0; vi < 3 && !placed; vi++) {
                if (tryPlace(ordersH[hi], ordersV[vi])) placed = true;
            }
        }
    }

    pop.className = 'tooltip-popover';
    pop.style.display = placed ? 'block' : 'none';

    fetch('api/track_click.php', { method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({demo_id:demoId,step_index:curStep,pin_index:idx}) }).catch(function(){});
}

function autoShowFirstTip() {
    var step = demoData.steps[curStep];
    if (!step || !step.pins || currentPinIdx >= step.pins.length) return;
    var el = document.querySelectorAll('.pin-marker')[currentPinIdx];
    if (!el) return;
    showTip({ currentTarget: el }, step.pins[currentPinIdx], currentPinIdx);
}

function hideTip() {
    document.getElementById('tooltipPopover').style.display = 'none';
    document.getElementById('areasContainer').innerHTML = '';
}

function updateNavButtons() {
    var step = demoData.steps[curStep];
    document.getElementById('prevBtn').disabled = curStep === 0;
    document.getElementById('prevBtn').style.opacity = curStep === 0 ? '0.4' : '1';
    var nb = document.getElementById('nextBtn');
    nb.disabled = false;
    nb.style.opacity = '1';
    var isLastStep = curStep === demoData.steps.length - 1;
    var isLastPin = step && step.pins ? currentPinIdx >= step.pins.length - 1 : true;
    nb.textContent = isLastStep && isLastPin ? '🔄 Replay' : 'Next ➡️';
}

function nextStep() {
    var step = demoData.steps[curStep];
    if (step && step.pins && currentPinIdx < step.pins.length - 1) {
        currentPinIdx++;
        renderPins();
        hideTip();
        var el = document.querySelectorAll('.pin-marker')[currentPinIdx];
        if (el) showTip({ currentTarget: el }, step.pins[currentPinIdx], currentPinIdx);
        updateNavButtons();
    } else if (curStep < demoData.steps.length - 1) {
        loadStep(curStep + 1);
    } else {
        restartDemo();
    }
}
function prevStep() { if (curStep > 0) loadStep(curStep - 1); }
function restartDemo() { hideTip(); currentPinIdx = 0; loadStep(0); }

function startDemo() {
    document.getElementById('welcomeOverlay').classList.remove('open');
    started = true;
    document.getElementById('viewerOverlay').classList.add('hidden');
    document.getElementById('viewerFooter').style.opacity = '1';
    autoShowFirstTip();
}

function handleTooltipAction() {
    var btn = document.getElementById('tooltipAction');
    hideTip();
    if (btn.dataset.action === 'next') {
        nextStep();
    } else if (btn.dataset.action === 'previous') {
        prevStep();
    } else if (btn.dataset.action === 'url') {
        window.open(btn.dataset.url, '_blank');
    }
}

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

document.addEventListener('keydown', function(e) {
    if (e.key === 'ArrowRight' || e.key === ' ') { e.preventDefault(); nextStep(); }
    else if (e.key === 'ArrowLeft') { e.preventDefault(); prevStep(); }
    else if (e.key === 'Escape') {
        if (document.getElementById('embedModal').classList.contains('open')) closeEmbedModal();
        else hideTip();
    }
});

document.getElementById('imageWrapper').onclick = function(e) {
    if (!e.target.closest('.tooltip-popover') && !e.target.closest('.pin-marker')) {
        hideTip();
    }
};

init();
</script>
</body>
</html>
