<?php
require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['error' => 'Method not allowed'], 405);
}

$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['id'])) {
    json_response(['error' => 'Invalid data'], 400);
}

$id = (int)$input['id'];
$demo = get_demo($id);

if (!$demo) {
    json_response(['error' => 'Demo not found'], 404);
}

$data = $input['data'] ?? $demo['data'];
$data['title'] = $input['title'] ?? $demo['title'];
$data['steps'] = $input['steps'] ?? $demo['data']['steps'] ?? [];

$status = $input['status'] ?? $demo['status'];

$json = json_encode($data, JSON_UNESCAPED_UNICODE);
$title = $data['title'];

$stmt = $conn->prepare("UPDATE demos SET data = ?, title = ?, status = ?, updated_at = NOW() WHERE id = ?");
$stmt->bind_param('sssi', $json, $title, $status, $id);
$result = $stmt->execute();
$stmt->close();

if ($result) {
    json_response(['success' => true, 'id' => $id]);
} else {
    json_response(['error' => 'Failed to save'], 500);
}
