<?php

class CategoryRepository
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

        $sql = 'SELECT id, name, slug, active, created_at
                FROM categories' . $whereSql . ' ORDER BY id DESC LIMIT :lim OFFSET :off';

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

        $sql = 'SELECT COUNT(*) FROM categories' . $whereSql;
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return (int) $stmt->fetchColumn();
    }


    public function findById(int $id): ?array
    {
        $sql = 'SELECT id, name, slug, active, created_at, updated_at FROM categories WHERE id = ? LIMIT 1';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ?: null;
    }


    public function getActive(): array
    {
        $sql = 'SELECT id, name, slug FROM categories WHERE active = "1" ORDER BY name ASC';
        $stmt = $this->pdo->query($sql);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function toggleActive(int $id, string $currentStatus): string
    {
        $newStatus = $currentStatus === '1' ? '0' : '1';

        $stmt = $this->pdo->prepare(
            'UPDATE categories SET active = ?, updated_at = NOW() WHERE id = ? LIMIT 1'
        );
        $stmt->execute([$newStatus, $id]);

        return $newStatus;
    }


    public function delete(int $id): bool
    {
        try {
            $stmt = $this->pdo->prepare('DELETE FROM categories WHERE id = ? LIMIT 1');
            $stmt->execute([$id]);
            return true;

        } catch (Exception $e) {

            return false;
        }
    }


    public function nameExists(string $name, ?int $excludeId = null): bool
    {
        if ($excludeId !== null) {
            $sql = 'SELECT id FROM categories WHERE name = ? AND id != ? LIMIT 1';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$name, $excludeId]);
        } else {
            $sql = 'SELECT id FROM categories WHERE name = ? LIMIT 1';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$name]);
        }

        return $stmt->fetch() !== false;
    }


    public function create(string $name, string $slug, string $active = '1'): int
    {
        $sql = 'INSERT INTO categories (name, slug, active, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$name, $slug, $active]);

        return (int) $this->pdo->lastInsertId();
    }


    public function update(int $id, string $name, string $slug): bool
    {
        try {
            $sql = 'UPDATE categories SET name = ?, slug = ?, updated_at = NOW() WHERE id = ? LIMIT 1';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$name, $slug, $id]);

            return true;
        } catch (Exception $e) {

            return false;
        }
    }


    public function isInUse(int $categoryId): int
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM products WHERE category_id = ?');
        $stmt->execute([$categoryId]);

        return (int) $stmt->fetchColumn();
    }
}
