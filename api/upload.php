<?php
require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['error' => 'Method not allowed'], 405);
}

if (!isset($_FILES['file'])) {
    json_response(['error' => 'No file uploaded'], 400);
}

$file = $_FILES['file'];

$allowed_types = ['image/jpeg', 'image/png', 'image/webp'];
if (!in_array($file['type'], $allowed_types)) {
    json_response(['error' => 'Only JPG, PNG, and WEBP files are allowed'], 400);
}

if ($file['size'] > MAX_FILE_SIZE) {
    json_response(['error' => 'File too large. Max 10MB'], 400);
}

$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
$filename = sanitize_filename($file['name']);
$filepath = 'uploads/' . $filename;

if (!move_uploaded_file($file['tmp_name'], __DIR__ . '/../' . $filepath)) {
    json_response(['error' => 'Failed to save file'], 500);
}

$stmt = $conn->prepare("INSERT INTO uploads (filename, filepath, filesize, mime_type) VALUES (?, ?, ?, ?)");
$stmt->bind_param('ssis', $filename, $filepath, $file['size'], $file['type']);
$stmt->execute();
$upload_id = $stmt->insert_id;
$stmt->close();

json_response([
    'success' => true,
    'id' => $upload_id,
    'filename' => $filename,
    'filepath' => $filepath,
    'url' => SITE_URL . '/' . $filepath
]);
