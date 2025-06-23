<?php

namespace App\Utils;

class FileUpload
{
    private array $config;
    private array $errors = [];

    public function __construct()
    {
        $config = require __DIR__ . '/../../config/config.php';
        $this->config = $config['upload'];
    }

    public function upload(array $file, int $userId): ?string
    {
        $this->errors = [];

        if (!$this->validateFile($file)) {
            return null;
        }

        $userDir = $this->config['path'] . $userId . '/';
        if (!is_dir($userDir)) {
            mkdir($userDir, 0755, true);
        }

        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $filename = uniqid() . '_' . time() . '.' . $extension;
        $filepath = $userDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            return $userId . '/' . $filename;
        }

        $this->errors[] = 'Помилка завантаження файлу';
        return null;
    }

    public function delete(string $relativePath): bool
    {
        $fullPath = $this->config['path'] . $relativePath;

        if (file_exists($fullPath)) {
            return unlink($fullPath);
        }

        return true;
    }

    public function getUrl(string $relativePath): string
    {
        return $this->config['url'] . $relativePath;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    private function validateFile(array $file): bool
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $this->errors[] = 'Помилка завантаження файлу';
            return false;
        }

        if ($file['size'] > $this->config['max_size']) {
            $this->errors[] = 'Розмір файлу перевищує дозволений (5MB)';
            return false;
        }

        if (!in_array($file['type'], $this->config['allowed_types'])) {
            $this->errors[] = 'Дозволені тільки JPEG та PNG зображення';
            return false;
        }

        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, $this->config['allowed_extensions'])) {
            $this->errors[] = 'Дозволені розширення: jpg, jpeg, png';
            return false;
        }

        if (!getimagesize($file['tmp_name'])) {
            $this->errors[] = 'Файл не є зображенням';
            return false;
        }

        return true;
    }
}