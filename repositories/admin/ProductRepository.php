<?php

class ProductRepository
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
            $whereSql = ' WHERE (p.name LIKE :keyword OR p.id = :id)';
            $params[':keyword'] = '%' . $search . '%';
            $params[':id'] = $search;
        }

        $sql = 'SELECT 
                p.id, 
                p.name,
                p.category_id, 
                p.image,
                p.price,
                c.name AS category_name,
                p.active, 
                p.created_at
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id'
            . $whereSql . ' ORDER BY p.id DESC LIMIT :lim OFFSET :off';

        $stmt = $this->pdo->prepare($sql);

        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v, PDO::PARAM_STR);
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
            $whereSql = ' WHERE (p.name LIKE :keyword OR p.id = :id)';
            $params[':keyword'] = '%' . $search . '%';
            $params[':id'] = $search;
        }

        $sql = 'SELECT COUNT(*) FROM products p' . $whereSql;
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return (int) $stmt->fetchColumn();
    }

    public function findById(int $id): ?array
    {
        $sql = 'SELECT * FROM products WHERE id = ? LIMIT 1';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ?: null;
    }

    public function toggleActive(int $id, string $currentStatus): string
    {
        $newStatus = $currentStatus === '1' ? '0' : '1';

        $stmt = $this->pdo->prepare(
            'UPDATE products SET active = ?, updated_at = NOW() WHERE id = ? LIMIT 1'
        );
        $stmt->execute([$newStatus, $id]);

        return $newStatus;
    }

    public function delete(int $id): bool
    {
        try {
            $stmt = $this->pdo->prepare('DELETE FROM products WHERE id = ? LIMIT 1');
            $stmt->execute([$id]);
            return true;
        } catch (Exception $e) {
            error_log('Product deletion failed: ' . $e->getMessage());

            return false;
        }
    }

    public function slugExists(string $slug, ?int $excludeId = null): bool
    {
        if ($excludeId !== null) {
            $sql = 'SELECT id FROM products WHERE slug = ? AND id != ? LIMIT 1';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$slug, $excludeId]);
        } else {
            $sql = 'SELECT id FROM products WHERE slug = ? LIMIT 1';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$slug]);
        }

        return $stmt->fetch() !== false;
    }

    public function create(
        int $categoryId,
        string $name,
        string $slug,
        ?string $image,
        float $price,
        string $shortDescription,
        string $longDescription
    ): int {
        $sql = 'INSERT INTO products (category_id, name, slug, image, price, active, short_description, long_description, created_at, updated_at) 
                VALUES (?, ?, ?, ?, ?, "1", ?, ?, NOW(), NOW())';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$categoryId, $name, $slug, $image, $price, $shortDescription, $longDescription]);

        return (int) $this->pdo->lastInsertId();
    }

    public function update(
        int $id,
        int $categoryId,
        string $name,
        string $slug,
        ?string $image,
        float $price,
        string $shortDescription,
        string $longDescription
    ): bool {
        try {
            $sql = 'UPDATE products 
                    SET category_id = ?, name = ?, slug = ?, image = ?, price = ?, 
                        short_description = ?, long_description = ?, updated_at = NOW() 
                    WHERE id = ? LIMIT 1';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$categoryId, $name, $slug, $image, $price, $shortDescription, $longDescription, $id]);

            return true;
        } catch (Exception $e) {
            error_log('Product update failed: ' . $e->getMessage());

            return false;
        }
    }

    public function getProductAddons(int $productId): array
    {
        $sql = 'SELECT addon_id FROM product_addons WHERE product_id = ?';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$productId]);

        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function syncAddons(int $productId, array $addonIds): void
    {
        // Delete existing addons
        $stmt = $this->pdo->prepare('DELETE FROM product_addons WHERE product_id = ?');
        $stmt->execute([$productId]);

        // Insert new addons
        if (!empty($addonIds)) {
            $stmt = $this->pdo->prepare('INSERT INTO product_addons (product_id, addon_id) VALUES (?, ?)');
            foreach ($addonIds as $addonId) {
                if (is_numeric($addonId)) {
                    $stmt->execute([$productId, $addonId]);
                }
            }
        }
    }

    public function deleteProductAddons(int $productId): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM product_addons WHERE product_id = ?');
        $stmt->execute([$productId]);
    }
}
