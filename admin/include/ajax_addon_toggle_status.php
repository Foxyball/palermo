<?php

declare(strict_types=1);

header('Content-Type: application/json');

require_once __DIR__ . '/../../include/connect.php';
require_once __DIR__ . '/functions.php';

requireAdminLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonError('Invalid request method', 405);
}

$addonId = isset($_POST['id']) ? (int)$_POST['id'] : 0;

if ($addonId <= 0) {
    sendJsonError('Invalid addon ID', 400);
}

try {
    $addon = fetchAddonById($pdo, $addonId);

    if (!$addon) {
        sendJsonError('Addon not found', 404);
    }

    $newStatus = toggleAddonStatus($pdo, $addonId, $addon['status']);

    sendJsonResponse(['success' => true, 'message' => 'Status updated', 'status' => $newStatus,]);
} catch (Throwable $e) {
    sendJsonError('Server error', 500);
}

function fetchAddonById(PDO $pdo, int $id): ?array
{
    $stmt = $pdo->prepare('SELECT id, status FROM addons WHERE id = ? LIMIT 1');
    $stmt->execute([$id]);
    $addon = $stmt->fetch(PDO::FETCH_ASSOC);

    return $addon ?: null;
}

function toggleAddonStatus(PDO $pdo, int $id, string $currentStatus): string
{
    $newStatus = $currentStatus === '1' ? '0' : '1';

    $stmt = $pdo->prepare('UPDATE addons SET status = ?, updated_at = NOW() WHERE id = ? LIMIT 1');
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
