<?php

class BlogRepository
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
            $whereSql = ' WHERE (b.title LIKE :keyword OR b.id = :id)';
            $params[':keyword'] = '%' . $search . '%';
            $params[':id'] = $search;
        }

        $sql = 'SELECT 
                b.id,
                b.user_id,
                b.category_id,
                c.name AS category_name,
                a.admin_name AS admin_name,
                g.title AS gallery_title,
                b.gallery_id,
                b.image,
                b.description, 
                b.title, 
                b.status, 
                b.created_at,
                b.updated_at
                FROM blogs b
                LEFT JOIN blog_categories c ON b.category_id = c.id
                LEFT JOIN admins a ON b.user_id = a.admin_id
                LEFT JOIN galleries g ON b.gallery_id = g.id    
                ' . $whereSql . ' ORDER BY b.id DESC LIMIT :lim OFFSET :off';

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
            $whereSql = ' WHERE (b.title LIKE :keyword OR b.id = :id)';
            $params[':keyword'] = '%' . $search . '%';
            $params[':id'] = $search;
        }

        $sql = 'SELECT COUNT(*) FROM blogs b' . $whereSql;
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return (int) $stmt->fetchColumn();
    }

    public function findById(int $id): ?array
    {
        $sql = 'SELECT 
            id, 
            user_id, 
            category_id, 
            gallery_id, 
            image, 
            title, 
            slug, 
            description, 
            status, 
            created_at, 
            updated_at 
            FROM blogs 
            WHERE id = ? LIMIT 1';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ?: null;
    }

    public function getActive(): array
    {
        $sql = 'SELECT id, title, slug FROM blogs WHERE status = "1" ORDER BY created_at DESC';
        $stmt = $this->pdo->query($sql);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getActiveBlogCategories(): array
    {
        $sql = 'SELECT id, name FROM blog_categories WHERE status = "1" ORDER BY name DESC';
        $stmt = $this->pdo->query($sql);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getActiveGalleries(): array
    {
        $sql = 'SELECT id, title FROM galleries WHERE active = "1" ORDER BY title DESC';
        $stmt = $this->pdo->query($sql);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function toggleStatus(int $id, string $currentStatus): string
    {
        $newStatus = $currentStatus === '1' ? '0' : '1';

        $stmt = $this->pdo->prepare(
            'UPDATE blogs SET status = ?, updated_at = NOW() WHERE id = ? LIMIT 1'
        );
        $stmt->execute([$newStatus, $id]);

        return $newStatus;
    }

    public function delete(int $id): bool
    {
        try {
            $stmt = $this->pdo->prepare('DELETE FROM blogs WHERE id = ? LIMIT 1');
            $stmt->execute([$id]);
            return true;
        } catch (Exception $e) {

            return false;
        }
    }

    public function create(
        int $userId,
        int $categoryId,
        ?int $galleryId,
        ?string $imagePath,
        string $title,
        string $slug,
        string $description,
        string $status = '1'
    ): int {
        $sql = 'INSERT INTO blogs (
            user_id,
            category_id,
            gallery_id,
            image,
            title,
            slug,
            description,
            status,
            created_at,
            updated_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            $userId,
            $categoryId,
            $galleryId,
            $imagePath,
            $title,
            $slug,
            $description,
            $status
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function update(
        int $id,
        int $userId,
        int $categoryId,
        ?int $galleryId,
        string $title,
        string $slug,
        string $description
    ): bool {
        try {
            $sql = 'UPDATE blogs SET 
                user_id = ?, 
                category_id = ?, 
                gallery_id = ?, 
                title = ?, 
                slug = ?, 
                description = ?, 
                updated_at = NOW() 
                WHERE id = ? LIMIT 1';

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                $userId,
                $categoryId,
                $galleryId,
                $title,
                $slug,
                $description,
                $id
            ]);

            return true;
        } catch (Exception $e) {

            return false;
        }
    }

    public function updateWithImage(
        int $id,
        int $userId,
        int $categoryId,
        ?int $galleryId,
        string $imagePath,
        string $title,
        string $slug,
        string $description
    ): bool {
        try {
            $sql = 'UPDATE blogs SET 
                user_id = ?, 
                category_id = ?, 
                gallery_id = ?, 
                image = ?, 
                title = ?, 
                slug = ?, 
                description = ?, 
                updated_at = NOW() 
                WHERE id = ? LIMIT 1';

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                $userId,
                $categoryId,
                $galleryId,
                $imagePath,
                $title,
                $slug,
                $description,
                $id
            ]);

            return true;
        } catch (Exception $e) {

            return false;
        }
    }
}
