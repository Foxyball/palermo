<?php

class OrderStatusRepository
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

        $sql = 'SELECT id, name, active, created_at
                FROM order_statuses' . $whereSql . ' ORDER BY id ASC LIMIT :lim OFFSET :off';

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

        $sql = 'SELECT COUNT(*) FROM order_statuses' . $whereSql;
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return (int) $stmt->fetchColumn();
    }


    public function findById(int $id): ?array
    {
        $sql = 'SELECT id, name, active, created_at FROM order_statuses WHERE id = ? LIMIT 1';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ?: null;
    }

    public function getActiveStatuses(): array
    {
        $sql = 'SELECT id, name FROM order_statuses WHERE active = "1" ORDER BY id ASC';
        $stmt = $this->pdo->query($sql);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function toggleActive(int $id, string $currentStatus): string
    {
        $newStatus = $currentStatus === '1' ? '0' : '1';

        $stmt = $this->pdo->prepare(
            'UPDATE order_statuses SET active = ?, updated_at = NOW() WHERE id = ? LIMIT 1'
        );
        $stmt->execute([$newStatus, $id]);

        return $newStatus;
    }


    public function delete(int $id): bool
    {
        try {
            $stmt = $this->pdo->prepare('DELETE FROM order_statuses WHERE id = ? LIMIT 1');
            $stmt->execute([$id]);
            return true;
        } catch (Exception $e) {
            error_log('Order status deletion failed: ' . $e->getMessage());

            return false;
        }
    }


    public function isInUse(int $statusId): int
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM orders WHERE status_id = ?');
        $stmt->execute([$statusId]);

        return (int) $stmt->fetchColumn();
    }

    public function isNameUnique(string $name, int $excludeId = 0): bool
    {
        if ($excludeId > 0) {
            $stmt = $this->pdo->prepare('SELECT id FROM order_statuses WHERE name = ? AND id != ? LIMIT 1');
            $stmt->execute([$name, $excludeId]);
        } else {
            $stmt = $this->pdo->prepare('SELECT id FROM order_statuses WHERE name = ? LIMIT 1');
            $stmt->execute([$name]);
        }

        return !$stmt->fetch();
    }


    public function create(string $name, string $active = '1'): int
    {
        $sql = 'INSERT INTO order_statuses (name, active, created_at) VALUES (?, ?, NOW())';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$name, $active]);

        return (int) $this->pdo->lastInsertId();
    }


    public function update(int $id, string $name, string $active): bool
    {
        try {
            $sql = 'UPDATE order_statuses SET name = ?, active = ?, updated_at = NOW() WHERE id = ? LIMIT 1';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$name, $active, $id]);

            return true;
        } catch (Exception $e) {
            error_log('Order status update failed: ' . $e->getMessage());

            return false;
        }
    }
}
