<?php

declare(strict_types=1);

header('Content-Type: application/json');

require_once __DIR__ . '/../../include/connect.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/../../repositories/admin/OrderStatusRepository.php';

requireAdminLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonError('Invalid request method', 405);
}

$statusId = isset($_POST['id']) ? (int)$_POST['id'] : 0;

if ($statusId <= 0) {
    sendJsonError('Invalid order status ID', 400);
}

try {
    $orderStatusRepository = new OrderStatusRepository($pdo);
    $status = $orderStatusRepository->findById($statusId);

    if (!$status) {
        sendJsonError('Order status not found', 404);
    }

    // Check if status is being used by any orders
    $ordersUsingStatus = $orderStatusRepository->isInUse($statusId);
    if ($ordersUsingStatus > 0) {
        sendJsonError('Cannot delete order status that is being used by ' . $ordersUsingStatus . ' order(s)', 400);
    }

    $orderStatusRepository->delete($statusId);

    sendJsonResponse([
        'success' => true,
        'message' => 'Order status deleted successfully',
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
