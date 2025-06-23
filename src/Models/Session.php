<?php

namespace App\Models;

use PDO;

class Session
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function createRememberToken(int $userId): string
    {
        $config = require __DIR__ . '/../../config/config.php';
        $token = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', time() + $config['remember']['lifetime']);

        $this->cleanupRememberTokens($userId);

        $sql = "INSERT INTO user_sessions (user_id, token, expires_at) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $token, $expiresAt]);

        return $token;
    }

    public function deleteRememberToken(string $token): bool
    {
        $sql = "DELETE FROM user_sessions WHERE token = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$token]);
    }

    public function cleanupRememberTokens(int $userId): void
    {
        $sql = "DELETE FROM user_sessions WHERE expires_at < NOW()";
        $this->db->prepare($sql)->execute();

        $sql = "DELETE FROM user_sessions 
                WHERE user_id = ? AND id NOT IN (
                    SELECT id FROM (
                        SELECT id FROM user_sessions 
                        WHERE user_id = ? 
                        ORDER BY created_at DESC 
                        LIMIT 5
                    ) as keep_tokens
                )";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $userId]);
    }
}