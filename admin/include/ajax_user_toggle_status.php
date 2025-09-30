<?php
header('Content-Type: application/json');

require_once(__DIR__ . '/../../include/connect.php');
require_once(__DIR__ . '/functions.php');

requireAdminLogin();


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Invalid method']);
    exit;
}

$userId = $_POST['user_id'] ?? 0;
if ($userId <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid user ID']);
    exit;
}

try {
    $stmt = $pdo->prepare('SELECT id, active FROM users WHERE id = ? LIMIT 1');
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$user) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'User not found']);
        exit;
    }

    $newStatus = $user['active'] === '1' ? '0' : '1';
    $upd = $pdo->prepare('UPDATE users SET active = ?, updated_at = NOW() WHERE id = ? LIMIT 1');
    $upd->execute([$newStatus, $userId]);

    echo json_encode([
        'success' => true,
        'message' => 'Status updated',
        'userId' => $userId,
        'active' => $newStatus,
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
