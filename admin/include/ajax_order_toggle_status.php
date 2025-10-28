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

$statusID = isset($_POST['id']) ? (int) $_POST['id'] : 0;

if ($statusID <= 0) {
    sendJsonError('Invalid order status ID', 400);
}

try {
    $orderStatusRepository = new OrderStatusRepository($pdo);
    $status = $orderStatusRepository->findById($statusID);

    if (!$status) {
        sendJsonError('Order status not found', 404);
    }

    $newStatus = $orderStatusRepository->toggleActive($statusID, $status['active']);

    sendJsonResponse([
        'success' => true,
        'message' => 'Status updated',
        'status' => $newStatus,
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