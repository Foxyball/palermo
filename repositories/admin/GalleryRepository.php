<?php

class GalleryRepository
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
            $whereSql = ' WHERE (title LIKE :keyword OR id = :id)';
            $params[':keyword'] = '%' . $search . '%';
            $params[':id'] = $search;
        }

        $sql = 'SELECT id, title, active, created_at
                FROM galleries' . $whereSql . ' ORDER BY id DESC LIMIT :lim OFFSET :off';

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
            $whereSql = ' WHERE (title LIKE :keyword OR id = :id)';
            $params[':keyword'] = '%' . $search . '%';
            $params[':id'] = $search;
        }

        $sql = 'SELECT COUNT(*) FROM galleries' . $whereSql;
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return (int) $stmt->fetchColumn();
    }

    public function findById(int $id): ?array
    {
        $sql = 'SELECT id, title, active, created_at, updated_at FROM galleries WHERE id = ? LIMIT 1';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ?: null;
    }

    public function getActive(): array
    {
        $sql = 'SELECT id, title FROM galleries WHERE active = "1" ORDER BY title ASC';
        $stmt = $this->pdo->query($sql);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function toggleActive(int $id, string $currentStatus): string
    {
        $newStatus = $currentStatus === '1' ? '0' : '1';

        $stmt = $this->pdo->prepare(
            'UPDATE galleries SET active = ?, updated_at = NOW() WHERE id = ? LIMIT 1'
        );
        $stmt->execute([$newStatus, $id]);

        return $newStatus;
    }

    public function delete(int $id): bool
    {
        try {
            $stmt = $this->pdo->prepare('DELETE FROM galleries WHERE id = ? LIMIT 1');
            $stmt->execute([$id]);
            return true;
        } catch (Exception $e) {
            error_log('Gallery deletion failed: ' . $e->getMessage());

            return false;
        }
    }

    public function titleExists(string $title, ?int $excludeId = null): bool
    {
        if ($excludeId !== null) {
            $sql = 'SELECT id FROM galleries WHERE title = ? AND id != ? LIMIT 1';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$title, $excludeId]);
        } else {
            $sql = 'SELECT id FROM galleries WHERE title = ? LIMIT 1';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$title]);
        }

        return $stmt->fetch() !== false;
    }

    public function create(string $title, string $active = '1'): int
    {
        $sql = 'INSERT INTO galleries (title, active, created_at, updated_at) VALUES (?, ?, NOW(), NOW())';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$title, $active]);

        return (int) $this->pdo->lastInsertId();
    }

    public function update(int $id, string $title): bool
    {
        try {
            $sql = 'UPDATE galleries SET title = ?, updated_at = NOW() WHERE id = ? LIMIT 1';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$title, $id]);

            return true;
        } catch (Exception $e) {
            error_log('Gallery update failed: ' . $e->getMessage());

            return false;
        }
    }

    public function deleteGalleryImages(int $galleryId): void
    {
        $stmt = $this->pdo->prepare('SELECT image FROM gallery_images WHERE gallery_id = ?');
        $stmt->execute([$galleryId]);
        $images = $stmt->fetchAll(PDO::FETCH_COLUMN);

        foreach ($images as $imagePath) {
            if (!empty($imagePath)) {
                $this->deleteImageFile($imagePath);
            }
        }

        $stmt = $this->pdo->prepare('DELETE FROM gallery_images WHERE gallery_id = ?');
        $stmt->execute([$galleryId]);
    }

    private function deleteImageFile(string $imagePath): void
    {
        $fullPath = __DIR__ . '/../../' . $imagePath;

        if (file_exists($fullPath) && is_file($fullPath)) {
            @unlink($fullPath);
        }
    }
}
