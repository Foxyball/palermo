<?php

class UserRepository
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
            $whereSql = ' WHERE (first_name LIKE :keyword OR last_name LIKE :keyword OR email LIKE :keyword OR id = :id)';
            $params[':keyword'] = '%' . $search . '%';
            $params[':id'] = $search;
        }

        $sql = 'SELECT id, first_name, last_name, email, active, created_at
                FROM users' . $whereSql . ' ORDER BY id DESC LIMIT :lim OFFSET :off';

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
            $whereSql = ' WHERE (first_name LIKE :keyword OR last_name LIKE :keyword OR email LIKE :keyword OR id = :id)';
            $params[':keyword'] = '%' . $search . '%';
            $params[':id'] = $search;
        }

        $sql = 'SELECT COUNT(*) FROM users' . $whereSql;
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return (int) $stmt->fetchColumn();
    }

    public function findById(int $id): ?array
    {
        $sql = 'SELECT id, first_name, last_name, email, active, address, city, phone, zip_code, created_at, updated_at 
                FROM users WHERE id = ? LIMIT 1';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ?: null;
    }

    public function toggleActive(int $id, string $currentStatus): string
    {
        $newStatus = $currentStatus === '1' ? '0' : '1';

        $stmt = $this->pdo->prepare(
            'UPDATE users SET active = ?, updated_at = NOW() WHERE id = ? LIMIT 1'
        );
        $stmt->execute([$newStatus, $id]);

        return $newStatus;
    }

    public function delete(int $id): bool
    {
        try {
            $stmt = $this->pdo->prepare('DELETE FROM users WHERE id = ? LIMIT 1');
            $stmt->execute([$id]);
            return true;
        } catch (Exception $e) {

            return false;
        }
    }

    public function emailExists(string $email, ?int $excludeId = null): bool
    {
        if ($excludeId !== null) {
            $sql = 'SELECT id FROM users WHERE email = ? AND id != ? LIMIT 1';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$email, $excludeId]);
        } else {
            $sql = 'SELECT id FROM users WHERE email = ? LIMIT 1';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$email]);
        }

        return $stmt->fetch() !== false;
    }

    public function create(
        string $firstName,
        string $lastName,
        string $email,
        string $passwordHash,
        ?string $address = null,
        ?string $city = null,
        ?string $phone = null,
        ?string $zipCode = null
    ): int {
        $sql = 'INSERT INTO users (first_name, last_name, email, password, address, city, phone, zip_code, created_at, updated_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$firstName, $lastName, $email, $passwordHash, $address, $city, $phone, $zipCode]);

        return (int) $this->pdo->lastInsertId();
    }

    public function update(
        int $id,
        string $firstName,
        string $lastName,
        string $email,
        ?string $passwordHash = null,
        ?string $address = null,
        ?string $city = null,
        ?string $phone = null,
        ?string $zipCode = null
    ): bool {
        try {
            $fields = ['first_name = ?', 'last_name = ?', 'email = ?', 'address = ?', 'city = ?', 'phone = ?', 'zip_code = ?'];
            $params = [$firstName, $lastName, $email, $address, $city, $phone, $zipCode];

            if ($passwordHash !== null) {
                $fields[] = 'password = ?';
                $params[] = $passwordHash;
            }

            $fields[] = 'updated_at = NOW()';
            $params[] = $id;

            $sql = 'UPDATE users SET ' . implode(', ', $fields) . ' WHERE id = ? LIMIT 1';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);

            return true;
        } catch (Exception $e) {

            return false;
        }
    }

    public function bulkCreate(array $emails, bool $isTestAccount = false): array
    {
        $validEmails = [];
        $invalidEmails = [];
        $existingEmails = [];
        $processedCount = 0;

        foreach ($emails as $email) {
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                if ($this->emailExists($email)) {
                    $existingEmails[] = $email;
                } else {
                    $validEmails[] = $email;
                }
            } else {
                $invalidEmails[] = $email;
            }
        }

        if (!empty($validEmails)) {
            try {
                $this->pdo->beginTransaction();

                foreach ($validEmails as $email) {
                    if ($isTestAccount) {
                        $password = '12345678';
                        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                        $this->create('New', 'User', $email, $passwordHash);
                    } else {
                        // Create user with empty password for password reset flow
                        $this->create('New', 'User', $email, '');
                    }
                    $processedCount++;
                }

                $this->pdo->commit();
            } catch (PDOException $e) {
                $this->pdo->rollback();
                throw $e;
            }
        }

        return [
            'processed' => $processedCount,
            'valid' => $validEmails,
            'invalid' => $invalidEmails,
            'existing' => $existingEmails
        ];
    }
}
