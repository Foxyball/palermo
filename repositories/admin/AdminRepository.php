<?php

class AdminRepository
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
            $whereSql = ' WHERE (admin_name LIKE :keyword OR admin_email LIKE :keyword OR admin_id = :admin_id)';
            $params[':keyword'] = '%' . $search . '%';
            $params[':admin_id'] = $search;
        }

        $sql = 'SELECT admin_id, admin_name, admin_email, active, is_super_admin, last_log_date, last_log_ip, created_at
                FROM admins' . $whereSql . ' ORDER BY admin_id DESC LIMIT :lim OFFSET :off';

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
            $whereSql = ' WHERE (admin_name LIKE :keyword OR admin_email LIKE :keyword OR admin_id = :admin_id)';
            $params[':keyword'] = '%' . $search . '%';
            $params[':admin_id'] = $search;
        }

        $sql = 'SELECT COUNT(*) FROM admins' . $whereSql;
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return (int) $stmt->fetchColumn();
    }

    public function findById(int $id): ?array
    {
        $sql = 'SELECT admin_id, admin_name, admin_email, active, is_super_admin, created_at, updated_at 
                FROM admins WHERE admin_id = ? LIMIT 1';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ?: null;
    }

    public function toggleActive(int $id, string $currentStatus): string
    {
        $newStatus = $currentStatus === '1' ? '0' : '1';

        $stmt = $this->pdo->prepare(
            'UPDATE admins SET active = ?, updated_at = NOW() WHERE admin_id = ? LIMIT 1'
        );
        $stmt->execute([$newStatus, $id]);

        return $newStatus;
    }

    public function delete(int $id): bool
    {
        try {
            $stmt = $this->pdo->prepare('DELETE FROM admins WHERE admin_id = ? LIMIT 1');
            $stmt->execute([$id]);
            return true;
        } catch (Exception $e) {

            return false;
        }
    }

    public function emailExists(string $email, ?int $excludeId = null): bool
    {
        if ($excludeId !== null) {
            $sql = 'SELECT admin_id FROM admins WHERE admin_email = ? AND admin_id != ? LIMIT 1';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$email, $excludeId]);
        } else {
            $sql = 'SELECT admin_id FROM admins WHERE admin_email = ? LIMIT 1';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$email]);
        }

        return $stmt->fetch() !== false;
    }

    public function create(string $name, string $email, string $passwordHash, int $isSuperAdmin = 0): int
    {
        $sql = 'INSERT INTO admins (admin_name, admin_email, admin_password, active, is_super_admin, created_at, updated_at) 
                VALUES (?, ?, ?, "1", ?, NOW(), NOW())';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$name, $email, $passwordHash, $isSuperAdmin]);

        return (int) $this->pdo->lastInsertId();
    }

    public function update(int $id, string $name, string $email, ?string $passwordHash = null, ?int $isSuperAdmin = null): bool
    {
        try {
            $fields = ['admin_name = ?', 'admin_email = ?'];
            $params = [$name, $email];

            if ($passwordHash !== null) {
                $fields[] = 'admin_password = ?';
                $params[] = $passwordHash;
            }

            if ($isSuperAdmin !== null) {
                $fields[] = 'is_super_admin = ?';
                $params[] = $isSuperAdmin;
            }

            $fields[] = 'updated_at = NOW()';
            $params[] = $id;

            $sql = 'UPDATE admins SET ' . implode(', ', $fields) . ' WHERE admin_id = ? LIMIT 1';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);

            return true;
        } catch (Exception $e) {

            return false;
        }
    }

    public function isLastSuperAdmin(): bool
    {
        $stmt = $this->pdo->query('SELECT COUNT(*) FROM admins WHERE is_super_admin = 1');
        return ((int) $stmt->fetchColumn()) <= 1;
    }

    public function isSuperAdmin(array $admin): bool
    {
        return (int) ($admin['is_super_admin'] ?? 0) === 1;
    }


    public function getCurrentAdmin(int $adminId): ?array
    {
        $sql = "SELECT admin_id, admin_name, admin_email, active, is_super_admin, 
                last_log_date, last_log_ip, created_at 
                FROM admins WHERE admin_id = ? LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$adminId]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        return $admin !== false ? $admin : null;
    }


    public function updateProfile(int $id, string $name, string $email): bool
    {
        try {
            $sql = 'UPDATE admins SET admin_name = ?, admin_email = ?, updated_at = NOW() 
                    WHERE admin_id = ? LIMIT 1';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$name, $email, $id]);

            return true;
        } catch (Exception $e) {

            return false;
        }
    }

    public function verifyPassword(int $adminId, string $password): bool
    {
        $stmt = $this->pdo->prepare('SELECT admin_password FROM admins WHERE admin_id = ? LIMIT 1');
        $stmt->execute([$adminId]);
        $hash = $stmt->fetchColumn();
        
        return $hash && md5($password) === $hash;
    }


    public function updatePassword(int $adminId, string $newPasswordHash): bool
    {
        try {
            $stmt = $this->pdo->prepare(
                'UPDATE admins SET admin_password = ?, updated_at = NOW() 
                 WHERE admin_id = ? LIMIT 1'
            );
            $stmt->execute([$newPasswordHash, $adminId]);

            return true;
        } catch (Exception $e) {

            return false;
        }
    }


    public function getPasswordHash(int $adminId): ?string
    {
        $stmt = $this->pdo->prepare('SELECT admin_password FROM admins WHERE admin_id = ? LIMIT 1');
        $stmt->execute([$adminId]);
        $hash = $stmt->fetchColumn();
        
        return $hash !== false ? $hash : null;
    }
}