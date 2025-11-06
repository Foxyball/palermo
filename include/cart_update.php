<?php
session_start();

header('Content-Type: application/json');

require_once(__DIR__ . '/connect.php');
require_once(__DIR__ . '/Cart.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

if (!isset($_SESSION['user_logged_in']) || !$_SESSION['user_logged_in']) {
    echo json_encode([
        'success' => false, 
        'redirect' => 'login'
    ]);
    exit;
}

$cartKey = isset($_POST['cart_key']) ? $_POST['cart_key'] : '';
$quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;

if (empty($cartKey)) {
    echo json_encode(['success' => false, 'message' => 'Cart key is required']);
    exit;
}

try {
    $cart = new Cart($pdo);
    
    if (!$cart->updateQuantity($cartKey, $quantity)) {
        echo json_encode(['success' => false, 'message' => 'Failed to update quantity']);
        exit;
    }
    
    $cartData = $cart->getData();
    
    echo json_encode([
        'success' => true,
        'message' => 'Quantity updated',
        'cart_count' => $cartData['cart_count'],
        'cart_total' => $cartData['cart_total'],
        'items' => $cartData['items']
    ]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'An error occurred']);
}
