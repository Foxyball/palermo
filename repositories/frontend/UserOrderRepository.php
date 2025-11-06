<?php

class UserOrderRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getUserOrders(int $userId, int $limit = 50, int $offset = 0): array
    {
        $sql = 'SELECT 
                o.id, 
                o.amount,
                o.status_id,
                o.message,
                o.order_address,
                o.created_at,
                os.name AS status_name
                FROM orders o
                LEFT JOIN order_statuses os ON o.status_id = os.id
                WHERE o.user_id = :user_id
                ORDER BY o.created_at DESC 
                LIMIT :lim OFFSET :off';

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':off', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function countUserOrders(int $userId): int
    {
        $sql = 'SELECT COUNT(*) FROM orders WHERE user_id = ?';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$userId]);

        return (int)$stmt->fetchColumn();
    }


    public function getUserOrderDetails(int $orderId, int $userId): ?array
    {
        $sql = 'SELECT 
                o.id, 
                o.amount,
                o.status_id,
                o.message,
                o.order_address,
                o.created_at,
                os.name AS status_name
                FROM orders o
                LEFT JOIN order_statuses os ON o.status_id = os.id
                WHERE o.id = :order_id AND o.user_id = :user_id
                LIMIT 1';

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':order_id', $orderId, PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();

        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$order) {
            return null;
        }

        $order['items'] = $this->getOrderItems($orderId);

        return $order;
    }


    private function getOrderItems(int $orderId): array
    {
        $sql = 'SELECT 
                oi.id,
                oi.product_id,
                oi.unit_price,
                oi.qty,
                oi.subtotal,
                p.name AS product_name,
                p.image AS product_image
                FROM order_items oi
                LEFT JOIN products p ON oi.product_id = p.id
                WHERE oi.order_id = ?
                ORDER BY oi.id ASC';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$orderId]);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get addons for each item
        foreach ($items as &$item) {
            $item['addons'] = $this->getItemAddons($item['id']);
        }

        return $items;
    }


    private function getItemAddons(int $orderItemId): array
    {
        $sql = 'SELECT 
                oia.addon_id,
                oia.price AS addon_price,
                a.name AS addon_name
                FROM order_item_addons oia
                LEFT JOIN addons a ON oia.addon_id = a.id
                WHERE oia.order_item_id = ?
                ORDER BY a.name ASC';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$orderItemId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
