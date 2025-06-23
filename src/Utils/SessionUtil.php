<?php

namespace App\Utils;

class SessionUtil
{
    private static bool $started = false;

    public static function start(): void
    {
        if (!self::$started) {
            $config = require __DIR__ . '/../../config/config.php';

            session_set_cookie_params([
                'lifetime' => $config['session']['lifetime'],
                'path' => '/',
                'secure' => isset($_SERVER['HTTPS']),
                'httponly' => true,
                'samesite' => 'Strict'
            ]);

            session_name($config['session']['name']);
            session_start();
            self::$started = true;

            if (!self::has('_token')) {
                session_regenerate_id(true);
                self::set('_token', self::generateToken());
            }
        }
    }

    public static function set(string $key, mixed $value): void
    {
        self::start();
        $_SESSION[$key] = $value;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        self::start();
        return $_SESSION[$key] ?? $default;
    }

    public static function has(string $key): bool
    {
        self::start();
        return isset($_SESSION[$key]);
    }

    public static function remove(string $key): void
    {
        self::start();
        unset($_SESSION[$key]);
    }

    public static function destroy(): void
    {
        self::start();
        session_destroy();
        self::$started = false;
    }

    public static function flash(string $key, mixed $value = null): mixed
    {
        if ($value !== null) {
            self::set('_flash_' . $key, $value);
            return $value;
        }

        $flashKey = '_flash_' . $key;
        $value = self::get($flashKey);
        self::remove($flashKey);

        return $value;
    }

    public static function getToken(): string
    {
        return self::get('_token', '');
    }

    public static function generateToken(): string
    {
        return bin2hex(random_bytes(32));
    }

    public static function verifyToken(string $token): bool
    {
        return hash_equals(self::getToken(), $token);
    }
}