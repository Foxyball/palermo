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

$categoryId = $_POST['id'] ?? 0;
if ($categoryId <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid category ID']);
    exit;
}

try {
    $stmt = $pdo->prepare('SELECT id FROM categories WHERE id = ? LIMIT 1');
    $stmt->execute([$categoryId]);
    $category = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$category) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Category not found']);
        exit;
    }

    $del = $pdo->prepare('DELETE FROM categories WHERE id = ? LIMIT 1');
    $del->execute([$categoryId]);

    echo json_encode(['success' => true, 'message' => 'Category deleted', 'category_id' => $categoryId]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
