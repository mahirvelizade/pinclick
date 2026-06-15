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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body class="editor-body">
    <nav class="navbar editor-navbar">
        <div class="nav-inner">
            <div class="nav-left">
                <a href="dashboard.php" class="nav-back" title="Back to Dashboard">←</a>
                <input type="text" class="editor-title-input" id="demoTitle" value="<?= htmlspecialchars($demo['title']) ?>" placeholder="Demo Title">
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
                            <span class="step-pins"><?= count($step['pins'] ?? []) ?> pins</span>
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
                <div class="editor-canvas" id="editorCanvas">
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
                </div>
            </div>
        </main>

        <aside class="editor-panel" id="pinEditorPanel">
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

    function init() {
        if (demoData.steps && demoData.steps.length > 0) {
            loadStep(0);
        } else {
            showNoImage();
        }
        document.getElementById('demoTitle').addEventListener('change', function() {
            demoData.title = this.value;
        });
    }

    function loadStep(index) {
        currentStep = index;
        selectedPin = null;
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
                renderPins();
            };
            if (img.complete) {
                renderPins();
            }
        } else {
            showNoImage();
        }

        updatePinEditor(null);
    }

    function showNoImage() {
        document.getElementById('stepImage').style.display = 'none';
        document.getElementById('noImageMsg').style.display = 'flex';
        document.getElementById('pinsContainer').innerHTML = '';
        document.getElementById('currentImageInfo').innerHTML = `<span class="text-muted">No image selected</span>`;
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
            pinEl.innerHTML = `<span class="pin-dot"></span><span class="pin-number">${idx + 1}</span>`;

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
        document.querySelectorAll('.pin-marker').forEach(el => el.classList.remove('selected'));
        const pinEl = document.querySelector(`.pin-marker[data-index="${index}"]`);
        if (pinEl) pinEl.classList.add('selected');
        updatePinEditor(index);
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

    document.getElementById('editorCanvas').addEventListener('click', function(e) {
        if (isDragging) return;
        const steps = demoData.steps || [];
        const step = steps[currentStep];
        if (!step || !step.image) return;

        if (e.target.closest('.pin-marker')) return;

        const rect = this.getBoundingClientRect();
        const x = ((e.clientX - rect.left) / rect.width) * 100;
        const y = ((e.clientY - rect.top) / rect.height) * 100;

        if (!step.pins) step.pins = [];

        const newPin = {
            x: Math.max(0, Math.min(100, x)),
            y: Math.max(0, Math.min(100, y)),
            title: 'Step ' + (step.pins.length + 1),
            text: 'Click here',
            action: 'next'
        };

        step.pins.push(newPin);
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

    function renderStepsList() {
        const list = document.getElementById('stepsList');
        const steps = demoData.steps || [];
        list.innerHTML = steps.map((step, i) => `
            <div class="step-item ${i === currentStep ? 'active' : ''}" data-index="${i}" onclick="selectStep(${i})">
                <div class="step-thumb">
                    ${step.image ? `<img src="${escapeHtml(step.image)}" alt="">` : '<span class="step-no-image">+</span>'}
                </div>
                <div class="step-info">
                    <span class="step-label">Step ${i + 1}</span>
                    <span class="step-pins">${(step.pins || []).length} pins</span>
                </div>
                <button class="step-delete" onclick="event.stopPropagation();deleteStep(${i})" title="Delete step">×</button>
            </div>
        `).join('');
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
                    steps: demoData.steps
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
                    status: 'published'
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
