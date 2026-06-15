<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Images - DemoFlow AI</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-inner">
            <a href="index.php" class="nav-brand">
                <span class="brand-icon">◆</span>
                <span>DemoFlow AI</span>
            </a>
            <div class="nav-links">
                <a href="dashboard.php" class="nav-link">Dashboard</a>
                <a href="upload.php" class="nav-link active">Upload</a>
            </div>
        </div>
    </nav>

    <main class="main-content">
        <div class="container">
            <div class="page-header">
                <h1>Upload Images</h1>
                <p class="text-muted">Upload screenshots for your demo steps</p>
            </div>

            <div class="upload-grid">
                <div class="glass-card upload-zone-card">
                    <div class="upload-zone" id="uploadZone">
                        <div class="upload-icon">
                            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#6D5DFB" stroke-width="1.5">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                <polyline points="17 8 12 3 7 8"/>
                                <line x1="12" y1="3" x2="12" y2="15"/>
                            </svg>
                        </div>
                        <h3>Drop images here</h3>
                        <p class="text-muted">or click to browse</p>
                        <p class="text-muted small">PNG, JPG, WEBP - Max 10MB</p>
                        <input type="file" id="fileInput" accept="image/png,image/jpeg,image/webp" multiple hidden>
                        <button class="btn btn-primary" onclick="document.getElementById('fileInput').click()">Browse Files</button>
                    </div>
                </div>

                <div class="glass-card uploads-list-card">
                    <h3>Uploaded Images</h3>
                    <div id="uploadsList" class="uploads-list">
                        <p class="text-muted" id="noUploads">No images uploaded yet</p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
    const uploadZone = document.getElementById('uploadZone');
    const fileInput = document.getElementById('fileInput');
    const uploadsList = document.getElementById('uploadsList');
    const noUploads = document.getElementById('noUploads');

    uploadZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadZone.classList.add('drag-over');
    });

    uploadZone.addEventListener('dragleave', () => {
        uploadZone.classList.remove('drag-over');
    });

    uploadZone.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadZone.classList.remove('drag-over');
        handleFiles(e.dataTransfer.files);
    });

    fileInput.addEventListener('change', () => {
        handleFiles(fileInput.files);
    });

    async function handleFiles(files) {
        for (const file of files) {
            if (!['image/png', 'image/jpeg', 'image/webp'].includes(file.type)) {
                showToast('Only PNG, JPG, WEBP allowed', 'error');
                continue;
            }
            if (file.size > 10 * 1024 * 1024) {
                showToast('File too large (max 10MB)', 'error');
                continue;
            }
            await uploadFile(file);
        }
    }

    async function uploadFile(file) {
        const formData = new FormData();
        formData.append('file', file);

        const card = document.createElement('div');
        card.className = 'upload-item';
        card.innerHTML = `
            <div class="upload-item-info">
                <span class="upload-item-name">${file.name}</span>
                <span class="upload-item-size">${(file.size / 1024).toFixed(1)} KB</span>
            </div>
            <div class="upload-progress"><div class="upload-progress-bar"></div></div>
        `;
        uploadsList.prepend(card);
        noUploads.style.display = 'none';

        try {
            const res = await fetch('api/upload.php', { method: 'POST', body: formData });
            const data = await res.json();
            if (data.success) {
                card.classList.add('upload-success');
                card.innerHTML = `
                    <div class="upload-item-info">
                        <span class="upload-item-name">${file.name}</span>
                        <span class="upload-item-size success-text">Uploaded ✓</span>
                    </div>
                    <div class="upload-item-actions">
                        <button class="btn btn-sm btn-ghost" onclick="copyPath('${data.filepath}')">Copy Path</button>
                    </div>
                `;
                showToast('Uploaded successfully', 'success');
            } else {
                card.remove();
                showToast(data.error || 'Upload failed', 'error');
            }
        } catch (err) {
            card.remove();
            showToast('Upload failed', 'error');
        }
    }

    function copyPath(path) {
        navigator.clipboard.writeText(path).then(() => {
            showToast('Path copied: ' + path, 'success');
        });
    }

    function showToast(msg, type = 'info') {
        const toast = document.createElement('div');
        toast.className = 'toast toast-' + type;
        toast.textContent = msg;
        document.body.appendChild(toast);
        setTimeout(() => toast.classList.add('show'), 10);
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }
    </script>
</body>
</html>
