<?php

class AddonRepository
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
            $whereSql = ' WHERE (name LIKE :keyword OR id = :id)';
            $params[':keyword'] = '%' . $search . '%';
            $params[':id'] = $search;
        }

        $sql = 'SELECT id, name, price, status, created_at
                FROM addons' . $whereSql . ' ORDER BY id DESC LIMIT :lim OFFSET :off';

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
            $whereSql = ' WHERE (name LIKE :keyword OR id = :id)';
            $params[':keyword'] = '%' . $search . '%';
            $params[':id'] = $search;
        }

        $sql = 'SELECT COUNT(*) FROM addons' . $whereSql;
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return (int) $stmt->fetchColumn();
    }


    public function findById(int $id): ?array
    {
        $sql = 'SELECT id, name, price, status, created_at, updated_at FROM addons WHERE id = ? LIMIT 1';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ?: null;
    }


    public function getActive(): array
    {
        $sql = 'SELECT id, name, price FROM addons WHERE status = "1" ORDER BY name ASC';
        $stmt = $this->pdo->query($sql);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function toggleStatus(int $id, string $currentStatus): string
    {
        $newStatus = $currentStatus === '1' ? '0' : '1';

        $stmt = $this->pdo->prepare(
            'UPDATE addons SET status = ?, updated_at = NOW() WHERE id = ? LIMIT 1'
        );
        $stmt->execute([$newStatus, $id]);

        return $newStatus;
    }

 
    public function delete(int $id): bool
    {
        try {
            $stmt = $this->pdo->prepare('DELETE FROM addons WHERE id = ? LIMIT 1');
            $stmt->execute([$id]);

            return true;
        } catch (Exception $e) {
            error_log('Addon deletion failed: ' . $e->getMessage());

            return false;
        }
    }


    public function nameExists(string $name, ?int $excludeId = null): bool
    {
        if ($excludeId !== null) {
            $sql = 'SELECT id FROM addons WHERE name = ? AND id != ? LIMIT 1';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$name, $excludeId]);
        } else {
            $sql = 'SELECT id FROM addons WHERE name = ? LIMIT 1';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$name]);
        }

        return $stmt->fetch() !== false;
    }

 
    public function create(string $name, float $price, string $status = '1'): int
    {
        $sql = 'INSERT INTO addons (name, price, status, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$name, $price, $status]);

        return (int) $this->pdo->lastInsertId();
    }


    public function update(int $id, string $name, float $price): bool
    {
        try {
            $sql = 'UPDATE addons SET name = ?, price = ?, updated_at = NOW() WHERE id = ? LIMIT 1';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$name, $price, $id]);

            return true;
        } catch (Exception $e) {
            error_log('Addon update failed: ' . $e->getMessage());

            return false;
        }
    }


    public function isInUse(int $addonId): int
    {
        // Check order_item_addons
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM order_item_addons WHERE addon_id = ?');
        $stmt->execute([$addonId]);
        $orderCount = (int) $stmt->fetchColumn();

        // Check product_addons
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM product_addons WHERE addon_id = ?');
        $stmt->execute([$addonId]);
        $productCount = (int) $stmt->fetchColumn();

        return $orderCount + $productCount;
    }
}