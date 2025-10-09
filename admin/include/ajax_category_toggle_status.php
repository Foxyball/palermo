<?php

declare(strict_types=1);

header('Content-Type: application/json');

require_once __DIR__ . '/../../include/connect.php';
require_once __DIR__ . '/functions.php';

requireAdminLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonError('Invalid request method', 405);
}

$categoryId = isset($_POST['id']) ? (int) $_POST['id'] : 0;

if ($categoryId <= 0) {
    sendJsonError('Invalid category ID', 400);
}

try {
    $category = fetchCategoryById($pdo, $categoryId);

    if (!$category) {
        sendJsonError('Category not found', 404);
    }

    $newStatus = toggleCategoryStatus($pdo, $categoryId, $category['active']);

    sendJsonResponse([
        'success' => true,
        'message' => 'Status updated',
        'category_id' => $categoryId,
        'active' => $newStatus,
    ]);
} catch (Throwable $e) {
    sendJsonError('Server error', 500);
}

function fetchCategoryById(PDO $pdo, int $id): ?array
{
    $stmt = $pdo->prepare('SELECT id, active FROM categories WHERE id = ? LIMIT 1');
    $stmt->execute([$id]);
    $category = $stmt->fetch(PDO::FETCH_ASSOC);

    return $category ?: null;
}

function toggleCategoryStatus(PDO $pdo, int $id, string $currentStatus): string
{
    $newStatus = $currentStatus === '1' ? '0' : '1';

    $stmt = $pdo->prepare(
        'UPDATE categories SET active = ?, updated_at = NOW() WHERE id = ? LIMIT 1'
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
