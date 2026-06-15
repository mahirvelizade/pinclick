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

$ip = get_client_ip();
$ua = $_SERVER['HTTP_USER_AGENT'] ?? '';

$stmt = $conn->prepare("INSERT INTO analytics_views (demo_id, ip, user_agent) VALUES (?, ?, ?)");
$stmt->bind_param('iss', $demo_id, $ip, $ua);
$stmt->execute();
$stmt->close();

$stmt = $conn->prepare("UPDATE demos SET views = views + 1 WHERE id = ?");
$stmt->bind_param('i', $demo_id);
$stmt->execute();
$stmt->close();

json_response(['success' => true]);
