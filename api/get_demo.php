<?php
require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    json_response(['error' => 'Method not allowed'], 405);
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id) {
    json_response(['error' => 'Invalid ID'], 400);
}

$demo = get_demo($id);

if (!$demo) {
    json_response(['error' => 'Demo not found'], 404);
}

json_response([
    'success' => true,
    'demo' => $demo
]);
