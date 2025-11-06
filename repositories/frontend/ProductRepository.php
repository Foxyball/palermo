<?php

class ProductRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getByCategorySlug(string $categorySlug, int $limit = 12, int $offset = 0): array
    {
        $sql = 'SELECT 
                p.id,
                p.name,
                p.slug,
                p.image,
                p.price,
                p.short_description,
                p.long_description,
                c.name AS category_name,
                c.slug AS category_slug
                FROM products p
                INNER JOIN categories c ON p.category_id = c.id
                WHERE c.slug = :slug AND p.active = "1" AND c.active = "1"
                ORDER BY p.id DESC
                LIMIT :limit OFFSET :offset';
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':slug', $categorySlug, PDO::PARAM_STR);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countByCategorySlug(string $categorySlug): int
    {
        $sql = 'SELECT COUNT(*) 
                FROM products p
                INNER JOIN categories c ON p.category_id = c.id
                WHERE c.slug = ? AND p.active = "1" AND c.active = "1"';
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$categorySlug]);

        return (int) $stmt->fetchColumn();
    }

    public function getBySlug(string $slug): ?array
    {
        $sql = 'SELECT 
                p.id,
                p.name,
                p.slug,
                p.image,
                p.price,
                p.short_description,
                p.long_description,
                p.created_at,
                c.name AS category_name,
                c.slug AS category_slug
                FROM products p
                INNER JOIN categories c ON p.category_id = c.id
                WHERE p.slug = ? AND p.active = "1"
                LIMIT 1';
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$slug]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ?: null;
    }

    public function getProductAddons(int $productId): array
    {
        $sql = 'SELECT 
                a.id,
                a.name,
                a.price
                FROM addons a
                INNER JOIN product_addons pa ON a.id = pa.addon_id
                WHERE pa.product_id = ? AND a.status = "1"
                ORDER BY a.name ASC';
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$productId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
