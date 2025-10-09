<?php

declare(strict_types=1);

header('Content-Type: application/json');

require_once __DIR__ . '/../../include/connect.php';
require_once __DIR__ . '/functions.php';

requireAdminLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonError('Invalid request method', 405);
}

$ids = getImageIdsFromRequest($_POST);

if (empty($ids)) {
    sendJsonError('No valid image IDs provided', 400);
}

try {
    $placeholders = implode(',', array_fill(0, count($ids), '?'));

    // Fetch existing images
    $stmt = $pdo->prepare("SELECT id, image FROM gallery_images WHERE id IN ($placeholders)");
    $stmt->execute($ids);
    $images = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

    if (empty($images)) {
        sendJsonResponse([
            'success' => false,
            'message' => 'No images found',
            'deleted_ids' => [],
            'not_found_ids' => $ids,
            'errors' => [],
        ]);
    }

    $foundIds = array_map(static fn($img) => (int) $img['id'], $images);
    $missingIds = array_values(array_diff($ids, $foundIds));

    $pdo->beginTransaction();
    $deleteStmt = $pdo->prepare("DELETE FROM gallery_images WHERE id IN ($placeholders)");
    $deleteStmt->execute($ids);
    $pdo->commit();

    // Delete files from storage
    foreach ($images as $image) {
        if (!empty($image['image'])) {
            deleteImageFile($image['image']);
        }
    }

    sendJsonResponse([
        'success' => true,
        'message' => sprintf('%d image(s) deleted', count($foundIds)),
        'deleted_ids' => $foundIds,
        'not_found_ids' => $missingIds,
        'errors' => [],
    ]);
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    sendJsonError('Server error', 500);
}


function getImageIdsFromRequest(array $post): array
{
    $ids = [];

    if (!empty($post['ids']) && is_array($post['ids'])) {
        $ids = $post['ids'];
    } elseif (!empty($post['id'])) {
        $ids = [$post['id']];
    }

    return array_values(
        array_unique(
            array_filter(
                array_map('intval', $ids),
                static fn($id) => $id > 0
            )
        )
    );
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
