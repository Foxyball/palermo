<?php

declare(strict_types=1);

header('Content-Type: application/json');

require_once __DIR__ . '/../../include/connect.php';
require_once __DIR__ . '/functions.php';

requireAdminLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonError('Invalid request method', 405);
}

$statusId = isset($_POST['id']) ? (int)$_POST['id'] : 0;

if ($statusId <= 0) {
    sendJsonError('Invalid order status ID', 400);
}

try {
    $status = fetchOrderStatusById($pdo, $statusId);

    if (!$status) {
        sendJsonError('Order status not found', 404);
    }

    deleteOrderStatus($pdo, $statusId);

    sendJsonResponse([
        'success' => true,
        'message' => 'Order status deleted successfully',
    ]);
} catch (Throwable $e) {
    sendJsonError('Server error', 500);
}

function fetchOrderStatusById(PDO $pdo, int $id): ?array
{
    $stmt = $pdo->prepare('SELECT id, name FROM order_statuses WHERE id = ? LIMIT 1');
    $stmt->execute([$id]);
    $status = $stmt->fetch(PDO::FETCH_ASSOC);

    return $status ?: null;
}

function deleteOrderStatus(PDO $pdo, int $id): void
{
    $stmt = $pdo->prepare('DELETE FROM order_statuses WHERE id = ? LIMIT 1');
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
