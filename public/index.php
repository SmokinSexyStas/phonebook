<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Utils\SessionUtil;
use App\Controllers\AuthController;
use App\Controllers\ContactController;

SessionUtil::start();

$config = require __DIR__ . '/../config/config.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = trim($uri, '/');
$method = $_SERVER['REQUEST_METHOD'];

$routes = [
    'GET:' => [AuthController::class, 'showLogin'],
    'GET:register' => [AuthController::class, 'showRegister'],

    'POST:login' => [AuthController::class, 'login'],
    'POST:register' => [AuthController::class, 'register'],
    'POST:logout' => [AuthController::class, 'logout'],

    'GET:contacts' => [ContactController::class, 'index'],
    'GET:contacts/list' => [ContactController::class, 'list'],
    'POST:contacts/create' => [ContactController::class, 'create'],
    'GET:contacts/([0-9]+)' => [ContactController::class, 'show'],
    'POST:contacts/([0-9]+)/delete' => [ContactController::class, 'delete'],
];

try {
    $routeFound = false;
    foreach ($routes as $routePattern => $callback) {
        list($routeMethod, $pattern) = explode(':', $routePattern, 2);

        if ($routeMethod !== $method) {
            continue;
        }

        $pattern = preg_replace('/\//', '\\/', $pattern);
        $pattern = '/^' . $pattern . '$/';

        if (preg_match($pattern, $uri, $matches)) {
            $routeFound = true;
            array_shift($matches);

            $controllerClass = $callback[0];
            $method = $callback[1];

            $controller = new $controllerClass();

            if (!empty($matches)) {
                $controller->$method(...array_map('intval', $matches));
            } else {
                $controller->$method();
            }
            break;
        }
    }

    if (!$routeFound) {
        http_response_code(404);
        echo '404 Not Found';
    }
} catch (Exception $e) {
    if ($config['app']['debug']) {
        echo 'Error: ' . $e->getMessage();
    } else {
        http_response_code(500);
        echo 'Internal Server Error';
    }
}
