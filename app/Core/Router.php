<?php
namespace App\Core;

class Router
{
    private array $routes = [];
    
    /**
     * Adicionar rota GET
     */
    public function get(string $path, callable $callback): void
    {
        $this->addRoute('GET', $path, $callback);
    }
    
    /**
     * Adicionar rota POST
     */
    public function post(string $path, callable $callback): void
    {
        $this->addRoute('POST', $path, $callback);
    }
    
    /**
     * Adicionar rota PUT
     */
    public function put(string $path, callable $callback): void
    {
        $this->addRoute('PUT', $path, $callback);
    }
    
    /**
     * Adicionar rota DELETE
     */
    public function delete(string $path, callable $callback): void
    {
        $this->addRoute('DELETE', $path, $callback);
    }
    
    /**
     * Adicionar rota
     */
    private function addRoute(string $method, string $path, callable $callback): void
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'callback' => $callback
        ];
    }
    
    /**
     * Executar roteador
     */
    public function run(): void
    {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $requestUri = $_SERVER['REQUEST_URI'];
        
        // Remover query string
        $requestUri = strtok($requestUri, '?');
        
        // Remover BASE_URL do início se existir
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
        
        // Procurar rota correspondente
        foreach ($this->routes as $route) {
            if ($route['method'] !== $requestMethod) {
                continue;
            }
            
            $pattern = $this->convertPathToRegex($route['path']);
            
            if (preg_match($pattern, $requestUri, $matches)) {
                // Remover match completo
                array_shift($matches);
                
                // Executar callback com parâmetros
                call_user_func_array($route['callback'], $matches);
                return;
            }
        }
        
        // Nenhuma rota encontrada - 404
        $this->notFound();
    }
    
    /**
     * Converter path para regex
     */
    private function convertPathToRegex(string $path): string
    {
        // Escapar barras
        $pattern = str_replace('/', '\/', $path);
        
        // Converter {param} para grupo de captura
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([^\/]+)', $pattern);
        
        // Adicionar âncoras
        return '/^' . $pattern . '$/';
    }
    
    /**
     * Página 404
     */
    private function notFound(): void
    {
        http_response_code(404);
        
        if (file_exists(__DIR__ . '/../../views/errors/404.php')) {
            include __DIR__ . '/../../views/errors/404.php';
        } else {
            echo '<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Página Não Encontrada</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: linear-gradient(135deg, #0f1419 0%, #1a1f28 100%);
            color: #ffffff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .error-container {
            text-align: center;
            max-width: 600px;
        }
        .error-code {
            font-size: 8rem;
            font-weight: 800;
            background: linear-gradient(135deg, #ff1647, #cc1138);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 20px;
            line-height: 1;
        }
        .error-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 15px;
            color: #ffffff;
        }
        .error-message {
            font-size: 1.1rem;
            color: #8b93a7;
            margin-bottom: 40px;
            line-height: 1.6;
        }
        .btn-home {
            display: inline-block;
            padding: 14px 32px;
            background: linear-gradient(135deg, #ff1647, #cc1138);
            color: #ffffff;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        .btn-home:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(255, 22, 71, 0.4);
        }
        @media (max-width: 768px) {
            .error-code {
                font-size: 5rem;
            }
            .error-title {
                font-size: 1.5rem;
            }
            .error-message {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-code">404</div>
        <h1 class="error-title">Página Não Encontrada</h1>
        <p class="error-message">
            Desculpe, a página que você está procurando não existe ou foi movida.
        </p>
        <a href="' . (defined('BASE_URL') ? BASE_URL : '/') . '" class="btn-home">
            Voltar para Home
        </a>
    </div>
</body>
</html>';
        }
        exit;
    }
}