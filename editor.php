<?php require_once 'config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$is_new = false;

if ($id) {
    $demo = get_demo($id);
    if (!$demo) {
        $id = 0;
    }
}

if (!$id) {
    $id = create_demo();
    $demo = get_demo($id);
    $is_new = true;
}

$data = $demo['data'];
$steps = $data['steps'] ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editor - <?= htmlspecialchars($demo['title']) ?> - PinClick</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body class="editor-body">
    <nav class="navbar editor-navbar">
        <div class="nav-inner">
            <div class="nav-left">
                <a href="dashboard.php" class="nav-back" title="Back to Dashboard">←</a>
                <input type="text" class="editor-title-input" id="demoTitle" value="<?= htmlspecialchars($demo['title']) ?>" placeholder="Demo Title">
            </div>
            <div class="nav-center">
                <div class="mode-toggle" id="modeToggle">
                    <button class="mode-btn active" data-mode="pin" onclick="setMode('pin')" title="Pin mode (P)">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 2C8 2 5 5 5 9c0 5 7 13 7 13s7-8 7-13c0-4-3-7-7-7z"/>
                            <circle cx="12" cy="9" r="2.5" fill="currentColor" fill-opacity="0.2"/>
                        </svg>
                        Pin
                        <span class="mode-shortcut">P</span>
                    </button>
                    <button class="mode-btn" data-mode="area" onclick="setMode('area')" title="Area mode (A)">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="18" height="18" rx="2"/>
                            <circle cx="12" cy="12" r="2" fill="currentColor" fill-opacity="0.25"/>
                        </svg>
                        Area
                        <span class="mode-shortcut">A</span>
                    </button>
                </div>
            </div>
            <div class="nav-right">
                <span class="nav-demo-id">ID: <?= $id ?></span>
                <button class="btn btn-ghost btn-sm" onclick="saveDemo()">💾 Save</button>
                <button class="btn btn-primary btn-sm" onclick="publishDemo()">📢 Publish</button>
                <a href="viewer.php?id=<?= $id ?>" target="_blank" class="btn btn-ghost btn-sm">👁 View</a>
            </div>
        </div>
    </nav>

    <div class="editor-layout">
        <aside class="editor-sidebar">
            <div class="sidebar-section">
                <h3>Steps</h3>
                <div class="steps-list" id="stepsList">
                    <?php foreach ($steps as $i => $step): ?>
                    <div class="step-item <?= $i === 0 ? 'active' : '' ?>" data-index="<?= $i ?>" onclick="selectStep(<?= $i ?>)">
                        <div class="step-thumb">
                            <?php if (!empty($step['image'])): ?>
                            <img src="<?= htmlspecialchars($step['image']) ?>" alt="">
                            <?php else: ?>
                            <span class="step-no-image">+</span>
                            <?php endif; ?>
                        </div>
                        <div class="step-info">
                            <span class="step-label">Step <?= $i + 1 ?></span>
                            <span class="step-pins"><?php
                                $pin_areas = 0;
                                foreach (($step['pins'] ?? []) as $p) { if (isset($p['areas']) && count($p['areas']) > 0) $pin_areas++; }
                                echo count($step['pins'] ?? []) . ' pins' . ($pin_areas > 0 ? ', ' . $pin_areas . ' areas' : '');
                            ?></span>
                        </div>
                        <button class="step-delete" onclick="event.stopPropagation();deleteStep(<?= $i ?>)" title="Delete step">×</button>
                    </div>
                    <?php endforeach; ?>
                </div>
                <button class="btn btn-ghost btn-sm btn-add-step" onclick="addStep()">
                    <span class="btn-icon">+</span> Add Step
                </button>
            </div>
        </aside>

        <main class="editor-main">
            <div class="editor-canvas-wrapper" id="canvasWrapper">
                <div class="editor-canvas" id="editorCanvas" style="position:relative">
                    <img id="stepImage" src="" alt="Step screenshot" class="editor-image" style="display:none">
                    <div class="no-image-message" id="noImageMsg">
                        <div class="empty-icon">
                            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#6D5DFB" stroke-width="1">
                                <rect x="3" y="3" width="18" height="18" rx="2"/>
                                <circle cx="8.5" cy="8.5" r="1.5"/>
                                <path d="M21 15l-5-5L5 21"/>
                            </svg>
                        </div>
                        <h3>No image for this step</h3>
                        <p class="text-muted">Upload a screenshot to start adding pins</p>
                        <button class="btn btn-primary" onclick="uploadForStep()">Upload Image</button>
                    </div>
                    <div class="pins-container" id="pinsContainer"></div>
                    <div class="areas-container" id="areasContainer"></div>
                    <div class="area-preview" id="areaPreview" style="display:none"></div>
                </div>
            </div>
        </main>

        <aside class="editor-panel" id="pinEditorPanel">
            <div class="panel-section">
                <h3>Accent Color</h3>
                <div class="form-group">
                    <div class="color-picker-row">
                        <input type="color" class="color-input" id="accentColor" value="#6D5DFB" onchange="onAccentColorChange()" title="Accent color">
                        <span class="color-hex" id="colorHex">#6D5DFB</span>
                        <button class="btn btn-ghost btn-sm" onclick="resetAccentColor()" title="Reset to default">↺</button>
                    </div>
                </div>
            </div>
            <div class="panel-section">
                <h3>Pin Editor</h3>
                <div id="pinEditorContent">
                    <p class="text-muted">Click on the image to create a pin</p>
                </div>
            </div>
            <div class="panel-section" id="stepSettings">
                <h3>Step Settings</h3>
                <div class="form-group">
                    <label>Current Image</label>
                    <div class="current-image-info" id="currentImageInfo">
                        <span class="text-muted">No image selected</span>
                    </div>
                    <button class="btn btn-ghost btn-sm" onclick="uploadForStep()">Change Image</button>
                </div>
            </div>
        </aside>
    </div>

    <div class="file-input-hidden">
        <input type="file" id="stepFileInput" accept="image/png,image/jpeg,image/webp" hidden>
    </div>

    <script>
    const DEMO_ID = <?= $id ?>;
    let demoData = <?= json_encode($data, JSON_UNESCAPED_UNICODE) ?>;
    let currentStep = 0;
    let selectedPin = null;
    let isDragging = false;
    let dragPinIndex = null;
    let dragOffset = { x: 0, y: 0 };

    let currentMode = 'pin';
    let isDrawing = false;
    let drawStart = { x: 0, y: 0 };
    let selectedArea = null;
    let isAreaDragging = false;
    let dragAreaIdx = null;
    let areaDragStartX = 0;
    let areaDragStartY = 0;
    let areaOrigX = 0;
    let areaOrigY = 0;
    let areaDragMoved = false;
    let undoStack = [];
    function getPinAreas() {
        var step = demoData.steps ? demoData.steps[currentStep] : null;
        if (!step) return null;
        if (selectedPin === null || selectedPin === undefined) return null;
        var pin = step.pins ? step.pins[selectedPin] : null;
        return pin ? (pin.areas || null) : null;
    }

    let isResizing = false;
    let resizeAreaIdx = null;
    let resizeHandle = null;
    let resizeStartX = 0;
    let resizeStartY = 0;
    let resizeOrig = { x: 0, y: 0, width: 0, height: 0 };

    function setMode(mode) {
        isResizing = false; resizeAreaIdx = null;
        isAreaDragging = false;
        dragAreaIdx = null;
        currentMode = mode;
        document.querySelectorAll('.mode-btn').forEach(function(b) {
            b.classList.toggle('active', b.dataset.mode === mode);
        });
        var canvas = document.getElementById('editorCanvas');
        canvas.style.cursor = mode === 'area' ? 'crosshair' : 'default';
        selectedArea = null;
        document.querySelectorAll('.area-box').forEach(function(el) { el.classList.remove('selected'); });
        if (selectedPin !== null && selectedPin !== undefined) {
            document.querySelectorAll('.pin-marker').forEach(function(el) { el.classList.remove('selected'); });
            var pinEl = document.querySelector('.pin-marker[data-index="' + selectedPin + '"]');
            if (pinEl) pinEl.classList.add('selected');
        }
        if (mode === 'area') {
            updateAreaEditor(null);
        } else {
            updatePinEditor(selectedPin);
        }
        renderAreas();
    }

    function init() {
        if (demoData.steps && demoData.steps.length > 0) {
            loadStep(0);
        } else {
            showNoImage();
        }
        document.getElementById('demoTitle').addEventListener('change', function() {
            demoData.title = this.value;
        });
        initAccentColor();

        document.addEventListener('keydown', function(e) {
            if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') return;
            if (e.key === 'p' || e.key === 'P') { setMode('pin'); e.preventDefault(); }
            if (e.key === 'a' || e.key === 'A') { setMode('area'); e.preventDefault(); }
            if (e.key === 'Escape') {
                isResizing = false; resizeAreaIdx = null;
        isResizing = false; resizeAreaIdx = null;
        isAreaDragging = false;
        dragAreaIdx = null;
                selectedArea = null; selectedPin = null;
                document.querySelectorAll('.area-box').forEach(function(el) { el.classList.remove('selected'); });
                document.querySelectorAll('.pin-marker').forEach(function(el) { el.classList.remove('selected'); });
                updateAreaEditor(null); updatePinEditor(null);
                renderAreas();
            }
            if ((e.key === 'Delete' || e.key === 'Backspace') && selectedArea !== null) {
                e.preventDefault();
                deleteArea(selectedArea);
            } else if ((e.key === 'Delete' || e.key === 'Backspace') && selectedPin !== null && selectedPin !== undefined) {
                e.preventDefault();
                deletePin(selectedPin);
            }
            if ((e.metaKey || e.ctrlKey) && e.key === 'z') {
                e.preventDefault();
                undoLastAction();
            }
        });
    }

    function initAccentColor() {
        var color = demoData.accent_color || '#6D5DFB';
        document.getElementById('accentColor').value = color;
        document.getElementById('colorHex').textContent = color;
        applyAccentColor(color);
    }

    function onAccentColorChange() {
        var color = document.getElementById('accentColor').value;
        demoData.accent_color = color;
        document.getElementById('colorHex').textContent = color;
        applyAccentColor(color);
        saveDemo();
    }

    function resetAccentColor() {
        demoData.accent_color = '#6D5DFB';
        document.getElementById('accentColor').value = '#6D5DFB';
        document.getElementById('colorHex').textContent = '#6D5DFB';
        applyAccentColor('#6D5DFB');
        saveDemo();
    }

    function applyAccentColor(color) {
        var r = parseInt(color.slice(1,3), 16);
        var g = parseInt(color.slice(3,5), 16);
        var b = parseInt(color.slice(5,7), 16);
        document.documentElement.style.setProperty('--accent-color', color);
        document.documentElement.style.setProperty('--accent-shadow', 'rgba(' + r + ',' + g + ',' + b + ',0.5)');
        document.documentElement.style.setProperty('--accent-glow', 'rgba(' + r + ',' + g + ',' + b + ',0.9)');
        renderPins();
        renderAreas();
    }

    function loadStep(index) {
        currentStep = index;
        selectedPin = null;
        isAreaDragging = false;
        dragAreaIdx = null;
        const steps = demoData.steps || [];
        if (!steps[index]) return;

        const step = steps[index];
        const img = document.getElementById('stepImage');

        document.querySelectorAll('.step-item').forEach(el => el.classList.remove('active'));
        const stepEl = document.querySelector(`.step-item[data-index="${index}"]`);
        if (stepEl) stepEl.classList.add('active');

        if (step.image) {
            img.src = step.image;
            img.style.display = 'block';
            document.getElementById('noImageMsg').style.display = 'none';
            document.getElementById('currentImageInfo').innerHTML = `<span>${step.image}</span>`;
            img.onload = function() {
                syncPinsContainer();
                renderPins();
                renderAreas();
            };
            if (img.complete) {
                syncPinsContainer();
                renderPins();
                renderAreas();
            }
        } else {
            showNoImage();
        }

        updatePinEditor(null);
    }

    function showNoImage() {
        document.getElementById('stepImage').style.display = 'none';
        document.getElementById('noImageMsg').style.display = 'flex';
        const c = document.getElementById('pinsContainer');
        c.innerHTML = '';
        c.style.left = c.style.top = c.style.width = c.style.height = '';
        document.getElementById('areasContainer').innerHTML = '';
        document.getElementById('currentImageInfo').innerHTML = `<span class="text-muted">No image selected</span>`;
    }

    function syncPinsContainer() {
        const img = document.getElementById('stepImage');
        const container = document.getElementById('pinsContainer');
        const imgRect = img.getBoundingClientRect();
        const canvasRect = document.getElementById('editorCanvas').getBoundingClientRect();
        container.style.left = (imgRect.left - canvasRect.left) + 'px';
        container.style.top = (imgRect.top - canvasRect.top) + 'px';
        container.style.width = imgRect.width + 'px';
        container.style.height = imgRect.height + 'px';
    }

    function renderPins() {
        const container = document.getElementById('pinsContainer');
        container.innerHTML = '';
        const steps = demoData.steps || [];
        const step = steps[currentStep];
        if (!step || !step.pins) return;

        step.pins.forEach((pin, idx) => {
            const pinEl = document.createElement('div');
            pinEl.className = 'pin-marker' + (selectedPin === idx ? ' selected' : '');
            pinEl.style.left = pin.x + '%';
            pinEl.style.top = pin.y + '%';
            pinEl.dataset.index = idx;
            pinEl.innerHTML = '<span class="pin-dot"></span><span class="pin-number">' + (idx + 1) + '</span>';

            pinEl.addEventListener('mousedown', (e) => {
                e.preventDefault();
                e.stopPropagation();
                selectPin(idx);
                startDrag(e, idx);
            });

            pinEl.addEventListener('click', (e) => {
                e.stopPropagation();
                selectPin(idx);
            });

            container.appendChild(pinEl);
        });
    }

    function selectPin(index) {
        selectedPin = index;
        selectedArea = null;
        document.querySelectorAll('.pin-marker').forEach(el => el.classList.remove('selected'));
        const pinEl = document.querySelector(`.pin-marker[data-index="${index}"]`);
        if (pinEl) pinEl.classList.add('selected');
        updatePinEditor(index);
        renderAreas();
    }

    function updatePinEditor(index) {
        const content = document.getElementById('pinEditorContent');
        if (index === null || index === undefined) {
            const steps = demoData.steps || [];
            const step = steps[currentStep];
            if (step && step.pins && step.pins.length > 0) {
                content.innerHTML = `<p class="text-muted">Click a pin to edit</p>`;
            } else {
                content.innerHTML = `<p class="text-muted">Click on the image to create a pin</p>`;
            }
            return;
        }

        const steps = demoData.steps || [];
        const step = steps[currentStep];
        if (!step || !step.pins[index]) return;

        const pin = step.pins[index];

        content.innerHTML = `
            <div class="form-group">
                <label>Title</label>
                <input type="text" class="form-input" id="pinTitle" value="${escapeHtml(pin.title || '')}" placeholder="Pin title">
            </div>
            <div class="form-group">
                <label>Tooltip Text</label>
                <textarea class="form-input pin-textarea" id="pinText" rows="3">${escapeHtml(pin.text || '')}</textarea>
            </div>
            <div class="form-group">
                <label>Action</label>
                <select class="form-input" id="pinAction" onchange="onPinActionChange()">
                    <option value="next" ${pin.action === 'next' ? 'selected' : ''}>Next Step</option>
                    <option value="previous" ${pin.action === 'previous' ? 'selected' : ''}>Previous Step</option>
                    <option value="url" ${pin.action === 'url' ? 'selected' : ''}>External URL</option>
                </select>
            </div>
            <div class="form-group" id="pinUrlGroup" style="${pin.action === 'url' ? '' : 'display:none'}">
                <label>URL</label>
                <input type="url" class="form-input" id="pinUrl" value="${escapeHtml(pin.url || '')}" placeholder="https://">
            </div>
            <div class="form-group">
                <label>Position</label>
                <div class="position-info">
                    <span>X: ${pin.x.toFixed(1)}%</span>
                    <span>Y: ${pin.y.toFixed(1)}%</span>
                </div>
            </div>
            <div class="pin-actions">
                <button class="btn btn-sm btn-ghost" onclick="savePinChanges()">💾 Save Pin</button>
                <button class="btn btn-sm btn-danger" onclick="deletePin(${index})">🗑 Delete</button>
            </div>
        `;
    }

    function onPinActionChange() {
        const action = document.getElementById('pinAction').value;
        document.getElementById('pinUrlGroup').style.display = action === 'url' ? '' : 'none';
    }

    function savePinChanges() {
        const steps = demoData.steps || [];
        const step = steps[currentStep];
        if (!step || !step.pins || !step.pins[selectedPin]) return;

        const pin = step.pins[selectedPin];
        pin.title = document.getElementById('pinTitle').value;
        pin.text = document.getElementById('pinText').value;
        pin.action = document.getElementById('pinAction').value;
        if (pin.action === 'url') {
            pin.url = document.getElementById('pinUrl').value;
        } else {
            delete pin.url;
        }

        saveDemo().then(() => {
            showToast('Pin saved', 'success');
        });
    }

    function deletePin(index) {
        if (!confirm('Delete this pin?')) return;
        const steps = demoData.steps || [];
        const step = steps[currentStep];
        if (!step || !step.pins) return;

        step.pins.splice(index, 1);
        selectedPin = null;
        renderPins();
        renderAreas();
        updatePinEditor(null);
        saveDemo();
    }

    function startDrag(e, index) {
        isDragging = true;
        dragPinIndex = index;
        const rect = document.getElementById('editorCanvas').getBoundingClientRect();
        dragOffset = {
            x: e.clientX - rect.left,
            y: e.clientY - rect.top
        };
        document.addEventListener('mousemove', onDrag);
        document.addEventListener('mouseup', endDrag);
    }

    function onDrag(e) {
        if (!isDragging) return;
        const canvas = document.getElementById('editorCanvas');
        const rect = canvas.getBoundingClientRect();
        const x = ((e.clientX - rect.left) / rect.width) * 100;
        const y = ((e.clientY - rect.top) / rect.height) * 100;

        const steps = demoData.steps || [];
        const step = steps[currentStep];
        if (!step || !step.pins || !step.pins[dragPinIndex]) return;

        step.pins[dragPinIndex].x = Math.max(0, Math.min(100, x));
        step.pins[dragPinIndex].y = Math.max(0, Math.min(100, y));

        if (selectedPin === dragPinIndex) {
            updatePinEditor(dragPinIndex);
        }
        renderPins();
    }

    function endDrag() {
        if (isDragging) {
            isDragging = false;
            document.removeEventListener('mousemove', onDrag);
            document.removeEventListener('mouseup', endDrag);
            if (dragPinIndex !== null) {
                const steps = demoData.steps || [];
                const step = steps[currentStep];
                if (step && step.pins && step.pins[dragPinIndex]) {
                    const pin = step.pins[dragPinIndex];
                    autoSavePin(pin);
                }
            }
            dragPinIndex = null;
        }
    }

    function autoSavePin(pin) {
        fetch('api/save_demo.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                id: DEMO_ID,
                title: demoData.title,
                steps: demoData.steps
            })
        }).catch(() => {});
    }

    /* Area Drawing */
    document.getElementById('editorCanvas').addEventListener('mousedown', function(e) {
        if (currentMode !== 'area') return;
        if (e.button !== 0) return;
        if (e.target.closest('.pin-marker') || e.target.closest('.area-box')) return;
        var steps = demoData.steps || [];
        var step = steps[currentStep];
        if (!step || !step.image) return;
        if (selectedPin === null || selectedPin === undefined) {
            showToast('Select a pin first to add areas', 'error');
            return;
        }
        if (step.pins[selectedPin] && step.pins[selectedPin].areas && step.pins[selectedPin].areas.length > 0) {
            showToast('This pin already has an area', 'error');
            return;
        }

        isDrawing = true;
        var rect = this.getBoundingClientRect();
        drawStart = {
            x: ((e.clientX - rect.left) / rect.width) * 100,
            y: ((e.clientY - rect.top) / rect.height) * 100
        };

    document.querySelectorAll('.area-box').forEach(function(a) { a.classList.remove('selected'); });
    var prev = document.getElementById('areaPreview');
    prev.style.display = 'block';
    prev.style.left = drawStart.x + '%';
    prev.style.top = drawStart.y + '%';
    prev.style.width = '0px';
    prev.style.height = '0px';
    prev.className = 'area-box drawing';
    e.preventDefault();
    });

    document.addEventListener('mousemove', function(e) {
        if (isResizing) {
            var canvas = document.getElementById('editorCanvas');
            var rect = canvas.getBoundingClientRect();
            var dx = ((e.clientX - resizeStartX) / rect.width) * 100;
            var dy = ((e.clientY - resizeStartY) / rect.height) * 100;

            var steps = demoData.steps || [];
            var step = steps[currentStep];
            var areas = getPinAreas();
            if (!areas || !areas[resizeAreaIdx]) return;

            var area = areas[resizeAreaIdx];
            var o = resizeOrig;
            var minSize = 3;

            if (resizeHandle === 'se') {
                area.width = Math.max(minSize, o.width + dx);
                area.height = Math.max(minSize, o.height + dy);
            } else if (resizeHandle === 'sw') {
                area.x = o.x + dx;
                area.width = Math.max(minSize, o.width - dx);
                area.height = Math.max(minSize, o.height + dy);
            } else if (resizeHandle === 'ne') {
                area.y = o.y + dy;
                area.width = Math.max(minSize, o.width + dx);
                area.height = Math.max(minSize, o.height - dy);
            } else if (resizeHandle === 'nw') {
                area.x = o.x + dx;
                area.y = o.y + dy;
                area.width = Math.max(minSize, o.width - dx);
                area.height = Math.max(minSize, o.height - dy);
            }

            area.width = Math.min(100 - area.x, area.width);
            area.height = Math.min(100 - area.y, area.height);
            if (area.width < minSize) area.width = minSize;
            if (area.height < minSize) area.height = minSize;

            renderAreas();
            if (selectedArea === resizeAreaIdx) {
                updateAreaEditor(resizeAreaIdx);
            }
            return;
        }
        if (isAreaDragging) {
            var canvas = document.getElementById('editorCanvas');
            var rect = canvas.getBoundingClientRect();
            var dx = ((e.clientX - areaDragStartX) / rect.width) * 100;
            var dy = ((e.clientY - areaDragStartY) / rect.height) * 100;

            var steps = demoData.steps || [];
            var step = steps[currentStep];
            var areas = getPinAreas();
            if (!areas || !areas[dragAreaIdx]) return;

            var area = areas[dragAreaIdx];
            area.x = Math.max(0, Math.min(100 - area.width, areaOrigX + dx));
            area.y = Math.max(0, Math.min(100 - area.height, areaOrigY + dy));

            if (Math.abs(e.clientX - areaDragStartX) > 3 || Math.abs(e.clientY - areaDragStartY) > 3) {
                areaDragMoved = true;
            }

            renderAreas();
            if (selectedArea === dragAreaIdx) {
                updateAreaEditor(dragAreaIdx);
            }
            return;
        }
        if (!isDrawing) return;
        var canvas = document.getElementById('editorCanvas');
        var rect = canvas.getBoundingClientRect();
        var x = ((e.clientX - rect.left) / rect.width) * 100;
        var y = ((e.clientY - rect.top) / rect.height) * 100;

        var sx = Math.min(drawStart.x, x);
        var sy = Math.min(drawStart.y, y);
        var sw = Math.abs(x - drawStart.x);
        var sh = Math.abs(y - drawStart.y);

        var prev = document.getElementById('areaPreview');
        prev.style.left = sx + '%';
        prev.style.top = sy + '%';
        prev.style.width = sw + '%';
        prev.style.height = sh + '%';
    });

    document.addEventListener('mouseup', function(e) {
        if (isResizing) {
            isResizing = false;
            if (resizeAreaIdx !== null) {
                var steps = demoData.steps || [];
                var step = steps[currentStep];
                var areas = getPinAreas();
                if (areas && areas[resizeAreaIdx]) {
                    saveDemo();
                }
            }
            resizeAreaIdx = null;
            return;
        }
        if (isAreaDragging) {
            isAreaDragging = false;
            if (dragAreaIdx !== null && areaDragMoved) {
                var steps = demoData.steps || [];
                var step = steps[currentStep];
                var areas = getPinAreas();
                if (areas && areas[dragAreaIdx]) {
                    saveDemo();
                }
            }
            areaDragMoved = false;
            document.querySelectorAll('.area-box').forEach(function(a) { a.style.cursor = ''; });
            dragAreaIdx = null;
            return;
        }
        if (!isDrawing) return;
        isDrawing = false;
        var prev = document.getElementById('areaPreview');
        prev.style.display = 'none';

        var canvas = document.getElementById('editorCanvas');
        var rect = canvas.getBoundingClientRect();
        var x = ((e.clientX - rect.left) / rect.width) * 100;
        var y = ((e.clientY - rect.top) / rect.height) * 100;

        var sx = Math.min(drawStart.x, x);
        var sy = Math.min(drawStart.y, y);
        var sw = Math.abs(x - drawStart.x);
        var sh = Math.abs(y - drawStart.y);

        var minSize = 1;
        if (sw < minSize || sh < minSize) return;

        var steps = demoData.steps || [];
        var step = steps[currentStep];
        if (!step) return;
        if (selectedPin === null || selectedPin === undefined) {
            showToast('Select a pin first', 'error');
            return;
        }
        var pin = step.pins[selectedPin];
        if (!pin) return;
        if (!pin.areas) pin.areas = [];

        var newArea = {
            x: Math.max(0, Math.min(100, sx)),
            y: Math.max(0, Math.min(100, sy)),
            width: Math.max(minSize, Math.min(100 - sx, sw)),
            height: Math.max(minSize, Math.min(100 - sy, sh))
        };

        var replaced = pin.areas.length > 0;
        pin.areas = [newArea];
        renderAreas();
        saveDemo();
        showToast(replaced ? 'Area replaced!' : 'Area created!', 'success');
    });

    function startResize(e, idx, handle) {
        if (e.button !== 0) return;
        e.stopPropagation();
        e.preventDefault();
        var steps = demoData.steps || [];
        var step = steps[currentStep];
        var areas = getPinAreas();
        if (!areas || !areas[idx]) return;
        var area = areas[idx];
        isResizing = true;
        resizeAreaIdx = idx;
        resizeHandle = handle;
        resizeStartX = e.clientX;
        resizeStartY = e.clientY;
        resizeOrig = { x: area.x, y: area.y, width: area.width, height: area.height };
    }

    function renderAreas() {
        var container = document.getElementById('areasContainer');
        container.innerHTML = '';
        var steps = demoData.steps || [];
        var step = steps[currentStep];
        if (!step) return;

        (step.pins || []).forEach(function(p, pi) {
            if (!p || !p.areas) return;
            var isSelectedPin = (selectedPin === pi);
            p.areas.forEach(function(area, ai) {
                var el = makeAreaEl(area);

                if (isSelectedPin) {
                    el.classList.add('area-draggable');
                    el.addEventListener('mousedown', function(e) {
                        if (e.button !== 0) return;
                        if (e.target.classList.contains('area-handle')) return;
                        e.stopPropagation();
                        isAreaDragging = true;
                        dragAreaIdx = ai;
                        areaDragStartX = e.clientX;
                        areaDragStartY = e.clientY;
                        areaOrigX = area.x;
                        areaOrigY = area.y;
                        areaDragMoved = false;
                        el.style.cursor = 'grabbing';
                    });
                    el.addEventListener('click', function(e) {
                        if (areaDragMoved) { areaDragMoved = false; el.style.cursor = ''; return; }
                        if (e.target.classList.contains('area-handle')) return;
                        e.stopPropagation();
                        selectedArea = ai;
                        document.querySelectorAll('.area-box').forEach(function(a) { a.classList.remove('selected'); });
                        el.classList.add('selected');
                        updateAreaEditor(ai);
                    });
                    if (selectedArea === ai) el.classList.add('selected');
                    ['nw', 'ne', 'sw', 'se'].forEach(function(h) {
                        var hEl = document.createElement('div');
                        hEl.className = 'area-handle area-handle-' + h;
                        hEl.addEventListener('mousedown', function(e) {
                            e.stopPropagation();
                            e.preventDefault();
                            startResize(e, ai, h);
                        });
                        el.appendChild(hEl);
                    });
                } else {
                    el.style.cursor = 'pointer';
                    el.addEventListener('click', function(e) {
                        if (e.target.classList.contains('area-handle')) {
                            selectPin(pi);
                            return;
                        }
                        e.stopPropagation();
                        selectPin(pi);
                    });
                }

                container.appendChild(el);
            });
        });
    }

    function makeAreaEl(area) {
        var el = document.createElement('div');
        el.className = 'area-box';
        el.style.left = area.x + '%';
        el.style.top = area.y + '%';
        el.style.width = area.width + '%';
        el.style.height = area.height + '%';
        return el;
    }

    function updateAreaEditor(index) {
        var content = document.getElementById('pinEditorContent');
        if (index === null || index === undefined) {
            var areas = getPinAreas();
            if (areas && areas.length > 0) {
                content.innerHTML = '<p class="text-muted">Click an area to edit</p>';
            } else if (currentMode === 'area') {
                if (selectedPin === null || selectedPin === undefined) {
                    content.innerHTML = '<p class="text-muted">Select a pin first, then drag on the image to create an area</p>';
                } else {
                    content.innerHTML = '<p class="text-muted">Drag on the image to create an area</p>';
                }
            } else {
                content.innerHTML = '<p class="text-muted">Click on the image to create a pin</p>';
            }
            return;
        }

        var areas = getPinAreas();
        if (!areas || !areas[index]) return;

        var area = areas[index];

        content.innerHTML = `
            <div class="form-group">
                <label>Area Position</label>
                <div class="position-info">
                    <span>X: ${area.x.toFixed(1)}%</span>
                    <span>Y: ${area.y.toFixed(1)}%</span>
                    <span>W: ${area.width.toFixed(1)}%</span>
                    <span>H: ${area.height.toFixed(1)}%</span>
                </div>
            </div>
            <div class="pin-actions">
                <button class="btn btn-sm btn-danger" onclick="deleteArea(${index})">🗑 Delete Area</button>
            </div>
        `;
    }

    function deleteArea(index) {
        if (!confirm('Delete this area?')) return;
        var areas = getPinAreas();
        if (!areas) return;

        areas.splice(index, 1);
        selectedArea = null;
        renderAreas();
        updateAreaEditor(null);
        saveDemo();
    }

    function undoLastAction() {
        if (undoStack.length === 0) return;
        var action = undoStack.pop();
        if (action.type === 'addPin') {
            var step = demoData.steps[action.stepIndex];
            if (step && step.pins) {
                step.pins.splice(action.pinIndex, 1);
                if (selectedPin === action.pinIndex) {
                    selectedPin = null;
                } else if (selectedPin !== null && selectedPin > action.pinIndex) {
                    selectedPin--;
                }
                renderPins();
                renderAreas();
                updatePinEditor(selectedPin);
                saveDemo();
                showToast('Undo: pin removed', 'info');
            }
        }
    }

    window.addEventListener('resize', function() {
        const img = document.getElementById('stepImage');
        if (img.style.display !== 'none') syncPinsContainer();
    });

    document.getElementById('editorCanvas').addEventListener('click', function(e) {
        if (isDragging) return;
        if (areaDragMoved) { areaDragMoved = false; return; }
        if (e.target.closest('.pin-marker') || e.target.closest('.area-box')) return;
        if (currentMode !== 'pin') {
            selectedArea = null;
            document.querySelectorAll('.area-box').forEach(function(a) { a.classList.remove('selected'); });
            updateAreaEditor(null);
            return;
        }
        const steps = demoData.steps || [];
        const step = steps[currentStep];
        if (!step || !step.image) return;

        const img = document.getElementById('stepImage');
        const imgRect = img.getBoundingClientRect();
        if (e.clientX < imgRect.left || e.clientX > imgRect.right || e.clientY < imgRect.top || e.clientY > imgRect.bottom) return;

        const x = ((e.clientX - imgRect.left) / imgRect.width) * 100;
        const y = ((e.clientY - imgRect.top) / imgRect.height) * 100;

        if (!step.pins) step.pins = [];

        const newPin = {
            x: Math.round(x * 100) / 100,
            y: Math.round(y * 100) / 100,
            title: 'Step ' + (step.pins.length + 1),
            text: 'Click here',
            action: 'next',
            areas: []
        };

        step.pins.push(newPin);
        undoStack.push({type: 'addPin', stepIndex: currentStep, pinIndex: step.pins.length - 1});
        renderPins();
        selectPin(step.pins.length - 1);
        saveDemo();
        showToast('Pin created!', 'success');
    });

    function selectStep(index) {
        const steps = demoData.steps || [];
        if (steps[index]) {
            loadStep(index);
        }
    }

    function addStep() {
        if (!demoData.steps) demoData.steps = [];
        demoData.steps.push({ image: '', pins: [] });
        renderStepsList();
        const newIndex = demoData.steps.length - 1;
        loadStep(newIndex);
        saveDemo();
        showToast('Step added', 'success');
    }

    function deleteStep(index) {
        const steps = demoData.steps || [];
        if (steps.length <= 1) {
            showToast('Cannot delete the only step', 'error');
            return;
        }
        if (!confirm('Delete this step?')) return;
        steps.splice(index, 1);
        renderStepsList();
        if (currentStep >= steps.length) currentStep = steps.length - 1;
        if (currentStep < 0) currentStep = 0;
        if (steps.length > 0) {
            loadStep(currentStep);
        } else {
            showNoImage();
        }
        saveDemo();
        showToast('Step deleted', 'success');
    }

    function countAreas(step) {
        var n = 0;
        (step.pins || []).forEach(function(p) { if (p.areas && p.areas.length > 0) n++; });
        return n;
    }

    function renderStepsList() {
        const list = document.getElementById('stepsList');
        const steps = demoData.steps || [];
        list.innerHTML = steps.map((step, i) => {
            var ac = countAreas(step);
            var extra = (step.pins || []).length + ' pins' + (ac > 0 ? ', ' + ac + ' areas' : '');
            return '<div class="step-item ' + (i === currentStep ? 'active' : '') + '" data-index="' + i + '" onclick="selectStep(' + i + ')">' +
                '<div class="step-thumb">' +
                (step.image ? '<img src="' + escapeHtml(step.image) + '" alt="">' : '<span class="step-no-image">+</span>') +
                '</div>' +
                '<div class="step-info">' +
                '<span class="step-label">Step ' + (i + 1) + '</span>' +
                '<span class="step-pins">' + extra + '</span>' +
                '</div>' +
                '<button class="step-delete" onclick="event.stopPropagation();deleteStep(' + i + ')" title="Delete step">×</button>' +
                '</div>';
        }).join('');
    }

    function uploadForStep() {
        document.getElementById('stepFileInput').click();
    }

    document.getElementById('stepFileInput').addEventListener('change', async function(e) {
        const file = e.target.files[0];
        if (!file) return;

        if (!['image/png', 'image/jpeg', 'image/webp'].includes(file.type)) {
            showToast('Only PNG, JPG, WEBP allowed', 'error');
            return;
        }
        if (file.size > 10 * 1024 * 1024) {
            showToast('File too large (max 10MB)', 'error');
            return;
        }

        const formData = new FormData();
        formData.append('file', file);

        try {
            const res = await fetch('api/upload.php', { method: 'POST', body: formData });
            const data = await res.json();
            if (data.success) {
                const steps = demoData.steps || [];
                if (!steps[currentStep]) {
                    steps[currentStep] = { image: '', pins: [] };
                }
                steps[currentStep].image = data.filepath;
                await saveDemo();
                loadStep(currentStep);
                renderStepsList();
                showToast('Image uploaded', 'success');
            } else {
                showToast(data.error || 'Upload failed', 'error');
            }
        } catch (err) {
            showToast('Upload failed', 'error');
        }

        e.target.value = '';
    });

    async function saveDemo() {
        try {
            const res = await fetch('api/save_demo.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    id: DEMO_ID,
                    title: document.getElementById('demoTitle').value,
                    steps: demoData.steps,
                    accent_color: demoData.accent_color
                })
            });
            const data = await res.json();
            if (data.success) {
                showToast('Saved!', 'success');
                return true;
            }
        } catch (e) {
            showToast('Save failed', 'error');
            return false;
        }
    }

    async function publishDemo() {
        const steps = demoData.steps || [];
        if (steps.length === 0) {
            showToast('Add at least one step', 'error');
            return;
        }
        for (const s of steps) {
            if (!s.image) {
                showToast('All steps need an image', 'error');
                return;
            }
        }
        try {
            await saveDemo();
            const res = await fetch('api/save_demo.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    id: DEMO_ID,
                    title: document.getElementById('demoTitle').value,
                    steps: demoData.steps,
                    status: 'published',
                    accent_color: demoData.accent_color
                })
            });
            const data = await res.json();
            if (data.success) {
                showToast('Published! Share the viewer link.', 'success');
            }
        } catch (e) {
            showToast('Publish failed', 'error');
        }
    }

    function escapeHtml(str) {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    function showToast(msg, type) {
        const t = document.createElement('div');
        t.className = 'toast toast-' + type;
        t.textContent = msg;
        document.body.appendChild(t);
        setTimeout(() => t.classList.add('show'), 10);
        setTimeout(() => { t.classList.remove('show'); setTimeout(() => t.remove(), 300); }, 3000);
    }

    init();
    </script>
</body>
</html>
