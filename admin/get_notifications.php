<?php
session_start();

header('Content-Type: application/json');

require_once(__DIR__ . '/../include/connect.php');
require_once(__DIR__ . '/include/functions.php');

// Check admin authentication
if (!checkAdminLogin()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as pending_count
        FROM orders 
        WHERE status_id = 1
    ");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    $pendingCount = (int)$result['pending_count'];

    $stmt = $pdo->prepare("
        SELECT 
            o.id,
            o.amount,
            o.created_at,
            CONCAT(u.first_name, ' ', u.last_name) as customer_name
        FROM orders o
        LEFT JOIN users u ON o.user_id = u.id
        WHERE o.status_id = 1
        ORDER BY o.created_at DESC
        LIMIT 5
    ");
    $stmt->execute();
    $pendingOrders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'pending_count' => $pendingCount,
        'orders' => $pendingOrders
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error fetching notifications'
    ]);
}
