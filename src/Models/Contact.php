<?php

namespace App\Models;

use PDO;

class Contact
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function create(array $data): int
    {
        $sql = "INSERT INTO contacts (user_id, first_name, last_name, phone, email, image_path) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $data['user_id'],
            $data['first_name'],
            $data['last_name'],
            $data['phone'],
            $data['email'],
            $data['image_path'] ?? null
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function findById(int $id, int $userId): ?array
    {
        $sql = "SELECT * FROM contacts WHERE id = ? AND user_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id, $userId]);

        $contact = $stmt->fetch();
        return $contact ?: null;
    }

    public function findAllByUser(int $userId, int $page = 1, int $perPage = 10): array
    {
        $offset = ($page - 1) * $perPage;

        $sql = "SELECT * FROM contacts 
                WHERE user_id = ? 
                ORDER BY first_name, last_name 
                LIMIT ? OFFSET ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $perPage, $offset]);

        return $stmt->fetchAll();
    }

    public function countByUser(int $userId): int
    {
        $sql = "SELECT COUNT(*) FROM contacts WHERE user_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);

        return (int) $stmt->fetchColumn();
    }

    public function delete(int $id, int $userId): bool
    {
        $sql = "DELETE FROM contacts WHERE id = ? AND user_id = ?";
        $stmt = $this->db->prepare($sql);

        return $stmt->execute([$id, $userId]);
    }

    public function getPaginationData(int $userId, int $page, int $perPage): array
    {
        $total = $this->countByUser($userId);
        $totalPages = ceil($total / $perPage);

        return [
            'current_page' => $page,
            'per_page' => $perPage,
            'total' => $total,
            'total_pages' => $totalPages,
            'has_prev' => $page > 1,
            'has_next' => $page < $totalPages,
            'prev_page' => $page > 1 ? $page - 1 : null,
            'next_page' => $page < $totalPages ? $page + 1 : null
        ];
    }
}