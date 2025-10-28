<?php

declare(strict_types=1);

header('Content-Type: application/json');

require_once __DIR__ . '/../../include/connect.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/../../repositories/admin/OrderRepository.php';

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
    error_log('Order status update error: ' . $e->getMessage());
    error_log('Stack trace: ' . $e->getTraceAsString());
    sendJsonError('Server error: ' . $e->getMessage(), 500);
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
