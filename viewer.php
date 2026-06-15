<?php require_once 'config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$demo = get_demo($id);
if (!$demo) { http_response_code(404); die('Demo not found'); }
$data = $demo['data'];
$json_data = json_encode($data, JSON_UNESCAPED_UNICODE);
$title = htmlspecialchars($data['title'] ?? 'Untitled Demo');
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
.btn:hover { background: rgba(109,93,251,0.1); border-color: var(--primary); }
.btn-primary { background: var(--primary); color: #fff; border-color: var(--primary); }
.btn-primary:hover { background: var(--secondary); border-color: var(--secondary); }
.btn-ghost:hover { background: rgba(255,255,255,0.05); }
.btn-sm { padding: 6px 12px; font-size: 12px; }
.text-muted { color: var(--text-muted); }

/* Viewer layout */
.viewer-page { display: flex; align-items: center; justify-content: center; padding: 20px; }
.viewer-container { width: 100%; max-width: 1200px; margin: 0 auto; }

.viewer-header {
    display: flex; align-items: center; justify-content: space-between; padding: 16px 0; gap: 16px;
}
.viewer-header h2 { font-size: 20px; font-weight: 700; }
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
    background: var(--primary); border: 3px solid #fff;
    box-shadow: 0 2px 12px rgba(109,93,251,0.5);
    animation: pinPulse 2s ease-in-out infinite;
}
@keyframes pinPulse {
    0%, 100% { box-shadow: 0 2px 12px rgba(109,93,251,0.5); }
    50% { box-shadow: 0 2px 20px rgba(109,93,251,0.8); }
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
.tooltip-card::after {
    content: ''; position: absolute; width: 12px; height: 12px;
    background: var(--bg-card); border-left: 1px solid var(--border);
    border-top: 1px solid var(--border); transform: rotate(45deg);
}
.tooltip-bottom .tooltip-card::after {
    top: -7px; left: 50%; margin-left: -6px;
    border-left: 1px solid var(--border); border-top: 1px solid var(--border);
    border-bottom: none; border-right: none;
}
.tooltip-top .tooltip-card::after {
    bottom: -7px; left: 50%; margin-left: -6px;
    border-bottom: 1px solid var(--border); border-right: 1px solid var(--border);
    border-top: none; border-left: none;
}
.tooltip-title {
    font-size: 13px; font-weight: 700; color: var(--primary);
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
.step-dot.active { background: var(--primary); transform: scale(1.3); }
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
    background: var(--primary); border: 3px solid rgba(255,255,255,0.25);
    color: #fff; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    box-shadow: 0 0 50px rgba(109,93,251,0.45);
    transition: box-shadow 0.2s ease;
    padding: 0; line-height: 1;
}
.play-btn:hover { box-shadow: 0 0 70px rgba(109,93,251,0.7); }
.play-btn svg { animation: playPulse 2s ease-in-out infinite; }
@keyframes playPulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.18); }
}
.play-label { margin-top: 20px; color: var(--text-muted); font-size: 14px; letter-spacing: 0.5px; }
</style>
</head>
<body class="viewer-page">
<div class="viewer-container">
    <div class="viewer-header">
        <h2 id="demoTitle"><?= $title ?></h2>
        <div class="viewer-controls">
            <a href="embed.php?id=<?= $id ?>" class="btn btn-ghost btn-sm">📦 Embed</a>
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
                <button class="play-btn"><svg width="32" height="32" viewBox="0 0 24 24" fill="none"><path d="M8 5v14l11-7z" fill="currentColor"/></svg></button>
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

<script>
// PinClick Viewer Engine
var PINCLICK_DATA = <?= $json_data ?>;
var PINCLICK_ID = <?= $id ?>;
var curStep = 0, demoData = null, demoId = <?= $id ?>;

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
    }

    // Try loading with absolute URL to avoid relative path issues
    var url = step.image;
    if (url.indexOf('://') === -1) {
        url = window.location.protocol + '//' + window.location.host + '/' + url.replace(/^\//, '');
    }
    img.onload = ready;
    img.onerror = function() {
        // Retry once with the original relative URL
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
        btn.textContent = '🔗 Open Link'; btn.dataset.action = 'url'; btn.dataset.url = pin.url || '#';
    } else if (pin.action === 'previous') {
        btn.textContent = '⬅️ Previous'; btn.dataset.action = 'previous';
    } else {
        btn.textContent = 'Next ➡️'; btn.dataset.action = 'next';
    }

    var wrap = document.getElementById('imageWrapper');
    var wr = wrap.getBoundingClientRect();
    var pr = e.currentTarget.getBoundingClientRect();
    var cx = pr.left - wr.left + pr.width / 2;
    var cy = pr.top - wr.top + pr.height / 2;

    var gap = 16;
    var dirH = pin.x < 50 ? 'right' : 'left';
    var dirV = pin.y < 50 ? 'down' : 'up';
    var arrow = dirV === 'down' ? 'bottom' : 'top';
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

    if (por.bottom > wr.height - 5) { fy = cy - gap; arrow = 'bottom'; }
    else if (por.top < 5) { fy = cy + gap; arrow = 'top'; }

    pop.style.left = Math.round(fx) + 'px';
    pop.style.top = Math.round(fy) + 'px';
    pop.className = 'tooltip-popover tooltip-' + arrow;
    pop.style.display = 'block';

    fetch('api/track_click.php', { method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({demo_id:demoId,step_index:curStep,pin_index:idx}) }).catch(function(){});
}

function hideTip() { document.getElementById('tooltipPopover').style.display = 'none'; }
function nextStep() { if (curStep < demoData.steps.length - 1) loadStep(curStep + 1); }
function prevStep() { if (curStep > 0) loadStep(curStep - 1); }
function restartDemo() { hideTip(); loadStep(0); }
function startDemo() {
    document.getElementById('viewerOverlay').classList.add('hidden');
    document.getElementById('viewerFooter').style.opacity = '1';
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
    else if (e.key === 'Escape') hideTip();
});

document.getElementById('imageWrapper').onclick = function(e) {
    if (!e.target.closest('.tooltip-popover') && !e.target.closest('.pin-marker')) hideTip();
};

init();
</script>
</body>
</html>
