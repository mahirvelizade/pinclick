<?php
require_once 'config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$demo = get_demo($id);

if (!$demo) {
    die('Demo not found');
}

$data = $demo['data'];
$images = [];
foreach ($data['steps'] as $step) {
    if (isset($step['image']) && file_exists(__DIR__ . '/' . $step['image'])) {
        $images[$step['image']] = __DIR__ . '/' . $step['image'];
    }
}

$export_dir = sys_get_temp_dir() . '/demoflow_export_' . $id;
if (is_dir($export_dir)) {
    array_map('unlink', glob($export_dir . '/*.*'));
    foreach (glob($export_dir . '/assets/*') as $f) unlink($f);
    foreach (glob($export_dir . '/images/*') as $f) unlink($f);
} else {
    mkdir($export_dir, 0755, true);
    mkdir($export_dir . '/assets', 0755, true);
    mkdir($export_dir . '/images', 0755, true);
}

file_put_contents($export_dir . '/demo.json', json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

foreach ($images as $relpath => $abspath) {
    $dest = $export_dir . '/images/' . basename($relpath);
    copy($abspath, $dest);
    $data['steps'] = array_map(function($s) use ($relpath) {
        if (($s['image'] ?? '') === $relpath) {
            $s['image'] = 'images/' . basename($relpath);
        }
        return $s;
    }, $data['steps']);
}

file_put_contents($export_dir . '/demo.json', json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

$viewer_html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$data['title']} - Demo</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div id="app">
        <div class="viewer-container" id="viewerContainer">
            <div class="viewer-header">
                <h2 id="demoTitle">{$data['title']}</h2>
                <div class="viewer-controls">
                    <button class="btn btn-sm btn-ghost" onclick="restartDemo()">↺ Restart</button>
                </div>
            </div>
            <div class="viewer-stage" id="viewerStage">
                <div class="viewer-canvas" id="viewerCanvas">
                    <div class="viewer-image-wrapper" id="imageWrapper">
                        <img id="viewerImage" src="" alt="Demo step" class="viewer-image">
                        <div class="pins-container" id="pinsContainer"></div>
                        <div class="tooltip-popover" id="tooltipPopover" style="display:none">
                            <div class="tooltip-card" id="tooltipCard">
                                <h4 class="tooltip-title" id="tooltipTitle"></h4>
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
    </div>
    <script src="assets/viewer.js"><\/script>
    <script>
        window.DEMO_DATA = null;
        function trackView() {}
        function trackClick() {}
        fetch('demo.json').then(r=>r.json()).then(d=>{window.DEMO_DATA=d;initViewer(d);});
    <\/script>
</body>
</html>
HTML;

file_put_contents($export_dir . '/index.html', $viewer_html);

$css = file_get_contents(__DIR__ . '/assets/style.css');
file_put_contents($export_dir . '/assets/style.css', $css);

$viewer_js = file_get_contents(__DIR__ . '/assets/viewer.js');
file_put_contents($export_dir . '/assets/viewer.js', $viewer_js);

$zip_file = sys_get_temp_dir() . '/demoflow_' . $id . '.zip';

$zip = new ZipArchive();
if ($zip->open($zip_file, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
    die('Failed to create ZIP');
}

$files = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($export_dir, RecursiveDirectoryIterator::SKIP_DOTS)
);
foreach ($files as $file) {
    $relative = 'demo/' . substr($file->getPathname(), strlen($export_dir) + 1);
    $zip->addFile($file->getRealPath(), $relative);
}
$zip->close();

array_map('unlink', glob($export_dir . '/*.*'));
foreach (glob($export_dir . '/assets/*') as $f) unlink($f);
foreach (glob($export_dir . '/images/*') as $f) unlink($f);
rmdir($export_dir . '/assets');
rmdir($export_dir . '/images');
rmdir($export_dir);

header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="demoflow-' . $id . '.zip"');
header('Content-Length: ' . filesize($zip_file));
readfile($zip_file);
unlink($zip_file);
exit;
