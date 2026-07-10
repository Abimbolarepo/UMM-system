<?php

require_once __DIR__ . "/../config/database.php";

class Category
{
    private $db;

    public function __construct(?PDO $db = null)
    {
        $this->db = $db ?? Database::getInstance()->getConnection();
    }

    /*
    |--------------------------------------------------------------------------
    | Get All Categories
    |--------------------------------------------------------------------------
    */
    public function getAllCategories(): array
    {
        $sql = "
            SELECT
                c.*,
                COUNT(sr.request_id) AS total_requests
            FROM categories c
            LEFT JOIN service_requests sr
                ON c.category_id = sr.category_id
            GROUP BY c.category_id
            ORDER BY c.category_name ASC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /*
    |--------------------------------------------------------------------------
    | Get Category By ID
    |--------------------------------------------------------------------------
    */
    public function getCategoryById(int $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM categories
            WHERE category_id = ?
        ");

        $stmt->execute([$id]);

        $category = $stmt->fetch(PDO::FETCH_ASSOC);

        return $category ?: null;
    }

    /*
    |--------------------------------------------------------------------------
    | Create Category
    |--------------------------------------------------------------------------
    */
    public function create(array $data): bool
    {
        $stmt = $this->db->prepare("
            INSERT INTO categories
            (
                category_name,
                description
            )
            VALUES
            (?, ?)
        ");

        return $stmt->execute([
            $data['category_name'],
            $data['description']
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Update Category
    |--------------------------------------------------------------------------
    */
    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare("
            UPDATE categories
            SET
                category_name = ?,
                description = ?
            WHERE category_id = ?
        ");

        return $stmt->execute([
            $data['category_name'],
            $data['description'],
            $id
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Delete Category
    |--------------------------------------------------------------------------
    */
    public function delete(int $id): bool
    {
        // Prevent deleting categories that are in use
        $check = $this->db->prepare("
            SELECT COUNT(*) AS total
            FROM service_requests
            WHERE category_id = ?
        ");

        $check->execute([$id]);

        $count = $check->fetch(PDO::FETCH_ASSOC);

        if ($count['total'] > 0) {
            return false;
        }

        $stmt = $this->db->prepare("
            DELETE FROM categories
            WHERE category_id = ?
        ");

        return $stmt->execute([$id]);
    }

    /*
    |--------------------------------------------------------------------------
    | Search Categories
    |--------------------------------------------------------------------------
    */
    public function search(string $keyword): array
    {
        $keyword = "%{$keyword}%";

        $stmt = $this->db->prepare("
            SELECT *
            FROM categories
            WHERE
                category_name LIKE ?
                OR description LIKE ?
            ORDER BY category_name ASC
        ");

        $stmt->execute([
            $keyword,
            $keyword
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /*
    |--------------------------------------------------------------------------
    | Count Categories
    |--------------------------------------------------------------------------
    */
    public function countCategories(): int
    {
        $stmt = $this->db->query("
            SELECT COUNT(*) AS total
            FROM categories
        ");

        return (int)$stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    /*
    |--------------------------------------------------------------------------
    | Check Duplicate Category Name
    |--------------------------------------------------------------------------
    */
    public function exists(string $name, ?int $excludeId = null): bool
    {
        if ($excludeId) {

            $stmt = $this->db->prepare("
                SELECT category_id
                FROM categories
                WHERE category_name = ?
                AND category_id != ?
            ");

            $stmt->execute([
                $name,
                $excludeId
            ]);

        } else {

            $stmt = $this->db->prepare("
                SELECT category_id
                FROM categories
                WHERE category_name = ?
            ");

            $stmt->execute([$name]);
        }

        return $stmt->fetch() !== false;
    }
}