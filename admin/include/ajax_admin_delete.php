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

$admin_id = isset($_POST['admin_id']) ? (int)$_POST['admin_id'] : 0;
if ($admin_id <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid admin ID']);
    exit;
}

if ($admin_id === (int)$current_admin['admin_id']) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'You cannot delete your own account']);
    exit;
}

try {
    // Check exists & super admin count safety
    $stmt = $pdo->prepare('SELECT admin_id, is_super_admin FROM admins WHERE admin_id = ? LIMIT 1');
    $stmt->execute([$admin_id]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$admin) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Admin not found']);
        exit;
    }

    if ((int)$admin['is_super_admin'] === 1) {
        $countStmt = $pdo->query("SELECT COUNT(*) FROM admins WHERE is_super_admin = 1");
        $superCount = (int)$countStmt->fetchColumn();
        if ($superCount <= 1) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Cannot delete the last super administrator']);
            exit;
        }
    }

    $del = $pdo->prepare('DELETE FROM admins WHERE admin_id = ? LIMIT 1');
    $del->execute([$admin_id]);

    echo json_encode(['success' => true, 'message' => 'Administrator deleted', 'admin_id' => $admin_id]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
