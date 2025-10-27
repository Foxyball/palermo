<?php

declare(strict_types=1);

header('Content-Type: application/json');

require_once __DIR__ . '/../../include/connect.php';
require_once __DIR__ . '/functions.php';

requireAdminLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonError('Invalid request method', 405);
}

$orderId = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$statusId = isset($_POST['status_id']) ? (int)$_POST['status_id'] : 0;

if ($orderId <= 0) {
    sendJsonError('Invalid order ID', 400);
}

if ($statusId <= 0) {
    sendJsonError('Invalid status ID', 400);
}

try {
    $order = fetchOrderById($pdo, $orderId);

    if (!$order) {
        sendJsonError('Order not found', 404);
    }

    // Verify the status exists and is active
    $status = fetchOrderStatusById($pdo, $statusId);
    if (!$status || $status['active'] != '1') {
        sendJsonError('Invalid or inactive status', 400);
    }

    updateOrderStatus($pdo, $orderId, $statusId);

    sendJsonResponse([
        'success' => true,
        'message' => 'Order status updated successfully',
    ]);
} catch (Throwable $e) {
    sendJsonError('Server error', 500);
}

function fetchOrderById(PDO $pdo, int $id): ?array
{
    $stmt = $pdo->prepare('SELECT id, status_id FROM orders WHERE id = ? LIMIT 1');
    $stmt->execute([$id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    return $order ?: null;
}

function fetchOrderStatusById(PDO $pdo, int $id): ?array
{
    $stmt = $pdo->prepare('SELECT id, name, active FROM order_statuses WHERE id = ? LIMIT 1');
    $stmt->execute([$id]);
    $status = $stmt->fetch(PDO::FETCH_ASSOC);

    return $status ?: null;
}

function updateOrderStatus(PDO $pdo, int $orderId, int $statusId): void
{
    $stmt = $pdo->prepare(
        'UPDATE orders SET status_id = ?, updated_at = NOW() WHERE id = ? LIMIT 1'
    );
    $stmt->execute([$statusId, $orderId]);
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
