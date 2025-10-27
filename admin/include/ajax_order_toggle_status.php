<?php

declare(strict_types=1);

header('Content-Type: application/json');

require_once __DIR__ . '/../../include/connect.php';
require_once __DIR__ . '/functions.php';

requireAdminLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonError('Invalid request method', 405);
}

$statusID = isset($_POST['id']) ? (int) $_POST['id'] : 0;

if ($statusID <= 0) {
    sendJsonError('Invalid order status ID', 400);
}

try {
    $status = fetchOrderStatusById($pdo, $statusID);

    if (!$status) {
        sendJsonError('Order status not found', 404);
    }

    $newStatus = toggleOrderStatusActive($pdo, $statusID, $status['active']);

    sendJsonResponse([
        'success' => true,
        'message' => 'Status updated',
        'status' => $newStatus,
    ]);
} catch (Throwable $e) {
    sendJsonError('Server error', 500);
}

function fetchOrderStatusById(PDO $pdo, int $id): ?array
{
    $stmt = $pdo->prepare('SELECT id, active FROM order_statuses WHERE id = ? LIMIT 1');
    $stmt->execute([$id]);
    $status = $stmt->fetch(PDO::FETCH_ASSOC);

    return $status ?: null;
}

function toggleOrderStatusActive(PDO $pdo, int $id, string $currentStatus): string
{
    $newStatus = $currentStatus === '1' ? '0' : '1';

    $stmt = $pdo->prepare(
        'UPDATE order_statuses SET active = ?, updated_at = NOW() WHERE id = ? LIMIT 1'
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