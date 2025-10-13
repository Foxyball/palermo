<?php

declare(strict_types=1);

header('Content-Type: application/json');

require_once __DIR__ . '/../../include/connect.php';
require_once __DIR__ . '/functions.php';

requireAdminLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonError('Invalid request method', 405);
}

$blogId = isset($_POST['id']) ? (int)$_POST['id'] : 0;

if ($blogId <= 0) {
    sendJsonError('Invalid blog ID', 400);
}

try {
    $blog = fetchBlogById($pdo, $blogId);

    if (!$blog) {
        sendJsonError('Blog not found', 404);
    }

    if (!empty($blog['image'])) {
        deleteImageFile($blog['image']);
    }

    deleteBlog($pdo, $blogId);

    sendJsonResponse([
        'success' => true,
        'message' => 'Blog post and associated image deleted successfully',
        'blog_id' => $blogId,
    ]);
} catch (Throwable $e) {
    sendJsonError('Server error', 500);
}

function fetchBlogById(PDO $pdo, int $id): ?array
{
    $stmt = $pdo->prepare('SELECT id, image FROM blogs WHERE id = ? LIMIT 1');
    $stmt->execute([$id]);
    $blog = $stmt->fetch(PDO::FETCH_ASSOC);

    return $blog ?: null;
}

function deleteBlog(PDO $pdo, int $id): void
{
    $stmt = $pdo->prepare('DELETE FROM blogs WHERE id = ? LIMIT 1');
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
