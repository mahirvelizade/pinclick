<?php
require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['error' => 'Method not allowed'], 405);
}

$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['id']) || !isset($input['step_index'])) {
    json_response(['error' => 'Invalid data'], 400);
}

$id = (int)$input['id'];
$stepIndex = (int)$input['step_index'];
$demo = get_demo($id);

if (!$demo) {
    json_response(['error' => 'Demo not found'], 404);
}

$steps = $demo['data']['steps'] ?? [];

if (!isset($steps[$stepIndex])) {
    json_response(['error' => 'Step not found'], 404);
}

$steps[$stepIndex]['pins'] = $input['pins'] ?? $steps[$stepIndex]['pins'] ?? [];
$steps[$stepIndex]['image'] = $input['image'] ?? $steps[$stepIndex]['image'] ?? '';

$demo['data']['steps'] = $steps;

if (save_demo($id, $demo['data'])) {
    json_response(['success' => true]);
} else {
    json_response(['error' => 'Failed to save'], 500);
}
