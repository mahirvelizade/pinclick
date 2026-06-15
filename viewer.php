<?php require_once 'config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$demo = get_demo($id);

if (!$demo) {
    http_response_code(404);
    die('Demo not found');
}

$data = $demo['data'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($data['title'] ?? 'Demo') ?> - DemoFlow AI</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
    <style>
    .viewer-page { background: #0B1020; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
    </style>
</head>
<body class="viewer-page">
    <div class="viewer-container" id="app">
        <div class="viewer-header">
            <h2 id="demoTitle"><?= htmlspecialchars($data['title'] ?? 'Demo') ?></h2>
            <div class="viewer-controls">
                <a href="embed.php?id=<?= $id ?>" class="btn btn-ghost btn-sm">Embed</a>
                <button class="btn btn-ghost btn-sm" onclick="restartDemo()">↺ Restart</button>
            </div>
        </div>
        <div class="viewer-stage" id="viewerStage">
            <div class="viewer-canvas" id="viewerCanvas">
                <div class="viewer-image-wrapper" id="imageWrapper">
                    <img id="viewerImage" src="" alt="Demo step" class="viewer-image">
                    <div class="pins-container" id="pinsContainer"></div>
                    <div class="tooltip-popover" id="tooltipPopover" style="display:none">
                        <div class="tooltip-arrow" id="tooltipArrow"></div>
                        <div class="tooltip-card" id="tooltipCard">
                            <p id="tooltipText"></p>
                            <button class="btn btn-primary btn-sm" id="tooltipAction" onclick="handleTooltipAction()"></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="viewer-footer">
            <button class="btn btn-ghost" onclick="prevStep()" id="prevBtn">← Previous</button>
            <div class="step-indicators" id="stepIndicators"></div>
            <button class="btn btn-primary" onclick="nextStep()" id="nextBtn">Next →</button>
        </div>
    </div>

    <script>
    window.BASE_URL = '';
    window.DEMO_DATA = <?= json_encode($data, JSON_UNESCAPED_UNICODE) ?>;
    window.DEMO_ID = <?= $id ?>;
    </script>
    <script src="assets/viewer.js"></script>
    <script>initViewer(window.DEMO_DATA);</script>
</body>
</html>
