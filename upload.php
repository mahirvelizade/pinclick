<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Images - PinClick</title>
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
