<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/autoload.php';

session_name(SESSION_NAME);
session_set_cookie_params([
    'lifetime' => SESSION_LIFETIME,
    'path' => '/',
    'domain' => '',
    'secure' => !APP_DEBUG,
    'httponly' => true,
    'samesite' => 'Lax'
]);
session_start();

if (isset($_SESSION['last_activity'])) {
    if (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT) {
        session_unset();
        session_destroy();
        session_start();
        $_SESSION['flash_error'] = 'Sessão expirada. Faça login novamente.';
        header('Location: ' . BASE_URL . '/auth/login');
        exit;
    }
}
$_SESSION['last_activity'] = time();

if (!isset($_SESSION['session_id_regenerated'])) {
    $_SESSION['session_id_regenerated'] = time();
} elseif (time() - $_SESSION['session_id_regenerated'] > 1800) {
    session_regenerate_id(true);
    $_SESSION['session_id_regenerated'] = time();
}

$requestUri = $_SERVER['REQUEST_URI'];
$scriptName = dirname($_SERVER['SCRIPT_NAME']);
$baseUrlPath = parse_url(BASE_URL, PHP_URL_PATH) ?: '';

if ($scriptName !== '/' && strpos($requestUri, $scriptName) === 0) {
    $requestUri = substr($requestUri, strlen($scriptName));
}
if ($baseUrlPath !== '/' && strpos($requestUri, $baseUrlPath) === 0) {
    $requestUri = substr($requestUri, strlen($baseUrlPath));
}

$requestUri = '/' . trim($requestUri, '/');
$requestUri = strtok($requestUri, '?');

try {
    $routes = [
        '/' => ['App\Controllers\AuthController', 'login'],
        '/auth/login' => ['App\Controllers\AuthController', 'login'],
        '/auth/callback' => ['App\Controllers\AuthController', 'callback'],
        '/auth/logout' => ['App\Controllers\AuthController', 'logout'],
        
        '/user/home' => ['App\Controllers\UserController', 'home'],
        '/user/profile' => ['App\Controllers\UserController', 'profile'],
        '/user/complete-profile' => ['App\Controllers\UserController', 'completeProfile'],
        '/user/update-profile' => ['App\Controllers\UserController', 'updateProfile'],
        '/user/my-entries' => ['App\Controllers\UserController', 'myEntries'],
        
        '/raffle/participate' => ['App\Controllers\RaffleController', 'participate'],
        '/raffle/results' => ['App\Controllers\RaffleController', 'results'],
        
        '/admin/dashboard' => ['App\Controllers\AdminController', 'dashboard'],
        '/admin/raffles' => ['App\Controllers\AdminController', 'raffles'],
        '/admin/create-raffle' => ['App\Controllers\AdminController', 'createRaffle'],
        '/admin/entries' => ['App\Controllers\AdminController', 'entries'],
        '/admin/approve-entry' => ['App\Controllers\AdminController', 'approveEntry'],
        '/admin/reject-entry' => ['App\Controllers\AdminController', 'rejectEntry'],
        '/admin/logs' => ['App\Controllers\AdminController', 'logs'],
        
        '/privacy' => function() {
            include __DIR__ . '/../views/static/privacy.php';
        }
    ];
    
    if (preg_match('#^/raffle/view/(\d+)$#', $requestUri, $matches)) {
        $controller = new App\Controllers\RaffleController();
        $controller->view((int)$matches[1]);
        exit;
    }
    
    if (preg_match('#^/admin/edit-raffle/(\d+)$#', $requestUri, $matches)) {
        App\Helpers\AuthHelper::requireAdmin();
        $controller = new App\Controllers\AdminController();
        $controller->editRaffle((int)$matches[1]);
        exit;
    }
    
    if (preg_match('#^/admin/delete-raffle/(\d+)$#', $requestUri, $matches)) {
        App\Helpers\AuthHelper::requireAdmin();
        $controller = new App\Controllers\AdminController();
        $controller->deleteRaffle((int)$matches[1]);
        exit;
    }
    
    if (preg_match('#^/admin/draw-winner/(\d+)$#', $requestUri, $matches)) {
        App\Helpers\AuthHelper::requireAdmin();
        $controller = new App\Controllers\AdminController();
        $controller->drawWinner((int)$matches[1]);
        exit;
    }
    
    if (isset($routes[$requestUri])) {
        $route = $routes[$requestUri];
        
        if (is_callable($route)) {
            $route();
        } else {
            [$controllerClass, $method] = $route;
            $controller = new $controllerClass();
            $controller->$method();
        }
    } else {
        http_response_code(404);
        include __DIR__ . '/../views/errors/404.php';
    }
    
} catch (Exception $e) {
    if (APP_DEBUG) {
        echo '<h1>Erro</h1>';
        echo '<pre>' . $e->getMessage() . '</pre>';
        echo '<pre>' . $e->getTraceAsString() . '</pre>';
    } else {
        http_response_code(500);
        include __DIR__ . '/../views/errors/500.php';
    }
}