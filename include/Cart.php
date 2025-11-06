<?php

class Cart
{
    private $pdo;
    
    public function __construct($pdo = null)
    {
        $this->pdo = $pdo;
        $this->initialize();
    }
    

    private function initialize()
    {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
    }
    

    public function add($productId, $quantity, $addons = [])
    {
        if ($productId <= 0 || $quantity < 1 || $quantity > 99) {
            return false;
        }
        
        // Get product details
        require_once(__DIR__ . '/../repositories/frontend/ProductRepository.php');
        $productRepo = new ProductRepository($this->pdo);
        $product = $productRepo->getByIdForCart($productId);
        
        if (!$product) {
            return false;
        }
        
        // Get addon details
        $addonDetails = [];
        $addonsTotal = 0;
        if (!empty($addons)) {
            $addonDetails = $productRepo->getAddonsByIds($addons);
            foreach ($addonDetails as $addon) {
                $addonsTotal += $addon['price'];
            }
        }
        
        // Create unique  key
        $cartKey = $this->generateCartKey($productId, $addons);
        $itemPrice = $product['price'] + $addonsTotal;
        
        // Add or update cart item
        if (isset($_SESSION['cart'][$cartKey])) {
            $_SESSION['cart'][$cartKey]['quantity'] += $quantity;
        } else {
            $_SESSION['cart'][$cartKey] = [
                'product_id' => $productId,
                'name' => $product['name'],
                'slug' => $product['slug'],
                'image' => $product['image'],
                'price' => $product['price'],
                'addons' => $addonDetails,
                'addons_total' => $addonsTotal,
                'item_price' => $itemPrice,
                'quantity' => $quantity
            ];
        }
        
        return true;
    }
    
    /**
     * Remove item from cart
     */
    public function remove($cartKey)
    {
        if (isset($_SESSION['cart'][$cartKey])) {
            unset($_SESSION['cart'][$cartKey]);
            return true;
        }
        return false;
    }
    
    /**
     * Update item quantity
     */
    public function updateQuantity($cartKey, $quantity)
    {
        if (isset($_SESSION['cart'][$cartKey])) {
            if ($quantity > 0 && $quantity <= 99) {
                $_SESSION['cart'][$cartKey]['quantity'] = $quantity;
                return true;
            } elseif ($quantity <= 0) {
                return $this->remove($cartKey);
            }
        }
        return false;
    }
    
    /**
     * Clear entire cart
     */
    public function clear()
    {
        $_SESSION['cart'] = [];
    }
    
    /**
     * Get all cart items with calculated totals
     */
    public function getItems()
    {
        $items = [];
        foreach ($_SESSION['cart'] as $cartKey => $item) {
            $items[] = array_merge($item, [
                'key' => $cartKey,
                'item_total' => $item['item_price'] * $item['quantity']
            ]);
        }
        return $items;
    }
    
    /**
     * Get cart totals
     */
    public function getTotals()
    {
        $count = 0;
        $total = 0;
        
        foreach ($_SESSION['cart'] as $item) {
            $count += $item['quantity'];
            $total += $item['item_price'] * $item['quantity'];
        }
        
        return [
            'count' => $count,
            'total' => $total
        ];
    }
    
    /**
     * Get complete cart data
     */
    public function getData()
    {
        $totals = $this->getTotals();
        
        return [
            'items' => $this->getItems(),
            'cart_count' => $totals['count'],
            'cart_total' => $totals['total']
        ];
    }
    
    /**
     * Check if cart is empty
     */
    public function isEmpty()
    {
        return empty($_SESSION['cart']);
    }
    
    /**
     * Generate unique cart key for product + addons combination
     */
    private function generateCartKey($productId, $addons = [])
    {
        $addonIds = array_map('intval', $addons);
        sort($addonIds);
        return $productId . '_' . implode('-', $addonIds);
    }
}
