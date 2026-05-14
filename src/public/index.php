<?php
session_start();
define('ROOT_PATH', dirname(__DIR__));

require_once ROOT_PATH . '/config/database.php';

// Autoloader
spl_autoload_register(function ($class) {
    foreach (['/app/controllers/', '/app/models/', '/core/'] as $directory) {
        $file = ROOT_PATH . $directory . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

require_once ROOT_PATH . '/core/Auth.php';


// CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Rutas dinámicas /u/{username}
if (preg_match('#^/u/([a-zA-Z0-9_]+)$#', $uri, $matches)) {
    $controller = new ProfileController();
    $controller->show($matches[1]);
    exit;
}

$routes = [
    'GET' => [
        '/'          => ['HomeController',    'index'],
        '/login'     => ['AuthController',    'loginForm'],
        '/register'  => ['AuthController',    'registerForm'],
        '/logout'    => ['AuthController',    'logout'],
        '/settings'  => ['SettingsController','index'],
        '/search'    => ['SearchController',  'index'],
        '/upload'    => ['UploadController', 'index'],
    ],
    'POST' => [
        '/login'     => ['AuthController',    'login'],
        '/register'  => ['AuthController',    'register'],
        '/settings'  => ['SettingsController','update'],
        '/settings/borrar'    => ['SettingsController','update'],  // reutiliza update con action=delete_photo
        '/upload'    => ['UploadController', 'store'],
        '/foto/borrar' => ['UploadController', 'destroy'],
        '/foto/editar' => ['UploadController', 'editCaption'],
    ],
];

$route = $routes[$method][$uri] ?? null;

if ($route) {
    [$class, $action] = $route;
    $controller = new $class();
    $controller->$action();
} else {
    http_response_code(404);
    echo "404 - Página no encontrada";
}
