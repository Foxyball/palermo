<?php

declare(strict_types=1);

header('Content-Type: application/json');

require_once __DIR__ . '/../../include/connect.php';
require_once __DIR__ . '/functions.php';

requireAdminLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonError('Invalid request method', 405);
}

$galleryId = isset($_POST['id']) ? (int) $_POST['id'] : 0;

if ($galleryId <= 0) {
    sendJsonError('Invalid gallery ID', 400);
}

try {
    $gallery = fetchGalleryById($pdo, $galleryId);

    if (!$gallery) {
        sendJsonError('Gallery not found', 404);
    }

    deleteGallery($pdo, $galleryId);

    sendJsonResponse([
        'success' => true,
        'message' => 'Gallery deleted successfully',
        'gallery_id' => $galleryId,
    ]);
} catch (Throwable $e) {
    sendJsonError('Server error', 500);
}

function fetchGalleryById(PDO $pdo, int $id): ?array
{
    $stmt = $pdo->prepare('SELECT id FROM galleries WHERE id = ? LIMIT 1');
    $stmt->execute([$id]);
    $gallery = $stmt->fetch(PDO::FETCH_ASSOC);

    return $gallery ?: null;
}

function deleteGallery(PDO $pdo, int $id): void
{
    $stmt = $pdo->prepare('DELETE FROM galleries WHERE id = ? LIMIT 1');
    $stmt->execute([$id]);
}

function sendJsonError(string $message, int $status = 400): void
{
    http_response_code($status);
    echo json_encode(['success' => false, 'message' => $message]);
    exit;
}

function sendJsonResponse(array $data): void
{
    echo json_encode($data);
    exit;
}
