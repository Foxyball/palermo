<?php

declare(strict_types=1);

header('Content-Type: application/json');

require_once __DIR__ . '/../../include/connect.php';
require_once __DIR__ . '/functions.php';

requireAdminLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonError('Invalid request method', 405);
}

$blogID = isset($_POST['id']) ? (int) $_POST['id'] : 0;

if ($blogID <= 0) {
    sendJsonError('Invalid blog post ID', 400);
}

try {
    $blog = fetchBlogById($pdo, $blogID);

    if (!$blog) {
        sendJsonError('Blog post not found', 404);
    }

    $newStatus = toggleBlogPostStatus($pdo, $blogID, $blog['status']);

    sendJsonResponse([
        'success' => true,
        'message' => 'Status updated',
        'status' => $newStatus,
    ]);
} catch (Throwable $e) {
    sendJsonError('Server error', 500);
}

function fetchBlogById(PDO $pdo, int $id): ?array
{
    $stmt = $pdo->prepare('SELECT id, status FROM blogs WHERE id = ? LIMIT 1');
    $stmt->execute([$id]);
    $blog = $stmt->fetch(PDO::FETCH_ASSOC);

    return $blog ?: null;
}

function toggleBlogPostStatus(PDO $pdo, int $id, string $currentStatus): string
{
    $newStatus = $currentStatus === '1' ? '0' : '1';

    $stmt = $pdo->prepare(
        'UPDATE blogs SET status = ?, updated_at = NOW() WHERE id = ? LIMIT 1'
    );
    $stmt->execute([$newStatus, $id]);

    return $newStatus;
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
