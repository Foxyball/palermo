<?php

declare(strict_types=1);

header('Content-Type: application/json');

require_once __DIR__ . '/../../include/connect.php';
require_once __DIR__ . '/functions.php';

requireAdminLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonError('Invalid request method', 405);
}

$orderId = $_POST['id'] ?? 0;
$statusId = $_POST['status_id'] ?? 0;

if ($orderId <= 0) {
    sendJsonError('Invalid order ID', 400);
}

if ($statusId <= 0) {
    sendJsonError('Invalid status ID', 400);
}

try {
    $orderRepo = new OrderRepository($pdo);
    $order = $orderRepo->findById($orderId);

    if (!$order) {
        sendJsonError('Order not found', 404);
    }

    if ($orderRepo->updateStatus($orderId, $statusId)) {
        sendJsonResponse([
            'success' => true,
            'message' => 'Status updated successfully',
        ]);
    } else {
        sendJsonError('Failed to update status', 500);
    }
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
