<?php

declare(strict_types=1);

header('Content-Type: application/json');

require_once __DIR__ . '/../../include/connect.php';
require_once __DIR__ . '/functions.php';

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
    $admin = fetchAdminById($pdo, $adminId);

    if (!$admin) {
        sendJsonError('Admin not found', 404);
    }

    $newStatus = toggleAdminStatus($pdo, $adminId, $admin['active']);

    sendJsonResponse([
        'success' => true,
        'message' => 'Status updated',
        'admin_id' => $adminId,
        'active' => $newStatus,
    ]);
} catch (Throwable $e) {
    sendJsonError('Server error', 500);
}

function fetchAdminById(PDO $pdo, int $id): ?array
{
    $stmt = $pdo->prepare('SELECT admin_id, active FROM admins WHERE admin_id = ? LIMIT 1');
    $stmt->execute([$id]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    return $admin ?: null;
}

function toggleAdminStatus(PDO $pdo, int $id, string $currentStatus): string
{
    $newStatus = $currentStatus === '1' ? '0' : '1';

    $stmt = $pdo->prepare(
        'UPDATE admins SET active = ?, updated_at = NOW() WHERE admin_id = ? LIMIT 1'
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
