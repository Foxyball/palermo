<?php

declare(strict_types=1);

header('Content-Type: application/json');

require_once __DIR__ . '/../../include/connect.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/../../repositories/admin/GalleryRepository.php';

requireAdminLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonError('Invalid request method', 405);
}

$galleryId = isset($_POST['id']) ? (int) $_POST['id'] : 0;

if ($galleryId <= 0) {
    sendJsonError('Invalid gallery ID', 400);
}

try {
    $galleryRepo = new GalleryRepository($pdo);
    $gallery = $galleryRepo->findById($galleryId);

    if (!$gallery) {
        sendJsonError('Gallery not found', 404);
    }

    $newStatus = $galleryRepo->toggleActive($galleryId, $gallery['active']);

    sendJsonResponse([
        'success' => true,
        'message' => 'Status updated',
        'gallery_id' => $galleryId,
        'active' => $newStatus,
    ]);
} catch (Throwable $e) {
    sendJsonError('Server error', 500);
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
