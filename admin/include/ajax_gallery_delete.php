<?php
header('Content-Type: application/json');

require_once(__DIR__ . '/../../include/connect.php');
require_once(__DIR__ . '/functions.php');

requireAdminLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Invalid method']);
    exit;
}

$galleryId = $_POST['id'] ?? 0;
if ($galleryId <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid gallery ID']);
    exit;
}

try {
    $stmt = $pdo->prepare('SELECT id FROM galleries WHERE id = ? LIMIT 1');
    $stmt->execute([$galleryId]);
    $gallery = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$gallery) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'gallery not found']);
        exit;
    }

    $del = $pdo->prepare('DELETE FROM galleries WHERE id = ? LIMIT 1');
    $del->execute([$galleryId]);

    echo json_encode(['success' => true, 'message' => 'gallery deleted', 'gallery_id' => $galleryId]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
