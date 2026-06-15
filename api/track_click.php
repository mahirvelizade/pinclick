<?php
require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['error' => 'Method not allowed'], 405);
}

$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['demo_id'])) {
    json_response(['error' => 'Invalid data'], 400);
}

$demo_id = (int)$input['demo_id'];
$step_index = (int)($input['step_index'] ?? 0);
$pin_index = (int)($input['pin_index'] ?? 0);
$ip = get_client_ip();

$stmt = $conn->prepare("INSERT INTO analytics_clicks (demo_id, step_index, pin_index, ip) VALUES (?, ?, ?, ?)");
$stmt->bind_param('iiis', $demo_id, $step_index, $pin_index, $ip);
$stmt->execute();
$stmt->close();

json_response(['success' => true]);
