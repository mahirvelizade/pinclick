/* PinClick - Viewer Engine */

let currentStepIndex = 0;
let demoData = null;
let demoId = null;

function initViewer(data) {
    demoData = data;
    document.getElementById('demoTitle').textContent = data.title || 'Untitled Demo';

    if (!data.steps || data.steps.length === 0) {
        document.getElementById('viewerStage').innerHTML = '<div class="viewer-canvas" style="padding:80px;text-align:center"><p class="text-muted">No steps in this demo</p></div>';
        return;
    }

    renderStepIndicators();
    loadViewerStep(0);

    if (window.DEMO_ID) {
        demoId = window.DEMO_ID;
        trackView();
    }
}

function renderStepIndicators() {
    const container = document.getElementById('stepIndicators');
    container.innerHTML = '';
    demoData.steps.forEach((_, i) => {
        const dot = document.createElement('div');
        dot.className = 'step-dot' + (i === currentStepIndex ? ' active' : '');
        dot.addEventListener('click', () => goToStep(i));
        container.appendChild(dot);
    });
}

function loadViewerStep(index) {
    const step = demoData.steps[index];
    if (!step) return;

    currentStepIndex = index;
    const img = document.getElementById('viewerImage');
    img.src = step.image;
    img.style.display = 'block';

    hideTooltip();

    function showPins() {
        setTimeout(renderViewerPins, 50);
    }

    img.onload = showPins;
    if (img.complete) {
        showPins();
    } else {
        img.addEventListener('load', showPins, { once: true });
    }

    document.getElementById('prevBtn').disabled = index === 0;
    document.getElementById('prevBtn').style.opacity = index === 0 ? '0.4' : '1';

    const nextBtn = document.getElementById('nextBtn');
    nextBtn.disabled = index === demoData.steps.length - 1;
    nextBtn.style.opacity = index === demoData.steps.length - 1 ? '0.4' : '1';
    nextBtn.textContent = index === demoData.steps.length - 1 ? 'Finish' : 'Next →';

    document.querySelectorAll('.step-dot').forEach((dot, i) => {
        dot.classList.toggle('active', i === index);
    });
}

function renderViewerPins() {
    const container = document.getElementById('pinsContainer');
    container.innerHTML = '';
    const step = demoData.steps[currentStepIndex];
    if (!step || !step.pins) return;

    step.pins.forEach((pin, idx) => {
        const pinEl = document.createElement('div');
        pinEl.className = 'pin-marker';
        pinEl.style.left = pin.x + '%';
        pinEl.style.top = pin.y + '%';
        pinEl.innerHTML = '<span class="pin-dot"></span>';
        pinEl.addEventListener('click', (e) => {
            e.stopPropagation();
            showTooltip(e, pin, idx);
        });
        container.appendChild(pinEl);
    });
}

function showTooltip(event, pin, idx) {
    const popover = document.getElementById('tooltipPopover');
    const text = document.getElementById('tooltipText');
    const actionBtn = document.getElementById('tooltipAction');

    const titleEl = document.getElementById('tooltipTitle');
    if (pin.title) {
        titleEl.textContent = pin.title;
        titleEl.style.display = 'block';
    } else {
        titleEl.style.display = 'none';
    }
    text.textContent = pin.text || 'Continue';

    if (pin.action === 'url') {
        actionBtn.textContent = 'Open Link →';
        actionBtn.dataset.url = pin.url || '#';
        actionBtn.dataset.action = 'url';
    } else if (pin.action === 'previous') {
        actionBtn.textContent = '← Previous';
        actionBtn.dataset.action = 'previous';
    } else {
        actionBtn.textContent = 'Next →';
        actionBtn.dataset.action = 'next';
    }

    const wrapper = document.getElementById('imageWrapper');
    const wrapperRect = wrapper.getBoundingClientRect();
    const pinEl = event.currentTarget;
    const pinRect = pinEl.getBoundingClientRect();

    const pinCenterX = pinRect.left - wrapperRect.left + pinRect.width / 2;
    const pinCenterY = pinRect.top - wrapperRect.top + pinRect.height / 2;

    popover.style.display = 'block';
    popover.style.left = Math.round(pinCenterX) + 'px';
    popover.style.top = (pinCenterY - 16) + 'px';

    const popoverRect = popover.getBoundingClientRect();
    const overflowsRight = popoverRect.right > wrapperRect.right;
    const overflowsLeft = popoverRect.left < wrapperRect.left;
    const overflowsTop = popoverRect.top < wrapperRect.top;
    const overflowsBottom = popoverRect.bottom > wrapperRect.bottom;

    let finalX = pinCenterX;
    let finalY = pinCenterY - 16;
    let arrowPos = 'bottom';

    if (overflowsTop && wrapperRect.bottom - pinCenterY > 200) {
        finalY = pinCenterY + 16;
        arrowPos = 'top';
    }

    if (overflowsRight) {
        finalX = wrapperRect.width - 10;
        popover.style.transform = 'translateX(-100%)';
    } else if (overflowsLeft) {
        finalX = 10;
        popover.style.transform = 'none';
    } else {
        popover.style.transform = 'translateX(-50%)';
    }

    if (finalY < 10) finalY = 10;
    if (finalY + popoverRect.height > wrapperRect.height - 10) {
        finalY = pinCenterY + 16;
        arrowPos = 'top';
    }

    popover.style.left = Math.round(finalX) + 'px';
    popover.style.top = Math.round(finalY) + 'px';
    popover.className = 'tooltip-popover tooltip-' + arrowPos;

    popover.style.display = 'block';

    if (demoId) {
        trackClick(idx);
    }
}

function hideTooltip() {
    document.getElementById('tooltipPopover').style.display = 'none';
}

function handleTooltipAction() {
    const btn = document.getElementById('tooltipAction');
    const action = btn.dataset.action;

    hideTooltip();

    if (action === 'next') {
        nextStep();
    } else if (action === 'previous') {
        prevStep();
    } else if (action === 'url') {
        window.open(btn.dataset.url, '_blank');
    }
}

function nextStep() {
    if (currentStepIndex < demoData.steps.length - 1) {
        loadViewerStep(currentStepIndex + 1);
    }
}

function prevStep() {
    if (currentStepIndex > 0) {
        loadViewerStep(currentStepIndex - 1);
    }
}

function restartDemo() {
    hideTooltip();
    loadViewerStep(0);
}

function goToStep(index) {
    hideTooltip();
    loadViewerStep(index);
}

function apiUrl(path) {
    return (window.BASE_URL || '') + path;
}

function trackView() {
    if (!demoId) return;
    fetch(apiUrl('api/track_view.php'), {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ demo_id: demoId })
    }).catch(() => {});
}

function trackClick(pinIndex) {
    if (!demoId) return;
    fetch(apiUrl('api/track_click.php'), {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ demo_id: demoId, step_index: currentStepIndex, pin_index: pinIndex })
    }).catch(() => {});
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'ArrowRight' || e.key === ' ') {
        e.preventDefault();
        nextStep();
    } else if (e.key === 'ArrowLeft') {
        e.preventDefault();
        prevStep();
    } else if (e.key === 'Escape') {
        hideTooltip();
    }
});

document.getElementById('imageWrapper').addEventListener('click', function(e) {
    if (!e.target.closest('.tooltip-popover') && !e.target.closest('.pin-marker')) {
        hideTooltip();
    }
});
