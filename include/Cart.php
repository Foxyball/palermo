<?php

declare(strict_types=1);

class Cart
{
    private const MAX_QUANTITY = 99;
    private const MIN_QUANTITY = 1;

    private $pdo;

    public function __construct($pdo = null)
    {
        $this->pdo = $pdo;
        $this->initialize();
    }

    private function initialize(): void
    {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
    }

    public function add(int $productId, int $quantity, array $addons = []): bool
    {
        if (!$this->validateQuantity($quantity) || $productId <= 0) {
            return false;
        }

        $productRepo = $this->getProductRepository();
        $product = $productRepo->getByIdForCart($productId);

        if (!$product) {
            return false;
        }

        $addonDetails = [];
        $addonsTotal = 0.0;

        if (!empty($addons)) {
            $addonDetails = $productRepo->getAddonsByIds($addons);
            $addonsTotal = $this->calculateAddonsTotal($addonDetails);
        }

        $cartKey = $this->generateCartKey($productId, $addons);
        $itemPrice = $product['price'] + $addonsTotal;

        if (isset($_SESSION['cart'][$cartKey])) {
            $_SESSION['cart'][$cartKey]['quantity'] += $quantity;
        } else {
            $_SESSION['cart'][$cartKey] = [
                'product_id' => $productId,
                'name' => $product['name'],
                'slug' => $product['slug'],
                'image' => $product['image'],
                'price' => (float)$product['price'],
                'addons' => $addonDetails,
                'addons_total' => $addonsTotal,
                'item_price' => $itemPrice,
                'quantity' => $quantity
            ];
        }

        return true;
    }

    public function remove(string $cartKey): bool
    {
        if (isset($_SESSION['cart'][$cartKey])) {
            unset($_SESSION['cart'][$cartKey]);
            return true;
        }

        return false;
    }

    public function updateQuantity(string $cartKey, int $quantity): bool
    {
        if (!isset($_SESSION['cart'][$cartKey])) {
            return false;
        }

        if ($quantity <= 0) {
            return $this->remove($cartKey);
        }

        if ($this->validateQuantity($quantity)) {
            $_SESSION['cart'][$cartKey]['quantity'] = $quantity;
            return true;
        }

        return false;
    }

    public function clear(): void
    {
        $_SESSION['cart'] = [];
    }

    public function getItems(): array
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

    public function getTotals(): array
    {
        $count = 0;
        $total = 0.0;

        foreach ($_SESSION['cart'] as $item) {
            $count += $item['quantity'];
            $total += $item['item_price'] * $item['quantity'];
        }

        return [
            'count' => $count,
            'total' => $total
        ];
    }

    public function getData(): array
    {
        $totals = $this->getTotals();

        return [
            'items' => $this->getItems(),
            'cart_count' => $totals['count'],
            'cart_total' => $totals['total']
        ];
    }

    public function isEmpty(): bool
    {
        return empty($_SESSION['cart']);
    }

    private function validateQuantity(int $quantity): bool
    {
        return $quantity >= self::MIN_QUANTITY && $quantity <= self::MAX_QUANTITY;
    }

    private function calculateAddonsTotal(array $addonDetails): float
    {
        $total = 0.0;

        foreach ($addonDetails as $addon) {
            $total += (float)$addon['price'];
        }

        return $total;
    }

    private function getProductRepository()
    {
        require_once __DIR__ . '/../repositories/frontend/ProductRepository.php';
        return new ProductRepository($this->pdo);
    }

    private function generateCartKey(int $productId, array $addons = []): string
    {
        $addonIds = array_map('intval', $addons);
        sort($addonIds);

        return $productId . '_' . implode('-', $addonIds);
    }
}

