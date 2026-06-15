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
                <span class="brand-icon">◆</span>
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
