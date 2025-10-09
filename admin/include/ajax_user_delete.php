<?php

declare(strict_types=1);

header('Content-Type: application/json');

require_once __DIR__ . '/../../include/connect.php';
require_once __DIR__ . '/functions.php';

requireAdminLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonError('Invalid request method', 405);
}

$userId = isset($_POST['user_id']) ? (int) $_POST['user_id'] : 0;

if ($userId <= 0) {
    sendJsonError('Invalid user ID', 400);
}

try {
    $user = fetchUserById($pdo, $userId);

    if (!$user) {
        sendJsonError('User not found', 404);
    }

    deleteUser($pdo, $userId);

    sendJsonResponse([
        'success' => true,
        'message' => 'User deleted successfully',
        'user_id' => $userId,
    ]);
} catch (Throwable $e) {
    sendJsonError('Server error', 500);
}

function fetchUserById(PDO $pdo, int $id): ?array
{
    $stmt = $pdo->prepare('SELECT id FROM users WHERE id = ? LIMIT 1');
    $stmt->execute([$id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    return $user ?: null;
}

function deleteUser(PDO $pdo, int $id): void
{
    $stmt = $pdo->prepare('DELETE FROM users WHERE id = ? LIMIT 1');
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
