<?php

namespace App\Middleware;

use App\Utils\SessionUtil;
use App\Models\User;
use App\Models\Session;

class AuthMiddleware
{
    public static function check(): void
    {
        SessionUtil::start();

        if (SessionUtil::has('user')) {
            return;
        }

        $config = require __DIR__ . '/../../config/config.php';
        $cookieName = $config['remember']['cookie_name'];

        if (isset($_COOKIE[$cookieName])) {
            $token = $_COOKIE[$cookieName];
            $userModel = new User();
            $user = $userModel->findByRememberToken($token);

            if ($user) {
                SessionUtil::set('user', [
                    'id' => $user['id'],
                    'login' => $user['login'],
                    'email' => $user['email']
                ]);

                $sessionModel = new Session();
                $newToken = $sessionModel->createRememberToken($user['id']);
                setcookie($cookieName, $newToken, [
                    'expires' => time() + $config['remember']['lifetime'],
                    'path' => '/',
                    'secure' => isset($_SERVER['HTTPS']),
                    'httponly' => true,
                    'samesite' => 'Strict'
                ]);
            } else {
                setcookie($cookieName, '', [
                    'expires' => time() - 3600,
                    'path' => '/'
                ]);
            }
        }
    }

    public static function requireAuth(): void
    {
        self::check();

        if (!SessionUtil::has('user')) {
            SessionUtil::flash('error', 'Необхідна авторизація');
            header('Location: /');
            exit;
        }
    }

    public static function requireGuest(): void
    {
        self::check();

        if (SessionUtil::has('user')) {
            header('Location: /');
            exit;
        }
    }
}