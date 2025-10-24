<?php

declare(strict_types=1);

header('Content-Type: application/json');

require_once __DIR__ . '/../../include/connect.php';
require_once __DIR__ . '/functions.php';

requireAdminLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonError('Invalid request method', 405);
}

$productID = isset($_POST['id']) ? (int) $_POST['id'] : 0;

if ($productID <= 0) {
    sendJsonError('Invalid product ID', 400);
}

try {
    $product = fetchProductById($pdo, $productID);

    if (!$product) {
        sendJsonError('Product not found', 404);
    }

    $newStatus = toggleProductStatus($pdo, $productID, $product['active']);

    sendJsonResponse([
        'success' => true,
        'message' => 'Status updated',
        'status' => $newStatus,
    ]);
} catch (Throwable $e) {
    sendJsonError('Server error', 500);
}

function fetchProductById(PDO $pdo, int $id): ?array
{
    $stmt = $pdo->prepare('SELECT id, active FROM products WHERE id = ? LIMIT 1');
    $stmt->execute([$id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    return $product ?: null;
}

function toggleProductStatus(PDO $pdo, int $id, string $currentStatus): string
{
    $newStatus = $currentStatus === '1' ? '0' : '1';

    $stmt = $pdo->prepare(
        'UPDATE products SET active = ?, updated_at = NOW() WHERE id = ? LIMIT 1'
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
