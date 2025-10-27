<?php

class OrderRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }


    public function findAll(string $search = '', int $limit = 10, int $offset = 0): array
    {
        $whereSql = '';
        $params = [];

        if ($search !== '') {
            $whereSql = ' WHERE (o.id = :id OR u.email LIKE :keyword OR u.first_name LIKE :keyword OR u.last_name LIKE :keyword)';
            $params[':keyword'] = '%' . $search . '%';
            $params[':id'] = $search;
        }

        $sql = 'SELECT 
                o.id, 
                o.user_id,
                o.amount,
                o.status_id,
                o.created_at,
                CONCAT(u.first_name, " ", u.last_name) AS customer_name,
                u.email AS customer_email,
                u.phone AS customer_phone,
                os.name AS status_name
                FROM orders o
                LEFT JOIN users u ON o.user_id = u.id
                LEFT JOIN order_statuses os ON o.status_id = os.id'
            . $whereSql . ' ORDER BY o.id DESC LIMIT :lim OFFSET :off';

        $stmt = $this->pdo->prepare($sql);

        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }

        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':off', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function findByStatus(int $statusId, string $search = '', int $limit = 10, int $offset = 0): array
    {
        $whereSql = ' WHERE o.status_id = :status_id';
        $params = [':status_id' => $statusId];

        if ($search !== '') {
            $whereSql .= ' AND (o.id = :id OR u.email LIKE :keyword OR u.first_name LIKE :keyword OR u.last_name LIKE :keyword)';
            $params[':keyword'] = '%' . $search . '%';
            $params[':id'] = $search;
        }

        $sql = 'SELECT 
                o.id, 
                o.user_id,
                o.amount,
                o.status_id,
                o.created_at,
                CONCAT(u.first_name, " ", u.last_name) AS customer_name,
                u.email AS customer_email,
                u.phone AS customer_phone,
                os.name AS status_name
                FROM orders o
                LEFT JOIN users u ON o.user_id = u.id
                LEFT JOIN order_statuses os ON o.status_id = os.id'
            . $whereSql . ' ORDER BY o.created_at DESC LIMIT :lim OFFSET :off';

        $stmt = $this->pdo->prepare($sql);

        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }

        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':off', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function countAll(string $search = ''): int
    {
        $whereSql = '';
        $params = [];

        if ($search !== '') {
            $whereSql = ' WHERE (o.id = :id OR u.email LIKE :keyword OR u.first_name LIKE :keyword OR u.last_name LIKE :keyword)';
            $params[':keyword'] = '%' . $search . '%';
            $params[':id'] = $search;
        }

        $sql = 'SELECT COUNT(*) FROM orders o LEFT JOIN users u ON o.user_id = u.id' . $whereSql;
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return (int) $stmt->fetchColumn();
    }


    public function countByStatus(int $statusId, string $search = ''): int
    {
        $whereSql = ' WHERE o.status_id = :status_id';
        $params = [':status_id' => $statusId];

        if ($search !== '') {
            $whereSql .= ' AND (o.id = :id OR u.email LIKE :keyword OR u.first_name LIKE :keyword OR u.last_name LIKE :keyword)';
            $params[':keyword'] = '%' . $search . '%';
            $params[':id'] = $search;
        }

        $sql = 'SELECT COUNT(*) FROM orders o LEFT JOIN users u ON o.user_id = u.id' . $whereSql;
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return (int) $stmt->fetchColumn();
    }


    public function findById(int $orderId): ?array
    {
        $sql = 'SELECT 
                o.*, 
                CONCAT(u.first_name, " ", u.last_name) AS customer_name,
                u.email AS customer_email,
                u.phone AS customer_phone,
                u.address AS customer_address,
                u.city AS customer_city,
                u.zip_code AS customer_zip,
                os.name AS status_name
                FROM orders o
                LEFT JOIN users u ON o.user_id = u.id
                LEFT JOIN order_statuses os ON o.status_id = os.id
                WHERE o.id = :id';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $orderId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ?: null;
    }


    public function updateStatus(int $orderId, int $statusId): bool
    {
        $sql = 'UPDATE orders SET status_id = :status_id WHERE id = :id';
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':id' => $orderId,
            ':status_id' => $statusId
        ]);
    }


    public function delete(int $orderId): bool
    {
        try {
            $this->pdo->beginTransaction();

            // Delete order_item_addons first
            $sql = 'DELETE oia FROM order_item_addons oia
                    INNER JOIN order_items oi ON oia.order_item_id = oi.id
                    WHERE oi.order_id = :order_id';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':order_id' => $orderId]);

            // Delete order_items
            $sql = 'DELETE FROM order_items WHERE order_id = :order_id';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':order_id' => $orderId]);

            // Delete the order
            $sql = 'DELETE FROM orders WHERE id = :id';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id' => $orderId]);

            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log('Order deletion failed: ' . $e->getMessage());
            return false;
        }
    }

    public function getActiveStatuses(): array
    {
        $sql = 'SELECT id, name FROM order_statuses WHERE active = "1" ORDER BY id ASC';
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function getStatusIdByName(string $statusName): ?int
    {
        $sql = 'SELECT id FROM order_statuses WHERE LOWER(name) = LOWER(:name) LIMIT 1';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':name' => $statusName]);
        $result = $stmt->fetchColumn();

        return $result !== false ? (int) $result : null;
    }
}
