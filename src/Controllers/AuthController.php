<?php

namespace App\Controllers;

use App\Models\Session;
use App\Models\User;
use App\Middleware\AuthMiddleware;
use App\Utils\SessionUtil;
use App\Utils\Validator;
use JetBrains\PhpStorm\NoReturn;

class AuthController extends BaseController
{
    private User $userModel;
    private Session $sessionModel;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = new User();
        $this->sessionModel = new Session();
    }

    public function showLogin(): void
    {
        $this->view('auth/login');
    }

    #[NoReturn] public function login(): void
    {
        if (!$this->validateCsrf()) {
            $this->redirect('/');
        }

        SessionUtil::flash('form_data', ['login' => $this->getPost('login')]);

        $validator = new Validator([
            'login' => $this->getPost('login'),
            'password' => $this->getPost('password'),
        ], [
            'login' => 'required',
            'password' => 'required',
        ]);

        if (!$validator->validate()) {
            SessionUtil::flash('errors', $validator->getErrors());
            $this->back();
        }

        $user = $this->userModel->findByLogin($this->getPost('login'));

        if (!$user || !$this->userModel->verifyPassword($this->getPost('password'), $user->password)) {
            SessionUtil::flash('errors', ['login' => ['Невірний логін або пароль']]);
            $this->back();
        }

        SessionUtil::set('user', [
            'id' => $user->id,
            'login' => $user->login,
            'email' => $user->email,
        ]);

        if ($this->getPost('remember') !== null) {
            $this->setRememberCookie($user->id);
        }

        $this->redirect('/');
    }

    public function showRegister(): void
    {
        AuthMiddleware::requireGuest();

        $this->view('auth/register');
    }

    #[NoReturn] public function register(): void
    {
        AuthMiddleware::requireGuest();

        if (!$this->validateCsrf()) {
            $this->redirect('/register');
        }

        SessionUtil::flash('form_data', [
            'login' => $this->getPost('login'),
            'email' => $this->getPost('email'),
        ]);

        $validator = new Validator([
            'login' => $this->getPost('login'),
            'email' => $this->getPost('email'),
            'password' => $this->getPost('password'),
            'password_confirm' => $this->getPost('password_confirm'),
        ], [
            'login' => 'required|alpha_num|max:16',
            'email' => 'required|email',
            'password' => 'required|password',
            'password_confirm' => 'required',
        ]);

        if (!$validator->validate()) {
            SessionUtil::flash('errors', $validator->getErrors());
            $this->back();
        }

        $errors = $this->registerValidate();

        if (!empty($errors)) {
            SessionUtil::flash('errors', $errors);
            $this->back();
        }

        if ($this->userModel->create([
            'login' => $this->getPost('login'),
            'email' => $this->getPost('email'),
            'password' => $this->getPost('password'),
        ])) {
            SessionUtil::flash('success', 'Реєстрація успішна!');
            $this->redirect('/');
        } else {
            SessionUtil::flash('errors', ['general' => ['Помилка реєстрації. Спробуйте пізніше']]);
            $this->back();
        }
    }

    #[NoReturn] public function logout(): void
    {
        $config = require __DIR__ . '/../../config/config.php';
        $cookieName = $config['remember']['cookie_name'];

        if (isset($_COOKIE[$cookieName])) {
            $this->sessionModel->deleteRememberToken($_COOKIE[$cookieName]);
            setcookie($cookieName, '', [
                'expires' => time() - 3600,
                'path' => '/'
            ]);
        }

        SessionUtil::destroy();
        $this->redirect('/');
    }

    private function setRememberCookie(int $userId): void
    {
        $config = require __DIR__ . '/../../config/config.php';
        $token = $this->sessionModel->createRememberToken($userId);

        setcookie($config['remember']['cookie_name'], $token, [
            'expires' => time() + $config['remember']['lifetime'],
            'path' => '/',
            'secure' => isset($_SERVER['HTTPS']),
            'httponly' => true,
            'samesite' => 'Strict',
        ]);
    }

    private function registerValidate(): array
    {
        $errors = [];

        if ($this->getPost('password') !== $this->getPost('password_confirm')) {
            $errors['password_confirm'] = ['Паролі не співпадають'];
        }

        if ($this->userModel->loginExists($this->getPost('login'))) {
            $errors['login'] = ['Користувач з таким логіном уже існує'];
        }

        if ($this->userModel->emailExists($this->getPost('email'))) {
            $errors['email'] = ['Користувач з такою поштою уже існує'];
        }

        return $errors;
    }
}