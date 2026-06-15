<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

define('DB_HOST', 'localhost');
define('DB_NAME', 'demoflow');
define('DB_USER', 'root');
define('DB_PASS', '');

define('SITE_URL', 'http://localhost/demoflow');
define('UPLOAD_DIR', __DIR__ . '/uploads');
define('UPLOAD_URL', SITE_URL . '/uploads');
define('MAX_FILE_SIZE', 10 * 1024 * 1024);

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die(json_encode(['error' => 'Database connection failed: ' . $conn->connect_error]));
}

$conn->set_charset('utf8mb4');

function json_response($data, $code = 200) {
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

function get_demo($id) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM demos WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $demo = $result->fetch_assoc();
    if ($demo) {
        $demo['data'] = json_decode($demo['data'], true);
    }
    $stmt->close();
    return $demo;
}

function save_demo($id, $data) {
    global $conn;
    $json = json_encode($data, JSON_UNESCAPED_UNICODE);
    $stmt = $conn->prepare("UPDATE demos SET data = ?, title = ?, updated_at = NOW() WHERE id = ?");
    $title = $data['title'] ?? 'Untitled Demo';
    $stmt->bind_param('ssi', $json, $title, $id);
    $result = $stmt->execute();
    $stmt->close();
    return $result;
}

function create_demo($title = 'Untitled Demo') {
    global $conn;
    $data = json_encode([
        'title' => $title,
        'steps' => []
    ]);
    $stmt = $conn->prepare("INSERT INTO demos (title, data) VALUES (?, ?)");
    $stmt->bind_param('ss', $title, $data);
    $stmt->execute();
    $id = $stmt->insert_id;
    $stmt->close();
    return $id;
}

function delete_demo($id) {
    global $conn;
    $demo = get_demo($id);
    if ($demo && isset($demo['data']['steps'])) {
        foreach ($demo['data']['steps'] as $step) {
            if (isset($step['image']) && file_exists(__DIR__ . '/' . $step['image'])) {
                unlink(__DIR__ . '/' . $step['image']);
            }
        }
    }
    $stmt = $conn->prepare("DELETE FROM demos WHERE id = ?");
    $stmt->bind_param('i', $id);
    $result = $stmt->execute();
    $stmt->close();
    return $result;
}

function get_all_demos() {
    global $conn;
    $result = $conn->query("SELECT id, title, status, views, created_at, updated_at FROM demos ORDER BY updated_at DESC");
    return $result->fetch_all(MYSQLI_ASSOC);
}

function sanitize_filename($name) {
    $name = preg_replace('/[^a-zA-Z0-9._-]/', '_', $name);
    return time() . '_' . $name;
}

function get_client_ip() {
    if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) return $_SERVER['HTTP_CF_CONNECTING_IP'];
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) return explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
    if (!empty($_SERVER['HTTP_X_REAL_IP'])) return $_SERVER['HTTP_X_REAL_IP'];
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}
