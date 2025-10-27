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

if ($orderId <= 0) {
    sendJsonError('Invalid order ID', 400);
}

try {
    $order = fetchOrderById($pdo, $orderId);

    if (!$order) {
        sendJsonError('Order not found', 404);
    }

    // Check if order has associated order items and delete them first
    deleteOrderItems($pdo, $orderId);
    
    // Delete the order
    deleteOrder($pdo, $orderId);

    sendJsonResponse([
        'success' => true,
        'message' => 'Order deleted successfully',
    ]);
} catch (Throwable $e) {
    sendJsonError('Server error', 500);
}

function fetchOrderById(PDO $pdo, int $id): ?array
{
    $stmt = $pdo->prepare('SELECT id FROM orders WHERE id = ? LIMIT 1');
    $stmt->execute([$id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    return $order ?: null;
}

function deleteOrderItems(PDO $pdo, int $orderId): void
{
    // First delete order item addons
    $stmt = $pdo->prepare('DELETE oia FROM order_item_addons oia 
                          INNER JOIN order_items oi ON oia.order_item_id = oi.id 
                          WHERE oi.order_id = ?');
    $stmt->execute([$orderId]);
    
    // Then delete order items
    $stmt = $pdo->prepare('DELETE FROM order_items WHERE order_id = ?');
    $stmt->execute([$orderId]);
}

function deleteOrder(PDO $pdo, int $id): void
{
    $stmt = $pdo->prepare('DELETE FROM orders WHERE id = ? LIMIT 1');
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