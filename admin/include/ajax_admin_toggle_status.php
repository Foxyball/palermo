<?php

declare(strict_types=1);

header('Content-Type: application/json');

require_once __DIR__ . '/../../include/connect.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/../../repositories/admin/AdminRepository.php';

requireAdminLogin();

$currentAdmin = getCurrentAdmin($pdo);

if (!isCurrentSuperAdmin($currentAdmin)) {
    sendJsonError('Permission denied', 403);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonError('Invalid request method', 405);
}

$adminId = isset($_POST['admin_id']) ? (int) $_POST['admin_id'] : 0;

if ($adminId <= 0) {
    sendJsonError('Invalid admin ID', 400);
}

if ($adminId === (int) $currentAdmin['admin_id']) {
    sendJsonError('You cannot change your own status', 400);
}

try {
    $adminRepository = new AdminRepository($pdo);
    $admin = $adminRepository->findById($adminId);

    if (!$admin) {
        sendJsonError('Admin not found', 404);
    }

    $newStatus = $adminRepository->toggleActive($adminId, $admin['active']);

    sendJsonResponse([
        'success' => true,
        'message' => 'Status updated',
        'admin_id' => $adminId,
        'active' => $newStatus,
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
