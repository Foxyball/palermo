<?php

declare(strict_types=1);

header('Content-Type: application/json');

require_once __DIR__ . '/../../include/connect.php';
require_once __DIR__ . '/../../repositories/admin/ProductRepository.php';
require_once __DIR__ . '/functions.php';

requireAdminLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonError('Invalid request method', 405);
}

$productId = isset($_POST['product_id']) ? (int) $_POST['product_id'] : 0;

if ($productId <= 0) {
    sendJsonError('Invalid product ID', 400);
}

try {
    $productRepo = new ProductRepository($pdo);
    
    $product = $productRepo->findById($productId);

    if (!$product) {
        sendJsonError('Product not found', 404);
    }

    $newStatus = $productRepo->toggleActive($productId, $product['active']);

    sendJsonResponse([
        'success' => true,
        'message' => 'Status updated',
        'product_id' => $productId,
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
