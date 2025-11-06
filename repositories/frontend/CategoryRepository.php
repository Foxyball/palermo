<?php

class CategoryRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getActive(): array
    {
        $sql = 'SELECT id, name, slug FROM categories WHERE active = "1" ORDER BY name ASC';
        $stmt = $this->pdo->query($sql);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
