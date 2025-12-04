<?php
namespace App\Controllers;

use App\Helpers\AuthHelper;
use App\Helpers\DatabaseHelper;
use App\Helpers\GoogleMockHelper;

class AuthController
{
    public function showLoginPage(): void
    {
        if (AuthHelper::isLogged()) {
            $this->redirect('/user/home');
        }
        
        include __DIR__ . '/../../views/auth/login.php';
    }
    
    public function callback(): void
    {
        // Limpar qualquer output
        if (ob_get_level()) {
            ob_clean();
        }
        
        if (AuthHelper::isLogged()) {
            $this->redirect('/user/home');
        }
        
        try {
            if (defined('GOOGLE_MOCK_MODE') && GOOGLE_MOCK_MODE === true) {
                $this->handleMockLogin();
            } else {
                $this->handleGoogleLogin();
            }
        } catch (\Exception $e) {
            error_log("Erro no callback: " . $e->getMessage());
            $_SESSION['flash_error'] = 'Erro ao fazer login. Tente novamente.';
            $this->redirect('/auth/login');
        }
    }
    
    private function handleMockLogin(): void
    {
        $userIndex = $_GET['user'] ?? 0;
        $mockData = GoogleMockHelper::getMockUser($userIndex);
        
        if (!$mockData) {
            $_SESSION['flash_error'] = 'Usuário mock inválido';
            $this->redirect('/auth/login');
        }
        
        $sql = "SELECT * FROM users WHERE google_id = ?";
        $existingUser = DatabaseHelper::fetchOne($sql, [$mockData['google_id']]);
        
        if ($existingUser) {
            $sqlUpdate = "UPDATE users 
                          SET name = ?, 
                              email = ?, 
                              avatar_url = ?,
                              updated_at = NOW()
                          WHERE id = ?";
            
            DatabaseHelper::query($sqlUpdate, [
                $mockData['name'],
                $mockData['email'],
                $mockData['avatar_url'],
                $existingUser['id']
            ]);
            
            $user = DatabaseHelper::fetchOne($sql, [$mockData['google_id']]);
        } else {
            $sqlInsert = "INSERT INTO users 
                          (google_id, name, email, avatar_url, role, created_at, updated_at) 
                          VALUES (?, ?, ?, ?, 'user', NOW(), NOW())";
            
            DatabaseHelper::query($sqlInsert, [
                $mockData['google_id'],
                $mockData['name'],
                $mockData['email'],
                $mockData['avatar_url']
            ]);
            
            $user = DatabaseHelper::fetchOne($sql, [$mockData['google_id']]);
        }
        
        if (!$user) {
            throw new \Exception("Erro ao buscar usuário após criar/atualizar");
        }
        
        // Fazer login
        AuthHelper::loginUser($user);
        
        // Redirecionar
        if ($user['profile_completed'] == 0) {
            $_SESSION['flash_info'] = 'Complete seu perfil para participar dos sorteios';
            $this->redirect('/user/profile');
        } else {
            $this->redirect('/user/home');
        }
    }
    
    private function handleGoogleLogin(): void
    {
        $_SESSION['flash_error'] = 'Login via Google ainda não implementado';
        $this->redirect('/auth/login');
    }
    
    public function logout(): void
    {
        AuthHelper::logout();
        $_SESSION['flash_success'] = 'Logout realizado com sucesso';
        $this->redirect('/auth/login');
    }
    
    public function completeProfile(): void
    {
        AuthHelper::requireLogin();
        $user = AuthHelper::getUser();
        
        if ($user['profile_completed'] == 1) {
            $this->redirect('/user/home');
        }
        
        include __DIR__ . '/../../views/auth/complete_profile.php';
    }
    
    public function saveProfile(): void
    {
        AuthHelper::requireLogin();
        $user = AuthHelper::getUser();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/auth/complete-profile');
        }
        
        $steamTradelink = $_POST['steam_tradelink'] ?? '';
        $phone = $_POST['phone'] ?? '';
        
        if (empty($steamTradelink) || empty($phone)) {
            $_SESSION['flash_error'] = 'Preencha todos os campos';
            $this->redirect('/auth/complete-profile');
        }
        
        $sql = "UPDATE users 
                SET steam_tradelink = ?, 
                    phone = ?, 
                    profile_completed = 1,
                    updated_at = NOW() 
                WHERE id = ?";
        
        DatabaseHelper::query($sql, [$steamTradelink, $phone, $user['id']]);
        
        $user['steam_tradelink'] = $steamTradelink;
        $user['phone'] = $phone;
        $user['profile_completed'] = 1;
        $_SESSION['user'] = $user;
        
        $sqlLog = "INSERT INTO audit_logs (user_id, action, details, ip_address, created_at) 
                   VALUES (?, 'profile_completed', ?, ?, NOW())";
        
        $details = json_encode(['completed_at' => date('Y-m-d H:i:s')]);
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        
        DatabaseHelper::query($sqlLog, [$user['id'], $details, $ip]);
        
        $_SESSION['flash_success'] = 'Perfil completado com sucesso!';
        $this->redirect('/user/home');
    }
    
    /**
     * Método centralizado de redirecionamento
     * Usa header() e JavaScript como fallback
     */
    private function redirect(string $path): void
    {
        $url = BASE_URL . $path;
        
        // Tentar header redirect
        if (!headers_sent()) {
            header('Location: ' . $url);
            exit;
        }
        
        // Fallback: JavaScript redirect
        echo '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="refresh" content="0;url=' . htmlspecialchars($url) . '">
    <title>Redirecionando...</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: linear-gradient(135deg, #0f1419 0%, #1a1f28 100%);
            color: #ffffff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
        }
        .redirect-box {
            text-align: center;
            padding: 40px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 16px;
            border: 1px solid rgba(255, 22, 71, 0.2);
        }
        .spinner {
            border: 3px solid rgba(255, 22, 71, 0.1);
            border-top: 3px solid #ff1647;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        h2 { margin: 0 0 10px; color: #ff1647; }
        p { margin: 0; color: #8b93a7; }
        a {
            color: #ff1647;
            text-decoration: none;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="redirect-box">
        <div class="spinner"></div>
        <h2>Redirecionando...</h2>
        <p>Se não for redirecionado automaticamente, <a href="' . htmlspecialchars($url) . '">clique aqui</a>.</p>
    </div>
    <script>
        setTimeout(function() {
            window.location.href = "' . addslashes($url) . '";
        }, 100);
    </script>
</body>
</html>';
        exit;
    }
}