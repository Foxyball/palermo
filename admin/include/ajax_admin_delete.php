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
    sendJsonError('You cannot delete your own account', 400);
}

try {
    $admin = fetchAdminById($pdo, $adminId);

    if (!$admin) {
        sendJsonError('Admin not found', 404);
    }

    if (isSuperAdmin($admin) && isLastSuperAdmin($pdo)) {
        sendJsonError('Cannot delete the last super administrator', 400);
    }

    deleteAdmin($pdo, $adminId);

    sendJsonResponse([
        'success' => true,
        'message' => 'Administrator deleted successfully',
        'admin_id' => $adminId,
    ]);
} catch (Throwable $e) {
    sendJsonError('Server error', 500);
}

function fetchAdminById(PDO $pdo, int $id): ?array
{
    $stmt = $pdo->prepare('SELECT admin_id, is_super_admin FROM admins WHERE admin_id = ? LIMIT 1');
    $stmt->execute([$id]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    return $admin ?: null;
}

function isSuperAdmin(array $admin): bool
{
    return (int) ($admin['is_super_admin'] ?? 0) === 1;
}

function isLastSuperAdmin(PDO $pdo): bool
{
    $stmt = $pdo->query('SELECT COUNT(*) FROM admins WHERE is_super_admin = 1');
    return ((int) $stmt->fetchColumn()) <= 1;
}

function deleteAdmin(PDO $pdo, int $id): void
{
    $stmt = $pdo->prepare('DELETE FROM admins WHERE admin_id = ? LIMIT 1');
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
