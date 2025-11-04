<?php

declare(strict_types=1);

header('Content-Type: application/json');

require_once __DIR__ . '/../../include/connect.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/../../repositories/admin/BlogRepository.php';

requireAdminLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonError('Invalid request method', 405);
}

$blogID = isset($_POST['id']) ? (int) $_POST['id'] : 0;

if ($blogID <= 0) {
    sendJsonError('Invalid blog post ID', 400);
}

try {
    $blogRepo = new BlogRepository($pdo);
    $blog = $blogRepo->findById($blogID);

    if (!$blog) {
        sendJsonError('Blog post not found', 404);
    }

    $newStatus = $blogRepo->toggleStatus($blogID, $blog['status']);

    sendJsonResponse([
        'success' => true,
        'message' => 'Status updated',
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
