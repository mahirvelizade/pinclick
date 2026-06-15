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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
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
                    <h1>My Demos</h1>
                    <p class="text-muted">Manage your interactive demo projects</p>
                </div>
                <button class="btn btn-primary" onclick="createDemo()">
                    <span class="btn-icon">+</span> New Demo
                </button>
            </div>

            <?php if (empty($demos)): ?>
            <div class="glass-card empty-state">
                <div class="empty-icon">
                    <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="#6D5DFB" stroke-width="1">
                        <rect x="3" y="3" width="18" height="18" rx="2"/>
                        <line x1="12" y1="8" x2="12" y2="16"/>
                        <line x1="8" y1="12" x2="16" y2="12"/>
                    </svg>
                </div>
                <h2>Create your first demo</h2>
                <p class="text-muted">Build interactive product demos with screenshots and clickable hotspots</p>
                <button class="btn btn-primary" onclick="createDemo()">Get Started</button>
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
                        <span class="meta-item">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            <?= $demo['views'] ?> views
                        </span>
                        <span class="meta-item">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            <?= date('M j, Y', strtotime($demo['created_at'])) ?>
                        </span>
                    </div>
                    <div class="demo-card-actions">
                        <a href="editor.php?id=<?= $demo['id'] ?>" class="btn btn-primary btn-sm" title="Edit"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></a>
                        <a href="viewer.php?id=<?= $demo['id'] ?>" target="_blank" class="btn btn-ghost btn-sm" title="View"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></a>
                        <a href="embed.php?id=<?= $demo['id'] ?>" class="btn btn-ghost btn-sm" title="Embed"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg></a>
                        <a href="export.php?id=<?= $demo['id'] ?>" class="btn btn-ghost btn-sm" title="Export"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg></a>
                        <button class="btn btn-danger btn-sm" onclick="deleteDemo(<?= $demo['id'] ?>)" title="Delete"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg></a>
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
