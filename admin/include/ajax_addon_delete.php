<?php

declare(strict_types=1);

header('Content-Type: application/json');

require_once __DIR__ . '/../../include/connect.php';
require_once __DIR__ . '/../../repositories/admin/AddonRepository.php';
require_once __DIR__ . '/functions.php';

requireAdminLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonError('Invalid request method', 405);
}

$addonId = isset($_POST['id']) ? (int) $_POST['id'] : 0;

if ($addonId <= 0) {
    sendJsonError('Invalid addon ID', 400);
}

try {
    $addonRepo = new AddonRepository($pdo);
    $addon = $addonRepo->findById($addonId);

    if (!$addon) {
        sendJsonError('Addon not found', 404);
    }

    // Check if addon is being used by orders or products
    $usageCount = $addonRepo->isInUse($addonId);
    if ($usageCount > 0) {
        sendJsonError("Cannot delete: This addon is used by $usageCount order(s) or product(s)", 409);
    }

    $deleted = $addonRepo->delete($addonId);

    if (!$deleted) {
        sendJsonError('Failed to delete addon', 500);
    }

    sendJsonResponse([
        'success' => true,
        'message' => 'Addon deleted successfully',
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
