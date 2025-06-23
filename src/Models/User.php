<?php

namespace App\Models;

use PDO;
use App\Entities\stdUser;

class User
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function create(array $data): bool
    {
        $sql = "INSERT INTO users (login, email, password) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            $data['login'],
            $data['email'],
            password_hash($data['password'], PASSWORD_DEFAULT)
        ]);
    }

    public function findByLogin(string $login): ?stdUser
    {
        $sql = "SELECT * FROM users WHERE login = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$login]);

        $userData = $stmt->fetch(PDO::FETCH_ASSOC);

        return $userData ? new stdUser($userData) : null;
    }

    public function findByEmail(string $email): ?stdUser
    {
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$email]);

        $userData = $stmt->fetch(PDO::FETCH_ASSOC);
        return $userData ? new stdUser($userData) : null;
    }

    public function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    public function loginExists(string $login): bool
    {
        return $this->findByLogin($login) !== null;
    }

    public function emailExists(string $email): bool
    {
        return $this->findByEmail($email) !== null;
    }

    public function findByRememberToken(string $token): ?stdUser
    {
        $sql = "SELECT * FROM users
                JOIN user_sessions ON users.id = user_sessions.user_id 
                WHERE user_sessions.token = ? AND user_sessions.expires_at > NOW()";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$token]);

        $userData = $stmt->fetch(PDO::FETCH_ASSOC);
        return $userData ? new stdUser($userData) : null;
    }
}