<?php
session_start();

header('Content-Type: application/json');

require_once(__DIR__ . '/Cart.php');

try {
    $cart = new Cart();
    $cartData = $cart->getData();
    
    echo json_encode([
        'success' => true,
        'cart_count' => $cartData['cart_count'],
        'cart_total' => $cartData['cart_total'],
        'items' => $cartData['items']
    ]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'An error occurred']);
}
