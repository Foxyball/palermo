<?php

header('Content-Type: application/json');

require_once(__DIR__ . '/connect.php');
require_once(__DIR__ . '/Cart.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Check if user is logged in
if (!isset($_SESSION['user_logged_in']) || !$_SESSION['user_logged_in']) {
    echo json_encode([
        'success' => false,
        'redirect' => BASE_URL . 'login',
        'message' => 'Please login to continue'
    ]);
    exit;
}

$userId = $_SESSION['user_id'];

$orderAddress = isset($_POST['order_address']) ? trim($_POST['order_address']) : '';
$message = isset($_POST['message']) ? trim($_POST['message']) : null;

if (empty($orderAddress)) {
    echo json_encode(['success' => false, 'message' => 'Delivery address is required']);
    exit;
}

try {
    $cart = new Cart($pdo);
    $cartData = $cart->getData();

    if (empty($cartData['items'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Your cart is empty',
            'redirect' => BASE_URL . 'cart'
        ]);
        exit;
    }

    $items = $cartData['items'];
    $totalAmount = $cartData['cart_total'];

    $pdo->beginTransaction();

    $stmt = $pdo->prepare("
        INSERT INTO orders (user_id, amount, status_id, message, order_address, status, created_at) 
        VALUES (?, ?, 1, ?, ?, 'pending', NOW())
    ");

    $stmt->execute([
        $userId,
        $totalAmount,
        $message,
        $orderAddress
    ]);

    $orderId = $pdo->lastInsertId();

    // Insert order items
    $itemStmt = $pdo->prepare("
        INSERT INTO order_items (order_id, product_id, unit_price, qty, subtotal) 
        VALUES (?, ?, ?, ?, ?)
    ");

    $addonStmt = $pdo->prepare("
        INSERT INTO order_item_addons (order_item_id, addon_id, price) 
        VALUES (?, ?, ?)
    ");

    foreach ($items as $item) {
        $subtotal = $item['item_price'] * $item['quantity'];

        // Insert order item WITHOUT addons
        $itemStmt->execute([
            $orderId,
            $item['product_id'],
            $item['price'],
            $item['quantity'],
            $subtotal
        ]);

        $orderItemId = $pdo->lastInsertId();

        // Insert addons, if any
        if (!empty($item['addons'])) {
            foreach ($item['addons'] as $addon) {
                $addonStmt->execute([
                    $orderItemId,
                    $addon['id'],
                    $addon['price']
                ]);
            }
        }
    }

    $pdo->commit();
    $cart->clear();

    echo json_encode([
        'success' => true,
        'message' => 'Order placed successfully',
        'order_id' => $orderId,
        'redirect' => BASE_URL . 'thank-you'
    ]);
} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }


    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while processing your order. Please try again.'
    ]);
}
