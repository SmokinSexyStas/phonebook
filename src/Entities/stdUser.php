<?php

namespace App\Entities;

class stdUser
{
    public ?int $id;
    public string $login;
    public string $email;
    public string $password;
    public ?string $createdAt;
    public ?string $updatedAt;

    public function __construct(array $data = [])
    {
        $this->id = $data['id'] ?? null;
        $this->login = $data['login'] ?? '';
        $this->email = $data['email'] ?? '';
        $this->password = $data['password'] ?? '';
        $this->createdAt = $data['created_at'] ?? null;
        $this->updatedAt = $data['updated_at'] ?? null;
    }
}