<?php
header('Content-Type: application/json');

require_once(__DIR__ . '/../../include/connect.php');
require_once(__DIR__ . '/functions.php');

requireAdminLogin();

$current_admin = getCurrentAdmin($pdo);
if (!isCurrentSuperAdmin($current_admin)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Permission denied']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Invalid method']);
    exit;
}

$admin_id = $_POST['admin_id'] ?? 0;
if ($admin_id <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid admin ID']);
    exit;
}

if ($admin_id === (int)$current_admin['admin_id']) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'You cannot change your own status']);
    exit;
}

try {
    $stmt = $pdo->prepare('SELECT admin_id, active FROM admins WHERE admin_id = ? LIMIT 1');
    $stmt->execute([$admin_id]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$admin) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Admin not found']);
        exit;
    }

    $newStatus = $admin['active'] === '1' ? '0' : '1';
    $upd = $pdo->prepare('UPDATE admins SET active = ?, updated_at = NOW() WHERE admin_id = ? LIMIT 1');
    $upd->execute([$newStatus, $admin_id]);

    echo json_encode([
        'success' => true,
        'message' => 'Status updated',
        'admin_id' => $admin_id,
        'active' => $newStatus,
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
