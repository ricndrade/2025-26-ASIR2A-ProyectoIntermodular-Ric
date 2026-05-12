<?php
session_start();
require_once __DIR__ . '/../config/database.php';

// En index.php, después de session_start():
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Autoloader simple
spl_autoload_register(function ($class) {
    $paths = [
        __DIR__ . '/../app/controllers/' . $class . '.php',
        __DIR__ . '/../app/models/' . $class . '.php',
        __DIR__ . '/../core/' . $class . '.php',
    ];
    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

// Router básico
$uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

$routes = [
    'GET'  => [
        '/'           => ['HomeController',  'index'],
        '/login'      => ['AuthController',  'loginForm'],
        '/logout'     => ['AuthController',  'logout'],
        '/galeria'    => ['FotoController',  'index'],
        '/foto/nueva' => ['FotoController',  'crearForm'],
    ],
    'POST' => [
        '/login'      => ['AuthController',  'login'],
        '/foto/nueva' => ['FotoController',  'crear'],
        '/foto/borrar'=> ['FotoController',  'borrar'],
    ],
];

$route = $routes[$method][$uri] ?? null;

if ($route) {
    [$controllerClass, $action] = $route;
    $controller = new $controllerClass();
    $controller->$action();
} else {
    http_response_code(404);
    echo "404 - Página no encontrada";
}