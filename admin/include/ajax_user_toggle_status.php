<?php

declare(strict_types=1);

header('Content-Type: application/json');

require_once __DIR__ . '/../../include/connect.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/../../repositories/admin/UserRepository.php';

requireAdminLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonError('Invalid request method', 405);
}

$userId = isset($_POST['user_id']) ? (int) $_POST['user_id'] : 0;

if ($userId <= 0) {
    sendJsonError('Invalid user ID', 400);
}

try {
    $userRepository = new UserRepository($pdo);
    $user = $userRepository->findById($userId);

    if (!$user) {
        sendJsonError('User not found', 404);
    }

    $newStatus = $userRepository->toggleActive($userId, $user['active']);

    sendJsonResponse([
        'success' => true,
        'message' => 'Status updated',
        'user_id' => $userId,
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
