<?php
session_start();

header('Content-Type: application/json');

require_once(__DIR__ . '/Cart.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$cartKey = $_POST['cart_key'] ?? '';

if (empty($cartKey)) {
    echo json_encode(['success' => false, 'message' => 'Invalid cart item']);
    exit;
}

try {
    $cart = new Cart();
    
    if (!$cart->remove($cartKey)) {
        echo json_encode(['success' => false, 'message' => 'Item not found in cart']);
        exit;
    }
    
    $cartData = $cart->getData();
    
    echo json_encode([
        'success' => true,
        'message' => 'Item removed',
        'cart_count' => $cartData['cart_count'],
        'cart_total' => $cartData['cart_total'],
        'items' => $cartData['items']
    ]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'An error occurred']);
}
