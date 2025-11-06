<?php

class OrderProcessingRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }


    public function createOrder(
        int $userId, 
        float $totalAmount, 
        array $items, 
        string $orderAddress, 
        ?string $message = null
    ): int {
        $this->pdo->beginTransaction();

        try {
        
            $orderId = $this->insertOrder($userId, $totalAmount, $orderAddress, $message);

            // Insert order items and addons
            foreach ($items as $item) {
                $orderItemId = $this->insertOrderItem($orderId, $item);
                
                if (!empty($item['addons'])) {
                    $this->insertOrderItemAddons($orderItemId, $item['addons']);
                }
            }

            $this->pdo->commit();

            return $orderId;
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }


    private function insertOrder(
        int $userId, 
        float $totalAmount, 
        string $orderAddress, 
        ?string $message
    ): int {
        $sql = "INSERT INTO orders (user_id, amount, status_id, message, order_address, status, created_at) 
                VALUES (?, ?, 1, ?, ?, 'pending', NOW())";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            $userId,
            $totalAmount,
            $message,
            $orderAddress
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    private function insertOrderItem(int $orderId, array $item): int
    {
        // Calculate subtotal 
        $subtotal = $item['item_price'] * $item['quantity'];

        $sql = "INSERT INTO order_items (order_id, product_id, unit_price, qty, subtotal) 
                VALUES (?, ?, ?, ?, ?)";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            $orderId,
            $item['product_id'],
            $item['price'], 
            $item['quantity'],
            $subtotal
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    private function insertOrderItemAddons(int $orderItemId, array $addons): void
    {
        $sql = "INSERT INTO order_item_addons (order_item_id, addon_id, price) 
                VALUES (?, ?, ?)";
        
        $stmt = $this->pdo->prepare($sql);

        foreach ($addons as $addon) {
            $stmt->execute([
                $orderItemId,
                $addon['id'],
                $addon['price']
            ]);
        }
    }
}
