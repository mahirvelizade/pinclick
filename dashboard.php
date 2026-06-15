<?php require_once 'config.php';

$demos = get_all_demos();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    if ($_POST['action'] === 'create') {
        $id = create_demo();
        echo json_encode(['success' => true, 'id' => $id]);
        exit;
    }
    if ($_POST['action'] === 'delete') {
        $id = (int)$_POST['id'];
        delete_demo($id);
        echo json_encode(['success' => true]);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - PinClick</title>
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
                <a href="dashboard.php" class="nav-link active">Dashboard</a>
                <a href="upload.php" class="nav-link">Upload</a>
            </div>
        </div>
    </nav>

    <main class="main-content">
        <div class="container">
            <div class="page-header">
                <div>
                    <h1>📊 My Demos</h1>
                    <p class="text-muted">Manage your interactive demo projects</p>
                </div>
                <button class="btn btn-primary" onclick="createDemo()">✨ New Demo</button>
            </div>

            <?php if (empty($demos)): ?>
            <div class="glass-card empty-state">
                <div class="empty-icon">🎯</div>
                <h2>Create your first demo</h2>
                <p class="text-muted">Build interactive product demos with screenshots and clickable hotspots</p>
                <button class="btn btn-primary" onclick="createDemo()">🚀 Get Started</button>
            </div>
            <?php else: ?>
            <div class="demos-grid">
                <?php foreach ($demos as $demo): ?>
                <div class="glass-card demo-card" data-id="<?= $demo['id'] ?>">
                    <div class="demo-card-header">
                        <h3><?= htmlspecialchars($demo['title']) ?></h3>
                        <span class="status-badge status-<?= $demo['status'] ?>"><?= $demo['status'] ?></span>
                    </div>
                    <div class="demo-card-meta">
                        <span class="meta-item">👁️ <?= $demo['views'] ?> views</span>
                        <span class="meta-item">📅 <?= date('M j, Y', strtotime($demo['created_at'])) ?></span>
                    </div>
                    <div class="demo-card-actions">
                        <a href="editor.php?id=<?= $demo['id'] ?>" class="btn btn-primary btn-sm" title="Edit">✏️</a>
                        <a href="viewer.php?id=<?= $demo['id'] ?>" target="_blank" class="btn btn-ghost btn-sm" title="View">👁️</a>
                        <a href="embed.php?id=<?= $demo['id'] ?>" class="btn btn-ghost btn-sm" title="Embed">🔗</a>
                        <a href="export.php?id=<?= $demo['id'] ?>" class="btn btn-ghost btn-sm" title="Export">📦</a>
                        <button class="btn btn-danger btn-sm" onclick="deleteDemo(<?= $demo['id'] ?>)" title="Delete">🗑️</button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </main>

    <script>
    async function createDemo() {
        const formData = new FormData();
        formData.append('action', 'create');
        try {
            const res = await fetch('dashboard.php', { method: 'POST', body: formData });
            const data = await res.json();
            if (data.success) {
                window.location.href = 'editor.php?id=' + data.id;
            }
        } catch (e) {
            showToast('Failed to create demo', 'error');
        }
    }

    async function deleteDemo(id) {
        if (!confirm('Delete this demo permanently?')) return;
        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('id', id);
        try {
            const res = await fetch('dashboard.php', { method: 'POST', body: formData });
            const data = await res.json();
            if (data.success) {
                document.querySelector(`.demo-card[data-id="${id}"]`).remove();
                showToast('Demo deleted', 'success');
            }
        } catch (e) {
            showToast('Failed to delete', 'error');
        }
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
