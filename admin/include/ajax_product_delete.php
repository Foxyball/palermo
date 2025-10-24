<?php

declare(strict_types=1);

header('Content-Type: application/json');

require_once __DIR__ . '/../../include/connect.php';
require_once __DIR__ . '/functions.php';

requireAdminLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonError('Invalid request method', 405);
}

$productId = isset($_POST['id']) ? (int)$_POST['id'] : 0;

if ($productId <= 0) {
    sendJsonError('Invalid product ID', 400);
}

try {
    $product = fetchProductById($pdo, $productId);

    if (!$product) {
        sendJsonError('Product not found', 404);
    }

    $pdo->beginTransaction();

    if (!empty($product['image'])) {
        deleteImageFile($product['image']);
    }

    deleteProductAddons($pdo, $productId);

    deleteProduct($pdo, $productId);

    $pdo->commit();

    sendJsonResponse([
        'success' => true,
        'message' => 'Product and associated data deleted successfully',
    ]);
} catch (Throwable $e) {
    $pdo->rollback();
    sendJsonError('Server error', 500);
}

function fetchProductById(PDO $pdo, int $id): ?array
{
    $stmt = $pdo->prepare('SELECT id, image FROM products WHERE id = ? LIMIT 1');
    $stmt->execute([$id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    return $product ?: null;
}

function deleteProductAddons(PDO $pdo, int $productId): void
{
    $stmt = $pdo->prepare('DELETE FROM product_addons WHERE product_id = ?');
    $stmt->execute([$productId]);
}

function deleteProduct(PDO $pdo, int $id): void
{
    $stmt = $pdo->prepare('DELETE FROM products WHERE id = ? LIMIT 1');
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
