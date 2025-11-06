<?php

header('Content-Type: application/json');

require_once(__DIR__ . '/connect.php');
require_once(__DIR__ . '/Cart.php');
require_once(__DIR__ . '/../repositories/frontend/OrderProcessingRepository.php');

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

    // Create order using repository
    $orderRepo = new OrderProcessingRepository($pdo);
    $orderId = $orderRepo->createOrder($userId, $totalAmount, $items, $orderAddress, $message);

    // Clear cart after successful order
    $cart->clear();

    echo json_encode([
        'success' => true,
        'message' => 'Order placed successfully',
        'order_id' => $orderId,
        'redirect' => BASE_URL . 'thank-you'
    ]);
} catch (PDOException $e) {
    // Log error for debugging
    error_log('Order processing error: ' . $e->getMessage());

    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while processing your order. Please try again.'
    ]);
}

