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

$productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
$addons = isset($_POST['addons']) ? $_POST['addons'] : [];

try {
    $cart = new Cart($pdo);
    
    if (!$cart->add($productId, $quantity, $addons)) {
        echo json_encode(['success' => false, 'message' => 'Failed to add product to cart']);
        exit;
    }
    
    $cartData = $cart->getData();
    
    echo json_encode([
        'success' => true,
        'message' => 'Product added to cart!',
        'cart_count' => $cartData['cart_count'],
        'cart_total' => $cartData['cart_total'],
        'items' => $cartData['items']
    ]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'An error occurred']);
}
