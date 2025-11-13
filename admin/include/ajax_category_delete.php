<?php

declare(strict_types=1);

header('Content-Type: application/json');

require_once __DIR__ . '/../../include/connect.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/../../repositories/admin/CategoryRepository.php';

requireAdminLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonError('Invalid request method', 405);
}

$categoryId = isset($_POST['id']) ? (int) $_POST['id'] : 0;

if ($categoryId <= 0) {
    sendJsonError('Invalid category ID', 400);
}

try {
    $categoryRepository = new CategoryRepository($pdo);
    $category = $categoryRepository->findById($categoryId);

    if (!$category) {
        sendJsonError('Category not found', 404);
    }

    // Check if category is being used by any products
    $productsUsingCategory = $categoryRepository->isInUse($categoryId);
    if ($productsUsingCategory > 0) {
        sendJsonError('Cannot delete category that is being used by ' . $productsUsingCategory . ' product(s)', 400);
    }

    $categoryRepository->delete($categoryId);

    sendJsonResponse([
        'success' => true,
        'message' => 'Category deleted successfully',
        'category_id' => $categoryId,
    ]);
} catch (Throwable $e) {
    sendJsonError('Server error occurred. Please try again.', 500);
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
