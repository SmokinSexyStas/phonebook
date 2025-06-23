<?php

namespace App\Controllers;

use App\Utils\SessionUtil;
use JetBrains\PhpStorm\NoReturn;

abstract class BaseController
{
    private array $post;

    protected array $data = [];

    public function __construct()
    {
        $this->post = $_POST;
    }

    protected function getPost($key, $default = null): mixed
    {
        return $this->post[$key] ?? $default;
    }

    protected function view(string $view, array $data = []): void
    {
        $this->data = array_merge($this->data, $data);

        $this->data['user'] = $this->getCurrentUser();
        $this->data['errors'] = SessionUtil::flash('errors');
        $this->data['success'] = SessionUtil::flash('success');
        $this->data['form_data'] = SessionUtil::flash('form_data');
        $this->data['csrf_token'] = SessionUtil::getToken();

        extract($this->data);

        ob_start();
        require __DIR__ . "/../Views/$view.php";
        $content = ob_get_clean();

        require __DIR__ . '/../Views/layouts/main.php';
    }

    #[NoReturn] protected function json(array $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    #[NoReturn] protected function redirect(string $url): void
    {
        header("Location: $url");
        exit;
    }

    #[NoReturn] protected function back(): void
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        $this->redirect($referer);
    }

    protected function getCurrentUser(): ?array
    {
        return SessionUtil::get('user');
    }

    protected function validateCsrf(): bool
    {
        $token = $_POST['_token'] ?? '';
        return SessionUtil::verifyToken($token);
    }

    protected function isAjax(): bool
    {
        return (
            (isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
                strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') ||

            (isset($_SERVER['CONTENT_TYPE']) &&
                str_contains($_SERVER['CONTENT_TYPE'], 'application/json')) ||

            (isset($_SERVER['HTTP_ACCEPT']) &&
                str_contains($_SERVER['HTTP_ACCEPT'], 'application/json'))
        );
    }
}