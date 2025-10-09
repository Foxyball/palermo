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

    deleteCategory($pdo, $categoryId);

    sendJsonResponse([
        'success' => true,
        'message' => 'Category deleted successfully',
        'category_id' => $categoryId,
    ]);
} catch (Throwable $e) {
    sendJsonError('Server error', 500);
}

function fetchCategoryById(PDO $pdo, int $id): ?array
{
    $stmt = $pdo->prepare('SELECT id FROM categories WHERE id = ? LIMIT 1');
    $stmt->execute([$id]);
    $category = $stmt->fetch(PDO::FETCH_ASSOC);

    return $category ?: null;
}

function deleteCategory(PDO $pdo, int $id): void
{
    $stmt = $pdo->prepare('DELETE FROM categories WHERE id = ? LIMIT 1');
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
