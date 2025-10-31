<?php

declare(strict_types=1);

header('Content-Type: application/json');

require_once __DIR__ . '/../../include/connect.php';
require_once __DIR__ . '/../../repositories/admin/BlogCategoryRepository.php';
require_once __DIR__ . '/functions.php';

requireAdminLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonError('Invalid request method', 405);
}

$categoryId = isset($_POST['id']) ? (int)$_POST['id'] : 0;

if ($categoryId <= 0) {
    sendJsonError('Invalid category ID', 400);
}

try {
    $blogCategoryRepo = new BlogCategoryRepository($pdo);
    $category = $blogCategoryRepo->findById($categoryId);

    if (!$category) {
        sendJsonError('Blog category not found', 404);
    }

    $newStatus = $blogCategoryRepo->toggleStatus($categoryId, $category['status']);

    sendJsonResponse([
        'success' => true,
        'message' => 'Status updated',
        'category_id' => $categoryId,
        'status' => $newStatus,
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
