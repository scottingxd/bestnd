<?php
/**
 * Sistema de Sorteios CS2
 * index.php - VERSÃO COMPATÍVEL
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/autoload.php';

use App\Controllers\AuthController;
use App\Controllers\UserController;
use App\Controllers\RaffleController;
use App\Controllers\AdminController;
use App\Helpers\AuthHelper;

// Pegar URL
$requestUri = $_SERVER['REQUEST_URI'];
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Remover query string
$requestUri = strtok($requestUri, '?');

// Remover BASE_URL
if (defined('BASE_URL') && BASE_URL !== '') {
    $basePath = parse_url(BASE_URL, PHP_URL_PATH);
    if ($basePath && strpos($requestUri, $basePath) === 0) {
        $requestUri = substr($requestUri, strlen($basePath));
    }
}

// Garantir que começa com /
if (empty($requestUri) || $requestUri[0] !== '/') {
    $requestUri = '/' . $requestUri;
}

// ==========================================
// ROTA RAIZ
// ==========================================

if ($requestUri === '/' && $requestMethod === 'GET') {
    if (AuthHelper::isLogged()) {
        header('Location: ' . BASE_URL . '/user/home');
    } else {
        header('Location: ' . BASE_URL . '/auth/login');
    }
    exit;
}

// ==========================================
// AUTENTICAÇÃO
// ==========================================

if ($requestUri === '/auth/login' && $requestMethod === 'GET') {
    // Se já está logado, redireciona
    if (AuthHelper::isLogged()) {
        header('Location: ' . BASE_URL . '/user/home');
        exit;
    }
    
    // Exibir página de login diretamente
    include __DIR__ . '/views/auth/login.php';
    exit;
}

if ($requestUri === '/auth/callback' && $requestMethod === 'GET') {
    $controller = new AuthController();
    
    // Tentar diferentes nomes de métodos
    if (method_exists($controller, 'callback')) {
        $controller->callback();
    } elseif (method_exists($controller, 'handleCallback')) {
        $controller->handleCallback();
    } elseif (method_exists($controller, 'googleCallback')) {
        $controller->googleCallback();
    } else {
        // Fallback: implementação inline
        include __DIR__ . '/config/auth_callback_fallback.php';
    }
    exit;
}

if ($requestUri === '/auth/logout' && $requestMethod === 'GET') {
    AuthHelper::logout();
    $_SESSION['flash_success'] = 'Logout realizado com sucesso';
    header('Location: ' . BASE_URL . '/auth/login');
    exit;
}

// ==========================================
// USUÁRIO
// ==========================================

if ($requestUri === '/user/home' && $requestMethod === 'GET') {
    AuthHelper::requireLogin();
    $controller = new UserController();
    $controller->home();
    exit;
}

if ($requestUri === '/user/profile' && $requestMethod === 'GET') {
    AuthHelper::requireLogin();
    $controller = new UserController();
    $controller->profile();
    exit;
}

if ($requestUri === '/user/update-profile' && $requestMethod === 'POST') {
    AuthHelper::requireLogin();
    $controller = new UserController();
    $controller->updateProfile();
    exit;
}

if ($requestUri === '/user/my-entries' && $requestMethod === 'GET') {
    AuthHelper::requireLogin();
    $controller = new UserController();
    $controller->myEntries();
    exit;
}

if ($requestUri === '/user/participate' && $requestMethod === 'POST') {
    AuthHelper::requireLogin();
    $controller = new UserController();
    $controller->participateInRaffle();
    exit;
}

// NOVA ROTA: Deletar participação
if ($requestUri === '/user/delete-entry' && $requestMethod === 'POST') {
    AuthHelper::requireLogin();
    $controller = new UserController();
    $controller->deleteEntry();
    exit;
}

// ==========================================
// RAFFLES
// ==========================================

if (preg_match('#^/raffle/view/(\d+)$#', $requestUri, $matches) && $requestMethod === 'GET') {
    AuthHelper::requireLogin();
    $controller = new RaffleController();
    $controller->view($matches[1]);
    exit;
}

if ($requestUri === '/raffle/results' && $requestMethod === 'GET') {
    $controller = new RaffleController();
    $controller->results();
    exit;
}

// ==========================================
// ADMIN
// ==========================================

if ($requestUri === '/admin/dashboard' && $requestMethod === 'GET') {
    AuthHelper::requireLogin();
    AuthHelper::requireAdmin();
    $controller = new AdminController();
    $controller->dashboard();
    exit;
}

if ($requestUri === '/admin/raffles' && $requestMethod === 'GET') {
    AuthHelper::requireLogin();
    AuthHelper::requireAdmin();
    $controller = new AdminController();
    if (method_exists($controller, 'raffles')) {
        $controller->raffles();
    } else {
        $controller->index();
    }
    exit;
}

if ($requestUri === '/admin/raffle/create' && $requestMethod === 'GET') {
    AuthHelper::requireLogin();
    AuthHelper::requireAdmin();
    $controller = new AdminController();
    $controller->createRaffle();
    exit;
}

if ($requestUri === '/admin/raffle/store' && $requestMethod === 'POST') {
    AuthHelper::requireLogin();
    AuthHelper::requireAdmin();
    $controller = new AdminController();
    $controller->storeRaffle();
    exit;
}

if (preg_match('#^/admin/raffle/edit/(\d+)$#', $requestUri, $matches) && $requestMethod === 'GET') {
    AuthHelper::requireLogin();
    AuthHelper::requireAdmin();
    $controller = new AdminController();
    $controller->editRaffle($matches[1]);
    exit;
}

if (preg_match('#^/admin/raffle/update/(\d+)$#', $requestUri, $matches) && $requestMethod === 'POST') {
    AuthHelper::requireLogin();
    AuthHelper::requireAdmin();
    $controller = new AdminController();
    $controller->updateRaffle($matches[1]);
    exit;
}

if (preg_match('#^/admin/raffle/delete/(\d+)$#', $requestUri, $matches) && $requestMethod === 'POST') {
    AuthHelper::requireLogin();
    AuthHelper::requireAdmin();
    $controller = new AdminController();
    $controller->deleteRaffle($matches[1]);
    exit;
}

if ($requestUri === '/admin/entries' && $requestMethod === 'GET') {
    AuthHelper::requireLogin();
    AuthHelper::requireAdmin();
    $controller = new AdminController();
    $controller->entries();
    exit;
}

if (preg_match('#^/admin/raffle/draw/(\d+)$#', $requestUri, $matches) && $requestMethod === 'POST') {
    AuthHelper::requireLogin();
    AuthHelper::requireAdmin();
    $controller = new AdminController();
    $controller->drawWinner($matches[1]);
    exit;
}

if ($requestUri === '/admin/logs' && $requestMethod === 'GET') {
    AuthHelper::requireLogin();
    AuthHelper::requireAdmin();
    $controller = new AdminController();
    $controller->logs();
    exit;
}

// ==========================================
// 404
// ==========================================

http_response_code(404);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Página Não Encontrada</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: linear-gradient(135deg, #0f1419 0%, #1a1f28 100%);
            color: #ffffff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .error-container { text-align: center; max-width: 600px; }
        .error-code {
            font-size: 8rem;
            font-weight: 800;
            background: linear-gradient(135deg, #ff1647, #cc1138);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 20px;
        }
        .error-title { font-size: 2rem; font-weight: 700; margin-bottom: 15px; }
        .error-message { font-size: 1.1rem; color: #8b93a7; margin-bottom: 40px; }
        .btn-home {
            display: inline-block;
            padding: 14px 32px;
            background: linear-gradient(135deg, #ff1647, #cc1138);
            color: #ffffff;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 600;
            transition: transform 0.3s ease;
        }
        .btn-home:hover { transform: translateY(-2px); }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-code">404</div>
        <h1 class="error-title">Página Não Encontrada</h1>
        <p class="error-message">Desculpe, a página que você está procurando não existe.</p>
        <a href="<?= BASE_URL ?>" class="btn-home">Voltar para Home</a>
    </div>
</body>
</html>