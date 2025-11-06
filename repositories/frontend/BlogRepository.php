<?php

class BlogRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getLatest(int $limit = 3): array
    {
        $sql = 'SELECT 
                b.id,
                b.title,
                b.slug,
                b.description,
                b.image,
                b.created_at,
                c.name AS category_name,
                a.admin_name AS author_name
                FROM blogs b
                LEFT JOIN blog_categories c ON b.category_id = c.id
                LEFT JOIN admins a ON b.user_id = a.admin_id
                WHERE b.status = "1"
                ORDER BY b.created_at DESC
                LIMIT :limit';
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAll(int $limit = 10, int $offset = 0): array
    {
        $sql = 'SELECT 
                b.id,
                b.title,
                b.slug,
                b.description,
                b.image,
                b.created_at,
                c.name AS category_name,
                a.admin_name AS author_name
                FROM blogs b
                LEFT JOIN blog_categories c ON b.category_id = c.id
                LEFT JOIN admins a ON b.user_id = a.admin_id
                WHERE b.status = "1"
                ORDER BY b.created_at DESC
                LIMIT :limit OFFSET :offset';
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getBySlug(string $slug): ?array
    {
        $sql = 'SELECT 
                b.id,
                b.title,
                b.slug,
                b.description,
                b.image,
                b.created_at,
                b.updated_at,
                c.name AS category_name,
                a.admin_name AS author_name
                FROM blogs b
                LEFT JOIN blog_categories c ON b.category_id = c.id
                LEFT JOIN admins a ON b.user_id = a.admin_id
                WHERE b.slug = ? AND b.status = "1"
                LIMIT 1';
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$slug]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ?: null;
    }

    public function countAll(): int
    {
        $sql = 'SELECT COUNT(*) FROM blogs WHERE status = "1"';
        $stmt = $this->pdo->query($sql);

        return (int) $stmt->fetchColumn();
    }
}
